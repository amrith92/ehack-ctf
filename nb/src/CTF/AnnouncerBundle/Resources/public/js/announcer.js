var Announcer = {
    host: null,
    port: null,
    audience: null,
    dingdong: null,
    socket: null,
    ready: false,
    init: function(params) {
        var self = this;
        
        self.host = params.host;
        self.port = params.port;
        self.audience = (params.audience) ? params.audience : document.getElementById('audience');
        self.dingdong = (params.dingdong) ? params.dingdong : document.getElementById('dingdong');
        
        if (null != self.host && null != self.port && typeof io != undefined) {
            self.socket = io.connect('http://' + self.host + ":" + self.port);
            self.ready = true;
        }
    },
    tuneIn: function() {
        var self = this;
        
        if (self.ready) {
            self.socket.on('notification', function (data) {
                self.audience.innerHTML = null;
                for(var i = 0; i < data.notifications.length ; i++) {
                    var j = Date.parse(data.notifications[i].updated_tstamp);
                    var d = new Date(j);
                    self.audience.innerHTML += '<div class="alert alert-notice fade in"><button type="button" class="close" data-dismiss="alert">&times;</button><h4 class="alert-heading">Hoooold Up! This is an announcement!</h4><div class="row-fluid"><div class="span10">' + data.notifications[i].announce + '</div><div class="span2">' + d.toLocaleString() + '</div></div></div>';
                    Announcer.dingdong.play();
                }
            });
        }
    }
};
