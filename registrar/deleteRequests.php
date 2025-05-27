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

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ids'])) {
    $ids = explode(',', $_GET['ids']);

    $con->begin_transaction();

    try {
        foreach ($ids as $id) {
            $id = intval($id); 
            if ($id <= 0) continue; 

            $query = "SELECT requestor_id, student_id FROM request WHERE id = $id";
            $result = $con->query($query);
            if ($result && $row = $result->fetch_assoc()) {
                $req_id = intval($row['requestor_id']);
                $stud_id = intval($row['student_id']);

                $sqldeletecomment = "DELETE FROM comments WHERE request_id = $id";
                $con->query($sqldeletecomment);

                $sqldeletepurpose = "DELETE FROM purpose WHERE request_id = $id";
                $con->query($sqldeletepurpose);

                $sqldeletereqdocu = "DELETE FROM reqdocu WHERE request_id = $id";
                $con->query($sqldeletereqdocu);

                $sqldeleteuploadeddocu = "DELETE FROM uploaded_docus WHERE request_id = $id";
                $con->query($sqldeleteuploadeddocu);

                $sqldeletereq = "DELETE FROM request WHERE id = $id";
                $con->query($sqldeletereq);

                if ($req_id > 0) {
                    $sqldeleterequestor = "DELETE FROM requestor WHERE id = $req_id";
                    $con->query($sqldeleterequestor);
                }

                if ($stud_id > 0) {
                    $sqldeletestudent = "DELETE FROM student WHERE id = $stud_id";
                    $con->query($sqldeletestudent);
                }
            }
        }

        $con->commit();

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    } catch (Exception $e) {
        $con->rollback();
        echo 'error: ' . $e->getMessage();
    }
    exit;
} else {
    echo 'Invalid request';
    exit;
}

$con->close();
?>
