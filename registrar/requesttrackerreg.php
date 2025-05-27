<?php
ob_start();
session_start();

if (empty($_SESSION['registrar_id']) || empty($_SESSION['registrar_name'])) {
    header("Location: login.php");
    exit();
}

include("registrar_header.php");
include("../conn.php");

$con = new connec();

$_SESSION["request_id"] = isset($_GET["id"]) ? $_GET["id"] : null;
$_SESSION["requestor_id"] = isset($_GET["requestor_id"]) ? $_GET["requestor_id"] : null;
$_SESSION["reference_no"] = isset($_GET["reference_no"]) ? htmlspecialchars($_GET["reference_no"]) : null;

$reg_id = isset($_GET['registrar_id']) ? $_GET['registrar_id'] : '';
$reg_name = isset($_GET['registrar_name']) ? $_GET['registrar_name'] : '';

$_SESSION["registrar_id"] = $reg_id;
$_SESSION["registrar_name"] = $reg_name; 

$id = isset($_SESSION["request_id"]) ? $_SESSION["request_id"] : null;
$requestor_id = isset($_SESSION["requestor_id"]) ? $_SESSION["requestor_id"] : null;
$reference_no = isset($_SESSION["reference_no"]) ? $_SESSION["reference_no"] : null;
$registrar_id = isset($_SESSION["registrar_id"]) ? $_SESSION["registrar_id"] : null;
$registrar_name = isset($_SESSION["registrar_name"]) ? $_SESSION["registrar_name"] : 'Unknown'; 

$sql = "SELECT * FROM request WHERE id = $id";
$result = $con->select_by_query($sql);

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

$sqlreqby = "SELECT request.id, requestor.req_name, status.status
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN relationship ON requestor.relationship_id = relationship.id 
JOIN `status` ON request.status_id = `status`.id
WHERE request.id = $id";
$resultreqby = $con->select_by_query($sqlreqby);

$sqlupload = "SELECT upload_requirements FROM request WHERE request.id = $id";
$resultupload = $con->query($sqlupload);

if ($resultupload) {

    $row = $resultupload->fetch_assoc();
    
    if ($row) {
        $uploadRequirements = $row['upload_requirements'];
        $disabled = $uploadRequirements == 0 ? 'disabled' : '';
    } else {
        $disabled = '';
    }
} else {
    $disabled = '';
}

$sqlStatuses = "SELECT id, status FROM `status`";
$allStatuses = $con->select_by_query($sqlStatuses);

$sqlreqby1 = "SELECT status.id AS current_status_id, status.status AS current_status
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN `status` ON request.status_id = `status`.id
WHERE request.id = $id";
$resultreqby1 = $con->select_by_query($sqlreqby1);
$currentStatus = $resultreqby1->fetch_assoc();
$currentStatusId = $currentStatus['current_status_id'];

$assisted_by_name = 'None';
$show_assist_button = false;
$show_unassist_button = false;

$sqlassist = "SELECT request.assisted_by_id, registrar.reg_name, registrar.reg_image
              FROM request
              LEFT JOIN registrar ON request.assisted_by_id = registrar.id
              WHERE request.id = $id";

$resultassist = $con->query($sqlassist);

if ($resultassist && $resultassist->num_rows > 0) {
    $row = $resultassist->fetch_assoc();
    if ($row['assisted_by_id'] === NULL) {
        $show_assist_button = true;
    } else {
        if ($row['assisted_by_id'] == $registrar_id) {
            $show_unassist_button = true;
        }
        if (!empty($row['reg_name'])) {
            $assisted_by_name = $row['reg_name'];
            $reg_image = $row ['reg_image'];
        }
    }
}

$disableDropdown = ($assisted_by_name === 'None') ? 'disabled' : '';
$hideButtons = ($assisted_by_name === 'None') ? 'style="display: none;"' : '';
$disableToggle = ($assisted_by_name === 'None') ? 'disabled' : '';
$toggleLabelText = ($assisted_by_name === 'None') ? 'Disabled' : 'Enable Upload';
$disableCommentSection = ($assisted_by_name === 'None') ? 'disabled' : '';
$show_delete_button = ($assisted_by_name === $registrar_name) || ($assisted_by_name === 'None');

$sqlreqby2 = "SELECT request.id, requestor.req_name, relationship.relationship, requestor.req_contact_no, requestor.req_email, requestor.signature
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN relationship ON requestor.relationship_id = relationship.id 
JOIN `status` ON request.status_id = `status`.id
WHERE request.id = $id";
$resultreqby2 = $con->select_by_query($sqlreqby2);

if ($resultreqby2->num_rows > 0) {
    $row2 = $resultreqby2->fetch_assoc();
}

$sqlstud = "SELECT student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix
FROM request 
JOIN student ON request.student_id = student.id 
WHERE request.id = $id";
$resultstud = $con->select_by_query($sqlstud);

$sqlstud1 = "SELECT student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix
FROM request 
JOIN student ON request.student_id = student.id 
WHERE request.id = $id";
$resultstud1 = $con->select_by_query($sqlstud1);

$sqlstud2 = "SELECT student.id, student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix
FROM request 
JOIN student ON request.student_id = student.id 
WHERE request.id = $id";
$resultstud2 = $con->select_by_query($sqlstud2);

if ($resultstud2 && $row = $resultstud2->fetch_assoc()) {
    $_SESSION['student_id'] = $row['id'];
} else {
    echo "No student found for the given request ID.";
}

if ($resultstud1->num_rows > 0) {
    $student = $resultstud1->fetch_assoc();
    $studentName = trim(implode(', ', array_filter([
        $student['stud_lastname'],
        $student['stud_firstname']
    ])) . ' ' . implode(' ', array_filter([
        $student['stud_midname'],
        $student['stud_suffix']
    ])));
} else {
    $studentName = 'Unknown Student';
}


$sqlsent = "SELECT request.sent_email_at FROM request WHERE request.id = $id";
$resultsent = $con->select_by_query($sqlsent);

$sqlreleased = "SELECT request.date_released FROM request WHERE request.id = $id";
$resultreleased = $con->select_by_query($sqlreleased);

