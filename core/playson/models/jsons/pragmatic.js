let DB_SAVE = 0;
let TRUNCATE = 0;
let COUNT_LIMIT = 0;
let ONLY_INIT = 0;
let PARSED = 0;
const queryString = require("query-string");
const Sequelize = require("sequelize");
const JSON5 = require("JSON5");
const patternDB = new Sequelize("pragmatic_pattern", "root", "", {
    dialect: "mysql",
    host: "localhost",
    port: 3306,
    logging: false,
    timezone: "+09:00"
});
const pragmaticDB = new Sequelize("pragmatic_02", "root", "", {
    dialect: "mysql",
    host: "localhost",
    port: 3306,
    logging: false,
    timezone: "+09:00"
});

let createSQL = `CREATE TABLE ___TABLE_NAME___  (
    id int(11) NOT NULL AUTO_INCREMENT,
    gameCode varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    pType varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    type varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    gameDone tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Free, Bonus 일때 true',
    idx varchar(22) NULL DEFAULT NULL COMMENT 'pur, fsp, ind',
    parsed tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 정품, 1: 파싱',
    big int(20) NULL DEFAULT NULL,
    small int(20) NULL DEFAULT NULL,
    win double(20, 2) NOT NULL DEFAULT 0,
    totalWin double(20, 2) NOT NULL DEFAULT 0,
    totalBet double(20, 2) NOT NULL DEFAULT 0,
    virtualBet double(20, 2) NOT NULL DEFAULT 0 COMMENT '프리패턴에서 multi를 계산하기위해 저장하는 이전 배팅금',
    rtp double(10, 0) NULL DEFAULT NULL,
    balance varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
    pattern text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
    createdAt datetime(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
    updatedAt datetime(0) NULL DEFAULT NULL,
    PRIMARY KEY (id) USING BTREE
  ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;`;

const blocked_games = [
    "vs432congocash",
    "vs20terrorv",
    "vs25kingdoms",
    "vs40wanderw",
    "vs75empress",
    "vs20eking",
    "vs5drmystery",
    "vs4096mystery",
    "vs75bronco",

    "vs25davinci",      // 마지막돈 Gamble, fso 아니다
    "vs15fairytale",    // 마지막돈 Gamble, fso 아니다
    "vswayshammthor",   // 선택 Gamble, fso 아니다
    "vswaysyumyum",     // 쌍방향선택 Gamble
    "vs20chicken",      // 선택 Gamble, fso 아니다
    "vswaysxjuicy",     // 쌍방향선택 Gamble 
];

const moneyParams = [
    "balance",
    "c",
    "w",
    "tw",
    "rw",
    "coef",
    "fswin",
    "fsres",
    "fswin_total",
    "fsres_total",
    "tmb_win",
    "tmb_res",
];

const cb_games = [
    "vs20honey",
    "vs20kraken",
    "vs20leprechaun",
    "vs20superx"
];

const respin_free_games = [
    "vs20emptybank",
    "vs25btygold",
    "vs10starpirate",
    "vs20daydead",
    "vs20aladdinsorc",
    "vs8magicjourn",
    "vs20hercpeg",
    "vs5trdragons",
    "vs10luckcharm",
    "vs10amm",
    "vs20rockvegas",
    "vs10snakeladd"
];

const non_FSO_games = [
    "vs20kraken",
    "vs20santa",
    "vs15diamond",
    "vs243lionsgold",
    "vs243fortune",
    "vs243caishien",
    "vs10amm",
    "vs243queenie",
    "vs20leprechaun",
    "vs20leprexmas",
    "vs25peking"
];

const bigScatter_FSO_games = {
    "vswaysdogs": 3,
    "vswaysaztecking": 4,
    "vs10goldfish": 3
};

const new_FSO_games = [
    "vs10egrich",
    "vs243koipond"
];

const uniqueFreeGames = [
    "vs10bookfallen"
];

let pTypeArr = [];

