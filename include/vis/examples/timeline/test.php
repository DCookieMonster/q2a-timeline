<?php
$servername = "localhost";
$username = "root";
$password = "9670";
$dbname = "q2a";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT `datetime`, `handle`, `event`, `params` FROM `qa_eventlog`";
$result = mysqli_query($conn, $sql);
$arr=array();
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {

    	$a=array('datetime' =>$row["datetime"] ,'handle' =>$row["handle"],'event' =>$row["event"] ,'params'=>$row["params"]);
    	array_push($arr, $a);
        //echo "datetime: " . $row["datetime"]. " - handle: " . $row["handle"]. " - event: " . $row["event"]. "<br>";
    }
} else {
    echo "[]";
}
echo json_encode($arr);

mysqli_close($conn);
?>