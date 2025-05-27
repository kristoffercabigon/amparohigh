<?php
ob_start();
session_start();

include("registrar_header.php");
include("../conn.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['reg_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $con->prepare('SELECT id, reg_name FROM registrar WHERE reg_email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $token = bin2hex(random_bytes(50));
            
            $stmt = $con->prepare('UPDATE registrar SET token = ?, expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE reg_email = ?');
            $stmt->bind_param('ss', $token, $email);
            $stmt->execute();

            $resetLink = "http://localhost/amparohigh/registrar/verifypassword.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true; 
                $mail->Username = 'amparohighschool24@gmail.com'; 
                $mail->Password = 'xtqc shbn gsoa zyqu';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port = 587; 

                $mail->setFrom('amparohighschool24@gmail.com', 'Amparo High School');
                $mail->addAddress($email);
                $mail->addEmbeddedImage('../images/emaillogo.png', 'logo');

                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "<div style='background-color: #f2f2f2; padding: 20px;'>
                                    <div style='background-color: #007BFF; padding: 20px;'>
                                        <img src='cid:logo' alt='Amparo High Logo' style='display: block; margin: 0 auto; max-width: 400px;' />
                                    </div>
                                    <div style='background-color: #fff; padding: 20px;'>
                                        <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Hi " . htmlspecialchars($user['reg_name']) . ",</p>
                                        <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>We received a request to reset your password. You can reset your password using the link below:</p>
                                        <div style='text-align: center;'>
                                            <span style='display: inline-block; background-color: #007BFF; padding: 10px 20px; font-size: 14px; border-radius: 5px;'>
                                                <a href=\"$resetLink\" style='color: #fff; text-decoration: none;'>$resetLink</a>
                                            </span>
                                        </div>
                                        <p style='font-size: 15px; color: #333; text-align: center; font-family: Arial, sans-serif;'>If you did not request this, please ignore this email.</p>
                                        <p style='font-size: 15px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Thanks,<br>Amparo High School</p>
                                    </div>
                                    <div style='background-color: #007BFF; padding: 30px;'></div>
                                </div>";
                $mail->send();

                $_SESSION['message'] = 'A password reset link has been sent to your email.';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Failed to send reset email. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $_SESSION['error'] = 'No account found with that email address.';
        }
    } else {
        $_SESSION['error'] = 'Invalid email address.';
    }

    header('Location: forgotpassword.php');
    exit();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/requesttracker.css" rel="stylesheet">
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
</head>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest titlenew">
                <img src="../images/forgot.png" alt="Register Icon" class="iconmagnify" />
                Reset Password
            </h2>    
            <hr class="hrrequest">
            <form action="forgotpassword.php" method="post" class="formngemail">
                <div class="form-box">
                    <h5>Email Verification</h5>
                    <br>
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-success">
                            <?php
                            echo htmlspecialchars($_SESSION['message']);
                            unset($_SESSION['message']);
                            ?>
                        </div>
                    <?php elseif (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            echo htmlspecialchars($_SESSION['error']);
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="form first">
                        <div class="details personal">
                            <div class="col-md-12">
                                <label class="form-label">Enter your email address</label>
                                <input type="email" id="email" name="reg_email" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" name="submit_email" class="btn btn-primary pinakabutton">
                            <span class="submitBtn">Submit</span>
                        </button>
                        <div class="spinner-border text-primary spinnerr" id="spinner" role="status" style="display: none; margin-left: 10px;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
