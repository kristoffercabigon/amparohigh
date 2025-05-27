<?php
include("header.php");

if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/newrequest.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
        body {
            background-image: url('images/amparo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            color: white;
            font-family: Georgia, serif;
        }

        .full-row {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 50px;
        }

        .left-img {
            text-align: center;
        }

        .left-img img {
            max-width: 90%;
            height: auto;
            border-radius: 10px;
        }

        .content-wrapper {
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            padding: 2.5rem;
            margin: 1rem;
            font-size: 1.25rem;
            line-height: 1.75;
        }

        h2,
        p {
            color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row full-row">
            <div class="col-md-6 left-img">
                <img src="images/home_logo.png" alt="Amparo Logo">
            </div>

            <div class="col-md-6">
                <div class="content-wrapper">
                    <p>Amparo High School is a public secondary school located in Caloocan, Philippines. The school operates under the Division of City Schools, Caloocan, DepEd NCR. Amparo was established in 1979. An annex was constructed in 2005.</p>
                    <p>Tel. No. 7094525<br>
                        25 Marang St, Barangay 179, Caloocan, Metro Manila</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>