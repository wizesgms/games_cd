module.exports = app => {
    const Event = app.db.sequelize.define('event', {
        id: { type: Sequelize.INTEGER, primaryKey: true, autoIncrement: true },
        userCode: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        gameCode: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        agentCode: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        txnID: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        txnType: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        bet: { type: Sequelize.DOUBLE, allowNull: false, dataValues: 0 },
        win: { type: Sequelize.DOUBLE, allowNull: false, dataValues: 0 },
        type: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        checked: { type: Sequelize.INTEGER, allowNull: false, defaultValue: false }
    });

    app.db.Event = Event;
}