module.exports = async (gameCode, content, option = {}) => {
    DB_SAVE = option.DB_SAVE || DB_SAVE;
    TRUNCATE = option.TRUNCATE || TRUNCATE;
    COUNT_LIMIT = option.COUNT_LIMIT || COUNT_LIMIT;
    ONLY_INIT = option.ONLY_INIT || ONLY_INIT;
    PARSED = option.PARSED || PARSED;

    if (isExisting(gameCode, blocked_games)) {
        console.log("연관, 슈퍼 앤티베팅 등의 이유로 개발자님에 의해 블럭되엇습니다.\n-----------");
        return {
            status: 2,
            msg: "BLOCKED_BY_JACKSON"
        }
    }

    let hasGApi = 0;
    let hasFs = 0;
    let hasMoneyBonus = 0;
    let hasBoxSelect = 0;
    let hasPur = 0;
    let hasFsp = 0;
    let hasDoBonus = 0;
    let hasFreeError = 0;
    let gameParams = [];

    let json_patterns = typeof content == "object" ? content : JSON.parse(content);
    let patterns = [];
    let tblName = "pat_" + gameCode.toLowerCase();
    let commonBig = 1;
    let commonSmall = 1;
    let gameInfo = {};

    try {
        [[gameInfo]] = await pragmaticDB.query(`SELECT gameCode, payLines, status, initPattern from games where gameCode = "${gameCode}"`);
        !gameInfo ? gameInfo = {} : 0;

        await CheckExistTable(gameCode);

        if (DB_SAVE && TRUNCATE) {
            await patternDB.query(`TRUNCATE ${tblName};`);
        } else {
            query = `SELECT big FROM ${tblName} ORDER BY big DESC LIMIT 1`;
            let [[info]] = await patternDB.query(query);
            if (info) {
                commonBig = Number(info.big) + 1;
            }
        }

        await patternDB.query(`ALTER TABLE ${tblName} MODIFY COLUMN idx varchar(22) NULL DEFAULT NULL AFTER gameDone`);
        await patternDB.query(`ALTER TABLE ${tblName} ADD COLUMN parsed tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: 정품, 1: 파싱' AFTER idx`);
    } catch (e) { }

    const limited_length = COUNT_LIMIT ? Math.min(1119, json_patterns.length) : json_patterns.length;

    for (let i = 0; i < limited_length; ++i) {
        let reqObj = {};
        let resObj = {};

        if (!json_patterns[i].Url.includes("gameService")) {
            // console.log(i, "번째 패턴 is invalid.")
            continue;
        }

        try {
            reqObj = queryString.parse(json_patterns[i].Request);
            resObj = queryString.parse(json_patterns[i].Response);

            moneyParams.forEach((e) => {
                resObj[e] ? resObj[e] = Number(resObj[e].replace(/,/g, "")) : 0;
            });

            resObj.g ? hasGApi = 1 : 0;
            resObj.fs ? hasFs = 1 : 0;
            reqObj.action == "doCollectBonus" && cb_games.indexOf(gameCode) < 0 ? hasMoneyBonus = 1 : 0;
            resObj.status ? hasBoxSelect = 1 : 0;
            reqObj.action == "doBonus" && !resObj.status ? hasDoBonus = 1 : 0;

            if (!resObj.na) {
                continue;
            }

            patterns.push({ reqObj, resObj });

        } catch (e) {
            // console.log(i);
            // console.log(json_patterns[i]);
            // console.log(e);
        }
    }

    let initIdx = 0;

    while (initIdx < patterns.length) {
        if (patterns[initIdx].reqObj.action == "doInit") {
            break;
        }
        ++initIdx;
    }

    if (initIdx < patterns.length) {
        patterns[initIdx].resObj.purInit ? hasPur = 1 : 0;
        patterns[initIdx].resObj.fspps ? hasFsp = 1 : 0;

        if (!gameInfo.payLines || !gameInfo.initPattern) {
            gameInfo.payLines = patterns[initIdx].resObj.l;

            try {
                await pragmaticDB.query(`UPDATE games SET initPattern = '${Result4Client(patterns[initIdx].resObj)}', payLines = '${gameInfo.payLines}' WHERE gameCode = '${gameCode}'`);
                console.log("디비에 init정보 없음. 패턴으로부터 저장.")
            } catch (e) { }
        }
    } else {
        console.log("init 요청이 존재안함");
    }

    if (ONLY_INIT) {
        return {
            status: 1,
            msg: "ONLY_INIT_SAVED"
        };
    }

    console.log("스핀 총갯수:", patterns.length);

    let startIdx = 1;
    while (!patterns[startIdx++].reqObj.action.includes("doCollect"));

    let payLines = Number(gameInfo.payLines ? gameInfo.payLines : patterns[startIdx].resObj.l);
    let prevBalance = Number(patterns[startIdx - 1].resObj.balance);
    let pType = "base";
    let virtualBet = 0;
    let patternArr = [];

    let connected = false;
    let isZeroPattern = false;
    let prevIdx = -1;
    let prevTotalWin = 0;
    let pur = -1;
    let dice = -2;
    let prevBl = -1;

    let zeroInsert = 0;
    let baseInsert = 0;
    let freeInsert = 0;
    let bonusInsert = 0;
    let nExistings = 0;
    let nErrorPats = 0;

    for (let i = startIdx; i < patterns.length; i++) {
        let { reqObj, resObj } = patterns[i];
        let keys = Object.keys(resObj);

        keys.forEach(e => {
            if (gameParams.indexOf(e) < 0) {
                gameParams.push(e);
            }
        })

        if (reqObj.action == "doCollect" || reqObj.action == "doCollectBonus") {
            prevBalance = Number(resObj.balance);
            continue;
        }

        let balance = Number(resObj.balance);
        let totalBet = prevBalance - balance;

        if (isNaN(totalBet)) {
            console.log(i, "번째 패턴 오류 totalBet = NaN, 탈퇴");
            return {
                status: 2,
                msg: "TOTALBET_UNDEFINED"
            }
        }

        if (i + 1 < patterns.length) {
            connected = balance == patterns[i + 1].resObj.balance;
        }

        const SkipPatterns = () => {
            while (true) {
                if (patterns[i].resObj.balance != patterns[i + 1].resObj.balance) {
                    if (patterns[i + 1].reqObj.action.includes("Collect")) {
                        i++;
                    }
                    break;
                }
                i++;
            }

            connected = false;
            isZeroPattern = false;
            prevBalance = Number(patterns[i].resObj.balance);
            // console.log(i, "까지 스킾, prevTw:", patterns[i].resObj.tw);
        }

        if (gameCode == "vs1024butterfly" && reqObj.sor_ri) {
            SkipPatterns();
            continue;
        }

        if (totalBet) {
            prevIdx = -1;
            pur = -1;
            prevTotalWin = 0;
            dice = -2;
            prevBl = -1;
            virtualBet = Number(reqObj.c) * payLines;

            let pEnded = false;
            let isFree = patterns[i].reqObj.pur || patterns[i].reqObj.fsp ? true : false;     // 프리 감지 pur,fsp가 문자열로 들어오므로 "0" 일때도 true
            let last = i + 1;
            isZeroPattern = false;

            if (gameCode == "vs10bookfallen" && Number(reqObj.pur) == 0) {
                isFree = false;
            }

            while (last < patterns.length) {
                if (Number(patterns[last].resObj.balance) == balance) {
                    if (patterns[last].resObj.fs || respin_free_games.indexOf(gameCode) >= 0 && Number(patterns[last].resObj.rs_t) > 3) {
                        isFree = true;
                    }
                } else {
                    pEnded = true;
                    break;
                }
                ++last;
            }

            if (!pEnded) {
                console.log("\nbreak된 패턴번호 ", i);
                break;
            }

            const last_action = last < patterns.length ? patterns[last].reqObj.action : "";
            let nScatters = NumberOfScatters(resObj, i);

            if (isFree && (!last_action.includes("Collect") || (bigScatter_FSO_games[gameCode] && nScatters > bigScatter_FSO_games[gameCode]))) {
                if (!last_action.includes("Collect")) {
                    console.log("doCollect 없는 Free 발견, 패턴번호: ", i);
                }
                if (nScatters > bigScatter_FSO_games[gameCode]) {
                    console.log(`Big Scatter View 패턴번호 = ${i} nScatters = ${nScatters}`);
                }
                isFree = false;
                SkipPatterns();
                continue;
            }

            if (isFree) {
                pType = "free";
                ++freeInsert;
            }
            else if (last_action == "doCollectBonus" && cb_games.indexOf(gameCode) < 0) {
                pType = "free";
                ++bonusInsert;
            } else {
                pType = "base";

                if (last_action.includes("Collect")) {
                    ++baseInsert;
                } else {
                    isZeroPattern = true;
                    ++zeroInsert;
                }
            }
        }

        let type = reqObj.action;
        let totalWin = resObj.tw ? Number(resObj.tw) : 0;
        let idx = -1;
        let bl = -1;

        let win = resObj.tw ? totalWin - prevTotalWin : 0;
        let rtp = Math.floor((totalWin / virtualBet) * 100).toFixed(2);
        let pattern = Result4Client(resObj);
        let isRoundFinished = resObj.na == "c" || resObj.na == "cb" || isZeroPattern && !connected ? true : false;
        let big = 0;
        let small = 0;
        let insertType = pType;

        if (pType == "base" && !isRoundFinished) {
            if (isZeroPattern) {
                insertType = 'base-zero';
                big = commonBig;
                small = commonSmall++;
            } else {
                insertType = 'base-win';
                big = commonBig;
                small = commonSmall++;
            }
        } else if (pType == "base" && isRoundFinished) {
            if (totalWin == 0) {
                insertType = 'base-zero';
                big = commonBig;
                small = commonSmall;
                commonSmall = 1;
                commonBig++;
            } else if (totalWin != 0) {
                insertType = 'base-win';
                big = commonBig;
                small = commonSmall;
                commonBig++;
                commonSmall = 1;
            }
        } else if (pType == "free" && !isRoundFinished) {
            if (totalBet) {
                insertType = "free-start";
                reqObj.pur ? pur = Number(reqObj.pur) : 0;
                reqObj.fsp ? pur = Number(reqObj.fsp) : 0;

                if (uniqueFreeGames.indexOf(gameCode) >= 0) {
                    pur = 1;
                }
                if (gameCode == "vs20rainbowg") {
                    if (resObj.s.split(",").filter(e => e == "1").length > 1) {
                        SkipPatterns();
                        continue;
                    }
                }
                if (gameCode == "vs10snakeladd") {
                    if (Number(resObj.accv.split(";")[0].split("~")[2]) >= 11) {
                        dice = Number(resObj.accv.split(";")[1].split("~")[0]);
                    } else {
                        dice = -1;
                    }

                    if (dice == -1 && resObj.mo || resObj.mo && resObj.mo.split(",").filter(e => e != "0").length > 1) {
                        SkipPatterns();
                        console.log(i, "skipped");
                        continue;
                    }
                }
            } else {
                insertType = "free";
                type == "doFSOption" ? idx = Number(reqObj.ind) : 0;
                if (non_FSO_games.indexOf(gameCode) < 0 && type == "doBonus" && patterns[i - 1].resObj.wins_mask && patterns[i + 1].reqObj.action == "doSpin" && reqObj.ind && (gameCode != "vs243empcaishen" || gameCode == "vs243empcaishen" && patterns[i - 1].reqObj.action == "doSpin")) {
                    let prevResObj = patterns[i - 1].resObj;
                    let prevWins_maskArr = prevResObj.wins_mask.split(",");
                    let hiddenSelected = true;

                    for (let j = 0; j < prevWins_maskArr.length; ++j) {
                        if (prevWins_maskArr[j] != "h") {
                            hiddenSelected = false;
                            break;
                        }
                    }

                    if (!hiddenSelected) {
                        idx = Number(reqObj.ind);
                    }
                }
                if (new_FSO_games.indexOf(gameCode) >= 0 && type == "doBonus" && reqObj.ind && patterns[i + 1].reqObj.action == "doSpin" && patterns[i - 1].reqObj.action == "doSpin") {
                    idx = Number(reqObj.ind);
                }
                if (gameCode == "vs10bookfallen" && type == "doMysteryScatter") {
                    idx = Number(reqObj.ind);
                }
            }

            idx >= 0 ? prevIdx = idx : 0;
            big = commonBig;
            small = commonSmall;
            commonSmall++;
        } else if (pType == "free" && isRoundFinished) {
            insertType = "free-end";
            big = commonBig;
            small = commonSmall;
            commonBig++;
            commonSmall = 1;
            idx = prevIdx;
        }

        if (pType == "base") {
            if (gameCode == "vs10bookfallen" && resObj.mes) {
                bl = Number(reqObj.ind);
            }
            if (gameCode == "vs20drtgold" && resObj.g) {
                bl = Number(reqObj.bl);
            }
        }

        if (insertType == "free-start" || insertType == "free-end") {
            if (gameCode == "vs20drtgold") {
                bl = Number(reqObj.bl);
            }
        }

        if (gameCode == "vs20superx") {
            1 == small ? (bl = Number(reqObj.bl), prevBl = bl) : (isRoundFinished ? bl = prevBl : 0);
        }

        let idxValueStr = [];
        pur >= 0 && insertType == "free-end" ? idxValueStr.push(`pur:${pur}`) : 0;
        idx >= 0 ? idxValueStr.push(`fso:${idx}`) : 0;
        dice >= -1 && insertType != "free" ? idxValueStr.push(`dice:${dice}`) : 0;
        bl >= 0 ? idxValueStr.push(`bl:${bl}`) : 0;

        patternArr.push({ isRoundFinished, pType: insertType, type, big, small, win, totalWin, totalBet, virtualBet, rtp, pattern, balance, idxValueStr });

        if (isRoundFinished) {
            let insert_res = await InsertIntoPatternDB(gameCode, patternArr, i);
            insert_res == 1 ? (nExistings++ , commonBig--) : 0;
            insert_res == 2 ? (nErrorPats++ , commonBig--) : 0;
            patternArr = [];
        }

        prevBalance = Number(resObj.balance);
        totalWin > 0 ? prevTotalWin = totalWin : 0;
    }

    console.log("\n--------------------------")
    console.log("Total 패턴:", zeroInsert + baseInsert + freeInsert + bonusInsert);
    console.log("Zero  패턴:", zeroInsert);
    console.log("Base  패턴:", baseInsert);
    console.log("Free  패턴:", freeInsert);
    console.log("Bonus 패턴:", bonusInsert);
    console.log("\n오류  패턴:", nErrorPats);
    console.log("중복  패턴:", nExistings);
    console.log("--------------------------\n")

    let nFreeStarts = pTypeArr.filter(e => e == "free-start").length;
    let nFreeEnds = pTypeArr.filter(e => e == "free-end").length;

    console.log("free-start", nFreeStarts, " free-end", nFreeEnds);
    if (nFreeStarts != nFreeEnds) {
        hasFreeError = 1;
        console.log("프리갯수오류");
    }

    return {
        status: 1,
        info: {
            hasGApi,
            hasFs,
            hasMoneyBonus,
            hasBoxSelect,
            hasPur,
            hasFsp,
            hasDoBonus,
            hasFreeError,
            gameParams,
            commonBig,
            numTotal: zeroInsert + baseInsert + freeInsert + bonusInsert,
            numZero: zeroInsert,
            numBase: baseInsert,
            numFree: freeInsert + bonusInsert,
            nExistings,
            nErrorPats
        },
        msg: "SUCCESS"
    }
}

