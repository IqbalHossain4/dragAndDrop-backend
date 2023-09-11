<?php
//............................. Access Policy.............................
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

//............................. Connect Database.............................
$conn = mysqli_connect("localhost", "root", "", "dragdrop");
if ($conn === false) {
    die("Error: Could Not Connect " . mysqli_connect_error());
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
//............................. Get Data in Fonts Table with id.............................
case "GET":
    $path = explode('/', $_SERVER['REQUEST_URI']);
    
    if (isset($path[4]) && is_numeric($path[4])) {
// Get a specific font by ID
        $fontId = $path[4];
        $font = mysqli_query($conn, "SELECT * FROM fonts WHERE id = $fontId");
        
        if (mysqli_num_rows($font) > 0) {
            $row = mysqli_fetch_array($font);
            echo json_encode(array(
                "id" => $row['id'],
                "fontUrl" => $row["fontUrl"],
                "fontName" => $row["fontName"],
                "upload_at" => $row["upload_at"]
            ));
        } else {
            echo json_encode(["error" => "Font not found"]);
        }

    } 

//.............................Get All Data in Fonts Table.............................
    elseif (empty($path[4])) {
        
        $json_array = [];
        $allFont = mysqli_query($conn, "SELECT * FROM fonts");
        if (mysqli_num_rows($allFont) > 0) {
            while ($row = mysqli_fetch_array($allFont)) {
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
    
    
//............................. Post in Fonts Table.............................
    case "POST":
        if (isset($_FILES['fontUrl'])) {
            $fontUrl = $_FILES['fontUrl']['name'];
            $fontUrl_temp = $_FILES['fontUrl']['tmp_name'];
            $fontName = $_POST['fontName'];
            $upload_at = date('Y-m-d');
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/projects/dragDrop/files' . "/" . $fontUrl;

// Check if data with the same fontId already exists
            $check_stmt = $conn->prepare("SELECT * FROM fonts WHERE fontUrl = ?");
            $check_stmt->bind_param("s", $fontUrl);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
// Data with the same fontId exists, update it
            if ($result->num_rows > 0) {
                echo json_encode(["Error" => "Font file with the same URL already exists"]);
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO fonts(fontUrl, fontName, upload_at) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $fontUrl, $fontName, $upload_at);

                if ($insert_stmt->execute()) {
                    move_uploaded_file($fontUrl_temp, $destination);
                    echo json_encode(["Success" => "TTF File Inserted Successfully"]);
                } else {
                    echo json_encode(["Error" => "TTF File Not Inserted"]);
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        } else {
            echo json_encode(["Error" => "Data not in correct format"]);
        }
        break;
    
    
//............................. Delete data in Fonts Table with id.............................
            case "DELETE":
                $path=explode('/', $_SERVER["REQUEST_URI"]);
                $result=mysqli_query($conn, "DELETE FROM fonts WHERE id='$path[4]'");
                if( $result){
                    echo json_encode(["Success"=>"Fonts Deleted Successfully"]);
                    return;
                }else{
    
                     echo json_encode(["Success"=>"please Check the Data"]);
                    return;
                }
                break;
}
?>
