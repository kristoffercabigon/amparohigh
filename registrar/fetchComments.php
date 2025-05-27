<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amparohigh";

$con = new mysqli($servername, $username, $password, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$request_id = $_GET['request_id'] ?? null;

if ($request_id && is_numeric($request_id)) {
    $sql_comments = "SELECT commentor, comment_text, commented_at, requestor_id, registrar_id, user_type 
                     FROM comments 
                     WHERE request_id = ? 
                     ORDER BY commented_at ASC";
    $stmt = $con->prepare($sql_comments);

    if (!$stmt) {
        die("Statement preparation failed: " . $con->error);
    }

    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result_comments = $stmt->get_result();

    $comments = [];

    if ($result_comments && $result_comments->num_rows > 0) {
        while ($comment = $result_comments->fetch_assoc()) {
            $color = '#000000'; 
            
            if ($comment["user_type"] == 0) {
                $color = '#D34A4E'; 
            } elseif ($comment["user_type"] == 1) {
                $color = '#648BBA'; 
            }

            $comment_text = htmlspecialchars($comment['comment_text']);
            
            $comment_text = preg_replace("/(\r\n|\n|\r|\\\n|\\\r)/", "", $comment_text);

            $comments[] = [
                'commentor' => htmlspecialchars($comment['commentor']),
                'comment_text' => $comment_text,
                'commented_at' => date('F j, Y, g:i a', strtotime($comment['commented_at'])),
                'color' => htmlspecialchars($color)
            ];
        }
    }
    
    $json = json_encode($comments);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo $json;
    } else {
        echo json_encode(['error' => 'Failed to encode JSON']);
    }
} else {
    echo json_encode([]);
}

$con->close();
?>
