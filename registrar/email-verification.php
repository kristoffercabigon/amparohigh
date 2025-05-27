<?php 
ob_start();

session_start();

include("registrar_header.php");

$error_message = '';

if(isset($_POST["verify_email"])){
    $reg_email = $_POST["reg_email"];
    $verification_code = $_POST["verification_code"];

    $conn = mysqli_connect("localhost", "root", "", "amparohigh");

    $reg_email = mysqli_real_escape_string($conn, $reg_email);
    $verification_code = mysqli_real_escape_string($conn, $verification_code);

    $sql = "UPDATE registrar SET verified_at = NOW() WHERE reg_email = '$reg_email' AND verification_code = '$verification_code'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) == 0) {
        $error_message = 'Verification Code is invalid';
    } else {
        $_SESSION["success_message"] = "Registration Successful";
        echo '<script type="text/javascript">',
             'showModalSuccess("'. htmlspecialchars($_SESSION["success_message"], ENT_QUOTES, 'UTF-8') .'");',
             '</script>';
    }
}
?>

<style>
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 60vh;
    margin-left: 0px !important;
    margin-right: 0px !important;
}

@media (min-width: 768px) {
    .container {
        max-width: 100% !important;
    }
}

@media (min-width: 576px) {
    .container {
        max-width: 100% !important;
    }
}

.form-box {
    background-color: #ffffff; 
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
    width: 100% !important; 
    text-align: center; 
}

.form-label {
    float: left !important;
}

.error {
    color: red;
    margin-top: 10px;
    font-size: 14px;
}

.invalid-feedback {
    display: block;
}

.pinakabutton {
    margin-top: 15px !important;
}

.form-email {
    display: flex;
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/requesttracker.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="js/registrar_register.js"></script>
    <script>
        function showModalSuccess(message) {
            document.getElementById('successMessage').textContent = message;
            $('#exampleModalSuccess').modal('show');
        }

        $(document).ready(function () {
            $('#exampleModalSuccess').on('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                window.location.href = 'login.php';
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('verification_code').value = '';
        });
    </script>
</head>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest titlenew">
                <img src="../images/add-user.png" alt="Register Icon" class="iconmagnify" />
                Register
            </h2>     
            <hr class="hrrequest">
            <form action="" method="post" class="form-email formngemail">
                <div class="form-box">
                    <h5>Email Verification</h5>
                    <br>
                    <div class="form first">
                        <div class="details personal">
                            <div class="col-md-12">
                                <label for="verification_code" class="form-label">Verification Code</label>
                                <input type="hidden" name="reg_email" value="<?php echo isset($_GET['reg_email']) ? $_GET['reg_email'] : ''; ?>">
                                <input type="text" name="verification_code" class="form-control" placeholder="Enter Verification Code" maxlength="40" id="verification_code" required>
                            </div>
                            <?php if ($error_message): ?>
                                <div class="error"><?php echo $error_message; ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="verify_email" class="btn btn-primary pinakabutton">
                            <span class="submitBtn">Submit</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalSuccess" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 1050;">
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
    <?php if (isset($_SESSION["success_message"])): ?>
        showModalSuccess('<?php echo $_SESSION["success_message"]; ?>');
        <?php unset($_SESSION["success_message"]); ?>
    <?php endif; ?>
</script>
</body>
</html>

<?php
ob_end_flush();
?>
