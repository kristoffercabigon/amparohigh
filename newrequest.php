<?php
ob_start();
include("header.php");
include("conn.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["btn_submit"])) {
    $con = new connec();

    function generateShortUniqueId($length = 5)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $uniqueId = '';
        for ($i = 0; $i < $length; $i++) {
            $uniqueId .= $characters[rand(0, $charactersLength - 1)];
        }
        return $uniqueId;
    }

    $level_id = $_POST["level_id"];
    $req_name = $_POST["req_name"];
    $relationship_id = $_POST["relationship_id"];
    $req_contact_no = $_POST["req_contact_no"];
    $req_email = $_POST["req_email"];
    $stud_lastname = $_POST["stud_lastname"];
    $stud_firstname = $_POST["stud_firstname"];
    $stud_midname = $_POST["stud_midname"];
    $stud_suffix = $_POST["stud_suffix"];
    $grade = $_POST["grade"];
    $section = $_POST["section"];
    $sylastattended = $_POST["sylastattended"];
    $stud_contact_no = $_POST["stud_contact_no"];
    $stud_email = $_POST["stud_email"];
    $reference_no = date('Ymd') . generateShortUniqueId();
    $pin = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    $status_id = isset($_POST['status_id']) ? $_POST['status_id'] : 1;
    $date_released = isset($_POST['date_released']) ? $_POST['date_released'] : NULL;
    $assisted_by_id = isset($_POST['assisted_by_id']) ? $_POST['assisted_by_id'] : NULL;
    $sent_email_at = isset($_POST['sent_email_at']) ? $_POST['sent_email_at'] : NULL;
    $upload_requirements = isset($_POST['upload_requirements']) ? $_POST['upload_requirements'] : 0;

    $sql = "INSERT INTO student (id, stud_lastname, stud_firstname, stud_midname, stud_suffix, grade, section, sylastattended, stud_contact_no, stud_email) 
            VALUES (0, '$stud_lastname', '$stud_firstname', " . ($stud_midname ? "'$stud_midname'" : "NULL") . ", " . ($stud_suffix ? "'$stud_suffix'" : "NULL") . ", '$grade', '$section', '$sylastattended', " . ($stud_contact_no ? "'$stud_contact_no'" : "NULL") . ", " . ($stud_email ? "'$stud_email'" : "NULL") . ")";
    $student_id = $con->insert_lastid($sql);

    $signatureDataUrl = isset($_POST['signature']) ? $_POST['signature'] : '';
    $signatureFilePath = '';

    if (!empty($signatureDataUrl)) {
        $encodedImg = explode(',', $signatureDataUrl);
        $decodedImg = base64_decode($encodedImg[1]);

        $signatureFileName = $reference_no . ".jpeg";
        $signatureFilePath = "images/signatures/" . $signatureFileName;

        if (!file_put_contents($signatureFilePath, $decodedImg)) {
            exit;
        }
    } else {
        echo "No signature data received.";
        exit;
    }

    $sql = "INSERT INTO requestor (id, req_name, relationship_id, req_contact_no, req_email,`signature`) VALUES (0, '$req_name', $relationship_id, '$req_contact_no', '$req_email', '$signatureFilePath')";
    $requestor_id = $con->insert_lastid($sql);

    $sql = "INSERT INTO request (
        id, status_id, level_id, requestor_id, student_id, assisted_by_id, reference_no, pin, request_date, sent_email_at, date_released, upload_requirements
    ) VALUES (
        0, 1, $level_id, $requestor_id, $student_id, " .
        ($assisted_by_id !== NULL ? "'$assisted_by_id'" : "NULL") . ", '$reference_no', '$pin', NOW(), " .
        ($sent_email_at !== NULL ? "'$sent_email_at'" : "NULL") . ", " .
        ($date_released !== NULL ? "'$date_released'" : "NULL") . ", " .
        ($upload_requirements !== NULL ? "'$upload_requirements'" : 0) . "
    )";

    $request_id = $con->insert_lastid($sql);

    if (isset($_POST["docus"]) && is_array($_POST["docus"])) {
        foreach ($_POST["docus"] as $item) {
            $document_remarks = isset($_POST['document_remarks']) ? $_POST['document_remarks'] : NULL;
            $notes = isset($_POST['notes']) ? $_POST['notes'] : NULL;

            if ($item == 10) {
                $sql = "INSERT INTO reqdocu (id, request_id, docus_id, document_remarks, notes, document_status_id, note_notif) VALUES (0, $request_id, $item, '$document_remarks', '$notes', 1, 0)";
            } else {
                $sql = "INSERT INTO reqdocu (id, request_id, docus_id, notes, document_status_id, note_notif) VALUES (0, $request_id, $item, '$notes', 1, 0)";
            }

            $con->insert_lastid($sql);
        }
    } else {
        echo "No documents selected or docus is not set.";
        exit;
    }

    if (isset($_POST["purposes"]) && is_array($_POST["purposes"])) {
        foreach ($_POST["purposes"] as $item) {
            $purpose_remarks = NULL;

            if ($item == 1) {
                $purpose_remarks = isset($_POST['schoolPurpose']) ? $_POST['schoolPurpose'] : NULL;
            } elseif ($item == 5) {
                $purpose_remarks = isset($_POST['othersPurpose']) ? $_POST['othersPurpose'] : NULL;
            } elseif ($item == 2) {
                $purpose_remarks = isset($_POST['employmentType']) ? $_POST['employmentType'] : NULL;
            }

            $sql = "INSERT INTO purpose (id, request_id, purposelist_id, purpose_remarks) VALUES (0, $request_id, $item, " . ($purpose_remarks !== NULL ? "'$purpose_remarks'" : "NULL") . ")";
            $con->insert_lastid($sql);
        }
    } else {
        echo "No purposes selected or purposes is not set.";
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amparohighschool24@gmail.com';
        $mail->Password = 'xtqc shbn gsoa zyqu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('amparohighschool24@gmail.com', 'Amparo High School');
        $mail->addAddress($req_email, $req_name);
        $mail->addEmbeddedImage('images/emaillogo.png', 'logo');

        $mail->isHTML(true);
        $mail->Subject = 'Request Submitted';
        $mail->Body = "<div style='background-color: #f2f2f2; padding: 20px;'>
                        <div style='background-color: #007BFF; padding: 20px;'>
                            <img src='cid:logo' alt='Amparo High Logo' style='display: block; margin: 0 auto; max-width: 400px;' />
                        </div>
                        <div style='background-color: #fff; padding: 20px;'>
                            <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Dear $req_name, your request has been submitted successfully. This is your unique reference no:</p>
                            <div style='text-align: center;'>
                                <span style='display: inline-block; background-color: #007BFF; color: #fff; padding: 10px 20px; font-size: 30px; border-radius: 5px;'>$reference_no</span>
                            </div>
                            <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>While below is your pin, use this to keep track your requested documents.</p>
                            <div style='text-align: center;'>
                                <span style='display: inline-block; background-color: #007BFF; color: #fff; padding: 10px 20px; font-size: 30px; border-radius: 5px;'>$pin</span>
                            </div>
                            <div style='background-color: #fff; padding: 20px; border-radius: 10px;'>
                                <p style='font-size: 18px; color: #333; font-family: Arial, sans-serif; text-align: center;'>
                                    <a href='https://www.facebook.com/' style='color: #007BFF; text-decoration: none;'>Click here to visit record tracker on our website</a>
                                </p>
                            </div>
                        </div>
                        <div style='background-color: #007BFF; padding: 30px;'></div>
                    </div>";
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header("Location: success.php?id=$request_id");
    exit();
}

