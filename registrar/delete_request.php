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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = intval($_POST['request_id']);
    $req_id = intval($_POST['requestor_id']);
    $stud_id = intval($_POST['student_id']); 

    $con->begin_transaction();

    try {
        $sqldeletecomment = "DELETE FROM comments WHERE request_id = $id";
        $resultdeletecomment = $con->query($sqldeletecomment);

        $sqldeletepurpose = "DELETE FROM purpose WHERE request_id = $id";
        $resultdeletepurpose = $con->query($sqldeletepurpose);

        $sqldeletereqdocu = "DELETE FROM reqdocu WHERE request_id = $id";
        $resultdeletereqdocu = $con->query($sqldeletereqdocu);

        $sqldeleteuploadeddocu = "DELETE FROM uploaded_docus WHERE request_id = $id";
        $resultdeleteuploadeddocu = $con->query($sqldeleteuploadeddocu);

        $sqldeletereq = "DELETE FROM request WHERE id = $id";
        $resultdeletereq = $con->query($sqldeletereq);

        $sqldeleterequestor = "DELETE FROM requestor WHERE id = $req_id";
        $resultdeleterequestor = $con->query($sqldeleterequestor);

        $sqldeletestudent = "DELETE FROM student WHERE id = $stud_id";
        $resultdeletestudent = $con->query($sqldeletestudent);

        $con->commit();

        echo 'success'; 
    } catch (Exception $e) {
        $con->rollback();
        echo 'error: ' . $e->getMessage(); 
    }
    exit;
}
?>