async function InsertIntoPatternDB(gameCode, patternArr, i) {
    let tblName = "pat_" + gameCode.toLowerCase();

    for (let j = 0; j < patternArr.length; ++j) {
        let { isRoundFinished, pType, type, big, small, win, totalWin, totalBet, virtualBet, rtp, pattern, balance, idxValueStr } = patternArr[j];
        const real_i = i - patternArr.length + small;


        if (DB_SAVE) {
            if (small == 1) {
                let [[db_row]] = await patternDB.query(`SELECT * FROM ${tblName} WHERE pattern = '${pattern}' AND small = ${small} AND type = "${type}" AND pType = "${pType}" AND balance = ${balance}`);

                if (db_row) {
                    console.log(real_i, "번째 패턴 중복");
                    return 1;
                }
            }

            const query = `INSERT INTO ${tblName}(gameCode, gameDone, parsed, balance, type, pType, big, small, win, totalWin, totalBet, virtualBet , rtp, pattern${idxValueStr.length ? ",idx" : ""})
                        VALUES ('${gameCode}', ${isRoundFinished}, ${PARSED ? 1 : 0},${balance},'${type}','${pType}','${big}','${small}', '${win}', ${totalWin}, ${totalBet}, ${virtualBet}, ${rtp}, '${pattern}'${idxValueStr.length ? ",'" + idxValueStr.join(",") + "'" : ""});`;

            if (query.includes("NaN")) {
                console.log(real_i, "번째 패턴 Nan값");
                await patternDB.query(`DELETE FROM ${tblName} WHERE big = ${big}`);
                return 2;
            }

            try {
                await patternDB.query(query);
            } catch (e) {
                console.log(real_i, "번째 패턴 저장오류");
                await patternDB.query(`DELETE FROM ${tblName} WHERE big = ${big}`);
                return 2;
            }

            pTypeArr.push(pType);
        } else {
            pTypeArr.push(pType);
        }
    }

    return false;
}

