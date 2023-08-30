<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST"); // Specify allowed methods

$conn = mysqli_connect("localhost", "root", "","dragdrop");

if($conn === false) {
    die("Error: Could Not Connect " . mysqli_connect_error());
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case "GET":
        echo "Get Api";
        break;

    case "POST":
        if(isset($_FILES['fontUrl'])){
            $fontUrl = $_FILES['fontUrl']['name'];
            $fontUrl_temp = $_FILES['fontUrl']['tmp_name'];
            $fontName = $_POST['fontName'];
            $upload_at = date('Y-m-d');
            $destination = $_SERVER['DOCUMENT_ROOT'].'/projects/dragDrop/files'."/".$fontUrl;

            $stmt = $conn->prepare("INSERT INTO fonts(fontUrl, fontName, upload_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fontUrl, $fontName, $upload_at);

            if ($stmt->execute()) {
                move_uploaded_file($fontUrl_temp, $destination);
                echo json_encode(["Success" => "TTF File Inserted Successfully"]);
            } else {
                echo json_encode(["Error" => "TTF File Not Inserted"]);
            }
            $stmt->close();
        } else {
            echo json_encode(["Error" => "Data not in correct format"]);
        }
        break;
}
?>
