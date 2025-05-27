<?php
if (!isset($_SESSION)) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="css/requesttracker.css" rel="stylesheet">
  <link rel="icon" href="images/logo.png">
  <link rel="stylesheet" href="./bootstrap-5.3.3-dist/css/bootstrap.min.css">
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
      cursor: pointer;
    }

    .notification-item {
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
        <img src="images/logo.png" alt="Logo" width="70" height="70" class="d-inline-block align-text-top">
        &nbsp; Amparo High School
      </a>

      <button class="navbar-toggler togglericon1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span><span id="notification-count1" class="badge bg-danger"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav navbar-nav1">
          <?php if (isset($_SESSION['ul']) && !empty($_SESSION['ul'])): ?>
            <?php echo $_SESSION['ul']; ?>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Request Form
            </a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="newrequest.php" style="color:black; background-color: white;" onmouseover="this.style.backgroundColor='#2172BE'" onmouseout="this.style.backgroundColor='white'">New Request</a>
              </li>
              <?php
              $requestTrackerUrl = isset($_SESSION['request_id'], $_SESSION['requestor_id']) && !empty($_SESSION['request_id']) && !empty($_SESSION['requestor_id'])
                ? "requesttrackermain.php?id=" . $_SESSION['request_id'] . "&requestor_id=" . $_SESSION['requestor_id']
                : "requesttracker.php";
              ?>
              <li>
                <a class="dropdown-item" href="<?php echo $requestTrackerUrl; ?>" style="color:black; background-color: white;" onmouseover="this.style.backgroundColor='#2172BE'" onmouseout="this.style.backgroundColor='white'">
                  Request Tracker
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">About Us</a>
          </li>
          <?php
          if (
            isset($_SESSION["reference_no"]) && !empty($_SESSION["reference_no"]) &&
            isset($_SESSION["requestor_id"]) && !empty($_SESSION["requestor_id"]) &&
            isset($_SESSION["request_id"]) && !empty($_SESSION["request_id"]) &&
            isset($_SESSION["reqpin"]) && !empty($_SESSION["reqpin"])
          ): ?>
            <li class="nav-item dropdown nav-item1">
              <a class="nav-link" href="#" id="notificationDropdown" role="button" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span id="notification-count" class="badge bg-danger"></span>
              </a>
              <ul class="dropdown-menu dropdownreqnot dropdownreqnot1" id="notification-list" aria-labelledby="notificationDropdown">
            </li>
        </ul>
        </li>
      <?php endif; ?>
      </ul>
      </div>
    </div>
  </nav>
  <script>
    const requestTrackerUrl = "<?php echo $requestTrackerUrl; ?>";

    function fetchNotifications() {
      $.ajax({
        url: 'fetch_notifications.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          const notificationCountElement = $('#notification-count');
          const notificationCountElement1 = $('#notification-count1');
          const notificationList = $('#notification-list');

          const totalNotifications = (data.unseenComments.length +
            (data.noteNotification ? 0 : 0) +
            (data.documentNotification ? 1 : 0) +
            (data.uploadRequirementStatusChanged ? 1 : 0)
          );

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
              notificationList.append(`
            <li class="dropdown-item notification-item parasadropdownnotif" onclick="handleNotificationClick(${comment.id}, '${requestTrackerUrl}')">
              <p style="margin-bottom: 3px;" class="pdisplay"><strong>${comment.commentor}</strong> left a comment on your request.</p>
              <small>Commented on: ${comment.commented_at}</small>
            </li>
          `);
            });
          } else {
            notificationList.append('<li class="dropdown-item notification-item parasadropdownnotif">No new comment notifications</li>');
          }

          if (data.noteNotification && data.documentNotification) {
            notificationList.prepend(`
          <li class="dropdown-item notification-item parasadropdownnotif" onclick="handleNoteNotificationClick(${data.noteNotificationId}, '${requestTrackerUrl}')">
            <p class="pdisplay" style="margin-bottom: 3px;"><strong>Important:</strong> Your document (${data.documentNotification})<br> has a new note.</p>
          </li>
        `);
          } else if (data.noteNotification) {
            notificationList.prepend(`
          <li class="dropdown-item notification-item parasadropdownnotif" onclick="handleNoteNotificationClick(${data.noteNotificationId}, '${requestTrackerUrl}')">
            <p class="pdisplay" style="margin-bottom: 3px;"><strong>Important:</strong> ${data.noteNotification}</p>
          </li>
        `);
          }

          if (data.uploadRequirementStatusChanged) {
            notificationList.prepend(`
          <li class="dropdown-item notification-item parasadropdownnotif">
            <p class="pdisplay" style="margin-bottom: 3px;"><strong>Important:</strong> Your upload requirements have<br> been enabled. You can now upload files.</p>
          </li>
        `);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
          setTimeout(fetchNotifications, 1000);
        }
      });
    }

    setInterval(fetchNotifications, 1000);

    function handleNotificationClick(notificationId, redirectUrl) {
      let notificationCountElement = $('#notification-count');
      let notificationCountElement1 = $('#notification-count1');
      let currentCount = parseInt(notificationCountElement.text()) || 0;
      let currentCount1 = parseInt(notificationCountElement1.text()) || 0;

      if (currentCount > 0) {
        notificationCountElement.text(currentCount - 1);
      }
      if (currentCount1 > 0) {
        notificationCountElement1.text(currentCount1 - 1);
      }

      $.ajax({
        url: 'update_notifications.php',
        method: 'POST',
        data: {
          notification_id: notificationId
        },
        success: function() {
          window.location.href = redirectUrl;
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
        }
      });
    }


    function handleNoteNotificationClick(notificationId, redirectUrl) {
      let notificationCountElement = $('#notification-count');
      let notificationCountElement1 = $('#notification-count1');
      let currentCount = parseInt(notificationCountElement.text()) || 0;
      let currentCount1 = parseInt(notificationCountElement1.text()) || 0;

      if (currentCount > 0) {
        notificationCountElement.text(currentCount - 1);
      }
      if (currentCount1 > 0) {
        notificationCountElement1.text(currentCount1 - 1);
      }

      $.ajax({
        url: 'update_notifications.php',
        method: 'POST',
        data: {
          notification_id: notificationId
        },
        success: function() {
          window.location.href = redirectUrl;
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
        }
      });
    }
  </script>

  <script>
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

  <script src="./bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>