<?php
//............................. Access Policy ..............
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

//.............................Connect Database  ..............
$conn = mysqli_connect("localhost", "root", "", "dragdrop");
if ($conn === false) {
    die("Error: Could Not Connect " . mysqli_connect_error());
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
//............................. Get data in GroupName table with selectedFonts table use join System ..............
case "GET":

$conditions = [];
if (isset($_GET['groupName'])) {
    $groupName = mysqli_real_escape_string($conn, $_GET['groupName']);
    $conditions[] = "f.groupName = '$groupName'";
}

$sql = "SELECT f.groupName, f.fontUrl, f.fontName, f.upload_at, f.status, f.fontId
        FROM selectedfonts AS f
        INNER JOIN groupname AS g ON f.groupName = g.groupName";

// Add dynamic conditions to the query
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY f.groupName, f.id, g.id";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    $currentGroupName = null;
    $groupData = null;

    while ($row = $result->fetch_assoc()) {
        if ($row['groupName'] !== $currentGroupName) {
            if ($groupData !== null) {
                $data[] = $groupData;
            }

            $groupData = [
                "groupName" => $row['groupName'],
                "entries" => []
            ];

            $currentGroupName = $row['groupName'];
        }

        $entry = [
            "fontUrl" => $row['fontUrl'],
            "fontName" => $row['fontName'],
            "upload_at" => $row['upload_at'],
            "status" => $row['status'],
            "fontId" => $row['fontId'],
            "groupName" => $row['groupName']
        ];
        $groupData["entries"][] = $entry;
    }

    if ($groupData !== null) {
        $data[] = $groupData;
    }
} else {
    echo "No records found";
}

echo json_encode($data);


break;


//............................. Delete data in groupName table with GroupName ..............
    case "DELETE":
        $path=explode('/', $_SERVER["REQUEST_URI"]);
        $deleteINGroup=mysqli_query($conn, "DELETE FROM groupname WHERE groupName='$path[4]'");
        $deleteINSelected=mysqli_query($conn, "DELETE FROM selectedfonts WHERE groupName='$path[4]'");
        if( $deleteINGroup){
            echo json_encode(["Success"=>"Fonts Deleted Successfully"]);
            return;
        }if($deleteINSelected){
            echo json_encode(["Success"=>"Fonts Deleted Successfully"]);
            return;
        }
        else{
    
             echo json_encode(["Success"=>"please Check the Data"]);
            return;
        }
        break;
    }

?>

