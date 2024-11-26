const axios = require("axios");
axios.defaults.timeout = 5000;

const interval = process.env.EVENT_INTERVAL || 1000 * 60; // 호출시간 간격, 기정으로 1분
let mainApp;

module.exports = async (app) => {
    mainApp = app;
    // 기본 루프 함수 실행
    setTimeout(mainLoop, 5000);
};

const mainLoop = async () => {
    const Event = mainApp.db.Event;
    const Agent = mainApp.db.Agent;
    const Game = mainApp.db.Game;
    const startTime = Date.now();
    // logger.info("================ [미처리이벤트 처리 루프 시작] =================");

    const events = await Event.findAll({
        where: { checked: 0, createdAt: { [Op.lt]: new Date(Date.now() - 1000 * 60) } },  //1분전의 미처리이벤트들  지금 현재것도 얻어오므로..
    });
    // logger.info(`[미처리이벤트 처리] 미처리이벤트: ${events.length}개,   소요시간: ${Date.now() - startTime}ms`);

    let successCnt = 0;
    for (const event of events) {
        // 1시간지난 미처리이벤트는 2로 한다.
        if (Date.now() - event.createdAt > 1000 * 60 * 60) {
            event.update({ checked: 2 });
            continue;
        }
        const agent = await Agent.findOne({ where: { code: event.agentCode } });
        const game = await Game.findOne({ where: { gameCode: event.gameCode } });

        if (agent && game) {
            const url = agent.siteEndpoint + "/gamecallback";
            const jsonBody = {
                method: "transaction",
                round_id: event.id,
                user_code: event.userCode,
                game_code: game.gameCode,
                game_name: game.gameName,
                txn_id: event.txnID,
                bet: event.bet,
                win: event.win,
                type: event.type,
                missed: 1,
                provider_code: "PRAGMATIC",
                sub_provider_code: "PRAGMATIC",
                txn_type: event.txnType
            };

            var response = null;
            for (var i = 0; i < 10; i++) {
                try {
                    // logger.info(`[미처리이벤트 처리] [요청] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                    var res = await axios.post(url, jsonBody)
                    response = res.data;
                    if (response.status == 1) {
                        // logger.info(`[미처리이벤트 처리] [요청 성공] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                        await event.update({ checked: 3 });
                        successCnt++;
                        break;
                    } else {
                        logger.info(`[미처리이벤트 처리] ${i + 1}번째 시도 [요청 오류] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                        logger.info("요청:")
                        logger.info(jsonBody);
                        if (response) {
                            logger.info("응답:")
                            logger.info(response);

                        }
                    }
                } catch (ex) {
                    logger.info(`[미처리이벤트 처리] ${i + 1}번째 시도 [요청 오류] ${url} | ${jsonBody.user_code}| ${jsonBody.game_code}`);
                    logger.info(ex)
                }
                // logger.info("-------");
            }
        } else {
            logger.info(`[미처리이벤트 처리] [오류] 에이젼트 또는 게임 정보 없음.`);
            logger.info(agent);
            logger.info(game);
        }
    }

    // logger.info(`[미처리이벤트 처리] 총 ${events.length}개중 ${successCnt}개 성공, ${(events.length - successCnt)}개 실패 ,  총 소요시간: ${Date.now() - startTime}ms`);
    // logger.info("================ [미처리이벤트 처리 루프 완료] =================");

    setTimeout(mainLoop, interval); //1분에 한번씩 꼬리물기 한다.
};