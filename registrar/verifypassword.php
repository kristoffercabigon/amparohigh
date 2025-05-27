<?php
ob_start();
session_start();

include("registrar_header.php");
include("../conn.php");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$error_message = '';
$success_message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $con->prepare('SELECT reg_email FROM registrar WHERE token = ? AND expires > NOW()');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();

    if ($reset) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'];
            $email = $reset['reg_email'];

            $stmt = $con->prepare('UPDATE registrar SET reg_password = ?, token = NULL, expires = NULL WHERE reg_email = ?');
            $stmt->bind_param('ss', $new_password, $email);
            $stmt->execute();

            $_SESSION['success_message'] = 'Your password has been successfully reset.';
            header("Location: login.php?status=success");
            exit();
        }
    } else {
        $error_message = 'Invalid or expired token.';
        $_SESSION['error_message'] = $error_message;
        header("Location: login.php?status=error");
        exit();
    }
} else {
    $error_message = 'No token provided.';
    $_SESSION['error_message'] = $error_message;
    header("Location: login.php?status=error");
    exit();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
                <form action="verifypassword.php?token=<?php echo htmlspecialchars($_GET['token']); ?>" method="post" class="formngemail">
                    <div class="form-box">
                        <h5>Create a new Password</h5>

                        <div class="form first">
                            <div class="details personal">
                                <div class="col-md-12">
                                    <label for="reg_password2" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" name="new_password" class="form-control" maxlength="40" id="reg_password2" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('reg_password2', 'togglePasswordIcon1')">
                                                <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary pinakabutton">
                                <span class="submitBtn">Reset Password</span>
                            </button>
                        </div>
                    </div>
                </form>
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
                    <?php
                    if (isset($_GET['status'])) {
                        if ($_GET['status'] === 'success') {
                            echo 'Your password has been successfully reset.';
                        } elseif ($_GET['status'] === 'error') {
                            echo htmlspecialchars($_SESSION['error_message']);
                            unset($_SESSION['error_message']);
                        }
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="modalOkButton">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_GET['status'])): ?>
                $('#alertModal').modal('show');
            <?php endif; ?>
        });

        document.getElementById('modalOkButton').addEventListener('click', function() {
            window.location.href = 'login.php';
        });

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