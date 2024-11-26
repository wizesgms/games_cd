const CronJob = require("cron").CronJob;
const moment = require("moment");
const { resetCountRequest, resetCountIp } = require("../utils/app_utils");

const saveGameSpendingHistory = async (app) => {
    const Spending = app.db.Spending;
    const ret = await app.db.sequelize.query(`SELECT games.gameCode code, IFNULL(bets.count,0) betCount, IFNULL(wins.count,0) winCount, IFNULL(calls.count,0) callCount, IFNULL(bets.amount,0) betAmount, IFNULL(wins.amount,0) winAmount, IFNULL(calls.betAmount,0) callBetAmount, IFNULL(calls.winAmount,0) callWinAmount FROM games LEFT JOIN (SELECT gameCode, SUM(bet) amount, COUNT(*) count FROM events WHERE DATE(createdAt) = DATE(NOW()) AND bet > 0 GROUP BY gameCode) bets ON bets.gameCode = games.gameCode LEFT JOIN (SELECT gameCode, SUM(win) amount, COUNT(*) count FROM events WHERE DATE(createdAt) = DATE(NOW()) AND win > 0 GROUP BY gameCode) wins ON wins.gameCode = games.gameCode LEFT JOIN (SELECT gameCode, SUM(betMoney) betAmount, SUM(summedMoney) winAmount, COUNT(*) count FROM calls WHERE DATE(updatedAt) = DATE(NOW()) AND callStatus = "CALL_END" GROUP BY gameCode) calls ON calls.gameCode = games.gameCode;`);
    const histories = ret[0].map(game => ({
        gameCode: game.code,
        betCount: game.betCount - game.callCount,
        winCount: game.winCount - game.callCount,
        betAmount: game.betAmount - game.callBetAmount,
        winAmount: game.winAmount - game.callWinAmount,
        spendingAmount: (game.betAmount - game.callBetAmount) - (game.winAmount - game.callWinAmount),
        callCount: game.callCount,
        callBetAmount: game.callBetAmount,
        callWinAmount: game.callWinAmount
    }));
    Spending.bulkCreate(histories);
};

const deleteGameSpendingHistory = async (app) => {
    const Spending = app.db.Spending;
    Spending.destroy({ where: { createdAt: { [Op.lt]: moment().subtract(30, "days").format("YYYY-MM-DD") } } });
};

module.exports = (app) => {
    const gameSpendingHistorySaver = new CronJob("0 0 * * * *", () => saveGameSpendingHistory(app));
    const gameSpendingHistoryDestroyer = new CronJob("0 0 9 * * *",  () => deleteGameSpendingHistory(app));
    gameSpendingHistorySaver.start();
    gameSpendingHistoryDestroyer.start();

    // 10초에 한번씩 호출
    const resetCountRequestCron = new CronJob("*/10 * * * * *", resetCountRequest);
    resetCountRequestCron.start();

    // 10분에 한번씩 호출
    const resetCountIpCron = new CronJob("0 */10 * * * *", resetCountIp);
    resetCountIpCron.start();
};

