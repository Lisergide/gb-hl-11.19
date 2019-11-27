const express = require('express');
const log4js = require('log4js');
const app = express();
app.set("port", process.env.PORT || 3034);

log4js.configure({
  appenders: {
    logstash: {
      type: '@log4js-node/logstash-http',
      url: 'http://localhost:9200/_bulk',
      application: 'logstash-log4js',
      logType: 'application',
      logChannel: 'node'
    }
  },
  categories: {
    default: {appenders: ['logstash'], level: 'info'}
  }
});

const logger = log4js.getLogger();
app.get('/hello', function (req, res) {
  logger.info('Hello World!');
  res.send('Hello World!');
});

app.listen(app.get('port'), function () {
  console.log("Server запущен на http://localhost:" +
    app.get("port") +
    ": нажмите Ctrl+C для завершения.");
});