$con = new connec();

$sqllevel = "SELECT * FROM level";
$resultlevel = $con->select_by_query($sqllevel);

$sqlrel = "SELECT * FROM relationship";
$resultrel = $con->select_by_query($sqlrel);

$documents = [];
$ids = [];
$sqldocus = "SELECT id, documents FROM docus";
$resultdocus = $con->select_by_query($sqldocus);

if ($resultdocus->num_rows > 0) {
    while ($row = $resultdocus->fetch_assoc()) {
        $documents[] = $row['documents'];
        $ids[] = $row['id'];
    }
}

$sqlreqdocuCount = "SELECT docus_id, COUNT(*) as request_count 
                    FROM reqdocu 
                    JOIN request ON reqdocu.request_id = request.id 
                    WHERE DATE(request.request_date) = CURDATE() 
                    GROUP BY docus_id";
$resultreqdocuCount = $con->select_by_query($sqlreqdocuCount);

$requestCounts = [];
if ($resultreqdocuCount->num_rows > 0) {
    while ($row = $resultreqdocuCount->fetch_assoc()) {
        $requestCounts[$row['docus_id']] = $row['request_count'];
    }
}

$purposes = [];
$purpose_ids = [];
$sqlpurposes = "SELECT id, purposelists FROM purposelist";
$resultpurposes = $con->select_by_query($sqlpurposes);

