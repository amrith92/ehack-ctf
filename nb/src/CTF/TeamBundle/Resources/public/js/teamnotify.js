var TeamNotify = {
    pollUrl: null,
    bindings: {
        alert: null,
        dingdong: null
    },
    ivl: null,
    init: function(params) {
        var self = this;
        
        self.pollUrl = (params.pollUrl) ? params.pollUrl : null;
        
        self.bindings.alert = (params.alert) ? params.alert : document.getElementById('team-alert');
        self.bindings.dingdong = (params.dingdong) ? params.dingdong : document.getElementById('dingdong');
        
        if (null != self.pollUrl) {
            self.ivl = window.setInterval(function() {
                TeamNotify.poll();
            }, 35000);
        }
    },
    poll: function() {
        var self = this;
        
        $.get(self.pollUrl, null, function(data) {
            if ('nil' != data) {
                var prev = TeamNotify.bindings.alert.innerHTML;
                if (data != prev) {
                    TeamNotify.bindings.dingdong.play();
                    TeamNotify.bindings.alert.innerHTML = data;
                    TeamNotify.bindings.alert.setAttribute('class', 'badge badge-warning');
                } else {
                    TeamNotify.bindings.alert.setAttribute('class', 'hidden');
                }
            } else {
                TeamNotify.bindings.alert.setAttribute('class', 'hidden');
            }
        });
    }
};
