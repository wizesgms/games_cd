require("rootpath")();
const axios = require("axios");
axios.defaults.timeout = 5000;

module.exports.OnRequest_GameService = async (req, res) => {
    var param = req.body;
    var action = req.body.action;

    const { agent, user, game, player } = await checkAvailable(req, res);
    if (!agent || !user || !game || !player) {
        return;
    }
    const { Event, Call } = req.app.db;

    switch (action) {
        case "doInit": player.HandleInit(param); break;
        case "doSpin":
            if (!player.machine) {
                return HandleError("Please Login!", res);
            }
            await player.HandleSpin(param, user);
            await SaveHistory(req, user, player);
            break;
        case "doCollect": player.HandleCollect(param); break;
        case "doBonus": player.HandleBonus(param); break;
        case "doCollectBonus": player.HandleCollectBonus(param); break;
        case "doMysteryScatter": player.HandleMystery(param); break;
        case "doFSOption": player.HandleFSOption(param); break;
        case "doGambleOption": player.HandleGambleOption(param); break; // 갬불링있는 게임들에서 이용 (다빈치)
        case "doGamble": player.HandleGamble(param); break; //갬불링있는 게임들에서 이용 (다빈치)
        default: break;
    }

    await SaveCallHistory(param, agent, player, Call);
    await HandleBalance(param, player, user, agent, Event);

    var result = Util.Result4Client(player.currentApi);
    await SaveReplay(req.body, player, req.app.db.Replay);

    await player.Save(param);
    return res.send(result);
}

async function SaveCallHistory(param, agent, player, Call) {
    let call = await Call.findOne({ where: { id: player.callHistId } });

    if (!call) { return; }
    if (player.callStatus == "CALL_START") {
        await call.update({
            summedMoney: player.machine.winMoney,
            callStatus: "CALLING",
        });
        player.callStatus = "CALLING";
    } else if (player.callStatus == "CALLING") {
        if (param.action == "doSpin" || param.action == "doGambleOption" || param.action == "doGamble" || (param.action == "doBonus" && !(param.symbol == "vs20doghouse" || param.symbol == "vs20tweethouse" || param.symbol == "vs20amuleteg"))) {
            await call.update({ summedMoney: call.summedMoney += player.machine.winMoney });
        }
    } else if (player.callStatus == "NOCALL") {
        logger.info("[콜금액 어드민에서 삭감] 콜금액:", call.calledMoney, ", 배당금액:", call.summedMoney);
        // TODO 써버 이력저장
        // await agent.update({ balance: agent.balance - call.summedMoney });
        await call.update({
            callStatus: "CALL_END"
        });
        player.callHistId = -1;
        player.callPattern = {};
    }
}

async function HandleBalance(param, player, user, agent, Event) {
    let action = param.action;

    if (action == "doSpin" || action == "doCollect" || action == "doCollectBonus") {
        let debit = 0, credit = 0;

        if (action == "doSpin") {
            debit = player.totalBet;
            credit = 0;
        } else {
            debit = 0;
            credit = player.balance - player.machine.prevBalance;
        }

        if (debit > 0 || credit > 0) {
            player.setBalance(debit, credit);

            var logStr = `(${player.curIndex} - ${player.userCode}, ${player.gameCode}, ${user.balance})`;

            if (global.logConfig.show_index) {
                logStr += `, (${player.curIndex - player.lastJackpotIndex}/${player.nextJackpot}, Call:${player.callHistId})`;
            }
            if (global.logConfig.show_pattern) {
            }
            if (global.logConfig.show_rtp) {
                logStr += `, (${Math.floor(credit)}/${debit}, player:${player.totalCredit}/${player.totalDebit}, ${player.realRtp}%, Total:${user.totalCredit}/${user.totalDebit}, RTP:${user.realRtp}/${user.targetRtp})`;
            }
            if (player.logInfo) {
                logStr += `, ${player.logInfo.type}: (${player.logInfo.range})`
            }
            if (player.machine.gameSort != "BASE" && player.machine.currentGame == "BASE") {
                logStr += `, Free Finish`;
            }
            player.logHist(logStr, 1);

            var eventObj = {
                userCode: user.userCode,
                gameCode: param.symbol,
                agentCode: agent.code,
                txnID: player.txnID,
                bet: debit,
                win: credit,
                type: (debit > 0 ? player.machine.currentGame : player.machine.gameSort).toUpperCase(),
                txnType: debit > 0 ? (player.viewCache.win > 0 ? "debit" : "debit_credit") : "credit"
            };
            let callObj = player.call ? player.call : {};
            await sendEvent2Agent(agent, eventObj, Event, player.virtualBet, player.callHistId, callObj);

            await user.setBalance(debit, credit, player.callHistId);
            await agent.setBalance(debit, credit, player.callHistId);
        }
    }

    if (action == "doBonus" && player.machine.gambleStatus == "GAMBLE" && player.machine.gambleResult != "NORESULT") {
        var eventObj = {
            userCode: user.userCode,
            gameCode: param.symbol,
            agentCode: agent.code,
            txnID: player.txnID,
            bet: 0,
            win: 0,
            type: player.machine.gambleResult,
            txnType: "debit_credit"
        };
        let callObj = player.call ? player.call : {};
        await sendEvent2Agent(agent, eventObj, Event, player.virtualBet, player.callHistId, callObj);
        logger.info(`토르 머신 agentCode: ${player.agentCode}, userCode: ${player.userCode}`);
        logger.info(JSON.stringify(player.machine));
        logger.info(`============================`)
    }
}

