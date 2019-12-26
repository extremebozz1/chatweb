<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat;charset=utf8mb4", "root", "");

date_default_timezone_set('Asia/Kolkata');

function fetch_user_last_activity($user_id, $connect)
{
    $query = "
    SELECT * FROM login_details 
    WHERE user_id = '$user_id' 
    ORDER BY last_activity DESC 
    LIMIT 1
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return $row['last_activity'];
    }
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
    $query = "
    SELECT * FROM chat_message 
    WHERE (from_user_id = '".$from_user_id."' 
    AND to_user_id = '".$to_user_id."') 
    OR (from_user_id = '".$to_user_id."' 
    AND to_user_id = '".$from_user_id."') 
    ORDER BY timestamp DESC
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '<ul class="list-unstyled">';
    foreach($result as $row)
    {
        $user_name = '';
        if($row["from_user_id"] == $from_user_id)
        {
            $user_name = '<b class="text-success">You</b>';
            $output .= '
            <li style="border-bottom:1px dotted #ccc">
                <div class="row">
                    <div class="col-md-6">
                        <div style="position:absolute; top:50px; font-size:10px;"><em>'.$row['timestamp'].'</em></div>
                    </div>                
                    <div class="col-md-6 text-right">
                        <h5>'.$user_name.'</h5>
                        <p>'.$row["chat_message"].'</p>
                    </div>
                </div>
            </li>
            ';
           
        }
        else
        {
            $user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
            $output .= '
            <li style="border-bottom:1px dotted #ccc">
                <div class="row">
                    <div class="col-md-6" style="float:left">
                        <h5>'.$user_name.'</h5>
                        <p>'.$row["chat_message"].'</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div style="position:absolute; top:50px; left:105px; font-size:10px;"><em>'.$row['timestamp'].'</em></div>
                    </div>
                </div>
            </li>
            ';
        }
        // $output .= '
        // <li style="border-bottom:1px dotted #ccc">
        //     <p>'.$user_name.' - '.$row["chat_message"].'
        //         <div align="right">
        //             - <small><em>'.$row['timestamp'].'</em></small>
        //         </div>
        //     </p>
        // </li>
        // ';
    }
    $output .= '</ul>';
    $query = "
    UPDATE chat_message 
    SET status = '0' 
    WHERE from_user_id = '".$to_user_id."' 
    AND to_user_id = '".$from_user_id."' 
    AND status = '1'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $output;
}

function get_user_name($user_id, $connect)
{
    $query = "SELECT username FROM login WHERE user_id = '$user_id'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return $row['username'];
    }
}

function count_unseen_message($from_user_id, $to_user_id, $connect)
{
    $query = "
    SELECT * FROM chat_message 
    WHERE from_user_id = '$from_user_id' 
    AND to_user_id = '$to_user_id' 
    AND status = '1'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $count = $statement->rowCount();
    $output = '';
    if($count > 0)
    {
        $output = '<span class="label label-success">'.$count.'</span>';
    }
    return $output;
}

function fetch_is_type_status($user_id, $connect)
{
    $query = "
    SELECT is_type FROM login_details 
    WHERE user_id = '".$user_id."' 
    ORDER BY last_activity DESC 
    LIMIT 1
    "; 
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '';
    foreach($result as $row)
    {
        if($row["is_type"] == 'yes')
        {
        $output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
        }
    }
    return $output;
}


?>