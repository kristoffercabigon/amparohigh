<?php

if (!isset($_SESSION)) {
  session_start();
}

$requestTrackerUrls = [];

if (
  isset($_SESSION['request_ids'], $_SESSION['reference_nos'], $_SESSION['registrar_id'], $_SESSION['registrar_name'], $_SESSION['requestor_ids']) &&
  !empty($_SESSION['request_ids']) && !empty($_SESSION['reference_nos']) &&
  !empty($_SESSION['registrar_id']) && !empty($_SESSION['registrar_name']) && !empty($_SESSION['requestor_ids'])
) {

  if (count($_SESSION['request_ids']) === count($_SESSION['reference_nos']) && count($_SESSION['request_ids']) === count($_SESSION['requestor_ids'])) {
    for ($i = 0; $i < count($_SESSION['request_ids']); $i++) {
      $request_id = $_SESSION['request_ids'][$i];
      $reference_no = $_SESSION['reference_nos'][$i];
      $registrar_id = $_SESSION['registrar_id'];
      $registrar_name = urlencode($_SESSION['registrar_name']);
      $requestor_id = $_SESSION['requestor_ids'][$i];

      $requestTrackerUrls[] = "requesttrackerreg.php?id=$request_id&reference_no=$reference_no&registrar_id=$registrar_id&registrar_name=$registrar_name&requestor_id=$requestor_id";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../css/requesttracker.css" rel="stylesheet">
  <link rel="icon" href="../images/logo.png">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
  <style>
    .navbar-nav .nav-link,
    .navbar,
    .dropdown-item {
      font-family: "Poppins", sans-serif;
      font-weight: 600;
      font-style: normal;
      color: white;
    }

    #notification-list {
      left: -440px;
      padding-left: 10px !important;
      padding-right: 10px !important;
      max-height: 400px;
      overflow-y: auto;
    }

    .parasadropdownnotif {
      display: flex !important;
      cursor: pointer;
    }

    .notification-item {
      padding-left: 10px !important;
      padding-right: 10px !important;
      border: 1px solid #3c91e6 !important;
      border-radius: 4px !important;
      margin-bottom: 5px;
      background-color: white;
      color: black;
    }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-lg bg-custom bg-primary">
    <div class="container-fluid">
      <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: white;">
        <img src="../images/logo.png" alt="Logo" width="70" height="70" class="d-inline-block align-text-top">
        &nbsp; Amparo High School
      </a>

      <button class="navbar-toggler togglericon1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span><span id="notification-count1" class="badge bg-danger"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav navbar-nav1" style="align-items: center;">
          <?php
          if (isset($_SESSION['registrar_id']) && !empty($_SESSION['registrar_id'])) {
            if (isset($_SESSION['ull'])) {
              echo $_SESSION['ull'];
            } else {
              echo '<li class="nav-item">
                    <a class="nav-link" href="#">Session Not Found</a>
                  </li>';
            }
          } else {
          ?>
            <li class="nav-item">
              <a class="nav-link" aria-current="page" href="index.php">Home</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Registrar
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="register.php" style="color:black; background-color: white;" onmouseover="this.style.backgroundColor=\'#2172BE\'" onmouseout="this.style.backgroundColor=\'white\'">Register</a>
                </li>
                <li>
                  <a class="dropdown-item" href="login.php" style="color:black; background-color: white;" onmouseover="this.style.backgroundColor=\'#2172BE\'" onmouseout="this.style.backgroundColor=\'white\'">Log in</a>
                </li>
              </ul>
            </li>
          <?php
          }
          ?>
          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">About Us</a>
          </li>
          <?php
          if (
            isset($_SESSION["registrar_email"]) && !empty($_SESSION["registrar_email"]) &&
            isset($_SESSION["registrar_name"]) && !empty($_SESSION["registrar_name"]) &&
            isset($_SESSION["registrar_id"]) && !empty($_SESSION["registrar_id"]) &&
            isset($_SESSION["registrar_password"]) && !empty($_SESSION["registrar_password"])
          ): ?>
            <li class="nav-item dropdown nav-item1">
              <a class="nav-link" href="#" id="notificationDropdown" role="button" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span id="notification-count" class="badge bg-danger"></span>
              </a>
              <ul class="dropdown-menu dropdownreqnot dropdownreqnot1" id="notification-list" aria-labelledby="notificationDropdown"></ul>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <script>
    const requestTrackerUrls = <?php echo json_encode($requestTrackerUrls); ?>;

    function fetchNotifications() {
      $.ajax({
        url: 'fetch_reg_notifications.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          const notificationCountElement = $('#notification-count');
          const notificationCountElement1 = $('#notification-count1');
          const notificationList = $('#notification-list');

          const totalNotifications = data.unseenComments.length + data.uploadeddocus.length;

          if (totalNotifications > 0) {
            notificationCountElement.text(totalNotifications);
            notificationCountElement1.text(totalNotifications);
          } else {
            notificationCountElement.text('');
            notificationCountElement1.text('');
          }

          notificationList.empty();

          if (data.unseenComments.length > 0) {
            data.unseenComments.forEach((comment) => {
              const redirectUrl = requestTrackerUrls.find(url => url.includes(`id=${comment.request_id}`)) || "#";
              notificationList.append(`
            <li class="dropdown-item notification-item parasadropdownnotif" onclick="goToRequestTrackerReg(${comment.request_id}, '${redirectUrl}')">
              <div style="display: flex !important; align-items: center;" class="containerngtext1">
                <button class="btn btn-primary btn-sm" style="margin-right: 8px;" onclick="handleNotificationClick(${comment.id}), ${comment.request_id}; event.stopPropagation();">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                  </svg>
                </button>
                <div class="containerngtext">
                  <p class="pdisplay" style="margin-bottom: 3px;"><strong>${comment.commentor}</strong> left a comment on request <br>
                  with reference no <strong>${comment.reference_no}</strong></p>
                  <small>Commented on ${comment.commented_at}</small>
                </div>
              </div>
            </li>
          `);
            });
          } else {
            notificationList.append('<li class="dropdown-item notification-item parasadropdownnotif">No new comment notifications</li>');
          }

          if (data.uploadeddocus.length > 0) {
            data.uploadeddocus.forEach((doc) => {
              const redirectUrl = requestTrackerUrls.find(url => url.includes(`id=${doc.request_id}`)) || "#";
              notificationList.append(`
            <li class="dropdown-item notification-item parasadropdownnotif" onclick="goToDocument(${doc.request_id}, '${redirectUrl}')">
              <div style="display: flex; align-items: center;">
                <button class="btn btn-primary btn-sm" style="margin-right: 8px;" onclick="handleDocumentNotificationClick(${doc.request_id}); event.stopPropagation();">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                  </svg>
                </button>
                <div>
                  <p class="pdisplay" style="margin-bottom: 3px;"><strong>${doc.req_name}</strong> uploaded ${doc.file_count} file(s) on request <br>
                  with reference no <strong>${doc.reference_no}</strong></p>
                  <small>Uploaded on ${doc.upload_date}</small>
                </div>
              </div>
            </li>
          `);
            });
          } else {
            notificationList.append('<li class="dropdown-item notification-item parasadropdownnotif">No new file upload notifications</li>');
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
          setTimeout(fetchNotifications, 1000);
        }
      });
    }


    setInterval(fetchNotifications, 1000);

    function handleNotificationClick(notificationId) {
      const notificationCountElement = $('#notification-count');
      const notificationCountElement1 = $('#notification-count1');

      let currentCount = parseInt(notificationCountElement.text()) || 0;
      let currentCount1 = parseInt(notificationCountElement1.text()) || 0;

      if (currentCount > 0) {
        notificationCountElement.text(currentCount - 1);
      }

      if (currentCount1 > 0) {
        notificationCountElement1.text(currentCount1 - 1);
      }

      $.ajax({
        url: 'update_reg_notifications.php',
        method: 'POST',
        data: {
          notification_id: notificationId
        },
        success: function() {},
        error: function(xhr, status, error) {
          alert('AJAX Error: ' + status + ' - ' + error);
        }
      });
    }


    function handleDocumentNotificationClick(requestId) {
      const notificationCountElement = $('#notification-count');
      const notificationCountElement1 = $('#notification-count1');

      let currentCount = parseInt(notificationCountElement.text()) || 0;
      let currentCount1 = parseInt(notificationCountElement1.text()) || 0;

      if (currentCount > 0) {
        notificationCountElement.text(currentCount - 1);
      }

      if (currentCount1 > 0) {
        notificationCountElement1.text(currentCount1 - 1);
      }

      $.ajax({
        url: 'update_document_notifications.php',
        method: 'POST',
        data: {
          request_id: requestId
        },
        success: function() {},
        error: function(xhr, status, error) {
          alert('AJAX Error: ' + status + ' - ' + error);
        }
      });
    }

    function goToDocument(requestID1, redirectUrl) {
      if (requestID1 && redirectUrl) {
        window.location.href = redirectUrl;
      } else {
        console.error('Invalid requestID or redirectUrl');
      }
    }

    function goToRequestTrackerReg(requestID, redirectUrl) {
      if (requestID && redirectUrl) {
        window.location.href = redirectUrl;
      } else {
        console.error('Invalid requestID or redirectUrl');
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      const notificationDropdown = document.getElementById('notificationDropdown');
      const dropdownMenu = document.getElementById('notification-list');

      notificationDropdown.addEventListener('click', function() {
        if (dropdownMenu.style.display === 'block') {
          dropdownMenu.style.display = 'none';
        } else {
          dropdownMenu.style.display = 'block';
        }
      });

      document.addEventListener('click', function(event) {
        if (!notificationDropdown.contains(event.target)) {
          dropdownMenu.style.display = 'none';
        }
      });
    });
  </script>

  <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>