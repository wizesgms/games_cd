require('rootpath')();

module.exports = app => {
    // require('routes/admin')(app);
    require('routes/gameAPI')(app);
    require('routes/agentAPI')(app);

    app.get('/', (req, res) => res.redirect('/game_list.do'));

};