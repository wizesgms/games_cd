const { Sequelize, Op } = require('sequelize');

global.Sequelize = Sequelize;
global.Op = Op;
global.EUtil = require("./utils/engine_utils");
global.Util = require("./utils/slot_utils");
global.logger = require("./logger");
global.PAT_SERVER_URL = process.env.PAT_SERVER_URL || "http://127.0.0.1:8942";

global.rtpConfig = {
    "BuyBonusDefaultMulti": 100,
    "JackpotNormalStart": 100,
    "JackpotNormalEnd": 200,
    "JackpotLongStart": 200,
    "JackpotLongEnd": 600,
    "JackpotLongPercent": 10,
    "FreeMinMulti": 0,
    "SmallBaseMaxMulti": 4,
}