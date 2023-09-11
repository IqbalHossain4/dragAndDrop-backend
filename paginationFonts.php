<?php
//............................. Access Policy.............................
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

//............................. Connect Database..........................
$conn = mysqli_connect("localhost", "root", "", "dragdrop");
if ($conn === false) {
    die("Error: Could Not Connect " . mysqli_connect_error());
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
//................. Get Data in Fonts Table With Pagination...........
case "GET":
    $sqlQuery= "SELECT * FROM fonts";
    $query=mysqli_query($conn,$sqlQuery);
    $num_rows=mysqli_num_rows( $query);


//Get Data with Name Number
    if(isset($_GET['pageNo'])){
        $getPageNo=$_GET['pageNo'];
        $offset=($getPageNo - 1) * 6;

    }
    $sql="SElECT * FROM fonts LIMIT 6 OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    $json_array = [];
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $json_array[] = array(
                        "id" => $row['id'],
                        "fontUrl" => $row["fontUrl"],
                        "fontName" => $row["fontName"],
                        "upload_at" => $row["upload_at"]
                    );
                }
                echo json_encode($json_array);
            } else {
                echo json_encode(["error" => "No fonts found"]);
            }
        }


break;
}


?>