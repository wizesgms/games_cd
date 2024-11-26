module.exports = app => {

    const Game = app.db.sequelize.define('game', {
        id: { type: Sequelize.INTEGER, primaryKey: true, autoIncrement: true },
        gameCode: Sequelize.STRING,
        banner: Sequelize.STRING,
        gameName: Sequelize.STRING,
        enName: Sequelize.STRING,
        memo: Sequelize.STRING,
        status: { type: Sequelize.INTEGER, allowNull: false, defaultValue: 1 },
        checked: { type: Sequelize.TINYINT, allowNull: false, defaultValue: 0 }
    },
        {
            timestamps: true,
            updatedAt: false,
        });
    app.db.Game = Game;
}