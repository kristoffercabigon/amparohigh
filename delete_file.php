<?php
session_start();
include("conn.php");

if (isset($_SESSION["request_id"]) && isset($_SESSION["reference_no"]) && isset($_SESSION["requestor_id"])) {
    $id = $_SESSION["request_id"];
    $reference_no = $_SESSION["reference_no"];
    $requestor_id = $_SESSION["requestor_id"];

    if (isset($_GET['file_id']) && isset($_GET['file_path'])) {
        $file_id = $_GET['file_id'];
        $file_path = $_GET['file_path'];

        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                $con = new connec();

                $sql = "DELETE FROM uploaded_docus WHERE id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $file_id);

                if ($stmt->execute()) {
                    $stmt->close();
                    echo "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>File Deleted</title>
                        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
                        <style>
                            .modal-backdrop {
                                z-index: 1040 !important;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='modal fade' id='confirmationModal' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                            <div class='modal-dialog' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title' id='exampleModalLabel'>Success</h5>
                                    </div>
                                    <div class='modal-body'>
                                        The file has been deleted successfully.
                                    </div>
                                    <div class='modal-footer'>
                                        <a href='requesttrackermain.php?id=$id&requestor_id=$requestor_id' class='btn btn-primary'>OK</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
                        <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js'></script>
                        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
                        <script>
                            $(document).ready(function() {
                                $('#confirmationModal').modal('show');
                            });
                        </script>
                    </body>
                    </html>";
                } else {
                    echo "Error deleting record from database.";
                }
            } else {
                echo "Error deleting file from server.";
            }
        } else {
            echo "File not found on server.";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Session data is missing.";
}
