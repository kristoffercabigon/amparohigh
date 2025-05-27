<?php
session_start();
include("conn.php");

if (isset($_SESSION["request_id"]) && isset($_SESSION["reference_no"]) && isset($_SESSION["requestor_id"]) && isset($_SESSION["req_name"])) {
    $id = $_SESSION["request_id"];
    $reference_no = $_SESSION["reference_no"];
    $requestor_id = $_SESSION["requestor_id"];
    $req_name = $_SESSION["req_name"];

    $con = new connec();

    $sqlreg = "SELECT assisted_by_id FROM request WHERE id = $id";
    $resultreg = $con->query($sqlreg);
    
    if ($resultreg->num_rows > 0) {
        $row = $resultreg->fetch_assoc();
        $registrar_id = $row['assisted_by_id'];
    } else {
        $registrar_id = null;
    }    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_FILES["file"]) && is_array($_FILES["file"]["name"])) {
            $target_dir = 'uploads/' . $reference_no . '/';
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $files = $_FILES["file"];
            $file_count = count($files["name"]);

            $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");

            for ($i = 0; $i < $file_count; $i++) {
                if ($files["error"][$i] == 0) {
                    $target_file = $target_dir . basename($files["name"][$i]);
                    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    if (in_array($file_type, $allowed_types)) {
                        if (move_uploaded_file($files["tmp_name"][$i], $target_file)) {
                            $filename = $files["name"][$i];
                            $filesize = $files["size"][$i];
                            $filetype = $files["type"][$i];

                            $con = new connec();
                            
                            $sql = "INSERT INTO uploaded_docus (id, request_id, req_name, reference_no, registrar_id, filename, filesize, filetype, reg_seen) VALUES (0, $id, '$req_name', '$reference_no', $registrar_id, '$filename', $filesize, '$filetype', 0)";

                            if ($con->query($sql) === TRUE) {
                            } else {
                                echo renderModal(
                                    'Error Storing File Information',
                                    "Sorry, there was an error storing information for file " . basename($files["name"][$i]) . ": " . $con->error,
                                    'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
                                );
                                exit(); 
                            }

                        } else {
                            echo renderModal(
                                'File Upload Error',
                                'Sorry, there was an error uploading the file ' . basename($files["name"][$i]) . '.',
                                'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
                            );
                            exit(); 
                        }
                    } else {
                        echo renderModal(
                            'Invalid File Type',
                            'Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed. File: ' . basename($files["name"][$i]),
                            'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
                        );
                        exit(); 
                    }
                } else {
                    echo renderModal(
                        'File Error',
                        'Error with file ' . basename($files["name"][$i]) . ': ' . $files["error"][$i],
                        'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
                    );
                    exit(); 
                }
            }
            echo renderModal(
                'Upload Successful',
                'File(s) uploaded successfully.',
                'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
            );
            exit(); 
        } else {
            echo renderModal(
                'No Files Uploaded',
                'No files were uploaded.',
                'requesttrackermain.php?id=' . $id . '&requestor_id=' . $requestor_id
            );
            exit(); 
        }
    }
} else {
    echo renderModal(
        'Session Error',
        'Session variables are not set.',
        'index.php'
    );
    exit(); 
}

function renderModal($title, $message, $redirectUrl) {
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Notification</title>
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
        <style>
            .modal-backdrop {
                z-index: 1040 !important;
            }
        </style>
    </head>
    <body>
        <div class='modal fade' id='notificationModal' tabindex='-1' role='dialog' aria-labelledby='modalTitle' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalTitle'>$title</h5>
                    </div>
                    <div class='modal-body'>
                        $message
                    </div>
                    <div class='modal-footer'>
                        <a href='$redirectUrl' class='btn btn-primary'>OK</a>
                    </div>
                </div>
            </div>
        </div>
        
        <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js'></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script>
            $(document).ready(function() {
                $('#notificationModal').modal('show');
            });
        </script>
    </body>
    </html>";
}
?>
