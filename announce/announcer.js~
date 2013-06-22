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
		if (notification.delivered == false) {
				notifications.push( notification );
		}
	})
	.on('end',function() {
		if(hasClients) {
				pollingTimer = setTimeout( pollingLoop, pollingInterval );
				updateSockets( { 'notifications': notifications } );
		}
	});
	connection.query('UPDATE announce SET delivered = ' + 1);
};

io.sockets.on( 'connection', function ( socket ) {
	if (!hasClients) {
		pollingLoop();
	}

	socket.on('disconnect', function () {
		
	});

	hasClients = true;
});

var updateSockets = function ( data ) {
  if(data.notifications != null && data.notifications.length != 0) {
    io.sockets.volatile.emit( 'notification' , data );
  }
};
