<?php 
include("header.php");
include("conn.php");

$con = new connec();

$id = "";
if(isset($_GET["id"]))
{
    $id = $_GET["id"];
}

    $sql = "SELECT * FROM request WHERE id = $id";
    $result = $con->select_by_query($sql);

    $rows = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Success</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/newrequest.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="js/newrequest.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsToRemove = [
            'level',
            'req_name',
            'req_contact_no',
            'req_email',
            'stud_lastname',
            'stud_firstname',
            'grade',
            'section',
            'sylastattended',
            'relationship',
            'otherDocument',
            'schoolPurpose',
            'othersPurpose',
            'stud_midname',
            'stud_suffix',
            'stud_contact_no',
            'stud_email',
            'sig-image',
            'sig-canvas',
            'signaturevalidation',
            'confirm-checkbox',
            'sig-dataUrl',
            'flexCheckDefault1',
            'flexCheckDefault2',
            'flexCheckDefault3',
            'flexCheckDefault4',
            'flexCheckDefault5',
            'flexCheckDefault6',
            'flexCheckDefault7',
            'flexCheckDefault8',
            'flexCheckDefault9',
            'flexCheckDefault10',
            'flexCheckPurpose0',
            'flexCheckPurpose1',
            'flexCheckPurpose2',
            'flexCheckPurpose3',
            'flexCheckPurpose4',
            'signature'
        ];

        itemsToRemove.forEach(item => localStorage.removeItem(item));
    });
</script>
</head>
<body>
<div id="NewRequest" class="bodysuccess">
    <div class="container">  
        <div class="row justify-content-center mt-3 mima1">  
            <form method="POST" id="form" class="mima1">
                <h2 class="titlenewrequest">
                    <img src="images/document.png" alt="Document Icon" class="icondocu" />
                    New Request
                </h2>
                <hr class="hrrequest">  
                <ul id="progressbar">  
                    <li class="active" id="step6">Welcome</li>  
                    <li class="active" id="step7">Request Details</li>    
                </ul>

                <div class="form-listing1 successtable">
                    <div class="form-title-area1">
                        <h3 class="form-title3">Submission Result</h3>
                    </div>

                    <div class="form-descrip1">
                        <div class="form-details">Your document request was submitted successfully. You will receive a confirmation email shortly with your reference number and pin.</div>
                    </div>

                    <table class="table table-bordered custom-table1">
                        <tr>
                            <td>Reference No.</td>
                            <td class="referencetable">
                            <?php
                                if (!empty($rows)) {
                                    foreach ($rows as $row) {
                                        echo '<span style="color: black;">' . $row["reference_no"] . '</span>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td>PIN</td>
                            <td class="pintable">
                            <?php
                                if (!empty($rows)) {
                                    foreach ($rows as $row) {
                                        echo '<span style="color: black;">' . $row["pin"] . '</span>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                    </table>

                    <div class="form-descrip1">
                        <div class="form-details8">You will be notified via email or in the number you provided once the document(s) is/are ready to claim. You can track the status of your request at the Request Tracker using the reference number provided.</div>
                    </div>
                </div>
                <input type="button" name="ProceedtoTrack" class="btn btn-primary successbtns" value="Proceed to Tracker" onclick="goToRequestTracker()"/>
                <input type="button" name="CloseSub" class="btn btn-secondary successbtns" value="Close" onclick="goToHome()"/>
            </form>    
        </div>
    </div>
</div>
    
</body>
</html>