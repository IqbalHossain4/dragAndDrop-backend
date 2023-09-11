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
//............................. Get data in Selected Fonts table with GroupName ..............
case "GET":
 
    if (isset($_GET['groupName'])) {
        $groupName = $_GET['groupName'];
        $sql = "SELECT * FROM selectedfonts WHERE groupName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $groupName);
        $stmt->execute();

        $result = $stmt->get_result();

        if (mysqli_num_rows($result) > 0) {
            $json_array["fontData"] = [];
            while ($row = mysqli_fetch_array($result)) {
                $json_array["fontData"][] = array(
                    "id" => $row['id'],
                    "fontUrl" => $row['fontUrl'],
                    "fontName" => $row['fontName'],
                    "upload_at" => $row['upload_at'],
                    "status" => $row['status'],
                    "fontId" => $row['fontId'],
                    "groupName" => $row['groupName']
                );
            }
            echo json_encode($json_array["fontData"]);
        } else {
            echo json_encode(["Error" => "No data found for the given ID"]);
        }

    } 
    
//............................. Get data in Selected Fonts table with id.............................
    elseif(isset($_GET['id'])) {
        $groupName = $_GET['id'];
        $sql = "SELECT * FROM selectedfonts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $groupName);
        $stmt->execute();
        $result = $stmt->get_result();
        if (mysqli_num_rows($result) > 0) {
            $json_array["fontData"] = [];
            while ($row = mysqli_fetch_array($result)) {
                $json_array["fontData"][] = array(
                    "id" => $row['id'],
                    "fontUrl" => $row['fontUrl'],
                    "fontName" => $row['fontName'],
                    "upload_at" => $row['upload_at'],
                    "status" => $row['status'],
                    "fontId" => $row['fontId'],
                    "groupName" => $row['groupName']
                );
            }
            echo json_encode($json_array["fontData"]);
        } else {
            echo json_encode(["Error" => "No data found for the given ID"]);
        }
    }

//............................. Get All Data in SelectedFonts Table.............................

     else {
        $slectedAllFonts= mysqli_query($conn, "SELECT * FROM selectedfonts");
        if(mysqli_num_rows($slectedAllFonts) > 0){
            while($row=mysqli_fetch_array($slectedAllFonts)){
                $json_array["fontData"][] = array(
                    "id" => $row['id'],
                    "fontUrl" => $row['fontUrl'],
                    "fontName" => $row['fontName'],
                    "upload_at" => $row['upload_at'],
                    "status" => $row['status'],
                    "fontId" => $row['fontId'],
                    "groupName" => $row['groupName']
                );
            }
            echo json_encode($json_array["fontData"]);
        }
    }

    break;


//............................. Post in Selected Fonts Table.............................
    case "POST":
        $fontsData =json_decode(file_get_contents('php://input')) ;
        $inserted_rows= 0 ;
        foreach ($fontsData as $data) {
// Check if the required properties exist in the JSON data
            if (
                property_exists($data, 'fontUrl') &&
                property_exists($data, 'fontName') &&
                property_exists($data, 'status') &&
                property_exists($data, 'fontId')&&
                property_exists($data, 'groupName')
            ) {
                $fontUrl = $data->fontUrl;
                $fontName = $data->fontName;
                $upload_at = date('Y-m-d');
                $status = $data->status;
                $fontId = $data->fontId;
                $groupName = $data->groupName;

// Check if data with the same fontId already exists
            $check_stmt = $conn->prepare("SELECT * FROM selectedfonts WHERE fontUrl = ?");
            $check_stmt->bind_param("s", $fontUrl);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            

            if ($result->num_rows > 0) {
                echo json_encode(["Error" => "Font file with the same URL already exists"]);
            }else{
                $stmt = $conn->prepare("INSERT INTO selectedfonts (fontUrl, fontName, upload_at, status, fontId, groupName) VALUES (?, ?, ?, ?, ?,?)");
                $stmt->bind_param("ssssss", $fontUrl, $fontName, $upload_at, $status, $fontId,$groupName);

                if ($stmt->execute()) {
                    $inserted_rows++;
                    echo "Succefully Uploade";
                } else {
                    echo "Error inserting data: " . $stmt->error;
                }
            }
            } else {

                echo "Error: Missing required properties in JSON data.";

            }


//............................. Post in GroupName Table.............................

 // Check if data with the same fontId already exists
            $check_stmt = $conn->prepare("SELECT * FROM groupname WHERE groupName = ?");
            $check_stmt->bind_param("s", $groupName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
// Data with the same fontId exists, update it
            if ($result->num_rows > 0) {
                echo json_encode(["Error" => "Font file with the same URL already exists"]);
            }else{
                $stmt = $conn->prepare("INSERT INTO groupname (groupName) VALUES (?)");
                $stmt->bind_param("s", $groupName);
    
                if ($stmt->execute()) {
                    echo "Succefully Uploade";
                } else {
                    echo "Error inserting data: " . $stmt->error;
                }
            }
        }
        break;


//............................. Delete in selectedFonts Table.............................
    case "DELETE":
        $path=explode('/', $_SERVER["REQUEST_URI"]);
        $result=mysqli_query($conn, "DELETE FROM selectedfonts WHERE fontId='$path[4]'");
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
