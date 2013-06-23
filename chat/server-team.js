"use strict";

/* Configuration variables */
var serverPort = 5561;

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
            currentClients: clients.length
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
	socket.on('adduser', function(username, team) {
		if (username == null || username == "") {
			username = "Who\'sThis_" + ++unknownCtr;
		}
		if (team == null || team == "") {
			team = "DefaultTeam";
		}
		username = htmlEntities(username);
		team = htmlEntities(team);

		datastore.sadd("clients_" + team, username);
		
		socket.username = username;
		socket.team = team;

		datastore.sadd("teams_" + team, socket.id);
		
		socket.emit('updateStatus', 'Connected!');
		socket.emit('updatechat', 'SERVER', 'you have connected');

		datastore.smembers("clients_" + team, function(err, clientlist) {
			datastore.smembers("teams_" + team, function(err, data) {
				for (var i = 0; i < data.length; ++i) {
					io.sockets.socket(data[i]).emit('updateusers', clientlist);
					if (socket.id == data[i]) {
						continue;
					}
					io.sockets.socket(data[i]).emit('updatechat', 'SERVER', username + ' has connected');
				}
			});
		});
	});
	
	socket.on('message', function(message) {
		message = censor(message);
		socket.emit('updatechat', socket.username, htmlEntities(message));

		datastore.smembers("teams_" + socket.team, function(err, data) {
			for (var i = 0; i < data.length; ++i) {
				if (socket.id == data[i]) {
					continue;
				}
				io.sockets.socket(data[i]).emit('updatechat', socket.username, htmlEntities(message));
			}
		});
	});
	
	socket.on('close', function() {
		datastore.srem("clients_" + socket.team, socket.username, function(err, cliret) {
			console.log("Client [" + cliret + "] removed from clients_" + socket.team);
			datastore.srem("teams_" + socket.team, socket.id, function(terr, tret) {
				console.log("Socket [" + socket.id + "] removed from teams_" + socket.team);
				datastore.smembers("clients_" + socket.team, function(clerr, clientlist) {
					datastore.smembers("teams_" + socket.team, function(err, data) {
						for (var i = 0; i < data.length; ++i) {
							io.sockets.socket(data[i]).emit('updateusers', clientlist);
							if (socket.id == data[i]) {
								continue;
							}

							io.sockets.socket(data[i]).emit('updatechat', 'SERVER', socket.username + ' has disconnected!');
						}
					});
				});
			});
		});
	});
});