$sqlreleased1 = "SELECT request.date_released FROM request WHERE request.id = $id";
$resultreleased1 = $con->select_by_query($sqlreleased);
$dateReleased2 = $resultreleased1->fetch_assoc();
$dateReleased = $dateReleased2['date_released'];

$sqldocuStatuses = "SELECT id, status FROM `docu_status`";
$alldocuStatuses = $con->select_by_query($sqldocuStatuses);

$sqlreqdoc = "SELECT reqdocu.docus_id, docus.documents, docu_status.status AS current_status, reqdocu.document_remarks, reqdocu.notes, reqdocu.document_status_id
FROM reqdocu 
JOIN docus ON reqdocu.docus_id = docus.id
JOIN request ON reqdocu.request_id = request.id
JOIN docu_status ON reqdocu.document_status_id = docu_status.id
WHERE reqdocu.request_id = $id";
$resultreqdoc = $con->select_by_query($sqlreqdoc);

$sqlpurpose = "SELECT purpose.purposelist_id, purposelist.purposelists, purpose.purpose_remarks
FROM purpose 
JOIN purposelist ON purpose.purposelist_id = purposelist.id
JOIN request ON purpose.request_id = request.id
WHERE purpose.request_id = $id";
$resultpurpose = $con->select_by_query($sqlpurpose);

$sqlstudent = "SELECT student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix, student.grade, student.section, student.sylastattended, student.stud_contact_no, student.stud_email
FROM request 
JOIN student ON request.student_id = student.id
WHERE request.id = $id";
$resultstudent = $con->select_by_query($sqlstudent);

$sqldl = "SELECT id, request_id, `filename`, filesize, filetype, upload_date FROM uploaded_docus WHERE request_id = $id";
$resultdl = $con->query($sqldl);

