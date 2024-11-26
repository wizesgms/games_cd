require('dotenv').config();
const axios = require('axios');
axios.defaults.timeout = 1000 * 60 * 60 * 24;
const fs = require('fs');
const Sequelize = require("sequelize");
const pragmaticDB = new Sequelize("pragmatic", "root", "", {
    dialect: "mysql",
    host: "localhost",
    port: 3306,
    logging: false,
    timezone: "+09:00"
});
const pragmatic02DB = new Sequelize("pragmatic_02", "root", "", {
    dialect: "mysql",
    host: "localhost",
    port: 3306,
    logging: false,
    timezone: "+09:00"
});
const patternDB = new Sequelize("pragmatic_pattern", "root", "", {
    dialect: "mysql",
    host: "localhost",
    port: 3306,
    logging: false,
    timezone: "+09:00"
});
const patCount = 100;

GeneratePattern();

async function GeneratePattern() {
    try {
        await pragmatic02DB.query(`ALTER TABLE games CHANGE COLUMN resStatus parseCount int(11) NOT NULL DEFAULT 0 COMMENT '우리 머신으로 생성한 패턴 갯수' AFTER enName`);
    } catch (e) { }

    let games = [];
    let [delGames] = await pragmatic02DB.query("SELECT gameCode FROM games WHERE status = 2");
    let [games02] = await pragmatic02DB.query("SELECT gameCode FROM games");
    let gamesObj = {};
    let excludeGames = [];

    try {
        excludeGames = require('./excludeGames.json');
    }catch(e) {}

    if (fs.existsSync('./genGames.json')) {
        let gameCodes = require("./genGames.json");
        [games] = await pragmaticDB.query(`SELECT gameCode, gameName FROM games WHERE status = 1 AND checked = 1 AND gameCode IN (${gameCodes.map(e => `"${e}"`).join()}) AND gameCode NOT IN (${excludeGames.map(e => `"${e}"`).join()})`);
    } else {
        [games] = await pragmaticDB.query(`SELECT gameCode, gameName FROM games WHERE status = 1 AND checked = 1 AND gameCode NOT IN (${excludeGames.map(e => `"${e}"`).join()})`);
    }

    for (const game of games) {
        gamesObj[game.gameCode] = { name: game.gameName };
    }

    for (const delGame of delGames) {
        delete gamesObj[delGame.gameCode];
    }

    for (const game02 of games02) {
        if (gamesObj[game02.gameCode]) {
            let parseInfo = { count: 0 };
            try {
                [[parseInfo]] = await patternDB.query(`SELECT COUNT(id) count FROM ${"pat_" + game02.gameCode.toLowerCase()} WHERE parsed = 1 AND gameDone = 1;`);
            } catch (e) { }
            gamesObj[game02.gameCode].parseCount = parseInfo.count;
        }
    }

    let numTotal = Object.keys(gamesObj).length;
    let numSuccess = 0, numFail = 0, numPass = 0;
    const printProcess = () => {
        console.log(`총   ${numTotal} 개\t성공 ${numSuccess} 개\t패스 ${numPass} 개\t실패 ${numFail} 개\t`);
        console.log(`======================================= ${((numSuccess + numPass + numFail) / numTotal * 100).toFixed(2)} % ==========================================`);
    }

    for (let gameCode in gamesObj) {
        console.log(`[생성중]\t${gameCode}`);
        let gameObj = gamesObj[gameCode];
        if (gameObj.parseCount >= 100) {
            await pragmatic02DB.query(`UPDATE games SET parseCount = ${gameObj.parseCount} WHERE gameCode = "${gameCode}"`);
            console.log(`[파싱패턴]\t${gameObj.parseCount} 개`);
            numPass++;
            printProcess();
            continue;
        }

        let startTime = Date.now();
        let timerId = setInterval(() => {
            console.log(`\t\t${gameCode}\t`, Math.floor((Date.now() - startTime) / 1000), "초\t 경과");
        }, 1000 * 60)

        try {
            let { data: genRes } = await axios.post("http://localhost:8949/pattern_gen.do", { gameCode, patCount }, { timeout: 1000 * 60 * 60 * 24 });
            if (genRes.status == 1) {
                console.log(`[성공]\t\t${gameCode}\t${gameObj.name}\t\t${genRes.nPatterns} 개 생성\t`, (Date.now() - startTime) / 1000, "초 경과");
                numSuccess++;
                try {
                    await pragmatic02DB.query(`UPDATE games SET parseCount = ${genRes.nPatterns + gameObj.parseCount} WHERE gameCode = "${gameCode}"`);
                } catch (e) { }
            } else {
                console.log("[실패]", genRes.msg);
                numFail++;
            }
            printProcess();
        } catch (e) {
            console.log("[네트워크 오류]");
            console.log(e.stack, "\n");
            clearTimeout(timerId);
            // break;
        }

        clearTimeout(timerId);
    }

    process.exit(1);
}