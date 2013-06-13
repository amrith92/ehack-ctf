var GlobalChatClient = {
    socket: null,
    username: null,
    host: null,
    port: null,
    bindings: {
        status: null,
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
        self.socket = io.connect('http://' + self.host + ':' + self.port);
            
        self.username = params.username;
        self.bindings.status = (params.status) ? params.status : document.getElementById('status');
        self.bindings.chat = (params.chat) ? params.chat : document.getElementById('chat');
        self.bindings.input = (params.input) ? params.input : document.getElementById('chat-bar');
        self.bindings.list = (params.list) ? params.list : document.getElementById('clientList');
        self.bindings.popcorn = (params.popcorn) ? params.popcorn : document.getElementById('popcorn');
        self.bindings.send = (params.send) ? params.send : document.getElementById('chat-send');
            
        // Event bindings
        self.bindings.input.addEventListener('keydown', function(event) {
            if(event.keyCode == 13) {
                event.preventDefault();
                var msg = GlobalChatClient.bindings.input.value;
                if (null !== msg && '' !== msg) {
                    GlobalChatClient.socket.emit('message', msg);
                    GlobalChatClient.bindings.input.value = '';
                }
            }
        });
        
        self.bindings.send.addEventListener('click', function() {
            var msg = GlobalChatClient.bindings.input.value;
            if (null !== msg && '' !== msg) {
                GlobalChatClient.socket.emit('message', msg);
                GlobalChatClient.bindings.input.value = '';
            }
        });
    },
    run: function() {
        var self = this;
            
        self.socket.on('connect', function() {
            GlobalChatClient.socket.emit('adduser', GlobalChatClient.username);
        });
            
        self.socket.on('updateusers', function(data) {
            GlobalChatClient.bindings.list.innerHTML = '';
            for(var i = 0; i < data.length; ++i) {
                GlobalChatClient.bindings.list.innerHTML += '<div>' + data[i] + '</div>';
            }
        });
            
        self.socket.on('updateStatus', function(str) {
            if(str == 'Connected!') {
                GlobalChatClient.bindings.status.innerHTML = '<span id="disconnect">Disconnect</span>';
                document.getElementById('disconnect').addEventListener('click', function() {
                    GlobalChatClient.socket.emit('close');
                    GlobalChatClient.bindings.status.innerHTML = '<span id="connect">Connect &raquo;</span>';
                    GlobalChatClient.bindings.chat.innerHTML = '';
                    GlobalChatClient.bindings.input.setAttribute('disabled', 'disabled');

                    document.getElementById('connect').addEventListener('click', function() {
                        GlobalChatClient.socket.emit('adduser', GlobalChatClient.username);
                    });
                });
                GlobalChatClient.bindings.input.removeAttribute('disabled');
            }
        });
            
        self.socket.on('updatechat', function (username, data) {
            GlobalChatClient.bindings.popcorn.play();
            GlobalChatClient.bindings.chat.innerHTML += ('<b>'+ username + ':</b> ' + data + '<br>');
            GlobalChatClient.bindings.chat.scrollTop = GlobalChatClient.bindings.chat.scrollHeight;
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
