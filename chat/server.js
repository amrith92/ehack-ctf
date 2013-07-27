"use strict";

/* Configuration variables */
var serverPort = 5560;

var unknownCtr = 0;

var CHAT_ON = true;

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;')
                      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function censor(str)
{
	var arr = [
		"fuck",
		"ass",
		"dick",
		"fucker",
		"fucking",
		"cunt",
		"pussy",
		"cock",
		"c0ck",
		"cum",
		"twat",
		"clit",
		"bitch",
		"fuk",
		"fuking",
		"motherfucker"
	];

	for (var i = 0; i < arr.length; ++i) {
		var reg = new RegExp(arr[i], "i");
		str = String(str).replace(reg, "");
	}

	return String(str);
}

/* Server code */

var app = require('http').createServer(function(request, response) {
    console.log((new Date()) + ' Tp HTTP Chat Server. Serving URL ' + request.url);

    if (request.url === '/status') {
        response.writeHead(200, {'Content-Type': 'application/json; charset=utf-8'});
        var responseObject = {
            currentClients: clients.length,
            totalHistory: history.length
        }
        response.end(JSON.stringify(responseObject));
    } else {
        response.writeHead(404, {'Content-Type': 'text/plain; charset=utf-8'});
        response.end('404. Well, that just means you\'ve got the wrong address!');
    }
});
var io = require('socket.io').listen(app),
		redis = require('redis'),
		datastore = redis.createClient(),
		mysql = require('mysql'),
		pollingInterval = 15000;

app.listen(serverPort, function() {
	console.log((new Date()) + " Server is listening on port " + serverPort);
});

var connection = mysql.createConnection( {
	host     : 'blackhole',
	user     : 'announcer',
	password : '@nnouncer',
	database : 'ctf'
});

connection.connect(function(err) {
	if (err != null) {
		console.log( err );
	}
});

var pollingCheckDb = function() {
	var query = connection.query('SELECT * FROM global_state WHERE id=1');

	query
	.on('error', function(err) {
		console.log( err );
	})
	.on('result', function( state ) {
		if (state.enable_chat == 0) {
			CHAT_ON = false;
		} else {
			CHAT_ON = true;
		}
	})
	.on('end',function() {
		// query ends
	});
};

setInterval( function() { pollingCheckDb(); }, pollingInterval );

io.sockets.on('connection', function (socket) {
	socket.on('adduser', function(username) {
		if (false == CHAT_ON) {
			socket.emit('updatechat', 'SERVER', 'Global Chat is currently OFF.');
			return;
		}

		if (username == null || username == "") {
			username = "Who\'sThis_" + ++unknownCtr;
		}
		username = htmlEntities(username);
		datastore.sadd("clients", username);
		
		socket.username = username;
		
		socket.emit('updateStatus', 'Connected!');
		socket.emit('updatechat', 'SERVER', 'you have connected');
		socket.broadcast.emit('updatechat', 'SERVER', username + ' has connected');
		datastore.smembers("clients", function(err, data) {
			io.sockets.emit('updateusers', data);
		});
		
	});
	
	socket.on('message', function(message) {
		if (false == CHAT_ON) {
			socket.emit('updatechat', 'SERVER', 'Global Chat is currently OFF.');
			return;
		}
		message = censor(message);
		message = message.substring(0, 140);
		socket.emit('updatechat', socket.username, htmlEntities(message));
		socket.broadcast.emit('updatechat', socket.username, htmlEntities(message));
	});
	
	socket.on('close', function() {
		console.log("Disconnect called!");
		datastore.srem("clients", socket.username, function(err, data) {
			datastore.smembers("clients", function(err, data) {
				io.sockets.emit('updateusers', data);
			});
		});
		
		socket.emit('updatechat', 'SERVER', 'you have been disconnected!');
		socket.broadcast.emit('updatechat', 'SERVER', socket.username + ' has disconnected!');
	});
});
