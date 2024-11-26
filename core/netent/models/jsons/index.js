const DB_SAVE = 1;
const TRUNCATE = 0;
const COUNT_LIMIT = 0;
const ONLY_INIT = 0;
const GAME_CODE = "vs10goldfish";
const RUN_MODE = 1;     // 1: 파일들 실행, 0: 한개만
const INFO_VIEW = 1;

const fs = require('fs');
const path = require('path');
const AnalysePatterns = require("./pragmatic");

let g_games = [];
let nfs_games = [];
let moneyBonus_games = [];
let box_games = [];
let pur_games = [];
let fsp_games = [];
let doBonus_games = [];
let free_error_games = [];

let files_info = [];
let paramInfo = {};
let gamesInfo = {};

CreatePattern();

async function CreatePattern() {
    const fileName = process.argv[3];

    fs.readdir(process.argv[2] || "./", async (err, files) => {
        if (err) {
            console.log("파일 리스트 얻기 오류", err);
            throw err;
        }
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (fileName && fileName != file) {
                continue;
            }
            if (path.extname(file) == ".json" && file.startsWith("vs")) {
                var game_code = file.split(".")[0].split("__")[0];
                // if (file.split(".")[0].split("__").length == 1 ) { continue; }  // vs20midas.json "__" 가 없는 파일은 넘기기
                if (!RUN_MODE && game_code != GAME_CODE) { continue; }
                console.log(i, file + " [작업중...]");

                let insertRes = await AnalysePatterns(game_code, require("./" + file), {
                    DB_SAVE,
                    TRUNCATE,
                    COUNT_LIMIT,
                    ONLY_INIT,
                    PARSED: fileName ? 1 : 0
                });

                if (insertRes.status == 1) {
                    infoHandle(file, game_code, insertRes.info);
                    console.log("\n[패턴 삽입 성공", insertRes.info);
                } else {
                    console.log("[패턴 삽입 오류]", file, insertRes.msg);
                }
                console.log("-----------");
            } else {
                console.log(i, file, " [Invalid File or Name]");
            }
        }

        if (INFO_VIEW) {
            for (const key in gamesInfo) {
                if (gamesInfo[key].hasGApi != undefined) {
                    gamesInfo[key].hasGApi ? g_games.push(key) : 0;
                    !gamesInfo[key].hasFs ? nfs_games.push(key) : 0;
                    gamesInfo[key].hasMoneyBonus ? moneyBonus_games.push(key) : 0;
                    gamesInfo[key].hasBoxSelect ? box_games.push(key) : 0;
                    gamesInfo[key].hasPur ? pur_games.push(key) : 0;
                    gamesInfo[key].hasFsp ? fsp_games.push(key) : 0;
                    gamesInfo[key].hasDoBonus ? doBonus_games.push(key) : 0;
                    gamesInfo[key].hasFreeError ? free_error_games.push(key) : 0;
                }
            }
            console.log("g_games\n", g_games.length, JSON.stringify(g_games));
            console.log("nfs_games\n", nfs_games.length, "\n", JSON.stringify(nfs_games));
            console.log("moneyBonus_games\n", moneyBonus_games.length, "\n", JSON.stringify(moneyBonus_games));
            console.log("box_games\n", box_games.length, "\n", JSON.stringify(box_games));
            console.log("pur_games\n", pur_games.length, "\n", JSON.stringify(pur_games));
            console.log("fsp_games\n", fsp_games.length, "\n", JSON.stringify(fsp_games));
            console.log("doBonus_games\n", doBonus_games.length, "\n", JSON.stringify(doBonus_games));
            console.log("free_error_games\n", free_error_games.length, "\n", JSON.stringify(free_error_games));
            console.log("\nfiles_info\n", JSON.stringify(files_info));
        }

        console.log(JSON.stringify(paramInfo));
        process.exit(1);
    });
}

function infoHandle(file, gameCode, info) {
    !gamesInfo[gameCode] ? gamesInfo[gameCode] = {} : 0;
    gamesInfo[gameCode].hasGApi == undefined ? gamesInfo[gameCode] = { ...gamesInfo[gameCode], hasGApi: 0, hasFs: 0, hasMoneyBonus: 0, hasBoxSelect: 0, hasPur: 0, hasFsp: 0, hasDoBonus: 0, hasFreeError: 0 } : 0;

    gamesInfo[gameCode].hasGApi |= info.hasGApi;
    gamesInfo[gameCode].hasFs |= info.hasFs;
    gamesInfo[gameCode].hasMoneyBonus |= info.hasMoneyBonus;
    gamesInfo[gameCode].hasBoxSelect |= info.hasBoxSelect;
    gamesInfo[gameCode].hasPur |= info.hasPur;
    gamesInfo[gameCode].hasFsp |= info.hasFsp;
    gamesInfo[gameCode].hasDoBonus |= info.hasDoBonus;
    gamesInfo[gameCode].hasFreeError |= info.hasFreeError;

    files_info.push({
        file,
        gameCode,
        hasFs: info.hasFs,
        hasMoneyBonus: info.hasMoneyBonus,
        hasBoxSelect: info.hasBoxSelect,
        hasPur: info.hasPur,
        hasFsp: info.hasFsp,
        hasDoBonus: info.hasDoBonus,
        hasFreeError: info.hasFreeError
    })

    let keys = Object.keys(paramInfo);

    info.gameParams.forEach(e => {
        if (keys.indexOf(e) < 0) {
            paramInfo[e] = 1;
        } else {
            paramInfo[e]++;
        }
    });
}