module.exports = app => {

    const Call = app.db.sequelize.define('call', {
        id: { type: Sequelize.INTEGER, primaryKey: true, autoIncrement: true },
        userCode: { type: Sequelize.STRING },
        gameCode: { type: Sequelize.STRING },
        agentCode: { type: Sequelize.STRING },
        betMoney: { type: Sequelize.INTEGER },
        calledMoney: { type: Sequelize.INTEGER },
        summedMoney: { type: Sequelize.INTEGER },
        type: { type: Sequelize.INTEGER },
        callStatus: { type: Sequelize.STRING }
    },
        {
            timestamps: true
        });

    app.db.Call = Call;
}