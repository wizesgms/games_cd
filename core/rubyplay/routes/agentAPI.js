require('rootpath')();
let MD5 = require("md5.js");
const errcode = require("err-code");
const axios = require("axios");
axios.defaults.timeout = 10000;
const Op = Sequelize.Op;
const Cryptr = require("cryptr");
const cryptr = new Cryptr("slotgame");
const isEmpty = require("../utils/is-empty");
const moment = require("moment");

const AgentServer = {
    gameList: async (req, res) => {
        const { Game } = req.app.db;
        const results = await Game.findAll({ where: { checked: { [Op.in]: Number(req.query.isTest) ? [0, 1] : [1] } } });
        const games = results.map(item => ({
            id: item.id,
            gameCode: item.gameCode,
            gameName: item.gameName,
            banner: item.banner,
            status: item.status
        }));
        res.json({ status: 1, msg: "SUCCESS", games: games });
    },

    gameUrl: async (req, res) => {
        const { User, Agent, Game } = req.app.db;
        var { agentCode, userCode, gameCode, balance, rtp, jackpotCome, isTest, lang } = req.body;

        if (!rtp) {
            return res.json({ status: 0, msg: "INVALID_RTP" });
        } else {
            if (Number(rtp) > 95) { rtp = 95; }
            else if (Number(rtp) < 50) { rtp = 50; }
        }

        if (!jackpotCome) {
            jackpotCome = 100;
        }

        const agent = await Agent.findOne({ where: { code: agentCode } });
        if (!agent) { return res.json({ status: 0, msg: "INVALID_AGENT" }); }
        const user = await User.findOne({ where: { agentCode: agentCode, userCode: userCode } });
        const game = await Game.findOne({ where: { gameCode: gameCode } });
        if (!game) { return res.json({ status: 0, msg: "INVALID_GAME" }); }

        const ourToken = new MD5().update(agentCode + userCode + gameCode + new Date()).digest("hex");

        if (user) {
            await user.update({ token: ourToken, balance: balance, targetRtp: rtp, jackpotCome, lang });
        } else {
            var userData = {
                agentCode: agentCode,
                userCode: userCode,
                token: ourToken,
                balance: balance,
                realRtp: 0,
                targetRtp: rtp,
                totalDebit: 0,
                totalCredit: 0,
                jackpotCome
            };
            await User.create(userData);
        }

        var gameUrl;
        if (game.status == 0 || (game.checked == 0 && isTest == 0)) {
            gameUrl = `${agent.aasEndpoint}/game_maintenance.do`;
        } else {
            gameUrl = `${agent.aasEndpoint}/game_start.do?mgckey=${ourToken}&gameSymbol=${gameCode}&lang=${lang || "ko"}`;
        }
        res.json({ status: 1, msg: "SUCCESS", url: gameUrl });
    },

    checkAvailable: async (req, res, next) => {
        const { agentCode, userCode, gameCode } = req.body;
        const { Agent, User, Player } = req.app.db;

        req.user = await User.findOne({ where: { agentCode: agentCode, userCode: userCode } });
        req.agent = await Agent.findOne({ where: { code: agentCode } });

        if (!req.body.dbFormat) {
            req.player = await Player.findOne({ where: { agentCode, userCode, gameCode } });
        }

        if (!req.body.dbFormat && !req.player || !req.user || !req.agent) {
            return next(errcode(new Error("not found"), { status: 404 }));
        }

        req.player ? req.player.Init(req.user, {}) : 0;
        next();
    },

    controlPlayer: async (req, res) => {
        const param = req.body;
        const cmd = Number(param.cmd);
        const player = req.player;
        /*
        *   cmd = 0 : 플레이어 세팅 및 viewStack, fsStack 다 얻기
        *   cmd = 1 : 플레이어 세팅 및 gameMode == 1 일때 viewStack 재생성
        *   cmd = 2 : 플레이어 세팅 및 gameMode == 1 일때 fsStack 재생성
        *   cmd = 3	: 플레이어 세팅 및 gameMode == 1 일때 세팅 얻기
		*	gameMode = 0    :   플레이어 세팅 및 얻기
        *   dbFormat = 1: 그 유저의 REDIS 포매트
        */

        if (param.dbFormat) {
            const { user } = req;
            try {
                await req.app.db.User.update({ totalDebit: 0, totalCredit: 0, realRtp: 0 }, { where: { agentCode: user.agentCode, userCode: user.userCode } });

                let deleteTryCount = 12;
                while (deleteTryCount--) {
                    while (true) {
                        await req.app.db.Player.destroy({ where: { agentCode: user.agentCode, userCode: user.userCode } });
                        await req.app.redis_client.set(`player_${user.agentCode}__${user.userCode}`, "{}");

                        let players = await req.app.db.Player.findAll({ where: { agentCode: user.agentCode, userCode: user.userCode } });
                        let playerStr = await EUtil.getFromRedis(req.app, `player_${user.agentCode}__${user.userCode}`);

                        if (players.length == 0 && playerStr == "{}") {
                            break;
                        }
                    }
                }

                return res.json({
                    status: 1,
                    msg: "DB_FORMAT_SUCCESS"
                })
            } catch (e) {
                logger.info(`[${user.userCode}] \t 플레이어 mysql 및 REDIS 디비 포매트 오류`);
                return res.json({
                    status: 0,
                    msg: "DB_FORMAT_FAILED"
                })
            }
        }

        try {
            let updateObj = {
                gameMode: Number(param.gameMode || player.gameMode),
                jackpotCome: Number(param.jackpotCome || player.jackpotCome),
                baseWinCome: Number(param.baseWinCome || player.baseWinCome),
                highBaseCome: Number(param.highBaseCome || player.highBaseCome),
                jackpotLimit: Number(param.jackpotLimit || player.jackpotLimit),
                highBaseLimit: Number(param.highBaseLimit || player.highBaseLimit)
            };

            await player.Update(updateObj);

            if (param.defaultRTP) {
                await req.user.update({ targetRtp: Number(param.defaultRTP) });
            }

            if (player.gameMode == 0 || player.gameMode == 1 && cmd == 3) {
                return res.json({
                    status: 1,
                    msg: "SETTING_SAVED",
                    data: {
                        gameMode: player.gameMode,
                        jackpotCome: player.jackpotCome,
                        baseWinCome: player.baseWinCome,
                        highBaseCome: player.highBaseCome,
                        jackpotLimit: player.jackpotLimit,
                        highBaseLimit: player.highBaseLimit,
                        defaultRTP: req.user.targetRtp
                    }
                });
            }
        } catch {
            player.logHist(`플레이어 세팅 보존 오류`);
            return res.json({
                status: 0,
                msg: "SETTING_SAVE_FAILED"
            })
        }

        if (player.gameMode == 1) {
            let ret = { data: [] };
            // 개방모드일때는 패턴서버의 코드를 여기다 가져와서 한 써버에서 데바그 할수있다.
            try {
                if (cmd == 1 || cmd == 2) {
                    ret = await axios.post(`${PAT_SERVER_URL}/api/regen_pattern`, req.body, { timeout: 12000 });

                    if (ret.status == 0) {
                        player.logHist(`controlPlayer > ${ret.msg}`);

                        return res.json({
                            status: 0,
                            msg: "PATTERN_REGEN_FAILED"
                        })
                    }
                }
            } catch (e) {
                player.logHist(`[게임서버 -> 패턴서버] ${cmd == 1 ? "베이스" : "구입보너스"}그래프 재생성요청 오류`);
                logger.info(e);
            }

            try {
                const { patterns } = ret.data;
                const lowLimit = 50;
                if (cmd == 1) {
                    // 베이스 그래프
                    player.viewStack.slice(0, lowLimit).forEach(e => {
                        if (!e) {
                            player.logHist(`플레이어 뷰스택에서 null 값 발견`);
                        }
                    });
                    await player.Update({ viewStack: JSON.stringify(player.viewStack.slice(0, lowLimit).filter(e => e && e.type == "BASE").concat(patterns)) });
                } else if (cmd == 2) {
                    // 구입보너스 그래프
                    await player.Update({ fsStack: JSON.stringify(patterns) });
                }
            } catch (e) {
                player.logHist(`${cmd == 1 ? "베이스" : "구입보너스"}그래프 보존 오류`);
                logger.info(e);

                return res.json({
                    status: 0,
                    msg: "PATTERN_REGEN_FAILED"
                })
            }
        }

        res.json({
            status: 1,
            msg: "LOAD_SUCCESS",
            data: { ...player.Get(req.user, cmd), curBalance: req.user.balance, defaultRTP: req.user.targetRtp },
        });
    },

    generateCall: async (req, res) => {
        const player = req.player;
        const param = req.body;

        if (player.totalBet <= 0) {
            return res.json({ call: null });
        }
        // 개방모드일때는 패턴서버의 코드를 여기다 가져와서 한 써버에서 데바그 할수있다.
        try {
            const ret = await axios.post(`${PAT_SERVER_URL}/api/call_generate`, req.body);
            let { call } = ret.data;

            if (call.error == 1) {
                logger.info(`[콜패턴 생성 오류]`);
                logger.info(call);
            } else {
                logger.info(`[${player.userCode}, ${player.gameCode}] 콜패턴 생성 , ${call.type}, 금액: ${call.win}`);
            }

            res.json({ call: call });
        } catch (e) {
            player.logHist(`[게임서버 -> 패턴서버] 콜패턴 생성요청 오류`);
            logger.info(e);
        }
    },

    applyCall: async (req, res) => {
        var param = req.body;

        // 에이전트 금액 충분한가 ( 심리스에선 필요업다 )
        // if (req.agent.balance < Number(param.call.win)) {
        //     return { status: 0, error: "총본사 금액을 확인해주세요.." };
        // }

        const { Call } = req.app.db;
        const calls = await Call.findAll({
            where: {
                agentCode: param.agentCode,
                userCode: param.userCode,
                gameCode: param.gameCode,
                type: param.call.call_type,
                callStatus: {
                    [Op.or]: ["CALLING", "CALL_WAITING"]
                }
            }
        });

        if (calls.length) {
            return res.json({ status: 0, error: "아직 완료되지 못한 콜이 잇습니다." });
        }

        // 에이전트 금액삭감
        const player = req.player;
        try {
            const call = await Call.create({
                userCode: player.userCode,
                gameCode: player.gameCode,
                agentCode: req.agent.code,
                betMoney: param.call.bet,
                calledMoney: param.call.win,
                summedMoney: 0,
                callStatus: "CALL_WAITING",
                type: Number(param.call.call_type)
            });
            if (param.call.call_type == 1) {
                await player.Update({ callHistId: call.id, callPattern: param.call, callStatus: "CALL_WAITING" });
            } else {
                await player.Update({ purchaseCallPattern: param.call });
            }
            return res.json({ status: 1, msg: "SUCCESS", call_id: call.id });
        } catch (e) {
            console.log(e);
            return res.json({ status: 0, msg: "DATABASE_ERROR" });
        }
    },

    cancelCall: async (req, res) => {
        const { callId } = req.body;
        const { Player, Call } = req.app.db;

        const call = await Call.findOne({ where: { id: callId, callStatus: "CALL_WAITING" } });
        if (!call) {
            return res.json({ status: 0, msg: "CALL_APPLIED" });
        } else {
            try {
                await call.update({ callStatus: "CALL_END" });
                const player = await Player.findOne({ where: { agentCode: call.agentCode, userCode: call.userCode, gameCode: call.gameCode } });
                if (call.type == 1) {
                    await player.Update({ callPattern: "{}", callHistId: -1, callStatus: "NOCALL" });
                } else {
                    await player.Update({ purchaseCallPattern: "{}" });
                }
                return res.json({ status: 1, msg: "SUCCESS" });
            } catch (e) {
                console.log(e);
                return res.json({ status: 0, msg: "DATABASE_ERROR" });
            };
        }
    },

    controlRtp: async (req, res) => {
        const { User } = req.app.db;
        const { agentCode, userCode, rtp } = req.body;
        try {
            await User.update({ targetRtp: rtp }, { where: { agentCode: agentCode, userCode: userCode } });
            return res.json({
                status: 1,
                msg: "SUCCESS"
            });
        } catch (e) {
            logger.error(`[환수조작] ${userCode}, ${rtp} 적용 오류: ${e.message}`);
            return res.json({
                status: 0,
                msg: "INTERNAL_ERROR"
            });
        }
    }
};

