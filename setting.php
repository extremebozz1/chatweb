<?php

include('database_connection.php');

session_start();

$message = '';


if(isset($_POST["changes"])){

    $query = "
    SELECT * FROM login 
     WHERE username = :username
  ";
  $statement = $connect->prepare($query);
  $statement->execute(
     array(
       ':username' => $_SESSION['username']
     )
   );
   $count = $statement->rowCount();
   if($count > 0)
   {
     $result = $statement->fetchAll();
     foreach($result as $row)
     {
       if($_POST["passlama"] == $row["password"])
       {
        $sql = "
        UPDATE login SET password = :password WHERE username = :username
     ";
     $statement = $connect->prepare($sql);
    
        // execute the query
        $statement->execute(array(
            ':username' => $_SESSION['username'],
            ':password' => $_POST["password"]
        ));
    
        // echo a message to say the UPDATE succeeded
        echo $statement->rowCount() . " records UPDATED successfully";
        header("location:index.php");
       }
       else
       {
        echo "Password Lama Salah";
       }
     }
  }
}
?>

<html>
    <head>
    <style>
    body,html{
        background-image:url(gradien.jpg);
        }
        .panel{
            margin : 50px;
        }
        .form-group{
            margin:30px;
        }
    }
    </style>
        <title>setting</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>
    <body>
    <center>
        <h1><font color="white">Changes Password</font></h1>
    </center>
    <br />
    <div class="panel panel-default">
    
        <form method="POST">
            <div class="form-group">
                <label>Enter Password</label>
                <input type="text" name="passlama" class="form-control" required />
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" class="form-control" required />
            </div>
                <div class="form-group">
                <input type="submit" name="changes" class="btn btn-info" value="Changes" />
            </div>
        </form>
    
    </div>
    </body>
</html>