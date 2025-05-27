<?php
$con = new mysqli('localhost', 'root', '', 'amparohigh');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); 
    $uploadRequirements = intval($_POST['upload_requirements']);
    
    $sqlupdate = "UPDATE request SET upload_requirements = ? WHERE id = ?";
    $stmt = $con->prepare($sqlupdate);

    if ($stmt) {
        $stmt->bind_param('ii', $uploadRequirements, $id);

        if ($stmt->execute()) {
            echo "Update successful";
        } else {
            echo "Update failed: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Prepare failed: " . $con->error;
    }

    $con->close();
}
?>
