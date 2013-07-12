$(document).ready(function() {
    renderTeamView();
    
    TeamNotify.init({
        pollUrl: Routing.generate('ctf_team_admin_alert_poll', null)
    });
    
    $('#team-update-status-button').click(function(e) {
        e.preventDefault();
        
        var _status = $('#team-new-status').val();
        if (null != _status && '' != _status) {
            $.post(Routing.generate('ctf_team_status_update', { 'status': _status }), null, function(data) {
                var response = JSON.parse(data);

                if ('success' == response.result) {
                    $('#update-result').html('<div class="alert alert-success">' + response.message + '</div>').fadeIn(1200).fadeOut(5000);
                    $('#current-status').html(response.status).fadeOut('fast').fadeIn('slow');
                } else {
                    $('#update-result').html('<div class="alert alert-error">' + response.message + '</div>').fadeIn(1200).fadeOut(5000);
                }
            });
        }
    });
});
