<?php 
ob_start();
include("registrar_header.php");
include("../conn.php");

$con = new connec();


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';


if (isset($_POST["btn_register"])) {

    $reg_name = $_POST["reg_name"];
    $reg_email = $_POST["reg_email"];
    $reg_password = $_POST["reg_password5"];
    
    $targetDir = "images/";
    $fileName = basename($_FILES["reg_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowTypes = array('jpg', 'png', 'jpeg');
    if (in_array($fileType, $allowTypes)) {
        if (move_uploaded_file($_FILES["reg_image"]["tmp_name"], $targetFilePath)) {
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
                $mail->addAddress($reg_email, $reg_name);

                $mail->addEmbeddedImage('../images/emaillogo.png', 'logo');
                $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

                $mail->isHTML(true);
                $mail->Subject = 'Registration successful. Verify your account';
                $mail->Body = "<div style='background-color: #f2f2f2; padding: 20px;'>
                                <div style='background-color: #007BFF; padding: 20px;'>
                                    <img src='cid:logo' alt='Amparo High Logo' style='display: block; margin: 0 auto; max-width: 400px;' />
                                </div>
                                <div style='background-color: #fff; padding: 20px;'>
                                    <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Dear $reg_name, your registration to Amparo High School website is successful. Below is your verification code:</p>
                                    <div style='text-align: center;'>
                                        <span style='display: inline-block; background-color: #007BFF; color: #fff; padding: 10px 20px; font-size: 30px; border-radius: 5px;'>$verification_code</span>
                                    </div>
                                    <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Use this code to verify your account.</p>
                                </div>
                                <div style='background-color: #007BFF; padding: 30px;'></div>
                            </div>";

                $mail->send();

                $sql = "INSERT INTO registrar (id, reg_name, reg_image, reg_email, reg_password, verification_code, verified_at, token, expires) 
                        VALUES (0, '$reg_name', '$targetFilePath', '$reg_email', '$reg_password', '$verification_code', '', NULL, NULL)";
                $con->insert2($sql);

                header("Location: email-verification.php?reg_email=" . $reg_email . "");
                exit();

            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Sorry, only JPG, JPEG and PNG files are allowed.";
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/requesttracker.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script src="js/registrar_register.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest">
                <img src="../images/add-user.png" alt="Register Icon" class="iconmagnify" />
                Register
            </h2>     
            <hr class="hrrequest">
            
            <div class="form-listing1">
                <div class="form-descrip1">
                    <form class="row g-3" method="post" action="" enctype="multipart/form-data">
                        <label for="reg_name" class="form-label inputbuttonlabel">Full Name.</label>
                        <div class="col-md-12 inputwithbutton">
                            <input type="text" class="form-control" id="reg_name" maxlength="50" name="reg_name" oninput="saveInputValue('reg_name', this.value)" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please provide your full name</div>
                        </div>

                        <div class="col-md-12">
                            <label for="reg_image" class="form-label">Display Picture</label>
                            <input type="file" name="reg_image" class="form-control" id="reg_image" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please upload a valid file</div>
                        </div>

                        <div class="col-md-12">
                            <label for="reg_email" class="form-label">Email</label>
                            <input type="text" name="reg_email" class="form-control" maxlength="40" id="reg_email" oninput="saveInputValue('reg_email', this.value)" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please provide your valid email</div>
                        </div>

                        <div class="col-md-12">
                            <label for="reg_password5" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="reg_password5" class="form-control" maxlength="40" id="reg_password5" oninput="saveInputValue('reg_password5', this.value)" required>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('reg_password5', 'togglePasswordIcon1')">
                                        <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                    </button>
                                </div>
                            </div>
                            <div class="feedback-container">
                                <div class="invalid-feedback">Please provide your password</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" class="form-control" maxlength="40" id="confirm_password" oninput="saveInputValue('confirm_password', this.value)" required>
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('confirm_password', 'togglePasswordIcon2')">
                                        <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon2">
                                    </button>
                                </div>
                            </div>
                            <div class="feedback-container">
                                <div class="invalid-feedback">Please provide your password</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" name="btn_register" id="btn_register" class="btn btn-primary registerreg">Register</button>
                            <div class="spinner-border text-primary spinnerr" id="spinner" role="status" style="display: none; margin-left: 10px;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
