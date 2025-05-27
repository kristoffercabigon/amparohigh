<?php
ob_start();

include("registrar_header.php");
include_once("../conn.php");

$con = new connec();

$regg_name = "";
$regg_image = "";
$regg_email = "";
$regg_pass = "";

$registrarId = isset($_GET['registrar_id']) ? $_GET['registrar_id'] : $_SESSION['registrar_id'];
$registrarnName = isset($_GET['registrar_name']) ? $_GET['registrar_name'] : $_SESSION['registrar_name'];

$sqlshowvalue = "SELECT id, reg_name, reg_image, reg_email, reg_password FROM registrar WHERE id = $registrarId";
$resultshowvalue = $con->query($sqlshowvalue);

if ($resultshowvalue->num_rows > 0) {
    $row = $resultshowvalue->fetch_assoc();
    $regg_name = $row["reg_name"];
    $regg_image = $row["reg_image"];
    $regg_email = $row["reg_email"];
    $regg_pass = $row["reg_password"];
}

if (isset($_POST["btn_update"])) {
    $reg_id = $_POST["reg_id"]; 
    $reg_name = $_POST["reg_name"];
    $reg_email = $_POST["reg_email"];
    $reg_oldpass = $_POST["reg_password1"];
    $reg_newpass = $_POST["reg_password2"];
    
    $targetDir = "images/";
    $fileName = basename($_FILES["reg_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowTypes = array('jpg', 'png', 'jpeg');
    $updateFields = [];

    if (!empty($fileName) && in_array($fileType, $allowTypes)) {
        if (!move_uploaded_file($_FILES["reg_image"]["tmp_name"], $targetFilePath)) {
            $_SESSION['alert_message'] = "Sorry, there was an error uploading your file.";
            header("Location: " . $_SERVER['PHP_SELF'] . "?registrar_id=$reg_id&registrar_name=" . urlencode($registrarnName));
            exit();
        } else {
            $updateFields[] = "reg_image = '$targetFilePath'";
        }
    }

    if (!empty($reg_name)) {
        $updateFields[] = "reg_name = '$reg_name'";
    }
    if (!empty($reg_email)) {
        $updateFields[] = "reg_email = '$reg_email'";
    }

    if (!empty($reg_oldpass) && $reg_oldpass === $regg_pass) {
        if (!empty($reg_newpass)) {
            $updateFields[] = "reg_password = '$reg_newpass'"; 
        }
    } elseif (!empty($reg_oldpass)) {
        $_SESSION['alert_message'] = "Old password does not match.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?registrar_id=$reg_id&registrar_name=" . urlencode($registrarnName));
        exit(); 
    }

    if (count($updateFields) > 0) {
        $updateFieldsStr = implode(', ', $updateFields);
        $sql = "UPDATE registrar SET $updateFieldsStr WHERE id = $reg_id";
        
        if ($con->update1($sql)) {
            $_SESSION['alert_message'] = "Update successful!";
        } else {
            $_SESSION['alert_message'] = "Update failed! SQL Error: " . $con->error;
        }
    } else {
        $_SESSION['alert_message'] = "No changes were made.";
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?registrar_id=$reg_id&registrar_name=" . urlencode($registrarnName));
    exit(); 
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/requesttracker.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="js/registrar_register.js"></script>
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const alertMessage = "<?php echo isset($_SESSION['alert_message']) ? $_SESSION['alert_message'] : ''; ?>";
    
    if (alertMessage) {
        const modalBodyText = document.getElementById('modalBodyText');
        modalBodyText.innerHTML = alertMessage;
        
        $('#alertModal').modal('show');

        <?php unset($_SESSION['alert_message']); ?>
    }
});
</script>
</head>
<body>
<div id="login" class="login">
    <div id="containersubmission" class="containersubmission">
        <div class="row justify-content-center mt-3">
            <h2 class="titlenewrequest">
                <img src="../images/edit.png" alt="Register Icon" class="iconmagnify" />
                Edit Profile
            </h2>     
            <hr class="hrrequest">
            <div class="form-listing1">
                <div class="form-descrip1">
                    <form class="row g-3" method="post" action="" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="reg_id" value="<?php echo htmlspecialchars($registrarId); ?>">
                        <label for="reg_name" class="form-label inputbuttonlabel">Full Name.</label>
                        <div class="col-md-12 inputwithbutton">
                            <input type="text" class="form-control" id="reg_name" maxlength="50" name="reg_name" value="<?php echo htmlspecialchars($regg_name); ?>" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please provide your full name</div>
                        </div>

                        <div class="col-md-12">
                            <label for="reg_image" class="form-label">Display Picture</label>
                            <input type="file" name="reg_image" class="form-control" id="reg_image">
                        </div>

                        <div class="col-md-12 clearpic">
                            <button type="button" id="clearButton" class="btn btn-secondary mt-2 clrbtnpic" style="display: none;">Clear</button>
                        </div>

                        <?php if (!empty($regg_image)) : ?>
                            <div class="col-md-12 mt-2">
                                <div class="d-flex align-items-center pababa">
                                    <div class="me-3 currentphoto">
                                        <label class="form-label">Current Picture</label>
                                        <img src="<?php echo htmlspecialchars($regg_image); ?>" alt="Current Display Picture" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                    <div class="me-3 currentphoto">
                                        <label class="form-label" id="replacewith" style="display: none;">Replace with</label>
                                        <img id="previewImage" src="#" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; display: none;">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12">
                            <label for="reg_email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="reg_email" maxlength="50" name="reg_email" value="<?php echo htmlspecialchars($regg_email); ?>" oninput="saveInputValue('reg_email', this.value)" required>
                        </div>
                        <div class="feedback-container">
                            <div class="invalid-feedback">Please provide your valid email</div>
                        </div>

                        <div class="col-md-12 labelforpasschange">
                            <label for="passchange" class="form-label passchange">Change Pasword</label>
                        </div>

                        <div class="col-md-12 bawasanonti">
                            <label for="reg_password1" class="form-label">Old Password</label>
                            <div class="input-group">
                                <input type="password" name="reg_password1" class="form-control" maxlength="40" id="reg_password1">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('reg_password1', 'togglePasswordIcon1')">
                                        <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="reg_password2" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="reg_password2" class="form-control" maxlength="40" id="reg_password2" >
                                <div class="input-group-append">
                                    <button class="btn btn-secondary showbutton" type="button" id="button-addon2" onclick="togglePassword('reg_password2', 'togglePasswordIcon1')">
                                        <img src="../images/hide.png" alt="Show Password" class="eye-icon" id="togglePasswordIcon1">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 lagayanngbuttons">
                            <div>
                                <button type="submit" name="btn_update" id="btn_update" class="btn btn-primary registerreg1">Update</button>
                                <div class="spinner-border text-primary spinnerr" id="spinner" role="status" style="display: none; margin-left: 10px;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div>
                                <button type="submit" name="btn_delete" id="btn_delete" class="btn btn-danger registerreg">Delete Account</button>
                                <div class="spinner-border text-primary spinnerr" id="spinner" role="status" style="display: none; margin-left: 10px;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
            <div class="modal-body" id="modalBodyText"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
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

<script>
document.getElementById('reg_image').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const previewImage = document.getElementById('previewImage');
    const replaceWithLabel = document.getElementById('replacewith');
    const clearButton = document.getElementById('clearButton');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
            replaceWithLabel.style.display = 'block';
            clearButton.style.display = 'inline-block'; 
        };
        reader.readAsDataURL(file);
    } else {
        previewImage.src = '#';
        previewImage.style.display = 'none';
        replaceWithLabel.style.display = 'none';
        clearButton.style.display = 'none'; 
    }
});

document.getElementById('clearButton').addEventListener('click', function() {
    const fileInput = document.getElementById('reg_image');
    fileInput.value = ''; 
    document.getElementById('previewImage').src = '#'; 
    document.getElementById('previewImage').style.display = 'none'; 
    document.getElementById('replacewith').style.display = 'none'; 
    this.style.display = 'none'; 
});
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

<script>
document.getElementById('btn_delete').addEventListener('click', function() {
    var spinner = document.getElementById('spinner');
    spinner.style.display = 'inline-block';  

    var registrarId = '<?php echo isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : '';?>';

    var url = 'deleteregistrar.php';
    var data = new FormData();
    data.append('registrar_id', registrarId);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            if (xhr.responseText === 'success') {
                alert('Account successfully deleted.');
                window.location.href = 'login.php'; 
            } else {
                alert('Failed to delete account: ' + xhr.responseText);
            }
        } else {
            alert('Failed to delete account. Please try again.');
        }
        spinner.style.display = 'none';  
    };

    xhr.onerror = function() {
        alert('An error occurred. Please try again.');
        spinner.style.display = 'none';  
    };

    xhr.send(data);
});
</script>

</body>
</html>
