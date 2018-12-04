var io = require('socket.io')(6001);
let Redis = require('ioredis');
let redis = new Redis();

redis.psubscribe('*', function (error, count) {
    console.log(error, count);
});

redis.on('pmessage', function (pattern, channel, message) {
    message = JSON.parse(message);
    io.emit(channel + ':' + message.event, message.data.message);
    console.log(channel, message);
});

