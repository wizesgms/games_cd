module.exports = app => {
    const Spending = app.db.sequelize.define(
        "spendings",
        {
            id: {
                type: Sequelize.INTEGER,
                primaryKey: true,
                autoIncrement: true
            },
            gameCode: {
                type: Sequelize.STRING,
                allowNull: false
            },
            betCount: {
                type: Sequelize.INTEGER,
                allowNull: false,
                defaultValue: 0
            },
            winCount: {
                type: Sequelize.INTEGER,
                allowNull: false,
                defaultValue: 0
            },
            betAmount: {
                type: Sequelize.DOUBLE(20, 2),
                allowNull: false,
                defaultValue: 0
            },
            winAmount: {
                type: Sequelize.DOUBLE(20, 2),
                allowNull: false,
                defaultValue: 0
            },
            spendingAmount: {
                type: Sequelize.DOUBLE(20, 2),
                allowNull: false,
                defaultValue: 0
            },
            callCount: {
                type: Sequelize.INTEGER,
                allowNull: false,
                defaultValue: 0
            },
            callBetAmount: {
                type: Sequelize.DOUBLE(20, 2),
                allowNull: false,
                defaultValue: 0
            },
            callWinAmount: {
                type: Sequelize.DOUBLE(20, 2),
                allowNull: false,
                defaultValue: 0
            },
        },
        {
            timestamps: true
        }
    );

    app.db.Spending = Spending;
};