async function SaveReplay(param, player, Replay) {
    if (param.action == "doInit" || param.action == "doFSOption") {
        return;
    }

    delete param.mgckey;

    let lastPattern = { ...player.lastPattern };
    if (lastPattern.na == "fso") {
        delete lastPattern.fs_opt_mask;
        delete lastPattern.fs_opt;
        lastPattern.na = "s";
    }

    player.replayLogList.push({
        cr: Buffer.from(Util.Result4Client(param), "utf8").toString("base64"),
        sr: Buffer.from(Util.Result4Client(lastPattern), "utf8").toString("base64")
    });

    let gameMode = !player.machine.currentGame ? player.machine.gameSort : player.machine.currentGame;

    if (gameMode == "BASE" && player.machine.winMoney == 0 && player.replayLogList.length == 1) {
        player.replayLogList = [];
    }

    if (param.action == "doCollect" || param.action == "doCollectBonus") {
        const totalWin = player.balance - player.machine.prevBalance;
        if (totalWin >= player.virtualBet * 2) {
            Replay.create({
                agentCode: player.agentCode,
                userCode: player.userCode,
                gameCode: player.gameCode,
                roundID: Date.now(),
                bet: player.virtualBet,
                win: totalWin,
                rtp: Math.floor((totalWin / player.virtualBet) * 100) / 100,
                playedDate: Math.floor(Date.now() / 1000) * 1000,
                data: JSON.stringify(player.replayLogList)
            });
        }
        player.replayLogList = [];
    }

    await player.Update({
        replayLogList: JSON.stringify(player.replayLogList)
    });
}

async function SaveHistory(req, user, player) {
    const { History } = req.app.db;
    const reqObj = req.body;
    let lastPattern = player.lastPattern;
    for (const key in lastPattern) {
        if (lastPattern.hasOwnProperty(key)) {
            const elem = lastPattern[key];
            if (Array.isArray(elem)) {
                lastPattern[key] = elem.toString();
            }
        }
    }

    if (reqObj.action === 'doSpin') {
        let apiObj = {
            request: reqObj,
            response: lastPattern
        };
        let histObj = {
            agentCode: player.agentCode,
            userCode: player.userCode,
            gameCode: player.gameCode,
            roundID: Date.now(),
            bet: player.virtualBet,
            win: player.machine.winMoney,
            balance: user.balance,
            data: JSON.stringify(apiObj)
        };
        await History.create(histObj);
    }
}

function HandleError(msg, res) {
    var obj = {
        action: "error",
        msg: msg
    }

    var result = Util.Result4Client(obj);
    return res.send(result);
}

