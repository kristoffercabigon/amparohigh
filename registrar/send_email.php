<?php
include("registrar_header.php");

$con = new connec();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requestId = $_POST['request_id'];
    $referenceNo = $_POST['reference_no'];
    $statusId = $_POST['status_id']; 

    $query = "SELECT req_email, req_name FROM request JOIN requestor ON request.requestor_id = requestor.id WHERE request.id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $requestId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $requestorEmail = $row['req_email'];
        $req_name = $row['req_name'];

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
            $mail->addAddress($requestorEmail);

            $mail->isHTML(true);
            $mail->addEmbeddedImage('../images/emaillogo.png', 'logo');
            $mail->Subject = 'Document(s) are ready to claim';
            $mail->Body = "<div style='background-color: #f2f2f2; padding: 20px;'>
            <div style='background-color: #007BFF; padding: 20px;'>
                <img src='cid:logo' alt='Amparo High Logo' style='display: block; margin: 0 auto; max-width: 400px;' />
            </div>
            <div style='background-color: #fff; padding: 20px;'>
                <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Dear $req_name, the documents for your request with reference no. $referenceNo are now ready to claim.</p>

                <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>Just head to the registrar of Amparo High School to claim your documents.</p>

                <p style='font-size: 18px; color: #333; text-align: center; font-family: Arial, sans-serif;'>The registrar is open from Monday to Friday (8:00 AM - 5:00 PM).</p>
            </div>
            <div style='background-color: #007BFF; padding: 30px;'></div>
        </div>";

            $mail->send();

            $updateQuery = "UPDATE request SET sent_email_at = NOW() WHERE id = ?";
            $updateStmt = $con->prepare($updateQuery);
            $updateStmt->bind_param("i", $requestId);
            $updateStmt->execute();
            $updateStmt->close();

            echo 'Email sent successfully!';
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'No requestor found with the given request ID.';
    }
}
?>
