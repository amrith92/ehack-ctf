var TeamChatClient = {
    socket: null,
    username: null,
    team: null,
    port: null,
    host: null,
    bindings: {
        chat: null,
        input: null,
        list: null,
        popcorn: null,
        send: null
    },
    init: function (params) {
        var self = this;
        
        self.host = params.host;
        self.port = params.port;
        if (typeof io != undefined) {
            self.socket = io.connect('http://' + self.host + ':' + self.port);
        }
            
        self.username = params.username;
        self.team = params.team;
        self.bindings.chat = (params.chat) ? params.chat : document.getElementById('chat');
        self.bindings.input = (params.input) ? params.input : document.getElementById('chat-bar');
        self.bindings.list = (params.list) ? params.list : document.getElementById('clientList');
        self.bindings.popcorn = (params.popcorn) ? params.popcorn : document.getElementById('popcorn');
        self.bindings.send = (params.send) ? params.send : document.getElementById('chat-send');
            
        // Event bindings
        self.bindings.input.addEventListener('keydown', function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
                var msg = TeamChatClient.bindings.input.value;
                if (null !== msg && '' !== msg) {
                    TeamChatClient.socket.emit('message', msg);
                    TeamChatClient.bindings.input.value = '';
                }
            }
        });
        
        self.bindings.send.addEventListener('click', function() {
            var msg = TeamChatClient.bindings.input.value;
            if (null !== msg && '' !== msg) {
                TeamChatClient.socket.emit('message', msg);
                TeamChatClient.bindings.input.value = '';
            }
        });
    },
    run: function() {
        var self = this;
            
        self.socket.on('connect', function() {
            TeamChatClient.socket.emit('adduser', TeamChatClient.username, TeamChatClient.team);
        });
            
        self.socket.on('updateusers', function(data) {
            TeamChatClient.bindings.list.innerHTML = '';
            var html = '<ul>';
            for(var i = 0; i < data.length; ++i) {
                html += '<li><a href="#">' + data[i] + '</a></li>';
            }
            html += "</ul>";
            TeamChatClient.bindings.list.innerHTML = html;
        });
            
        self.socket.on('updatechat', function (username, data) {
            TeamChatClient.bindings.popcorn.play();
            if ('SERVER' == username) {
                TeamChatClient.bindings.chat.innerHTML += ('<div class="message message-text message-text-success">' + '<b>'+ username + ':</b> ' + data + '</div>');
                TeamChatClient.bindings.chat.scrollTop = TeamChatClient.bindings.chat.scrollHeight;
            } else {
                if (TeamChatClient.username == username) {
                    TeamChatClient.bindings.chat.innerHTML += ('<div class="message right message-text message-text-info"><b>me:</b> ' + data + '</div><br />');
                } else {
                    TeamChatClient.bindings.chat.innerHTML += ('<div class="message message-text left"><b>'+ username + ':</b> ' + data + '</div><br />');
                }
                
                TeamChatClient.bindings.chat.scrollTop = TeamChatClient.bindings.chat.scrollHeight;
            }
        });
    },
    disconnect: function() {
        var self = this;
        
        self.bindings.status.innerHTML = '<input type="button" id="connect" value="Connect!" />';
        self.bindings.chat.innerHTML = '';
        self.bindings.input.setAttribute('disabled', 'disabled');
        self.socket.emit('close');
    }
};
