"use strict";

/* Configuration variables */
var serverPort = 5560;

var unknownCtr = 0;

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
		datastore = redis.createClient();

app.listen(serverPort, function() {
	console.log((new Date()) + " Server is listening on port " + serverPort);
});

io.sockets.on('connection', function (socket) {
	socket.on('adduser', function(username) {
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
		
		/*if(history.length > 0) {
			socket.emit('updateHistory', JSON.stringify(history));
		}*/
	});
	
	socket.on('message', function(message) {
		message = censor(message);
		/*history.push({
			username: socket.username,
			message: message
		});*/
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