if (isset($_SESSION["request_id"]) && isset($_SESSION["reference_no"]) && isset($_SESSION["registrar_id"])) {
    $id = $_SESSION["request_id"];
    $requestor_id = isset($_SESSION["requestor_id"]) ? $_SESSION["requestor_id"] : null; 
    $registrar_id = isset($_SESSION["registrar_id"]) ? $_SESSION["registrar_id"] : null;
    $registrar_name = isset($_SESSION["registrar_name"]) ? $_SESSION["registrar_name"] : 'Unknown'; 

    $con = new connec(); 

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
        if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {
            $comment = $_POST['comment_text'];
            $comment = preg_replace("/(\r\n|\n|\r)/", " ", $comment); 
            $comment = trim($comment);
            $comment = $con->real_escape_string($comment);

            $sql_insert_comment = "INSERT INTO comments (id, commentor, request_id, reference_no, registrar_id, requestor_id, comment_text, commented_at, user_type) 
                                   VALUES (0, ?, ?, ?, ?, ?, ?, NOW(), 0)";
            
            $stmt = $con->prepare($sql_insert_comment);
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);
            }
            
            $stmt->bind_param('sisiis', $registrar_name, $id, $reference_no, $registrar_id, $requestor_id, $comment);
            
            if ($stmt->execute() === TRUE) {
                header("Location: requesttrackerreg.php?id=" . urlencode($_SESSION['request_id']) . 
                       "&reference_no=" . urlencode($_SESSION['reference_no']) . 
                       "&requestor_id=" . urlencode($_SESSION['requestor_id']) . 
                       "&registrar_id=" . urlencode($_SESSION['registrar_id']) . 
                       "&registrar_name=" . urlencode($_SESSION['registrar_name']));
                exit(); 
            } else {
                echo "Error: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Comment cannot be empty.";
        }
    }

} else { 
    echo "Session variables are not set.<br>";
    echo "Request ID: " . (isset($_SESSION["request_id"]) ? $_SESSION["request_id"] : 'Not set') . "<br>";
    echo "Reference No: " . (isset($_SESSION["reference_no"]) ? $_SESSION["reference_no"] : 'Not set') . "<br>";
    echo "Registrar ID: " . (isset($_SESSION["registrar_id"]) ? $_SESSION["registrar_id"] : 'Not set') . "<br>";
    echo "Registrar Name: " . (isset($_SESSION["registrar_name"]) ? $_SESSION["registrar_name"] : 'Not set') . "<br>";
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($studentName); ?> (Request Details)</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../css/newrequest.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>

<div id="RequestTracker" class="requesttracker">
    <div class="container">  
        <div class="row justify-content-center mt-3 parasagilid">
            <h2 class="titlenewrequest">
                <img src="../images/file.png" alt="Magnify Icon" class="iconmagnify" />
                Request Details
            </h2>
            
            <hr class="hrrequest">  

            <ul id="progressbar" class="progressbarparasabtn">
                <li class="<?php echo $currentStatusId >= 1 ? 'active' : ''; ?>" id="step1">In queue</li>
                <li class="<?php echo $currentStatusId >= 2 ? 'active' : ''; ?>" id="step2">Processing</li>
                <li class="<?php echo $currentStatusId >= 3 ? 'active' : ''; ?>" id="step3">On Hold</li>
                <li class="<?php echo $currentStatusId >= 4 ? 'active' : ''; ?>" id="step4">Ready to claim</li>
                <li class="<?php echo $currentStatusId >= 5 ? 'active' : ''; ?>" id="step5">Released</li>
            </ul>

            <div class="containtitle">
                <div class="assistingby assistingby1">
                    <label for="reg_name" class="form-label assistingbylabel">
                        Assisting by: 
                        <?php echo htmlspecialchars($assisted_by_name); ?>
                        <?php if (isset($reg_image) && !empty($reg_image)): ?>
                            <img src="<?php echo htmlspecialchars($reg_image, ENT_QUOTES, 'UTF-8'); ?>" alt="Registrar Image">
                        <?php endif; ?>
                    </label>
                    <?php if ($show_assist_button): ?>
                        <form id="assist-form" style="display: inline-block;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($id); ?>">
                            <input type="hidden" name="reg_id" value="<?php echo htmlspecialchars($_SESSION['registrar_id']); ?>">
                            <input type="hidden" name="assist" value="true"> 
                            <button type="submit" id="assist-button" class="btn btn-primary assistregbtn">Assist</button>
                            <div class="spinner-border text-primary" id="spinner" role="status" style="display: none; margin-left: 10px;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </form>
                    <?php elseif ($show_unassist_button): ?>
                        <form id="unassist-form" style="display: inline-block;">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($id); ?>">
                            <input type="hidden" name="unassist" value="true"> 
                            <button type="submit" id="unassist-button" class="btn btn-secondary unassistregbtn">Unassist</button>
                            <div class="spinner-border text-primary" id="unassist-spinner" role="status" style="display: none; margin-left: 10px;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-listing2 afterassistingby"> 
                <div class="form-title-area">
                    <div class="title-button-container">
                        <h3 class="form-title2">Request Details</h3>
                        <?php if ($show_delete_button): ?>
                            <button id="deleteButton" class="btn btn-danger btn-delete">Delete Request</button>
                        <?php endif; ?>
                    </div>
                </div>
                <hr class="hr2">
                <table class="table table-bordered custom-table2">
                    <tr>
                        <td>Request Date and Time:</td>
                        <td class="referencetable">
                            <?php
                            if (!empty($rows)) {
                                foreach ($rows as $row) {
                                    $dateTime = new DateTime($row["request_date"]);
                                    $dateFormatted = $dateTime->format('F j, Y');
                                    $timeFormatted = $dateTime->format('h:i A');
                                    echo '<span style="color: black;">' . $dateFormatted . ' at ' . $timeFormatted . '</span>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Reference Number:</td>
                        <td class="pintable">
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
                        <td>Requested by:</td>
                        <td class="pintable">
                            <?php
                            if ($resultreqby->num_rows > 0) {
                                while ($row = $resultreqby->fetch_assoc()) {
                                    echo '<span style="color: black;">' . $row["req_name"] . '</span>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Student Name:</td>
                        <td class="pintable">
                            <?php
                            if ($resultstud->num_rows > 0) {
                                while ($row = $resultstud->fetch_assoc()) {
                                    echo '<span style="color: black;">' . $row["stud_lastname"] . ', ' . $row["stud_firstname"] . ' ' . $row["stud_midname"] . ' ' . $row["stud_suffix"] . '</span>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Request Status:</td>
                        <td class="pintable">
                            <div class="form-descrip-requestor">
                                <div style="margin-top:10px;" class="dropdown-listing1">
                                    <select id="statusDropdown" style="text-align:center;" class="form-select" required aria-label="Select Status" name="status_id" data-request-id="<?php echo htmlspecialchars($id); ?>" data-reference-no="<?php echo htmlspecialchars($reference_no); ?>" <?php echo $disableDropdown; ?>>
                                        <option value="">Select Status</option>
                                        <?php
                                        if ($allStatuses->num_rows > 0) {
                                            while ($statusRow = $allStatuses->fetch_assoc()) {
                                                $selected = ($statusRow['id'] == $currentStatusId) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($statusRow['id']) . '" ' . $selected . '>' . htmlspecialchars($statusRow['status']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback dropdown1">Please select a status.</div>
                                </div>
                                <div id="emailButtonContainer" style="margin-bottom:10px; display:none;">
                                    <div class="spinner-border text-primary" id="spinner" role="status" style="display: none; margin-left: 10px;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <button id="sendEmailButton" name="sendEmailButtonButton" class="btn btn-primary">Send Email</button>
                                </div>
                            </div>
                            <span id="statusMessage"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Email sent at:</td>
                        <td class="pintable">
                            <span id="emailSentAt">
                                <?php
                                if ($resultsent->num_rows > 0) {
                                    while ($row = $resultsent->fetch_assoc()) {
                                        if ($row["sent_email_at"] !== NULL && $row["sent_email_at"] !== '') {
                                            $sentDateTime = new DateTime($row["sent_email_at"]);
                                            $formattedDate = $sentDateTime->format('F j, Y');
                                            $formattedTime = $sentDateTime->format('g:i A');
                                            echo '<span style="color: black;">' . $formattedDate . ' at ' . $formattedTime . '</span>';
                                        } else {
                                            echo '<span style="color: black;">Email is not sent yet.</span>';
                                        }
                                    }
                                } else {
                                    echo '<span style="color: black;">No email record found.</span>';
                                }
                                ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Released at:</td>
                        <td class="pintable">
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <span id="emailSentAt">
                                    <?php
                                    $dateReleased = null; 
                                    $hasReleasedDate = false; 

                                    if ($resultreleased->num_rows > 0) {
                                        while ($row = $resultreleased->fetch_assoc()) {
                                            if ($row["date_released"] !== NULL && $row["date_released"] !== '') {
                                                $hasReleasedDate = true;
                                                $sentDateTime = new DateTime($row["date_released"]);
                                                $formattedDate = $sentDateTime->format('F j, Y');
                                                $formattedTime = $sentDateTime->format('g:i A');
                                                echo '<span style="color: black;">' . $formattedDate . ' at ' . $formattedTime . '</span>';
                                                $dateReleased = $row["date_released"]; 
                                            } else {
                                                if ($currentStatusId != 5) {
                                                    echo '<span style="color: black; margin-bottom: 10px;">Not yet released.</span>';
                                                }
                                            }
                                        }
                                    } else {
                                        echo '<span style="color: black;">No request record found.</span>';
                                    }
                                    ?>
                                </span>
                                <button id="trashButton" class="btn" <?php echo $hasReleasedDate ? '' : 'style="display:none;"'; ?> onclick="unrelease()">
                                    <i class="fas fa-trash-alt" style="color: red;"></i>
                                </button>
                            </div>
                            <div id="releaseButtonContainer" style="text-align: center;">
                                <button id="releaseButton" class="btn btn-primary" <?php echo ($dateReleased !== NULL && $dateReleased !== '') ? 'style="display:none;"' : ''; ?>>Log Date and Time</button>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <table class="table table-bordered custom-table3">
                <thead>
                    <tr>
                        <th scope="col" class="doculabel">Requested Document</th>
                        <th scope="col" class="doculabel">Status</th>
                        <th scope="col" class="doculabel">Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($resultreqdoc->num_rows > 0) {
                        while ($row = $resultreqdoc->fetch_assoc()) {
                            $documents = explode(',', $row["documents"]);
                            $docus_id = $row["docus_id"];
                            $currentStatusId = $row["document_status_id"];
                            $notes = $row["notes"]; 
                            
                            foreach ($documents as $document) {
                                echo '<tr>';
                                
                                if ($docus_id == 10) {
                                    echo '<td class="docucontent"><span style="color: black;">' . trim($document) . ': </span> <span style="color: black;">' . htmlspecialchars($row["document_remarks"]) . '</span></td>';
                                } else {
                                    echo '<td class="docucontent"><span style="color: black;">' . htmlspecialchars(trim($document)) . '</span></td>';
                                }
                                
                                echo '<td style="text-align:center;" class="docucontent reqdocu1">';
                                echo '<div style="padding-right:0px; padding-left:0px; margin-bottom:0px;" class="dropdown-listing1">';
                                echo '<select id="statusDropdown' . $docus_id . '" class="form-select" style="text-align:center;" name="status_id" data-doc-id="' . $docus_id . '" ' . $disableDropdown . '>';
                                echo '<option value="">Select Status</option>';
                                
                                $alldocuStatuses->data_seek(0); 
                                while ($statusRow = $alldocuStatuses->fetch_assoc()) {
                                    $selected = ($statusRow['id'] == $currentStatusId) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($statusRow['id']) . '" ' . $selected . '>' . htmlspecialchars($statusRow['status']) . '</option>';
                                }
                                
                                echo '</select>';
                                echo '<div class="invalid-feedback dropdown1">Please select a status.</div>';
                                echo '</div>';
                                echo '</td>';
                                
                                echo '<td class="docucontent">';
                                if ($notes) {
                                    echo '<span class="notedocu" id="noteText' . $docus_id . '">' . htmlspecialchars($notes) . '</span>';
                                    echo '<div style="gap: 5px;flex-flow: wrap;display: flex;justify-content: center;margin-top: 10px;">';
                                    echo '<button type="button" class="btn btn-primary view-button" data-doc-id="' . $docus_id . '" data-note-text="' . htmlspecialchars($notes) . '" data-document-name="' . htmlspecialchars(trim($document)) . '" onclick="viewNoteModal(this)">View</button>';
                                    echo '<button type="button" class="btn btn-primary" data-doc-id="' . $docus_id . '" data-note-text="' . htmlspecialchars($notes) . '" data-document-name="' . htmlspecialchars(trim($document)) . '" onclick="editNoteModal(this)" ' . $hideButtons . '>Edit</button>'; 
                                    echo '<button type="button" class="btn btn-danger" data-doc-id="' . $docus_id . '" onclick="deleteNote(this)" ' . $hideButtons . '>Delete</button>';
                                    echo '</div>';
                                } else {
                                    echo '<button type="button" class="btn btn-primary" data-doc-id="' . $docus_id . '" data-document-name="' . htmlspecialchars(trim($document)) . '" onclick="showAddNoteModal(this)" ' . $hideButtons . '>Add Note</button>';
                                }
                                echo '</td>';
                                
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>

            <div class="form-listing2">
                <div class="form-title-area">
                    <h3 class="form-title2">Requestor Details</h3>
                </div>
                <hr class="hr2">
                <table class="table table-bordered custom-table2">
                    <tr>
                        <td>Requestor Name:</td>
                        <td class="referencetable">
                            <?php
                            if (isset($row2)) {
                                echo '<span style="color: black;">' . htmlspecialchars($row2["req_name"]) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Relationship to the Student:</td>
                        <td class="pintable">
                            <?php
                            if (isset($row2)) {
                                echo '<span style="color: black;">' . htmlspecialchars($row2["relationship"]) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact Number:</td>
                        <td class="pintable">
                            <?php
                            if (isset($row2)) {
                                echo '<span style="color: black;">' . htmlspecialchars($row2["req_contact_no"]) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Email Address:</td>
                        <td class="pintable">
                            <?php
                            if (isset($row2)) {
                                echo '<span style="color: black;">' . htmlspecialchars($row2["req_email"]) . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Signature:</td>
                        <td class="pintable">
                            <?php
                            if (isset($row2)) {
                                $signaturePath = htmlspecialchars($row2["signature"]);
                                echo '<img src="../' . $signaturePath . '" alt="Signature" style="max-width: 300px; max-height: 100px;">';
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Purpose:</td>
                        <td class="pintable">
                            <?php
                            if ($resultpurpose->num_rows > 0) {
                                echo '<ul style="display: inline-block; list-style-type: none; padding-left: 0; margin: 0;">'; 
                                while ($row = $resultpurpose->fetch_assoc()) {
                                    $purposelists = explode(',', $row["purposelists"]);
                                    $purposelist_id = $row["purposelist_id"];
                                    
                                    foreach ($purposelists as $purposelist) {
                                        echo '<li style="margin-bottom: 10px;">'; 
                                        
                                        if (in_array($purposelist_id, [1, 2, 5])) {
                                            echo '<span style="color: black;">' . trim($purposelist) . ': </span> <span style="color: black;">' . $row["purpose_remarks"] . '</span>';
                                        } else {
                                            echo '<span style="color: black;">' . trim($purposelist) . '</span>';
                                        }
                                        
                                        echo '</li>';
                                    }
                                }
                                echo '</ul>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="form-listing2">
                <div class="form-title-area">
                    <h3 class="form-title2">Student Details</h3>
                </div>
                <hr class="hr2">
                <table class="table table-bordered custom-table2">
                    <?php
                    $studentName = '';
                    $grade = '';
                    $section = '';
                    $sylastattended = '';
                    $contactNo = '';
                    $email = '';

                    if ($resultstudent->num_rows > 0) {
                        while ($row = $resultstudent->fetch_assoc()) {
                            $studentName = $row["stud_lastname"] . ', ' . $row["stud_firstname"] . ' ' . $row["stud_midname"] . ' ' . $row["stud_suffix"];
                            $grade = $row["grade"];
                            $section = $row["section"];
                            $sylastattended = $row["sylastattended"];
                            $contactNo = $row["stud_contact_no"];
                            $email = $row["stud_email"];
                        }
                    }
                    ?>
                    <tr>
                        <td>Student Name:</td>
                        <td class="referencetable">
                            <span style="color: black;"><?php echo $studentName; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Grade:</td>
                        <td class="pintable">
                            <span style="color: black;"><?php echo $grade; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Section:</td>
                        <td class="pintable">
                            <span style="color: black;"><?php echo $section; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>School Year Last Attended:</td>
                        <td class="pintable">
                            <span style="color: black;"><?php echo $sylastattended; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>Contact Number:</td>
                        <td class="pintable">
                            <span style="color: black;">
                                <?php echo !empty($contactNo) ? $contactNo : 'None'; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Email Address:</td>
                        <td class="pintable">
                            <span style="color: black;">
                                <?php echo !empty($email) ? $email : 'None'; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="form-listing2">
                <div class="form-title-area uploadarea">
                    <h3 class="form-title2">Uploaded Requirements</h3>
                    <div class="form-check form-switch togglearea">
                        <label id="toggleLabel" class="form-check-label labelswitch" for="flexSwitchCheckDefault"><?php echo $toggleLabelText; ?></label>
                        <input class="form-check-input toggleupload" type="checkbox" role="switch" id="flexSwitchCheckDefault" <?php echo $disableToggle; ?>>
                    </div>
                </div>
                <hr class="hr2">
                <div class="uploadcontent">
                    <form id="uploadForm" action='upload.php' method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file" class="form-label">Select files</label>
                            <input type="file" class="form-control" name="files[]" id="files" multiple>
                            <input type="hidden" name="fileData" id="fileData"> 
                            <div id="fileError" class="invalid-feedback">Please select at least one file.</div>
                            <ul id="fileList" class="file-list"></ul>
                        </div>
                        <button type="submit" id="uploadfile" name="uploadfile" class="btn btn-primary uploadfilebtn">Upload files</button>
                    </form>
                    <p class="noteupload">Note: File upload is only for requestor/student. Enable this section if uploading of document or requirement is necessary.</p>
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th colspan='2' class="labelupload">File Name</th>
                        <th class="labelupload">File Type</th>
                        <th class="labelupload">File Size</th>
                        <th class="labelupload">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if ($resultdl->num_rows > 0) {
                            while ($file = $resultdl->fetch_assoc()) {
                                $file_id = $file['id'];
                                $file_name = $file['filename'];
                                $file_type = $file['filetype'];
                                $filesizeInBytes = $file['filesize'];
                                $converted_size = $filesizeInBytes / (1024 * 1024);
                                $file_path = '../uploads/' . $reference_no . '/' . $file_name;

                                $isImage = in_array($file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

                                echo "<tr>";
                                echo "<td colspan='2' style='text-align: center;'>{$file_name}</td>";
                                echo "<td style='text-align: center;'>{$file_type}</td>";
                                echo "<td style='text-align: center;'>" . number_format($converted_size, 2) . " MB</td>";
                                echo "<td style='text-align: center;'>
                                        <div class='d-flex flex-column flex-sm-row align-items-center justify-content-center'>
                                            <a href='{$file_path}' class='btn btn-primary' download>Download</a>";

                                if ($isImage) {
                                    echo "<button type='button' class='btn btn-secondary viewbuttonimage mt-2 mt-sm-0 ms-sm-2' onclick='viewImage(\"{$file_path}\", \"{$file_name}\")'>View</button>";
                                }

                                echo "</div></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center;'>No files uploaded yet.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>

            <div class="form-listing2">
                <div class="form-title-area">
                    <h3 class="form-title2">Comments</h3>
                </div>
                <hr class="hr2">

                <div class="comments-list mt-3">
                </div>
            </div>

            <form id="commentForm" action="requesttrackerreg.php?id=<?php echo ($_SESSION['request_id']); ?>&reference_no=<?php echo ($_SESSION['reference_no']); ?>&requestor_id=<?php echo ($_SESSION['requestor_id']); ?>&registrar_id=<?php echo ($_SESSION['registrar_id']); ?>&registrar_name=<?php echo ($_SESSION['registrar_name']); ?>" class="add-commentform" method="POST">
                <div class="mb-3">
                    <label for="comment_text" class="form-label comment-label">Add a Comment</label>
                    <textarea class="form-control forcomment" id="comment_text" name="comment_text" rows="3" <?php echo $disableCommentSection; ?>></textarea>
                    <div id="commentError" class="invalid-feedback">Please fill up the textarea</div>
                </div>
                <button type="submit" name="submit_comment" class="btn btn-primary commentbtn" <?php echo $disableCommentSection; ?>>Submit</button>
            </form>
            
            <div class="modal fade" id="addNoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNoteModalLabel">Add Note</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body addnotemodal">
                            <form id="addNoteForm">
                                <div class="mb-3">
                                    <label for="noteText" class="form-label" id="addNoteLabel">Note for:</label>
                                    <textarea class="form-control" name="noteText" id="noteText" rows="4" required></textarea>
                                </div>
                                <input type="hidden" id="docId" name="docus_id">
                                <input type="hidden" name="request_id" value="<?php echo $id; ?>">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveAddNoteButton">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="viewNoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalLabel">View Note</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body viewnotemodal">
                            <form id="viewNoteForm">
                                <div class="mb-3">
                                    <label for="viewNoteText" class="form-label" id="noteLabel">Note for:</label>
                                    <textarea class="form-control" name="viewNoteText" id="viewNoteText" rows="4" readonly></textarea>
                                </div>
                                <input type="hidden" id="viewDocId" name="docus_id">
                                <input type="hidden" name="request_id" value="<?php echo $id; ?>">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="viewImageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewImageModalLabel">View Image</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body bodyviewimage">
                            <div class="mb-3">
                                <label for="viewNoteText" class="form-label" id="imageLabel">Image for:</label>
                                <img src="" class="img-fluid" id="viewImage" alt="Selected Image">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editNoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Note</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body editnotemodal">
                            <form id="editNoteForm">
                                <div class="mb-3">
                                    <label for="editNoteText" class="form-label" id="editNoteLabel">Note for:</label>
                                    <textarea class="form-control" name="editNoteText" id="editNoteText" rows="4"></textarea>
                                </div>
                                <input type="hidden" id="editDocId" name="docus_id">
                                <input type="hidden" name="request_id" value="<?php echo $id; ?>">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="saveEditNoteButton">Save changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteNoteModal" tabindex="-1" aria-labelledby="deleteNoteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteNoteModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this note?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmDeleteNote">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-labelledby="deleteRequestModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteRequestModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this request?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmDeleteRequest">Confirm</button>
                            <div class="spinner-border text-primary" id="spinner" role="status" style="display: none;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="alertModalunrelease" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="alertModalunreleaseLabel">Alert</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="AlertmodalunreleaseBodyText">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileError = document.getElementById('fileError');

    function validateFileInput() {
        if (!fileInput.files.length) {
            fileInput.classList.remove('is-valid');
            fileInput.classList.add('is-invalid');
            fileError.style.display = 'block';
            return false;
        } else {
            fileInput.classList.remove('is-invalid');
            fileInput.classList.add('is-valid');
            fileError.style.display = 'none';
            return true;
        }
    }

    fileInput.addEventListener('change', validateFileInput);

    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        if (!validateFileInput()) {
            event.preventDefault(); 
        }
    });
});
</script>

<script>
document.getElementById('file').addEventListener('change', function(event) {
    const fileList = document.getElementById('fileList');
    const fileInput = document.getElementById('file');
    const fileError = document.getElementById('fileError');
    const fileData = document.getElementById('fileData'); 

    fileList.innerHTML = ''; 
    const files = event.target.files;
    const fileCount = files.length;

    fileData.value = ''; 

    if (fileCount === 0) {
        fileError.style.display = 'block';
        fileInput.classList.add('is-invalid'); 
        fileInput.classList.remove('is-valid'); 
        fileInput.value = ''; 
    } else {
        fileError.style.display = 'none';
        fileInput.classList.remove('is-invalid'); 
        fileInput.classList.add('is-valid'); 

        let fileNames = [];
        for (let i = 0; i < fileCount; i++) {
            const listItem = document.createElement('li');
            listItem.classList.add('file-item');
            
            const fileName = document.createElement('span');
            fileName.classList.add('file-name');
            fileName.textContent = files[i].name;
            
            listItem.appendChild(fileName);
            fileList.appendChild(listItem);
            
            fileNames.push(files[i].name); 
        }
        fileData.value = fileNames.join(',');
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('comment_text');
    var errorDiv = document.getElementById('commentError');

    function validateTextarea() {
        if (textarea.value.trim() === '') {
            textarea.classList.remove('is-valid');
            textarea.classList.add('is-invalid');
            errorDiv.style.display = 'block';
        } else {
            textarea.classList.remove('is-invalid');
            textarea.classList.add('is-valid');
            errorDiv.style.display = 'none';
        }
    }

    textarea.addEventListener('input', validateTextarea);

    function saveScrollPosition() {
        sessionStorage.setItem('scrollPosition', window.scrollY);
    }

    function restoreScrollPosition() {
        var scrollPosition = sessionStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, parseInt(scrollPosition, 10));
            sessionStorage.removeItem('scrollPosition');
        }
    }

    document.getElementById('commentForm').addEventListener('submit', function(event) {
        if (textarea.value.trim() === '') {
            event.preventDefault();
            textarea.classList.add('is-invalid');
            errorDiv.style.display = 'block';
        } else {
            saveScrollPosition();
            window.location.reload();
        }
    });

    restoreScrollPosition();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const statusDropdown = document.getElementById('statusDropdown');
    const statusMessage = document.getElementById('statusMessage');
    const emailButtonContainer = document.getElementById('emailButtonContainer');
    const progressbarItems = document.querySelectorAll('#progressbar li');

    function updateStatus(requestId, statusId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (xhr.status === 200) {
                updateProgressBar(statusId);
                updateButtonVisibility();
            } else {
                statusMessage.textContent = 'Error updating status.';
                statusMessage.style.color = 'red';
            }
        };

        xhr.send('request_id=' + encodeURIComponent(requestId) + '&status_id=' + encodeURIComponent(statusId));
    }

    function updateProgressBar(statusId) {
        progressbarItems.forEach((item, index) => {
            if (index + 1 <= statusId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    function updateButtonVisibility() {
        const selectedValue = statusDropdown.value;
        if (selectedValue === '4') {
            emailButtonContainer.style.display = 'block';
        } else {
            emailButtonContainer.style.display = 'none';
        }
    }

    statusDropdown.addEventListener('change', function () {
        const statusId = this.value;
        const requestId = this.getAttribute('data-request-id');
        updateStatus(requestId, statusId);
    });

    updateButtonVisibility();
    updateProgressBar(statusDropdown.value || '<?php echo $currentStatusId; ?>');
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    function updateStatus(docId, statusId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_doc_status.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        xhr.onload = function () {
            if (xhr.status === 200) {
                console.log('Status updated successfully!');
            } else {
                console.error('Error updating status.');
            }
        };

        xhr.send('docus_id=' + encodeURIComponent(docId) + '&status_id=' + encodeURIComponent(statusId));
    }

    document.querySelectorAll('select[name="status_id"]').forEach(dropdown => {
        dropdown.addEventListener('change', function () {
            const statusId = this.value;
            const docId = this.getAttribute('data-doc-id');
            if (statusId && docId) { 
                updateStatus(docId, statusId);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var statusDropdown = document.getElementById('statusDropdown');
    var statusId = statusDropdown.value;

    function updateReleaseButtonVisibility() {
        if (statusId == '5') {
            document.getElementById('releaseButtonContainer').style.display = 'block';
        } else {
            document.getElementById('releaseButtonContainer').style.display = 'none';
        }
    }

    updateReleaseButtonVisibility();

    statusDropdown.addEventListener('change', function () {
        statusId = this.value;
        updateReleaseButtonVisibility();
    });

    document.getElementById('sendEmailButton').addEventListener('click', function (e) {
        e.preventDefault();

        var requestId = statusDropdown.getAttribute('data-request-id');
        var referenceNo = statusDropdown.getAttribute('data-reference-no');

        document.getElementById('sendEmailButton').style.display = 'none';
        document.getElementById('spinner').style.display = 'inline-block';

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "send_email.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            document.getElementById('spinner').style.display = 'none'; 

            if (xhr.status === 200 && xhr.responseText.includes('Email sent successfully!')) {
                var timestampXhr = new XMLHttpRequest();
                timestampXhr.open("POST", "fetch_timestamp.php", true);
                timestampXhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                timestampXhr.onload = function() {
                    if (timestampXhr.status === 200) {
                        document.getElementById('emailSentAt').innerHTML = timestampXhr.responseText;
                    }
                };
                timestampXhr.send("request_id=" + requestId);
            } else {
                document.getElementById('sendEmailButton').style.display = 'inline-block';
            }
        };

        xhr.send("request_id=" + requestId + "&reference_no=" + referenceNo);
    });

    document.getElementById('releaseButton').addEventListener('click', function (e) {
        e.preventDefault();

        var requestId = statusDropdown.getAttribute('data-request-id');

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "release_request.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200 && xhr.responseText.includes('Request released successfully!')) {

                window.location.reload();
            }
        };

        xhr.send("request_id=" + requestId);
    });
});
</script>

<script>
let deleteDocId = null;

function showAddNoteModal(button) {
    var docId = button.getAttribute('data-doc-id');
    var documentName = button.getAttribute('data-document-name');
    
    document.getElementById('noteText').value = ''; 
    document.getElementById('docId').value = docId;
    document.getElementById('addNoteLabel').textContent = 'Note for ' + documentName;
    
    var myModal = new bootstrap.Modal(document.getElementById('addNoteModal'));
    myModal.show();
}

function editNoteModal(button) {
    var docId = button.getAttribute('data-doc-id');
    var noteText = decodeURIComponent(button.getAttribute('data-note-text'));
    var documentName = button.getAttribute('data-document-name');
    
    document.getElementById('editNoteText').value = noteText;
    document.getElementById('editNoteLabel').textContent = 'Note for ' + documentName;
    document.getElementById('editDocId').value = docId;
    
    var myModal = new bootstrap.Modal(document.getElementById('editNoteModal'));
    myModal.show();
}

document.getElementById('saveAddNoteButton').addEventListener('click', function() {
    var noteText = document.getElementById('noteText').value;
    var docId = document.getElementById('docId').value;
    var requestId = document.querySelector('input[name="request_id"]').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_note.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            window.location.reload(); 
        } else {
            console.error('Failed to save note.');
        }
    };
    xhr.send('saveNoteText=1&noteText=' + encodeURIComponent(noteText) + '&request_id=' + requestId + '&docus_id=' + docId);
});

document.getElementById('saveEditNoteButton').addEventListener('click', function() {
    var noteText = document.getElementById('editNoteText').value;
    var docId = document.getElementById('editDocId').value;
    var requestId = document.querySelector('input[name="request_id"]').value;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_note.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status == 200) {
            window.location.reload();
        } else {
            console.error('Failed to save note.');
        }
    };
    xhr.send('saveNoteText=1&noteText=' + encodeURIComponent(noteText) + '&request_id=' + requestId + '&docus_id=' + docId);
});

function viewNoteModal(button) {
    var docId = button.getAttribute('data-doc-id');
    var noteText = decodeURIComponent(button.getAttribute('data-note-text'));
    var documentName = button.getAttribute('data-document-name');
    
    document.getElementById('viewNoteText').textContent = noteText;
    document.getElementById('noteLabel').textContent = 'Note for ' + documentName;
    document.getElementById('viewDocId').value = docId;
    
    var myModal = new bootstrap.Modal(document.getElementById('viewNoteModal'));
    myModal.show();
}

function deleteNote(button) {
    deleteDocId = button.getAttribute('data-doc-id');
    
    var myModal = new bootstrap.Modal(document.getElementById('deleteNoteModal'));
    myModal.show();
}

document.getElementById('confirmDeleteNote').addEventListener('click', function() {
    if (deleteDocId) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_note.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (this.status == 200) {
                window.location.reload(); 
            } else {
                console.error('Failed to delete note.');
            }
        };
        xhr.send('deleteNote=1&docus_id=' + encodeURIComponent(deleteDocId) + '&request_id=' + encodeURIComponent(document.querySelector('input[name="request_id"]').value));
        
        var myModal = bootstrap.Modal.getInstance(document.getElementById('deleteNoteModal'));
        myModal.hide();
        
        deleteDocId = null;
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleSwitch = document.getElementById('flexSwitchCheckDefault');
    const uploadForm = document.getElementById('uploadForm');
    const uploadButton = document.getElementById('uploadfile');
    const label = document.getElementById('toggleLabel');

    function setFormDisabled(disabled) {
        const formControls = uploadForm.querySelectorAll('input, select, textarea, button');
        formControls.forEach(control => control.disabled = disabled);
    }

    let isEnabled = localStorage.getItem('toggleState') === 'enabled';

    if (isEnabled) {
        toggleSwitch.checked = true;
        label.textContent = 'Enabled';
        label.style.backgroundColor = 'green';
        label.style.color = 'white';
        setFormDisabled(false);
        uploadButton.disabled = false;
    } else {
        toggleSwitch.checked = false;
        label.textContent = 'Disabled';
        label.style.backgroundColor = '#dc3545'; 
        label.style.color = 'white';
        setFormDisabled(true);
        uploadButton.disabled = true;
    }

    fetch('get_toggle_state.php?id=<?php echo $id; ?>')
        .then(response => response.json())
        .then(data => {
            if (data.upload_requirements === 1) {
                toggleSwitch.checked = true;
                label.textContent = 'Enabled';
                label.style.backgroundColor = 'green';
                label.style.color = 'white';
                setFormDisabled(false);
                uploadButton.disabled = false;
                localStorage.setItem('toggleState', 'enabled');
            } else {
                toggleSwitch.checked = false;
                label.textContent = 'Disabled';
                label.style.backgroundColor = '#dc3545';
                label.style.color = 'white';
                setFormDisabled(true);
                uploadButton.disabled = true;
                localStorage.setItem('toggleState', 'disabled');
            }
        })
        .catch(error => console.error('Error fetching toggle state:', error));

    function updateUploadRequirements(isChecked) {
        fetch('update_toggle.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=<?php echo $id; ?>&upload_requirements=' + (isChecked ? '1' : '0')
        })
        .then(response => response.text())
        .then(text => console.log('Update response:', text))
        .catch(error => console.error('Error updating toggle state:', error));
    }

    toggleSwitch.addEventListener('change', function() {
        const isChecked = this.checked;
        setFormDisabled(!isChecked);
        uploadButton.disabled = !isChecked;
        updateUploadRequirements(isChecked);

        if (isChecked) {
            label.textContent = 'Enabled';
            label.style.backgroundColor = 'green';
            label.style.color = 'white';
            localStorage.setItem('toggleState', 'enabled');
        } else {
            label.textContent = 'Disabled';
            label.style.backgroundColor = '#dc3545';
            label.style.color = 'white';
            localStorage.setItem('toggleState', 'disabled');
        }
    });
});
</script>

<script>
document.getElementById('assist-form')?.addEventListener('submit', function(event) {
    event.preventDefault();

    document.getElementById('assist-button').style.display = 'none';
    document.getElementById('spinner').style.display = 'inline-block';

    const formData = new FormData(this);

    fetch('update_assist.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            window.location.reload();
        } else {
            console.log('Update failed:', data);
            document.getElementById('assist-button').style.display = 'inline-block';
            document.getElementById('spinner').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('assist-button').style.display = 'inline-block';
        document.getElementById('spinner').style.display = 'none';
    });
});

document.getElementById('unassist-form')?.addEventListener('submit', function(event) {
    event.preventDefault();

    document.getElementById('unassist-button').style.display = 'none';
    document.getElementById('unassist-spinner').style.display = 'inline-block';

    const formData = new FormData(this);

    fetch('unassist.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data.includes('success')) {
            window.location.reload();
        } else {
            console.log('Update failed:', data);
            document.getElementById('unassist-button').style.display = 'inline-block';
            document.getElementById('unassist-spinner').style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('unassist-button').style.display = 'inline-block';
        document.getElementById('unassist-spinner').style.display = 'none';
    });
});
</script>

<script>
    function viewImage(filePath, fileName) {
    document.getElementById('viewImage').src = filePath;
    document.getElementById('imageLabel').textContent = "Image for: " + fileName;
    var viewImageModal = new bootstrap.Modal(document.getElementById('viewImageModal'));
    viewImageModal.show();
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const requestId = <?php echo json_encode($_SESSION["request_id"]); ?>;
    const requestorId = <?php echo json_encode($_SESSION["requestor_id"]); ?>;
    const studentId = <?php echo json_encode(isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null); ?>;
    const registrarName = <?php echo json_encode(isset($_SESSION["registrar_name"]) ? $_SESSION["registrar_name"] : 'Unknown'); ?>;
    const registrarId = <?php echo json_encode(isset($_SESSION["registrar_id"]) ? $_SESSION["registrar_id"] : null); ?>;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteRequestModal'));

    document.getElementById('deleteButton').addEventListener('click', function() {
        deleteModal.show();
    });

    document.getElementById('confirmDeleteRequest').addEventListener('click', function() {
        const confirmButton = document.getElementById('confirmDeleteRequest');
        const spinner = document.getElementById('spinner');
        
        confirmButton.style.display = 'none';
        spinner.style.display = 'inline-block'; 

        const requestData = {
            request_id: requestId,
            requestor_id: requestorId,
            student_id: studentId,
            registrar_name: registrarName,
            registrar_id: registrarId,
            delete: true
        };

        fetch('delete_request1.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(requestData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.status === 'success') {
                window.location.href = result.redirect;
            } else {
                alert('An error occurred: ' + result.message);
            }
            deleteModal.hide();
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            spinner.style.display = 'none';
            confirmButton.style.display = 'inline-block';
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const requestId = "<?php echo $id; ?>";
    const commentsList = document.querySelector('.comments-list');

    function fetchComments() {
        fetch('fetchComments.php?request_id=' + requestId)
            .then(response => response.json())
            .then(data => {
                commentsList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(comment => {
                        const commentItem = document.createElement('div');
                        commentItem.className = 'comment-item';

                        const commentor = document.createElement('p');
                        commentor.className = 'comment-submitted';
                        commentor.innerHTML = `<strong style="color: ${comment.color};">${comment.commentor}:</strong> ${comment.comment_text}`;
                        
                        const commentedAt = document.createElement('p');
                        commentedAt.innerHTML = `<small>Posted on: ${comment.commented_at}</small>`;

                        commentItem.appendChild(commentor);
                        commentItem.appendChild(commentedAt);

                        commentsList.appendChild(commentItem);
                    });

                } else {
                    commentsList.innerHTML = '<p class="no-comment">No comments yet.</p>';
                }
            })
            .catch(error => console.error('Error fetching comments:', error));
    }

    fetchComments();
    setInterval(fetchComments, 1000); 
});
</script>

<script>
function unrelease() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "unrelease.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    var params = "assist=1";

    xhr.onload = function() {
        var alertModal = new bootstrap.Modal(document.getElementById('alertModalunrelease'));
        var alertBody = document.getElementById('AlertmodalunreleaseBodyText');

        if (xhr.status >= 200 && xhr.status < 300) {
            if (xhr.responseText === 'success') {
                alertBody.innerHTML = 'Released date has been cleared.';
                alertModal.show();
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                alertBody.innerHTML = 'An error occurred while clearing the released date.';
                alertModal.show();
            }
        } else {
            alertBody.innerHTML = 'Request failed: ' + xhr.statusText;
            alertModal.show();
        }
    };

    xhr.onerror = function() {
        var alertModal = new bootstrap.Modal(document.getElementById('alertModalunrelease'));
        var alertBody = document.getElementById('AlertmodalunreleaseBodyText');
        
        alertBody.innerHTML = 'Request failed.';
        alertModal.show();
    };

    xhr.send(params);
}
</script>

</body>
</html>