if ($resultpurposes->num_rows > 0) {
    while ($row = $resultpurposes->fetch_assoc()) {
        $purposes[] = $row['purposelists'];
        $purpose_ids[] = $row['id'];
    }
}

date_default_timezone_set('Asia/Manila');
$currentHour = date('G');

$officeStartHour = 8;
$officeEndHour = 17;

$isOpen = ($currentHour >= $officeStartHour && $currentHour < $officeEndHour);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/newrequest.css" rel="stylesheet">
    <link rel="icon" href="images/tablogo.png">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="js/newrequest.js"></script>
</head>

<body>
    <div id="ClosedRequest" style="display: <?= $isOpen ? 'none' : 'block'; ?>">
        <div class="container">
            <div class="row justify-content-center mt-3 tanggalin">
                <div class="parasaclosehrs">
                    <h2 class="titlenewrequest">
                        <img src="images/document.png" alt="Document Icon" class="icondocu" />
                        New Request
                    </h2>
                    <hr class="hrrequest">
                    <div class="form-listing3">
                        <div class="form-title-area">
                            <h3 class="form-title2"><img src="images/clock.png" id="clock">Office Hours</h3>
                        </div>
                        <hr class="hr2">
                        <div class="form-descrip areangclosedba">
                            <div class="form-details closedba">
                                <p class="textclosed">CLOSED</p>
                            </div>
                            <div class="form-details" style="margin-top: 5px; margin-bottom: 15px;">Document request is currently closed. Please try again tomorrow or on another day.</div>
                            <div class="schedule-details" style="justify-content: center;align-items: center;">Monday to Friday
                                <br>
                                8:00 AM to 5:00 PM
                            </div>
                            <p class="form-description" style="margin-top: 15px; font-size: 15px; margin-bottom: 5px;">* Note: Document request is open only during office hours</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="NewRequest" style="display: <?= $isOpen ? 'block' : 'none'; ?>">
        <div class="container">
            <div class="row justify-content-center mt-3 tanggalin">
                <form method="POST" id="form" class="tanggalin">
                    <h2 class="titlenewrequest">
                        <img src="images/document.png" alt="Document Icon" class="icondocu" />
                        New Request
                    </h2>
                    <hr class="hrrequest">
                    <ul id="progressbar">
                        <li class="active" id="step6">Welcome</li>
                        <li id="step7">Request Details</li>
                    </ul>
                    <fieldset class="step active">
                        <?php
                        include("welcome.php");
                        ?>
                        <input type="button" name="next-step" class="btn btn-primary nextwelcome" value="Next" onclick="nextStep();" />
                    </fieldset>

                    <fieldset class="step" id="step1">

                        <div class="dropdown-listing mb-3">
                            <label for="level" class="form-label">Select Level</label>
                            <select id="level" class="form-select" required aria-label="Select Level" name="level_id" onchange="handleLevelChange(this.value)">
                                <option value="">Select Level</option>
                                <?php
                                if ($resultlevel->num_rows > 0) {
                                    while ($row = $resultlevel->fetch_assoc()) {
                                        echo '<option value="' . $row["id"] . '">' . $row["level"] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback dropdown1">Please select a level.</div>
                        </div>

                        <p class="p1">Choose the documents to be requested and enter the number of copies you intend to have.</p>
                        <table class="table table-bordered custom-table">
                            <thead>
                                <tr>
                                    <th scope="col" class="checkbox1"></th>
                                    <th scope="col" class="requestdocu">Requested Documents</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $index => $document) : ?>
                                    <?php
                                    $docId = $ids[$index];
                                    $requestCountToday = isset($requestCounts[$docId]) ? $requestCounts[$docId] : 0;
                                    $isDisabled = $requestCountToday >= 100;
                                    $limitReachedMessage = $isDisabled ? '<p class="text-danger">The limit of 100 request per document for this day has been reached. Try requesting another day.</p>' : '';
                                    ?>
                                    <tr>
                                        <td style="text-align:center;">
                                            <div class="form-check">
                                                <input class="form-check-input checkdrop" type="checkbox"
                                                    value="<?php echo htmlspecialchars($docId); ?>"
                                                    name="docus[]"
                                                    id="flexCheckDefault<?php echo $index + 1; ?>"
                                                    onchange="toggleCopies(<?php echo $index + 1; ?>); toggleOtherDocumentInput()"
                                                    <?php if ($isDisabled) echo 'disabled'; ?>>
                                                <label class="form-check-label" for="flexCheckDefault<?php echo $index + 1; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="form-documents">
                                            <?php echo htmlspecialchars($document); ?>
                                            <?php echo $limitReachedMessage; ?>
                                            <?php if ($index + 1 == 10) : ?>
                                                <div id="otherDocumentInput" class="form-descrip-requestor4" style="display: none;">
                                                    <div class="form-details4">
                                                        <input type="text" class="form-control" id="otherDocument" name="document_remarks" oninput="saveInputValue('otherDocument', this.value)" required disabled>
                                                        <div class="invalid-feedback">Please specify the document.</div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        <div class="note" id="checkboxNote">
                            Please specify requested document(s).
                        </div>

                        <div class="form-listing3">
                            <div class="form-title-area">
                                <h3 class="form-title2">Requesting Individual's Information</h3>
                            </div>
                            <hr class="hr2">

                            <div class="form-descrip-requestor">
                                <label for="req_name" class="form-label name-label">Name</label>
                                <div class="form-details1">
                                    <input type="text" class="form-control" name="req_name" id="req_name" oninput="saveInputValue('req_name', this.value)" required>
                                    <div class="invalid-feedback">Please indicate your name.</div>
                                </div>
                            </div>

                            <div class="form-descrip-requestor">
                                <label for="relationship" class="form-label name-label reldrop">Relationship to the Student</label>
                                <div class="dropdown-listing1">
                                    <select id="relationship" class="form-select" required aria-label="Select Relationship" name="relationship_id" value="<?php echo $_SESSION["relationship"]; ?>" onchange="saveInputValue('relationship', this.value)">
                                        <option value="">Select Relationship</option>
                                        <?php
                                        if ($resultrel->num_rows > 0) {
                                            while ($row = $resultrel->fetch_assoc()) {
                                                echo '<option value="' . $row["id"] . '" style="color: black;">' . $row["relationship"] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback dropdown1">Please select a relationship.</div>
                                </div>
                            </div>

                            <div class="form-row3">
                                <div class="form-descrip-requestor">
                                    <label for="req_contact_no" class="form-label name-label">Contact Number</label>
                                    <div class="form-details3">
                                        <input type="text" class="form-control" name="req_contact_no" id="req_contact_no" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, ''); saveInputValue('req_contact_no', this.value)" required>
                                        <div class="invalid-feedback">Please enter a valid contact number.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="req_email" class="form-label name-label">Email Address</label>
                                    <div class="form-details3">
                                        <input type="email" class="form-control" name="req_email" id="req_email" oninput="saveInputValue('req_email', this.value)" required>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                    <p class="form-description1">* Valid and active email is required. The Reference No. for request tracking will be sent to this email address.</p>
                                </div>
                            </div>

                            <div class="form-descrip-requestor1">
                                <label for="purposes" class="form-label name-label3">Purpose(s) of request</label>
                                <div id="purposeCheckboxNote" class="purpose-label">Please specify the purpose(s)</div>
                                <?php foreach ($purposes as $index => $purpose) : ?>
                                    <div class="form-details2">
                                        <input class="form-check-input checkdrop" type="checkbox" name="purposes[]" value="<?php echo htmlspecialchars($purpose_ids[$index]); ?>" id="flexCheckPurpose<?php echo $index; ?>" onchange="togglePurposeInput()">
                                        <label class="purpose-check-label" for="flexCheckPurpose<?php echo $index; ?>"><?php echo htmlspecialchars($purpose); ?></label>

                                        <?php if ($index == 0) : ?>
                                            <div id="additionalPurposeInput" class="form-details5" style="display: none; margin-top: 5px;">
                                                <input class="form-control twice" id="schoolPurpose" name="schoolPurpose" oninput="saveInputValue('schoolPurpose', this.value)" required disabled>
                                                <div class="invalid-feedback">Please specify the school name.</div>
                                            </div>
                                        <?php elseif ($index == 1) : ?>
                                            <div id="employPurposeInput" class="form-details7" style="display: none; margin-top: 5px;">
                                                <label class="form-label1">Select Employment Type:</label>
                                                <div class="form-check1">
                                                    <input type="radio" class="form-check-input radiodrop" id="local" name="employmentType" value="Local" oninput="saveInputValue('employmentType', this.value)" required checked>
                                                    <label class="form-check-label" for="local">Local</label>
                                                </div>
                                                <div class="form-check1 mb-3">
                                                    <input type="radio" class="form-check-input radiodrop" id="abroad" name="employmentType" value="Abroad" oninput="saveInputValue('employmentType', this.value)" required>
                                                    <label class="form-check-label" for="abroad">Abroad</label>
                                                </div>
                                            </div>
                                        <?php elseif ($index == 4) : ?>
                                            <div id="additionalOthersPurposeInput" class="form-details5" style="display: none; margin-top: 5px;">
                                                <input class="form-control twice" id="othersPurpose" name="othersPurpose" oninput="saveInputValue('othersPurpose', this.value)" required disabled>
                                                <div class="invalid-feedback">Please specify the purpose.</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-listing3">
                            <div class="form-title-area">
                                <h3 class="form-title2">Student's Information</h3>
                            </div>
                            <hr class="hr2">

                            <div class="form-row">
                                <div class="form-descrip-requestor">
                                    <label for="stud_lastname" class="form-label name-label">Last Name</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" name="stud_lastname" id="stud_lastname" oninput="saveInputValue('stud_lastname', this.value)" required>
                                        <div class="invalid-feedback">Please enter the last name.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="stud_firstname" class="form-label name-label">First Name</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" name="stud_firstname" id="stud_firstname" oninput="saveInputValue('stud_firstname', this.value)" required>
                                        <div class="invalid-feedback">Please enter the first name.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-descrip-requestor">
                                    <label for="stud_midname" class="form-label name-label">Middle Name</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" placeholder="Optional" name="stud_midname" id="stud_midname" oninput="saveInputValue('stud_midname', this.value)">
                                        <div class="invalid-feedback">Please enter the middle name.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="stud_suffix" class="form-label name-label">Suffix</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Optional" name="stud_suffix" id="stud_suffix" oninput="saveInputValue('stud_suffix', this.value)">
                                        <div class="input-group-append">
                                            <span class="input-group-text" data-toggle="tooltip" data-placement="top" title="Examples: Jr., Sr., III, IV">
                                                <i class="fas fa-question-circle"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback">Please enter the suffix.</div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-descrip-requestor">
                                    <label for="grade" class="form-label name-label">Grade</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" name="grade" id="grade" oninput="saveInputValue('grade', this.value)" required>
                                        <div class="invalid-feedback">Please enter the grade.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="section" class="form-label name-label">Section</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" name="section" id="section" oninput="saveInputValue('section', this.value)" required>
                                        <div class="invalid-feedback">Please enter the section.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="sylastattended" class="form-label name-label">School year last attended</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" name="sylastattended" id="sylastattended" oninput="saveInputValue('sylastattended', this.value)" required>
                                        <div class="invalid-feedback">Please enter the school year.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row2">
                                <div class="form-descrip-requestor">
                                    <label for="stud_contact_no" class="form-label name-label">Contact Number</label>
                                    <div class="form-details1">
                                        <input type="text" class="form-control" placeholder="Optional" name="stud_contact_no" id="stud_contact_no" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, ''); saveInputValue('stud_contact_no', this.value)">
                                        <div class="invalid-feedback">Please enter a valid contact number.</div>
                                    </div>
                                </div>

                                <div class="form-descrip-requestor">
                                    <label for="stud_email" class="form-label name-label">Email Address</label>
                                    <div class="form-details1">
                                        <input type="email" class="form-control" placeholder="Optional" name="stud_email" id="stud_email" oninput="saveInputValue('stud_email', this.value)">
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-listing3">
                            <div class="form-title-area">
                                <h3 class="form-title2">Confirmation</h3>
                            </div>
                            <hr class="hr2">

                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12" style="display: none;">
                                        <img id="sig-image" src="" alt="Your signature will go here!" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="e-signature">E-Signature</h3>
                                        <p class="sign-paragraph">Sign in the canvas below</p>
                                    </div>
                                </div>
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-12 text-center">
                                        <canvas id="sig-canvas" class="img-fluid" width="620" height="160">
                                            Get a better browser, bro.
                                        </canvas>
                                        <div class="signaturevalidation" id="signaturevalidation" style="font-size: 13px; color: red; display: none;">
                                            Please provide your signature
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary" id="sig-submitBtn" style="display: none;">Submit Signature</button>
                                            <button class="btn btn-secondary" id="sig-clearBtn">Clear Signature</button>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row justify-content-center align-items-center confirmatory">
                                    <div class="col-md-12 confirmation">
                                        <div class="checkbox-container">
                                            <input class="form-check-input checkdrop" type="checkbox" id="confirm-checkbox" name="confirm-checkbox" required>
                                            <label class="form-check-label1" for="confirm-checkbox" id="confirm-checkbox-label">
                                                I, <span id="full-name-placeholder">[Full Name]</span>, hereby confirm that the informations provided in the form is accurate
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display: none;">
                                        <textarea id="sig-dataUrl" name="signature" class="form-control" rows="5">Data URL for your signature will go here!</textarea>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-12" style="display: none;">
                                        <img id="sig-image" src="" alt="Your signature will go here!" />
                                    </div>
                                </div>
                                <br />
                            </div>
                        </div>

                        <input type="submit" id="submit" name="btn_submit" class="btn btn-primary submit" value="Submit" />
                        <input type="button" name="pre-step" class="btn btn-secondary previous" value="Previous" onclick="prevStep();" />
                        <div class="spinner-border text-primary spinnerr" id="spinner" role="status" style="display: none; margin-left: 10px;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<?php
?>