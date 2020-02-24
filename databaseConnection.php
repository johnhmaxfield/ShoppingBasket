
<?php

// Set up connection to SQL database

$dbConnect = mysqli_connect("localhost", "root","", "catalogue");
if(mysqli_connect_errno())
{
    echo "Connection to catalogue database failed ".mysqli_connect_error();
}

?>
