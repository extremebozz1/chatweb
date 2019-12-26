<?php

//fecth_is_type_status.php

include('database_connection.php');

session_start();

$query = "
FROM login_details 
SET is_type = '".$_POST["is_type"]."' 
";

$statement = $connect->prepare($query);

$statement->execute();

?>