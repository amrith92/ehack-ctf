function updateCurrentStats() {
    $.get(Routing.generate('ctf_quest_current', null), null, function(data) {
        var res = JSON.parse(data);

        $('#stage-display').text(res.stage).fadeIn();
        $('#level-display').text(res.level).fadeIn();
        $('#title-display').text(res.title).fadeIn();
        $('#score-display').text(res.score).fadeIn();
        
        $('.accordion-body.in').collapse('hide');
        $('#collapse-' + res.stage).not('.in').collapse('show');
        $('.accordion-inner ul li').each(function (index) {
            $(this).removeClass('selected-level');
        });
        $('#collapse-' + res.stage + ' .accordion-inner ul li:eq(' + (res.level - 1) + ')').addClass('selected-level');
    });
}

var cli_on = false, clu_on = false;
$(document).ready(function() {
    $.ajaxSetup({ cache: true });
    $('#toggle-chat-btn').click(function() {
        var btn = $(this);
        
        if ('true' != $('#chat-client').attr('data-shown')) {
            $('#chat-client').css({
                position: 'absolute',
                top: btn.offset().top + btn.outerHeight() + 10,
                left: btn.offset().left
            }).attr('data-shown', 'true').show();
        } else {
            $('#chat-client').attr('style', '').attr('data-shown', 'false').fadeOut();
        }
    });
    
    $('#client-list-button').click(function(e) {
        e.preventDefault();
        
        if (false == cli_on) {
            $('#client-list-button').attr('data-title', 'Member List');
            $('#client-list-button').attr('data-html', 'true');
            $('#client-list-button').attr('data-placement', 'left');
            $('#client-list-button').attr('data-content', $('#clientList').html());
            $('#client-list-button').popover('show');
            cli_on = true;
        } else {
            $('#client-list-button').popover('destroy');
            cli_on = false;
        }
    });

    $('#continue-quest-button').click(function(e) {
       e.preventDefault();
       
       $.get(Routing.generate('ctf_quest_continue', null), null, function(data) {
           $('#question-dyn').html(data).show().fadeIn();
           updateCurrentStats();
       });
    });
    
    $('#question-container').on('click', '#clue-button', function(e) {
        e.preventDefault();
        
        if ($(this).attr('data-content') == null) {
            document.getElementById('clue-button-loader').innerHTML = document.getElementById('loader').innerHTML;
            $.get($(this).attr('data-link'), null, function(data) {
                $('#clue-button').attr('data-content', data);
                $('#clue-button').popover({
                    title: 'Your Hint',
                    content: data,
                    html: true,
                    placement: 'right',
                    delay: 50
                });
                $('#clue-button').popover('show');
                clu_on = true;
                document.getElementById('clue-button-loader').innerHTML = null;
            });
        } else {
            if (false == clu_on) {
                $('#clue-button').popover('show');
                clu_on = true;
            } else {
                $('#clue-button').popover('close');
                clu_on = false;
            }
        }
    });
    
    $('#question-container').on('submit', '#answer-form', function(e) {
        e.preventDefault();
        
        document.getElementById('answer-button-loader').innerHTML = document.getElementById('loader').innerHTML;
        
        $.post($('#answer-form').attr('action'), $('#answer-form').serialize(), function(data) {
            var response = JSON.parse(data);
            
            document.getElementById('answer-button-loader').innerHTML = null;
            
            if ('success' == response.result || 'stoptoshare' == response.result) {
                $('#question-result').html('<div class="alert alert-success">' + response.message + '</div>').show().fadeIn(1200).fadeOut(5000);
                
                document.getElementById('question-dyn').innerHTML = '<div id="question-dyn-loader">' + document.getElementById('loader').innerHTML + '</div>';
                
                if ('success' == response.result) {
                    $.get(Routing.generate('ctf_quest_fetch', { qid: response.next }), null, function(data) {
                        var rp = JSON.parse(data);

                        $('#question-dyn').html(rp.message).show().fadeIn();

                        if ('success' == rp.result) {
                            updateCurrentStats();
                        }
                    });
                } else {
                    $.get(Routing.generate('ctf_quest_stoptoshare', { next: response.next }), null, function(data) {
                        $('#question-dyn').html(data).show().fadeIn();
                        updateCurrentStats();
                    });
                }
            } else if ('finish' == response.result) {
                $('#question-result').html('<div class="alert alert-success">' + response.message + '</div>').show().fadeIn(1200).fadeOut(5000);
                
                document.getElementById('question-dyn').innerHTML = '<div id="question-dyn-loader">' + document.getElementById('loader').innerHTML + '</div>';
                
                $.get(Routing.generate('ctf_quest_finish'), null, function(data) {
                    $('#question-dyn').html(data).show().fadeIn();
                    updateCurrentStats();
                });
            } else {
                $('#question-result').html('<div class="alert alert-error">' + response.message + '</div>').show().fadeIn(1200).fadeOut(5000);
            }
        });
    });
    
    $('.fetch-item').click(function(e) {
        e.preventDefault();
        
        document.getElementById('question-dyn').innerHTML = '<div id="question-dyn-loader">' + document.getElementById('loader').innerHTML + '</div>';
        
        $.get(this, null, function(data) {
            var rp = JSON.parse(data);
            
            $('#question-dyn').html(rp.message).show().fadeIn();
            
            if ('success' == rp.result) {
                updateCurrentStats();
            }
        });
    });
    
    $.get(Routing.generate('ctf_quest_rank', null), null, function(data) {
        var response = JSON.parse(data);
        if (null != response.rank && false != response.rank) {
            $('#rank-display').text(response.rank).fadeOut().fadeIn('slow');
        }
    });
    setInterval(function() {
        $.get(Routing.generate('ctf_quest_rank', null), null, function(data) {
            var response = JSON.parse(data);
            if (null != response.rank && false != response.rank) {
                $('#rank-display').text(response.rank).fadeOut().fadeIn('slow');
            }
        });
    }, 20000);
    
    continueBoot();
});
