const path = require('path');
const express = require('express');
const bodyParser = require('body-parser');
const session = require('express-session');
const morgan = require('morgan');
const compression = require('compression');
const sessionCheck = require("./utils/session");
const cookie = require("./utils/cookie");
const encrypt = require("./utils/encrypt");
const { countRequest, countIp } = require("./utils/app_utils");
global.rootPath = __dirname;
global.serverType = "Pragmatic1125";

const redis = require('redis');
const port_redis = 6379;

module.exports = () => {
    const app = express()
        .set('views', path.resolve(__dirname, 'views'))
        .set('view engine', 'ejs');

    app.engine('html', require('ejs').renderFile);

    app.redis_client = redis.createClient(port_redis);

    app.use(cookie());
    app.use(encrypt());
    app.use(compression());
    app.use(sessionCheck());

    app.use([
        express.static(path.resolve(__dirname, 'public')),
        express.static(path.resolve(__dirname, 'views')),
        bodyParser.urlencoded({ extended: true }),
        bodyParser.json({ limit: '50mb' }),
        bodyParser.text(),
        session({
            secret: process.env.SESSION_SECRET || '@#@$(*&^%$#JUHGFDSNBVCX#@$#$',
            resave: false,
            saveUninitialized: false,
        }),
    ]);

    app.use(function (req, res, next) {

        // Website you wish to allow to connect
        res.setHeader('Access-Control-Allow-Origin', '*');

        // Request methods you wish to allow
        res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

        // Request headers you wish to allow
        res.setHeader('Access-Control-Allow-Headers', '*');

        // Set to true if you need the website to include cookies in the requests sent
        // to the API (e.g. in case you use sessions)
        res.setHeader('Access-Control-Allow-Credentials', true);

        // Pass to next layer of middleware
        next();
    });

    app.use(countRequest);
    app.use(countIp);

    return app;
};