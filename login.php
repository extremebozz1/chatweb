<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id']))
{
 header('location:index.php');
}

if(isset($_POST["login"]))
{
 $query = "
   SELECT * FROM login 
    WHERE username = :username
 ";
 $statement = $connect->prepare($query);
 $statement->execute(
    array(
      ':username' => $_POST["username"]
    )
  );
  $count = $statement->rowCount();
  if($count > 0)
  {
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
      if($_POST["password"] == $row["password"])
      {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $sub_query = "
        INSERT INTO login_details 
        (user_id) 
        VALUES ('".$row['user_id']."')
        ";
        $statement = $connect->prepare($sub_query);
        $statement->execute();
        $_SESSION['login_details_id'] = $connect->lastInsertId();
        header("location:index.php");
      }
      else
      {
       $message = "<label>Username / Password Wrong</label>";
      }
    }
 }
 else
 {
  $message = "<label>Username / Password Wrong</labe>";
 }
}

?>

<html>  
    <head>  
        <title>Login</title> 
        <style>
        body,html{
        background-image:url(wallpaper-paris-keren.jpg);
        }
        #kiri
        {
        width:50%;
        height:100px;
        float:left;
        }
        #kanan
        {
        width:50%;
        height:100px;
        float:right;
        }
        #panel-body{
          background-image:url(biru.jpg);
        }
        </style> 
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>  
    <body>  
      <div class="container">
        <br />
        <h3 align="center"><font color="white">Login</font></h3>
        <div id="kanan">
        
        </div>
        <div id="kiri">   
          <br />
          <div class="panel panel-default">
            <div class="panel-heading">Chat Application Login</div>
            <div class="panel-body">
              <form method="post">
                <p class="text-danger"><?php echo $message; ?></p>
                <div class="form-group">
                  <label>Enter Username</label>
                  <input type="text" name="username" class="form-control" required />
                </div>
                <div class="form-group">
                  <label>Enter Password</label>
                  <input type="password" name="password" class="form-control" required />
                </div>
                <div class="form-group">
                  <input type="submit" name="login" class="btn btn-info" value="Login" />
                </div>
                <div align="center">
                  <a href="register.php">Register</a>
                </div>
              </form>
            </div>
        </div>
      </div>
    </body>  
</html>
