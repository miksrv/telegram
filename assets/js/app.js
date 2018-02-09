/**
 * @package    TestWork
 * @subpackage JavaScript
 * @category   App
 * @author     Misha (Mik™) <miksrv.ru> <miksoft.tm@gmail.com>
 */

var App = App || {};

App.Var = {
    ChatID: false,
    Timer: false
};

App.Func = {
    /**
     * Get new messages for current chat
     * @param integer id
     */
    getUpdates: function(id, avatar) {
        $.getJSON("/ajax.php?action=getUpdates&chat=" + id, function(data) {
            if (data.status == true && data.messages.length > 0) {
                $('#chat-content .messages-list').append('<li><div class="avatar"><img src="/loads/' + avatar + '" alt="" />' 
                + '</div><p>' + data.messages[0].item_text + '</p><span class="date">' + data.messages[0].item_date + '</span></li>');
            }
        });
    }, // getUpdates: function(id)
    /**
     * Return all chat messages
     * @param integer id
     */
    GetChat: function(id) {
        if (App.Var.ChatID === id) {
            return ;
        }

        if (App.Var.Timer != false) {
            clearInterval(App.Var.Timer);
        }

        $('.user-list li').removeClass('active');

        $.getJSON("/ajax.php?chat=" + id, function(data) {
            App.Var.ChatID = id;

            var items = [];

            $.each(data.messages, function(key, val) {
                items.push('<li><div class="avatar"><img src="/loads/' + (val.item_answer == 1 ? 'manager.png' : data.user[0].user_avatar) + '" alt="" />' 
                         + '</div><p>' + val.item_text + '</p><span class="date">' + val.item_date + '</span></li>');
            });
            
            if (data.user[0].user_favorite == 1) {
                $('#star-switch').addClass('active');
            } else {
                $('#star-switch').removeClass('active');
            }
            
            
            $('#chat' + id).addClass('active');
            $('#new' + id).fadeOut();
            $("#chat-content").html('');
            $( "<ul/>", {
                "class": "messages-list",
                html: items.join( "" )
            }).appendTo("#chat-content");

            App.Var.Timer = setInterval(function() {App.Func.getUpdates(id, data.user[0].user_avatar)}, 10000);
            
            $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
        });
    }, // GetChat: function(id)
    /**
     * Send telegram message
     * @returns {undefined}
     */
    Send: function() {
        if (App.Var.ChatID === false) {
            alert('Выберите чат');

            return ;
        }

        var message = $('#message').val();

        $.post("/ajax.php?chat=" + App.Var.ChatID, {text: $('#message').val()}).done(function(data) {
            if (data.status == true) {
                $('#chat-content .messages-list').append('<li><div class="avatar"><img src="/loads/' + data.user.user_avatar + '" alt="" />' 
                + '</div><p>' + data.messages.item_text + '</p><span class="date">' + data.messages.item_date + '</span></li>');

                $('#message').val('');
            }
        });

        $("#chat-content").animate({ scrollTop: $('#chat-content').prop("scrollHeight")}, 500);

        return;
    }, // Send: function()
    /**
     * Keystroke interception
     * @param object event
     */
    KeyPress: function(event) {
        if (event.keyCode === 13) {
            return this.Send();
        }
        
        return;
    }, // KeyPress: function(event)
    /**
     * Swicth ON \ OFF favorite chat
     * @returns {undefined}
     */
    Favorite: function() {
        if (App.Var.ChatID === false) {
            alert('Выберите чат');

            return ;
        }
        
        if ($('#star-switch').hasClass('active')) {
            $.getJSON("/ajax.php?chat=" + App.Var.ChatID + "&favorite=0");
            $('#star-switch').removeClass('active');
        } else {
            $.getJSON("/ajax.php?chat=" + App.Var.ChatID + "&favorite=1");
            $('#star-switch').addClass('active');
        }
        
        this.UpdateUserList();
    }, // Favorite: function()
    /**
     * Get user list updates
     */
    UpdateUserList: function() {
        $.getJSON("/ajax.php?action=getUserList", function(data) {
            if (data.status == true) {
                $('.user-list ul').html('');
                $('.user-list ul').append(data.content);
                
                if (App.Var.ChatID != false) {
                    $('#chat' + App.Var.ChatID).addClass('active');
                }
            }
        });
    }, // UpdateUserList: function()
    /**
     * Set chat window size
     */
    Resize: function() {
        $('#chat-content').height($(window).height() - 85);
        $('.user-list').height($(window).height() - 70);
    } // Resize: function()
};

$(document).ready(function() {
    setInterval(function() {App.Func.UpdateUserList()}, 10000);
    App.Func.Resize();
});

$( window ).resize(function() {
    App.Func.Resize();
});