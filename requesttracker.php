<?php
ob_start();
include("header.php");
include("conn.php");

$con = new connec();

if (isset($_GET["action"]) && $_GET["action"] == "logout") {
    $_SESSION["reference_no"] = null;
    $_SESSION["request_id"] = null;
    $_SESSION["requestor_id"] = null;

    session_unset(); 
    session_destroy(); 

    header("Location: requesttracker.php");
    exit();
}

$login_success = false;

if (isset($_POST["btn_track"])) {
    $reference_no = $_POST["log_reference_no"];
    $pin = $_POST["log_pin"];

    $result = $con->select_login("request", $reference_no);

    if ($result && $row = $result->fetch_assoc()) {
        if ($row["pin"] == $pin) {
            $_SESSION["reference_no"] = $row["reference_no"];
            $_SESSION["requestor_id"] = $row["requestor_id"];
            $_SESSION["request_id"] = $row["id"];
            $_SESSION["reqpin"] = $row["pin"];

            $_SESSION["ul"] = '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    ' . htmlspecialchars($_SESSION["reference_no"], ENT_QUOTES, 'UTF-8') . '
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a class="dropdown-item" href="requesttracker.php?action=logout" 
                           style="color:black; background-color: white;" 
                           onmouseover="this.style.backgroundColor=\'#2172BE\'" 
                           onmouseout="this.style.backgroundColor=\'white\'">
                           Logout
                        </a>
                    </li>
                </ul>
            </li>
            ';   
            
            $login_success = true;
            $success_message = "Your request has been found.";
        } else {
            $_SESSION["error_message"] = "Invalid PIN.";
        }
    } else {
        $_SESSION["error_message"] = "Invalid reference number.";
    }
    
    if (isset($_SESSION["error_message"])) {
        echo '<script type="text/javascript">',
             'showModalWrong("'. htmlspecialchars($_SESSION["error_message"], ENT_QUOTES, 'UTF-8') .'");',
             '</script>';
    } else {
        $_SESSION["success_message"] = "Your request has been tracked";
        echo '<script type="text/javascript">',
             'showModalSuccess("'. htmlspecialchars($_SESSION["success_message"], ENT_QUOTES, 'UTF-8') .'");',
             '</script>';
    }    
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Tracker</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/requesttracker.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="js/requesttracker.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        var request_id = <?php echo json_encode(isset($_SESSION["request_id"]) ? $_SESSION["request_id"] : ''); ?>;
        var requestor_id = <?php echo json_encode(isset($_SESSION["requestor_id"]) ? $_SESSION["requestor_id"] : ''); ?>;
        var reference_no = <?php echo json_encode(isset($_SESSION["reference_no"]) ? $_SESSION["reference_no"] : ''); ?>;

        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($login_success): ?>
                showModalSuccess('<?php echo $success_message; ?>');
            <?php endif; ?>
        });

        function showModalWrong(message) {
            document.getElementById('errorMessage').textContent = message;
            $('#exampleModalWrong').modal('show');
        }

        function showModalSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            $('#exampleModalSuccess').modal('show');
        }

        $(document).ready(function () {
            $('#exampleModalSuccess').on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                window.location.href = 'requesttrackermain.php?id=' + request_id + '&requestor_id=' + requestor_id + '&reference_no=' + reference_no;
            });
        });
    </script>

</head>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest">
                <img src="images/magnify.png" alt="Magnify Icon" class="iconmagnify" />
                Request Tracker
            </h2>     
            <hr class="hrrequest">
            
            <div class="form-listing1">
                <div class="form-descrip1">
                    <form class="row g-3" method="post" action="">
                        <label for="reference_no" class="form-label inputbuttonlabel">Reference No.</label>
                        <div class="col-md-12 inputwithbutton">
                            <input type="text" class="form-control referenceinput" id="reference_no" maxlength="20" name="log_reference_no"
                                aria-label="Recipient's username" aria-describedby="button-addon2" oninput="saveInputValue('reference_no', this.value)" required>
                            <button class="btn btn-secondary clearbutton" type="button" id="button-addon2" onclick="clearInput('reference_no')">Clear</button>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please enter your reference number</div>
                        </div>

                        <div class="col-md-12">
                            <label for="pin" class="form-label">PIN</label>
                            <div class="input-group">
                                <input type="password" name="log_pin" class="form-control" maxlength="4" id="pin" oninput="this.value = this.value.replace(/[^0-9]/g, ''); saveInputValue('pin', this.value)" required>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('pin', 'togglePasswordIcon1')">
                                        <img src="images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                    </button>
                                </div>
                            </div>
                            <div class="feedback-container">
                                <div class="invalid-feedback">Please provide your password</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" name="btn_track" id="btn_track" class="btn btn-primary btntrack">Track</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalWrong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <lottie-player src="https://lottie.host/43ddc791-4009-4f2a-9774-301b94ef42b1/kHit2WTQe9.json" background="##FFFFFF" speed="1" style="width: 150px; height: 150px" autoplay direction="1" mode="normal"></lottie-player>
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalSuccess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBodySuccess">
                <lottie-player src="https://lottie.host/25d67f38-cfdb-49e8-9e5c-92b336abde3e/Ol8Aw6nfrs.json" background="##FFFFFF" speed="1" style="width: 150px; height: 150px" autoplay direction="1" mode="normal"></lottie-player>
                <p id="successMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if (isset($_SESSION["error_message"])): ?>
        showModalWrong('<?php echo $_SESSION["error_message"]; ?>');
        <?php unset($_SESSION["error_message"]); ?>
    <?php endif; ?>

    
    <?php if (isset($_SESSION["success_message"])): ?>
        showModalSuccess('<?php echo $_SESSION["success_message"]; ?>');
        <?php unset($_SESSION["success_message"]); ?>
    <?php endif; ?>
</script>

<script>
    function togglePassword(fieldId, iconId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.src = "images/show.png"; 
        } else {
            passwordField.type = "password";
            icon.src = "images/hide.png"; 
        }
    }
</script>

</body>
</html>
