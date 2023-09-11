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
//................. Get Data in Fonts Table Search...........
case "GET":
    if (isset($_GET['searchQuery'])) {
        $searchText = mysqli_real_escape_string($conn, $_GET['searchQuery']); 
        $sql = "SELECT * FROM fonts WHERE fontName LIKE '%$searchText%'";
        $srcData = mysqli_query($conn, $sql); 

        $json_array = [];
        
        if ($srcData) {
            if (mysqli_num_rows($srcData) > 0) {
                while ($row = mysqli_fetch_array($srcData)) {
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
    }
    
    
    break;

}



?>