const axios = require("axios");

module.exports = (app) => {
    global.io = require('socket.io')(app.server, { 'pingInterval': 1000, 'pingTimeout': 10000000 });

    io.on('connection', function (socket) {
        socket.on("join", async (data) => {
            const { token, gameCode } = data;
            const user = await app.db.User.findOne({ where: { token } });
            
            if(!user){
                console.log(`[Socket Connection join] Error finding user with token<${token}>`);
                return;
            }
            socket.user = user;
            socket.gameCode = gameCode;
            socket.userAddress = socket.conn.remoteAddress.replace('::ffff:', '');

            try {
		        // Notify JUSTSLOT when the game starts
		        const agent = await app.db.Agent.findOne({ where: { code: user.agentCode } });
		        const url = agent.siteEndpoint + "/gamecallback";
		        const jsonBody = {
		            method: "game_start",
		            user_code: user.userCode,
		            provider_code: "PRAGMATIC",
		            game_code: gameCode
		        };

                if (user.agentCode != "lobby") {
                    await sendNoticeEvent(url, jsonBody, "join");
                }
		        console.log(socket.user.userCode, "user connected, gameCode:", gameCode);
            } catch (e) {
                console.log(`[Socket Connection join] Error finding user with token<${token}>`);
                console.log(e.message);
            }
        });

        socket.on('disconnect', async () => {
            if (!socket.user || !socket.gameCode) { return; }
            const player = await app.db.Player.findOne({ where: { userCode: socket.user.userCode, gameCode: socket.gameCode } });

            if (!player) {
                console.log("[Error] Error finding player");
                console.log(`socket.user.userCode = ${socket.user.userCode}`);
                console.log(`socket.gameCode = ${socket.gameCode}`);
                return;
            }

            await player.Update({ connected: 0, token: '' });
            // Notify JUSTSLOT when the game ends
            const agent = await app.db.Agent.findOne({ where: { code: player.agentCode } });
            const url = agent.siteEndpoint + "/gamecallback";
            const jsonBody = {
                method: "game_end",
                user_code: player.userCode,
                provider_code: "PRAGMATIC",
                game_code: player.gameCode
            };

            if (player.agentCode != "lobby") {
                await sendNoticeEvent(url, jsonBody, "end");
            }
            console.log(socket.user.userCode, "user disconnected, gameCode:", socket.gameCode);
        });
    });

    global.sendSocket = function (msg, data, userCode) {
        if (!userCode) {
            io.sockets.emit(msg, data);
        } else {
            for (var curSocket of io.sockets.sockets) {
                if (curSocket[1].user.userCode == userCode) {
                    curSocket[1].emit(msg, data);
                }
            }
        }
    }
};

async function sendNoticeEvent(url, jsonBody, reason) {
    var successflag = false;
    for (var i = 0; i < 10; i++) {
        try {
            let res = await axios.post(url, jsonBody);
            if (res.data.status == 1) {
                successflag = true;
                break;
            }
        } catch (ex) {
            logger.info(`Socket ${reason} notification error ${i + 1} time: ${url}`);
            logger.info(ex);
        }
    }
    if (!successflag) {
        logger.info(`Socket ${reason} notification failed 10 times [Error]: ${url}`);
    }
}
