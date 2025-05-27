<?php 
ob_start();
include("registrar_header.php");
include("../conn.php");

$con = new connec();

if (isset($_GET["action"]) && $_GET["action"] == "logoutt") {
    $_SESSION["reg_name"] = null;
    $_SESSION["id"] = null;
    $_SESSION["reg_email"] = null;
    $_SESSION["reg_password"] = null;

    session_unset(); 
    session_destroy();

    header("Location: index.php");
    exit();
}

$login_success = false;

if (isset($_POST["btn_login"])) {
    $reg_email = $_POST["log_regemail"];
    $reg_password = $_POST["log_regpassword"];

    $result = $con->select_login1("registrar", $reg_email);

    if ($result && $row = $result->fetch_assoc()) {
        if ($row["reg_password"] == $reg_password) {
            $_SESSION["registrar_email"] = $row["reg_email"];
            $_SESSION["registrar_name"] = $row["reg_name"];
            $_SESSION["registrar_id"] = $row["id"];
            $_SESSION["registrar_password"] = $row["reg_password"];

            $sqlimage = "SELECT reg_image FROM registrar WHERE id = " . $_SESSION["registrar_id"];
            $resultimage = $con->select_by_query($sqlimage);
            $image_row = $resultimage->fetch_assoc();
            $_SESSION["reg_image"] = $image_row["reg_image"];

            $_SESSION["ull"] = '
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="' . htmlspecialchars($_SESSION["reg_image"], ENT_QUOTES, 'UTF-8') . '" alt="Profile Image" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                    ' . htmlspecialchars($_SESSION["registrar_name"], ENT_QUOTES, 'UTF-8') . '
                </a>
                <ul class="dropdown-menu">
                    <li class="nav-item"> 
                        <a class="dropdown-item" href="requestspanel.php?registrar_id=' . urlencode($_SESSION["registrar_id"]) . '&registrar_name=' . urlencode($_SESSION["registrar_name"]) . '&status_id=0" 
                            style="color:black; background-color: white;" 
                            onmouseover="this.style.backgroundColor=\'#2172BE\'" 
                            onmouseout="this.style.backgroundColor=\'white\'">
                            Request Panel
                        </a>
                        <a class="dropdown-item" href="assistingrequests.php?registrar_id=' . urlencode($_SESSION["registrar_id"]) . '&registrar_name=' . urlencode($_SESSION["registrar_name"]) . '&status_id=0" 
                            style="color:black; background-color: white;" 
                            onmouseover="this.style.backgroundColor=\'#2172BE\'" 
                            onmouseout="this.style.backgroundColor=\'white\'">
                            Assisting Requests
                        </a>
                        <a class="dropdown-item" href="updaterigistrarprofile.php?registrar_id=' . urlencode($_SESSION["registrar_id"]) . '&registrar_name=' . urlencode($_SESSION["registrar_name"]) . '" 
                            style="color:black; background-color: white;" 
                            onmouseover="this.style.backgroundColor=\'#2172BE\'" 
                            onmouseout="this.style.backgroundColor=\'white\'">
                            Edit Profile
                        </a>
                        <a class="dropdown-item" href="login.php?action=logoutt" 
                           style="color:black; background-color: white;" 
                           onmouseover="this.style.backgroundColor=\'#2172BE\'" 
                           onmouseout="this.style.backgroundColor=\'white\'">
                           Logout
                        </a>
                    </li>
                </ul>
            </li>';

            $login_success = true;
            $success_message = "Log-in successful.";
        } else {
            $_SESSION["error_message"] = "Wrong password.";
        }
    } else {
        $_SESSION["error_message"] = "Your email address is wrong or non-existing.";
    }

    if (isset($_SESSION["error_message"])) {
        echo '<script type="text/javascript">',
             'showModalWrong("'. htmlspecialchars($_SESSION["error_message"], ENT_QUOTES, 'UTF-8') .'");',
             '</script>';
    } else {
        $_SESSION["success_message"] = "Log-in successful.";
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
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/requesttracker.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="js/registrar_login.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        var reg_id = <?php echo json_encode(isset($_SESSION["registrar_id"]) ? $_SESSION["registrar_id"] : ''); ?>;
        var reg_name = <?php echo json_encode(isset($_SESSION["registrar_name"]) ? $_SESSION["registrar_name"] : ''); ?>;

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
                window.location.href = 'requestspanel.php?registrar_id=' + reg_id + '&registrar_name=' + reg_name;
            });
        });
    </script>
</head>
<style>
    .log_regemail{
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
</style>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest">
                <img src="../images/log-in.png" alt="Login Icon" class="iconmagnify" />
                Log-in
            </h2>     
            <hr class="hrrequest">
            
            <div class="form-listing1">
                <div class="form-descrip1">
                    <form class="row g-3" method="post" action="">
                        <label for="log_regemail" class="form-label inputbuttonlabel">Email Address</label>
                        <div class="col-md-12 inputwithbutton">
                            <input type="text" class="form-control referenceinput log_regemail" id="log_regemail" maxlength="50" name="log_regemail"
                                oninput="saveInputValue('log_regemail', this.value)" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Your email address is wrong or non-existing.</div>
                        </div>

                        <div class="col-md-12">
                            <label for="log_regpassword" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="log_regpassword" class="form-control" maxlength="40" id="log_regpassword" required>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('log_regpassword', 'togglePasswordIcon1')">
                                        <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                    </button>
                                </div>
                            </div>
                            <div class="feedback-container">
                                <div class="invalid-feedback">Please provide your password</div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between align-items-center">

                            <a href="forgotpassword.php" class="forgot-password-link">Forgot Password?</a>
                            
                            <button type="submit" name="btn_login" id="btn_login" class="btn btn-primary loginbtn">Log in</button>
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

<div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyText">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
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
            icon.src = "../images/show.png";
        } else {
            passwordField.type = "password";
            icon.src = "../images/hide.png"; 
        }
    }
</script>

</body>
</html>