async function checkAvailable(req, res) {
    const param = req.body;
    const gameCode = param.symbol;
    const token = param.mgckey;

    const { Game, User, Player, Agent } = req.app.db;
    const game = await Game.findOne({ where: { gameCode, status: 1 } });
    if (!game) { return HandleError("GAME_NOT_ALLOWED", res); }

    const user = await User.findOne({ where: { token } });
    if (!user) { return HandleError("INVALID_TOKEN", res); }

    const agent = await Agent.findOne({ where: { code: user.agentCode } });

    let redis_str = await EUtil.getFromRedis(req.app, `player_${user.agentCode}__${user.userCode}`);
    let redisObj = JSON.parse(redis_str ? redis_str : "{}");
    let player = redisObj[gameCode] ? Player.build(redisObj[gameCode]) : null;

    if (!(player instanceof Player)) {
        [player, created] = await Player.findOrCreate({ where: { gameCode, userCode: user.userCode, agentCode: user.agentCode } });
        if (created) {
            Object.assign(player, { jackpotCome: user.jackpotCome, nextJackpot: Math.floor(Math.random() * 20) * 7 + 60 });
        }
    }

    Object.assign(player, { token, connected: 1 });

    if (player.totalCredit >= player.totalDebit + 1000000) {
        logger.info(`[플레이어 환수율 오버] 오류 시작`);
        logger.info(player.toJSON());
        logger.info(`[플레이어 환수율 오버] 오류 끝`);
        return HandleError("GAME_CREDIT_ERROR", res);
    }

    await player.Init(user, param);
    const totalBet = player.SetTotalBet(param);

    if (player.machine.currentGame == "BASE" && player.machine.tumbleStatus != "TUMBLE" && totalBet > agent.balance) {
        logger.info(`오류 :   에이전트금액:${agent.balance}, 배팅: ${totalBet} `);
        return HandleError("Insufficient Agent Balance", res);
    }

    //에이전트에 유저금액 확인
    const url = agent.siteEndpoint + "/gamecallback";
    if (agent.code != "lobby") {
        const jsonBody = {
            method: "user_balance",
            user_code: user.userCode
        };

        var successflag = false;
        for (var i = 0; i < 10; i++) {
            try {
                let res = await axios.post(url, jsonBody);
                let uinfo = res.data;
                if (uinfo.status == 1) {
                    successflag = true;
                    user.balance = Number(uinfo.balance);
                    user.agentBalance = Number(uinfo.agentBalance);
                    user.agentType = Number(uinfo.agentType);
                    if (user.agentType == 2 && uinfo.subAgentBalance) {
                        user.subAgentBalance = Number(uinfo.subAgentBalance);
                    }
                    // logger.info(`${i + 1} 번째만에 얻기성공: ${url}`);
                    // console.log(uinfo);
                    break;
                }
            } catch (ex) {
                logger.info(`${i + 1} 번째 유저금액 얻기 오류: ${url}`);
                logger.info(ex);
            }
        }
        if (!successflag) {
            logger.info(`10 번째만에도 성공못함 [Error]: ${url}`);
            return HandleError("Network Error", res);
        }
    }

    if (req.body.action != "doInit" && player.machine.currentGame == "BASE" && player.machine.tumbleStatus != "TUMBLE") {
        if (Number(totalBet) > user.balance) {
            logger.info(`유저금액 충전하세요 :   에이전트:${user.agentCode}, 유저:${user.userCode} 보유:${user.balance}, 배팅: ${totalBet} `);
            if (user.lang == "en") {
                return HandleError("Please charge your cash to bet.", res);
            }
            return HandleError("베팅을 하려면 캐셔에서 고객님의 계좌로 송금을 추가하십시오.", res);
        }
        if (Number(totalBet) > user.agentBalance) {
            logger.info(`에이전트 금액 충전하세요 :   에이전트:${user.agentCode},에이전트금액:${user.agentBalance}, 유저:${user.userCode} 보유:${user.balance}, 배팅: ${totalBet} `);
            return HandleError("Insufficient Just Agent Balance", res);
        }
        if (user.subAgentBalance && Number(totalBet) > user.subAgentBalance) {
            logger.info(`[Sub 에이전트 금액 오류] ${user.userCode} 에이전금액 충전하세요, 보유:${user.subAgentBalance}, 배팅: ${totalBet} `);
            return HandleError("Insufficient Sub Agent Balance", res);
        }
    }

    player.balance = user.balance;
    return { agent, user, game, player };
}

async function sendEvent2Agent(agent, eventObj, Event, virtualBet, callHistId, callObj = {}) {
    var event = await Event.create(eventObj);

    if (agent.code == "lobby") {
        await event.update({ checked: 1 });
        return;
    }
    const url = agent.siteEndpoint + "/gamecallback";
    var jsonBody = {
        method: "transaction",
        round_id: event.id,
        user_code: event.userCode,
        game_code: event.gameCode,
        txn_id: event.txnID,
        bet: event.bet,
        win: event.win,
        type: event.type,
        virtual_bet: virtualBet,
        provider_code: "PRAGMATIC",
        sub_provider_code: "PRAGMATIC",
        call: callObj,
        txn_type: event.txnType,
        call_hist_id: callHistId
    };
    var response = null;

    for (var i = 0; i < 5; i++) {
        try {
            var res = await axios.post(url, jsonBody)
            response = res.data;
            if (response.status == 1) {
                // logger.info(`[이벤트 처리] ${i + 1}번째 시도 [성공] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                await event.update({ checked: 1 });
                break;
            } else {
                logger.info(`[이벤트 처리] ${i + 1}번째 시도 [요청 오류] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                logger.info("요청:")
                logger.info(jsonBody);
                if (response) {
                    logger.info("응답:")
                    logger.info(response);
                }
            }
        } catch (ex) {
            logger.info(`[이벤트 처리] ${i + 1}번째 시도 [요청 오류] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
            logger.info(ex)
        }
        logger.info("-------");
    }

    return response;
}