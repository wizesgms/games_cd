const { DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_LOGGING } = process.env;

module.exports = async (app) => {
    const logging = (DB_LOGGING || 'false') !== 'false';

    const sequelize = new Sequelize('pragmatic', DB_USER, DB_PASS, {
        dialect: 'mysql',
        host: DB_HOST,
        port: DB_PORT,
        logging,
        typeValidation: true,
        timezone: "+09:00"
    });

    await sequelize.sync();

    app.db = { ...(app.db || {}), sequelize };
}