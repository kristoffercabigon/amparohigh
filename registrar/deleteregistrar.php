<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = isset($_POST['registrar_id']) ? intval($_POST['registrar_id']) : 0;

    if ($reg_id > 0) {
        $con->begin_transaction();

        try {
            $updateCommentsStmt = $con->prepare("UPDATE comments SET registrar_id = NULL WHERE registrar_id = ?");
            $updateCommentsStmt->bind_param("i", $reg_id);

            if (!$updateCommentsStmt->execute()) {
                throw new Exception("Update comments failed: " . $updateCommentsStmt->error);
            }

            $updateCommentsStmt->close();

            $updateRequestStmt = $con->prepare("UPDATE request SET assisted_by_id = NULL, status_id = 1 WHERE assisted_by_id = ?");
            $updateRequestStmt->bind_param("i", $reg_id);

            if (!$updateRequestStmt->execute()) {
                throw new Exception("Update request failed: " . $updateRequestStmt->error);
            }

            $updateRequestStmt->close();

            $deleteStmt = $con->prepare("DELETE FROM registrar WHERE id = ?");
            $deleteStmt->bind_param("i", $reg_id);

            if ($deleteStmt->execute()) {
                $con->commit();
                
                $_SESSION = array();
                session_unset();
                session_destroy();

                echo 'success'; 
            } else {
                throw new Exception("Deletion failed: " . $deleteStmt->error);
            }

            $deleteStmt->close();
        } catch (Exception $e) {
            $con->rollback();
            echo 'error: ' . $e->getMessage();
        }
    } else {
        echo 'error: Invalid registrar ID';
    }
} else {
    echo 'error: Invalid request method';
}

$con->close();
?>
