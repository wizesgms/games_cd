require('rootpath')();

module.exports = app => {

    require('utils/init_app_module')(app)
        .add('models/users.js')
        .add('models/games.js')
        .add('models/agents.js')
        .add('models/events.js')
        .add('models/histories.js')
        .add('models/players.js')
        .add('models/calls.js')
        .add('models/replays.js')
        .add('models/spendings.js')
        .init();

};