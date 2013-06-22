var express = require('express')
	, http = require('http')
	, mysql = require('mysql');

var app = express()
	, server = http.createServer(app)
	, io = require('socket.io').listen(server)
	, hasClients = false
	, port = 5656
	, pollingInterval = 5000
	, pollingTimer;

var connection = mysql.createConnection( {
	host     : 'localhost',
	user     : 'announcer',
	password : '@nnouncer',
	database : 'ctf'
});

connection.connect(function(err) {
    console.log( err );
});

server.listen(port);

app.get('/', function (req, res) {
	res.sendfile(__dirname + '/index.html');
});

console.log('Listening on ' + port);

var pollingLoop = function () {
	var query = connection.query('SELECT * FROM announce'),
			notifications = [];

	query
	.on('error', function(err) {
		console.log( err );
		updateSockets( err );
	})
	.on('result', function( notification ) {
		if (notification.delivered == 0) {
				notifications.push( notification );
		}
	})
	.on('end',function() {
		updateSockets( { 'notifications': notifications } );
	});
	connection.query('UPDATE announce SET delivered = ' + 1);
};

pollingTimer = setTimeout( pollingLoop, pollingInterval );

io.sockets.on( 'connection', function ( socket ) {
	socket.on('disconnect', function () {
		
	});

	hasClients = true;
});

var updateSockets = function ( data ) {
  if(data.notifications != null && data.notifications.length != 0) {
    io.sockets.volatile.emit( 'notification' , data );
  }
};
