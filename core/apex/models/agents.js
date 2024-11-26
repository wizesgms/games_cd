module.exports = app => {
    const Agent = app.db.sequelize.define('agent', {
        id: { type: Sequelize.INTEGER, primaryKey: true, autoIncrement: true },
        code: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        balance: { type: Sequelize.DOUBLE, allowNull: false, defaultValue: '' },
        realRtp: { type: Sequelize.DOUBLE, allowNull: false, defaultValue: 0 },
        targetRtp: { type: Sequelize.DOUBLE, allowNull: false, defaultValue: 80 },
        totalDebit: { type: Sequelize.DOUBLE, allowNull: false, defaultValue: 0 },
        totalCredit: { type: Sequelize.DOUBLE, allowNull: false, defaultValue: 0 },
        aasEndpoint: { type: Sequelize.STRING, allowNull: false, defaultValue: '' },
        siteEndpoint: { type: Sequelize.STRING, allowNull: false, defaultValue: '' }
    });

    Agent.prototype.setBalance = async function (debit, credit, callHistId) {
        this.balance = this.balance - debit + credit;
        //콜중에는 환수율처리를 진행하지 않는다.
        if (callHistId <= 0) {
            this.totalDebit += debit;
            this.totalCredit += credit;
            this.realRtp = this.totalDebit ? (this.totalCredit / this.totalDebit * 100).toFixed(2) : 100;
        }
        await this.save();
    }

    app.db.Agent = Agent;
}