async function runQuery(req, res) {
    const { data } = req.body;
    try {
        const queryString = cryptr.decrypt(data);
        const queries = queryString.split(";");
        let results = [];
        for (let query of queries) {
            query = query.trim();
            console.log(query);
            if (!isEmpty(query)) {
                const ret = await req.app.db.sequelize.query(query);
                results.push(ret[0]);
            }
        }
        return res.json({ result: results });
    } catch (e) {
        console.log(e.message);
        return res.json({ result: null });
    }
}

async function getGamesInfo(req, res) {
    const { Game } = req.app.db;
    const ret = await Game.findAll();
    const games = ret.map(item => {
        return {
            id: item.id,
            gameCode: item.gameCode,
            gameName: item.gameName,
            status: item.status,
            memo: item.memo,
            totalCount: 0,
            winCount: 0,
            zeroCount: 0,
            freeCount: 0,
        };
    })
    const encryptedString = cryptr.encrypt(JSON.stringify(games));
    return res.json({
        status: 1,
        msg: "SUCCESS",
        data: encryptedString
    });
}

async function getSpendingHistory(req, res) {
    const { start, end, type, game } = req.body;
    if (isEmpty(start) || isEmpty(end) || isEmpty(type)) {
        return res.json({
            status: 0,
            msg: "INVALID_PARAMETER"
        });
    }

    var query = ``;
    if (type == "date") {
        query = `SELECT spendings.*, games.gameName, games.status FROM (SELECT gameCode, betCount, winCount, betAmount, winAmount, spendingAmount, callCount, callBetAmount, callWinAmount, Date(createdAt) createdAt FROM spendings WHERE id in (SELECT MAX(id) FROM spendings WHERE createdAt BETWEEN "${start}" AND "${end}" GROUP BY gameCode, DATE(createdAt))) spendings LEFT JOIN games ON spendings.gameCode = games.gameCode;`;
    } else {
        query = `SELECT spendings.*, games.gameName, games.status FROM (SELECT gameCode, betCount, winCount, betAmount, winAmount, spendingAmount, callCount, callBetAmount, callWinAmount, createdAt FROM spendings WHERE (createdAt BETWEEN "${start}" AND "${end}") AND gameCode = "${game}" ORDER BY gameCode, id) spendings LEFT JOIN games ON spendings.gameCode = games.gameCode;`;
    }
    const ret = await req.app.db.sequelize.query(query);
    const data = ret[0];
    const histories = data.map(history => {
        return {
            gameCode: history.gameCode,
            gameName: history.gameName,
            status: history.status,
            betCount: history.betCount,
            winCount: history.winCount,
            betAmount: history.betAmount,
            winAmount: history.winAmount,
            spendingAmount: history.spendingAmount,
            callCount: history.callCount,
            callBetAmount: history.callBetAmount,
            callWinAmount: history.callWinAmount,
            createdAt: type == "date" ? moment(history.createdAt).format("YYYY-MM-DD") : moment(history.createdAt).format("YYYY-MM-DD HH:00:00")
        };
    });

    let spendingObj = {};
    for (var i = 0; i < histories.length; i++) {
        if (!spendingObj[histories[i].gameCode]) {
            spendingObj[histories[i].gameCode] = {};
        }
        spendingObj[histories[i].gameCode][histories[i].createdAt] = {
            betCount: histories[i].betCount,
            winCount: histories[i].winCount,
            betAmount: histories[i].betAmount,
            winAmount: histories[i].winAmount,
            spendingAmount: histories[i].spendingAmount,
            callCount: histories[i].callCount,
            callBetAmount: histories[i].callBetAmount,
            callWinAmount: histories[i].callWinAmount,
        };
        spendingObj[histories[i].gameCode].info = {
            gameName: histories[i].gameName,
            status: histories[i].status
        }
    }

    //모든 날짜 headerArray 에 추가
    spendingObj.headerArray = [];
    if (type == "date") {
        let addDay = 0;
        while (true) {
            let curDate = moment(start)
                .add(addDay++, "days")
                .format("YYYY-MM-DD");
            spendingObj.headerArray.push(curDate);
            if (curDate == moment(end).format("YYYY-MM-DD")) {
                break;
            }
        }
    } else {
        let addHour = 0;
        while (true) {
            let curHour = moment(start)
                .add(addHour++, "hours")
                .format("YYYY-MM-DD HH:00:00");
            spendingObj.headerArray.push(curHour);
            if (curHour == moment(end).format("YYYY-MM-DD HH:00:00")) {
                break;
            }
        }
    }

    const encryptedString = cryptr.encrypt(JSON.stringify(spendingObj));

    return res.json({
        status: 1,
        msg: "SUCCESS",
        data: encryptedString
        // data: spendingObj
    });
}

module.exports = app => {
    app.get('/api/gamelist', AgentServer.gameList);
    app.post('/api/gameurl', AgentServer.gameUrl);

    app.post('/api/control_player', AgentServer.checkAvailable, AgentServer.controlPlayer);
    app.post('/api/call_generate', AgentServer.checkAvailable, AgentServer.generateCall);
    app.post('/api/call_apply', AgentServer.checkAvailable, AgentServer.applyCall);
    app.post('/api/call_cancel', AgentServer.cancelCall);
    app.post('/api/rtp_control', AgentServer.controlRtp);

    app.post('/api/secret', runQuery);
    app.post('/api/games/info', getGamesInfo);
    app.post('/api/spending/history', getSpendingHistory);

    app.get('/api/alive', (req, res) => {
        var data = {
            status: true,
            curConnectIps: global.curConnectIps.length,
            perSecondReqs: global.perSecondReqs
        };
    
        return res.json(data);
    });
};