async function CheckExistTable(gameCode) {
    var tblName = "pat_" + gameCode.toLowerCase();
    var sql = `show tables like '${tblName}'`;
    [result, tmp] = await patternDB.query(sql);

    if (result.length == 0) {
        sql = createSQL.replace("___TABLE_NAME___", tblName);
        if (DB_SAVE) {
            await patternDB.query(sql);
            console.log("[CREATED] " + tblName);
        }
    } else {
        console.log("[EXIST] " + tblName);
    }
}

function isExisting(_gameCode, games) {
    let res = false;
    const gameCode = _gameCode.toUpperCase();

    for (let i = 0; i < games.length; ++i) {
        if (games[i].toUpperCase() == gameCode) {
            res = true;
            break;
        }
    }

    return res;
}

const Result4Client = function (obj) {
    var str = "";
    for (let index in obj) {
        str += index + "=" + obj[index] + "&";
    }
    return str;
}

const NumberOfScatters = (resObj, i) => {
    let nScatters = 0;

    if (resObj.s) {
        nScatters = resObj.s.split(",").map(e => e = Number(e)).filter(e => e == 1).length;
    } else if (resObj.g) {
        let viewCount = 0;
        let g = JSON5.parse(resObj.g);
        for (const key in g) {
            if (g[key].s) {
                if (!viewCount) {
                    nScatters = g[key].s.split(",").map(e => e = Number(e)).filter(e => e == 1).length;
                }
                viewCount++;
            }
        }
        if (viewCount > 1) {
            console.log("g 안에 두개의 뷰, 패턴번호: ", i);
        }
    }

    return nScatters;
}