<?php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id']))
{
    header("location:login.php");
}

$query = "
SELECT * FROM login 
WHERE user_id != '".$_SESSION['user_id']."' 
";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();


foreach($result as $row)
{
    $status = '';
    

    if(!isset($_SESSION['username']))
    {
        $status = '<span class="label label-success">O</span>';
    }
    else
    {
        $status = '<span class="label label-success">O</span>';
    }
    
}

?>

<html>  
    <head>  
        <title>Chatting Application</title>
        <style>
            body,html{
                background-image:url(gradien.jpg);
            }
            .row{
                background-color:white;
            }
            .col-sm-6{
                padding-top:10px;
            }
            .nama{
                float:left;
            }
            .log{
                float:right;
            }
            

        </style>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <link rel="stylesheet" href="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.css">
            <link rel="stylesheet" href="stlye.css">
            <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    </head>  
    <body>  
        <div class="container">
            <br />
            <h3 align="center"><font color="white">Wellcome to My Chat Application</font></h3><br />
            <br />
            <div class="row">
                <div class="col-sm-6">
                    
                    <div class="table-responsive">
                        <h4 align="center">Online User</h4>
                        <h5 align="center"><a href="setting.php">Setting</a></h5>
                        <div class="foto">
                            <img src="profil.jpg" width="50" height="50">
                        </div>
                        <div class="nama">
                            <p><?php echo $_SESSION['username'];  echo $status ?></p>
                        </div>
                        <div class="log">
                            <p><a href="logout.php">Logout</a></p>
                        </div>
                        <div id="user_details"></div>
                        
                    </div>
                </div>
                <div class="col-sm-6" id="user_model_details">
                    
                </div>    
            </div>
        </div>
    </body>  
</html>  


<script>  
$(document).ready(function(){

    fetch_user();

    setInterval(function(){
    update_last_activity();
    fetch_user();
    update_chat_history_data();
    }, 5000);

    function fetch_user()
    {
        $.ajax({
            url:"fetch_user.php",
            method:"POST",
            success:function(data){
                $('#user_details').html(data);
            }
        })
    }

 function update_last_activity()
 {
    $.ajax({
        url:"update_last_activity.php",
        success:function()
        {

        }
    })
 }

 //chat box
 function make_chat_dialog_box(to_user_id, to_user_name)
 {
    var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
    modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
    modal_content += fetch_user_chat_history(to_user_id);
    modal_content += '</div>';
    modal_content += '<div class="form-group">';
    modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
    modal_content += '</div><div class="form-group" align="right">';
    modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
    $('#user_model_details').html(modal_content);
 }

// in chat room
 $(document).on('click', '.start_chat', function(){
    var to_user_id = $(this).data('touserid');
    var to_user_name = $(this).data('tousername');
    make_chat_dialog_box(to_user_id, to_user_name);
    $("#user_dialog_"+to_user_id).dialog({
        autoOpen:false,
        width:400
    });
    $('#user_dialog_'+to_user_id).dialog('open');
    $('#chat_message_'+to_user_id).emojioneArea({
        pickerPosition:"top",
        toneStyle: "bullet"
    });
 });

//send button
 $(document).on('click', '.send_chat', function(){
    var to_user_id = $(this).attr('id');
    var chat_message = $('#chat_message_'+to_user_id).val();
    $.ajax({
        url:"insert_chat.php",
        method:"POST",
        data:{to_user_id:to_user_id, chat_message:chat_message},
        success:function(data)
        {
            $('#chat_message_'+to_user_id).val('');
            var element = $('#chat_message_'+to_user_id).emojioneArea();
            element[0].emojioneArea.setText('');
            $('#chat_history_'+to_user_id).html(data);
        }
    })
 });

 //chat room
 function fetch_user_chat_history(to_user_id)
 {
    $.ajax({
        url:"fetch_user_chat_history.php",
        method:"POST",
        data:{to_user_id:to_user_id},
        success:function(data){
            $('#chat_history_'+to_user_id).html(data);
        }
    })
 }

 function update_chat_history_data()
 {
    $('.chat_history').each(function(){
        var to_user_id = $(this).data('touserid');
        fetch_user_chat_history(to_user_id);
    });
 }

 //notif typing
 $(document).on('click', '.ui-button-icon', function(){
    $('.user_dialog').dialog('destroy').remove();
 });

 $(document).on('focus', '.chat_message', function(){
    var is_type = 'yes';
    $.ajax({
        url:"update_is_type_status.php",
        method:"POST",
        data:{is_type:is_type},
        success:function()
        {

        }
    })
 });

 $(document).on('blur', '.chat_message', function(){
    var is_type = 'no';
    $.ajax({
        url:"update_is_type_status.php",
        method:"POST",
        data:{is_type:is_type},
        success:function()
        {
    
        }
    })
 });
 
});  
</script>
