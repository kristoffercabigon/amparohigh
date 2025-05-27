<?php
ob_start();
include("conn.php");

session_start(); 

if (empty($_SESSION["request_id"])) {
   
    $_SESSION["request_id"] = $_GET['request_id']; 
    header("Location: requesttracker.php"); 
    exit();
}

include("header.php");

$con = new connec();

$id = "";
if(isset($_GET["id"]))
{
    $id = $_GET["id"];
}
$requestor_id = "";

if(isset($_GET["requestor_id"]))
{
    $requestor_id = $_GET["requestor_id"];
}

if (isset($_GET["reference_no"])) {
    $reference_no = htmlspecialchars($_GET["reference_no"]);
} else {
    if (isset($_SESSION['reference_no'])) {
        $reference_no = $_SESSION['reference_no'];
    } else {
        $reference_no = null;
    }
}

$sql = "SELECT * FROM request WHERE id = $id";
$result = $con->select_by_query($sql);

$rows = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

$sqlreqby = "SELECT request.id, requestor.req_name, status.status
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN relationship ON requestor.relationship_id = relationship.id 
JOIN `status` ON request.status_id = `status`.id
WHERE request.id = $id";
$resultreqby = $con->select_by_query($sqlreqby);

$sqlreqby1 = "SELECT request.id, status.status, status.id AS status_id
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN `status` ON request.status_id = `status`.id
WHERE request.id = $id";
$resultreqby1 = $con->select_by_query($sqlreqby1);
$currentStatus = $resultreqby1->fetch_assoc();
$currentStatusId = $currentStatus['status_id'];

$sqlstud = "SELECT student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix
FROM request 
JOIN student ON request.student_id = student.id 
WHERE request.id = $id";
$resultstud = $con->select_by_query($sqlstud);

$sqlreqdoc = "SELECT reqdocu.docus_id, docus.documents, docu_status.status, reqdocu.document_remarks, reqdocu.notes
FROM reqdocu 
JOIN docus ON reqdocu.docus_id = docus.id
JOIN request ON reqdocu.request_id = request.id
JOIN docu_status ON reqdocu.document_status_id = docu_status.id
WHERE reqdocu.request_id = $id";
$resultreqdoc = $con->select_by_query($sqlreqdoc);

$sqldl = "SELECT id, request_id, `filename`, filesize, filetype, upload_date FROM uploaded_docus WHERE request_id = $id";
$resultdl = $con->query($sqldl);

$assisted_by_name = '';

$sqlassist = "SELECT request.assisted_by_id, registrar.reg_name, registrar.reg_image, registrar.id as registrar_id
              FROM request
              LEFT JOIN registrar ON request.assisted_by_id = registrar.id
              WHERE request.id = $id";

$resultassist = $con->query($sqlassist);

if ($resultassist && $resultassist->num_rows > 0) {
    $row = $resultassist->fetch_assoc();
    
    $_SESSION['registrar_id'] = $row['registrar_id']; 
    
    if (!empty($row['reg_name'])) {
        $assisted_by_name = $row['reg_name'];
        $reg_image = $row['reg_image'];
    } else {
        $assisted_by_name = 'None';
    }
}

$id = intval($_GET['id']); 
$sqlupdatetoggle = "SELECT upload_requirements FROM request WHERE id = $id";
$resultupdatetoggle = $con->select_by_query($sqlupdatetoggle);

if ($resultupdatetoggle) {
    $row = $resultupdatetoggle->fetch_assoc();
    $uploadRequirements = $row['upload_requirements'];
} else {
    $uploadRequirements = 0; 
}

if (isset($_SESSION["request_id"]) && isset($_SESSION["reference_no"]) && isset($_SESSION["requestor_id"])) {
    $id = $_SESSION["request_id"];
    $reference_no = $_SESSION["reference_no"];
    $requestor_id = $_SESSION["requestor_id"];
    $registrar_id = $_SESSION['registrar_id'] ?? null; 
    $req_name = $_SESSION["req_name"] ?? 'Unknown'; 

    $con = new connec(); 

    $sqlreqby2 = "SELECT requestor.req_name
                  FROM request 
                  JOIN requestor ON request.requestor_id = requestor.id 
                  WHERE request.id = ?";
    $stmt = $con->prepare($sqlreqby2);
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }
    
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $resultreqby2 = $stmt->get_result();

    if ($resultreqby2->num_rows > 0) {
        $row = $resultreqby2->fetch_assoc();
        $req_name = $row['req_name'];
    } else {
        $req_name = 'Unknown';
    }

    $_SESSION['req_name'] = $req_name;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
        if (isset($_POST['comment_text']) && !empty($_POST['comment_text'])) {
            $comment = $_POST['comment_text'];
            $comment = preg_replace("/(\r\n|\n|\r)/", " ", $comment); 
            $comment = trim($comment);
            $comment = $con->real_escape_string($comment);

            $sql_insert_comment = "INSERT INTO comments (id, commentor, request_id, reference_no, registrar_id, requestor_id, comment_text, commented_at, user_type) 
                                   VALUES (0, ?, ?, ?, ?, ?, ?, NOW(), 1)";
            
            $stmt = $con->prepare($sql_insert_comment);
            if ($stmt === false) {
                die("Error preparing statement: " . $con->error);
            }

            $stmt->bind_param('sisiis', $req_name, $id, $reference_no, $registrar_id, $requestor_id, $comment);
            
            if ($stmt->execute() === TRUE) {
                header("Location: requesttrackermain.php?id=$id&requestor_id=$requestor_id&reference_no=$reference_no");
                exit(); 
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Comment cannot be empty.";
        }
    }  
} else { 
    echo "Session variables are not set.";
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Tracker</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/newrequest.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>
<body>

<div id="RequestTracker" class="requesttracker">
    <div class="container">  
        <div class="row justify-content-center mt-3 parasagilid">  
            <h2 class="titlenewrequest">
                <img src="images/magnify.png" alt="Magnify Icon" class="iconmagnify" />
                Request Details
            </h2>
            
            <hr class="hrrequest">  

            <ul id="progressbar">
                <li class="<?php echo $currentStatusId >= 1 ? 'active' : ''; ?>" id="step1">In queue</li>
                <li class="<?php echo $currentStatusId >= 2 ? 'active' : ''; ?>" id="step2">Processing</li>
                <li class="<?php echo $currentStatusId >= 3 ? 'active' : ''; ?>" id="step3">On Hold</li>
                <li class="<?php echo $currentStatusId >= 4 ? 'active' : ''; ?>" id="step4">Ready to claim</li>
                <li class="<?php echo $currentStatusId >= 5 ? 'active' : ''; ?>" id="step5">Released</li>
            </ul>

            <div class="assistingby assistingby1 kulet">
                <label for="reg_name" class="form-label assistingbylabel">
                    Assisting by: 
                    <?php echo htmlspecialchars($assisted_by_name); ?>
                    <?php if (isset($reg_image) && !empty($reg_image)): ?>
                        <img 
                            src="registrar/<?php echo htmlspecialchars($reg_image, ENT_QUOTES, 'UTF-8'); ?>" 
                            alt="Registrar Image" 
                            style="cursor: pointer;" 
                            onclick="showRegImageModal('registrar/<?php echo htmlspecialchars($reg_image, ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($assisted_by_name, ENT_QUOTES, 'UTF-8'); ?>')"
                        >
                    <?php endif; ?>
                </label>
            </div>

            <div class="form-listing2 afterassistingby">
                <div class="form-title-area">
                    <h3 class="form-title2">Request Details</h3>
                </div>
                <hr class="hr2">
                    <table class="table table-bordered custom-table2">
                        <tr>
                            <td>Request Date and Time:</td>
                            <td class="referencetable">
                            <?php
                                if (!empty($rows)) {
                                    foreach ($rows as $row) {
                                        $dateTime = new DateTime($row["request_date"]);
                                        
                                        $dateFormatted = $dateTime->format('F j, Y');
                                        
                                        $timeFormatted = $dateTime->format('h:i A');
                                        
                                        echo '<span style="color: black;">' . $dateFormatted . ' ' . $timeFormatted . '</span><br>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Reference Number:</td>
                            <td class="pintable">
                            <?php
                                if (!empty($rows)) {
                                    foreach ($rows as $row) {
                                        echo '<span style="color: black;">' . $row["reference_no"] . '</span>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Requested by:</td>
                            <td class="pintable">
                            <?php
                                if ($resultreqby->num_rows > 0) {
                                    while ($row = $resultreqby->fetch_assoc()) {
                                    echo '<span style="color: black;">' . $row["req_name"] . '</span>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Student Name:</td>
                            <td class="pintable">
                            <?php
                                if ($resultstud->num_rows > 0) {
                                    while ($row = $resultstud->fetch_assoc()) {
                                    echo '<span style="color: black;">' . $row["stud_lastname"] . ', ' . $row["stud_firstname"] . ' ' . $row["stud_midname"] . ' ' . $row["stud_suffix"] . '</span>';
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                        <tr>
                        <td>Request Status:</td>
                        <td class="pintable">
                            <span style="color: black;"><?php echo $currentStatus['status']; ?></span>
                        </td>
                    </tr>
                    </table>
                </div>

                <table class="table table-bordered custom-table3">
                    <thead>
                        <tr>
                            <th scope="col" class="doculabel">Requested Document</th>
                            <th scope="col" class="doculabel">Status</th>
                            <th scope="col" class="doculabel">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultreqdoc->num_rows > 0) {
                            while ($row = $resultreqdoc->fetch_assoc()) {

                                $documents = explode(',', $row["documents"]);
                                $docus_id = $row["docus_id"];

                                foreach ($documents as $document) {
                                    echo '<tr>';

                                    if ($docus_id == 10) {
                                        echo '<td class="docucontent"><span style="color: black;">' . htmlspecialchars(trim($document)) . ': </span> <span style="color: black;">' . htmlspecialchars($row["document_remarks"]) . '</span></td>';
                                    } else {
                                        echo '<td class="docucontent"><span style="color: black;">' . htmlspecialchars(trim($document)) . '</span></td>';
                                    }

                                    echo '<td class="docucontent"><span style="color: black;">' . htmlspecialchars($row["status"]) . '</span></td>';

                                    echo '<td class="docucontent">';
                                    echo '<span class="notedocu" id="NoteText" style="color: black;">' . htmlspecialchars($row["notes"]) . '</span><br>';

                                    if (!empty(trim($row["notes"]))) {
                                        echo '<button type="button" class="btn btn-primary view-button" data-doc-id="' . htmlspecialchars($docus_id) . '" data-note-text="' . htmlspecialchars($row["notes"]) . '" data-document-name="' . htmlspecialchars(trim($document)) . '">View</button>';
                                    }

                                    echo '</td>';

                                    echo '</tr>';
                                }
                            }
                        } else {
                            echo '<tr><td colspan="3" class="no-doc-message">No document selected</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <div class="form-listing2">
                    <div class="form-title-area">
                        <h3 class="form-title2">Upload Requirements</h3>
                    </div>
                    <hr class="hr2">
                    <div class="uploadcontent">
                        <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="file" class="form-label">Select files</label>
                                <input type="file" class="form-control" name="file[]" id="file" multiple <?php echo ($uploadRequirements == 0) ? 'disabled' : ''; ?>>
                                <input type="hidden" name="fileData" id="fileData"> 
                                <div id="fileError" class="invalid-feedback">Please select at least one file.</div>
                                <div id="fileSizeError" style="color: red; display: none; margin-top: 16px">Sorry, but the total file size exceeds the maximum allowed limit of 25 MB.</div> 
                                <ul id="fileList" class="file-list"></ul>
                            </div>
                            <button type="submit" id="uploadfile" name="uploadfile" class="btn btn-primary uploadfilebtn" <?php echo ($uploadRequirements == 0) ? 'disabled' : ''; ?>>Upload files</button>
                        </form>
                        <?php if ($uploadRequirements == 0): ?>
                            <p class="noteupload">Note: File upload is disabled by default. This will only be enabled once the registrar has a requirement.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <table class="table table-bordered table-striped" id="fileTable">
                    <thead>
                        <tr>
                            <th colspan="2" class="labelupload">File Name</th>
                            <th class="labelupload">File Type</th>
                            <th class="labelupload">File Size</th>
                            <th class="labelupload">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resultdl && $resultdl->num_rows > 0) {
                            $totalFilesize = 0;
                            $fileCount = 0;
                            while ($file = $resultdl->fetch_assoc()) {
                                $file_id = $file['id'];
                                $file_name = $file['filename'];
                                $file_type = $file['filetype'];
                                $filesizeInBytes = $file['filesize'];
                                $converted_size = $filesizeInBytes / (1024 * 1024);
                                $totalFilesize += $filesizeInBytes;
                                $file_path = 'uploads/' . $reference_no . '/' . $file_name;

                                $isImage = in_array($file_type, ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

                                echo "<tr>";
                                echo "<td colspan='2' style='text-align: center;'>{$file_name}</td>";
                                echo "<td style='text-align: center;'>{$file_type}</td>";
                                echo "<td style='text-align: center;'>" . number_format($converted_size, 2) . " MB</td>";
                                echo "<td style='text-align: center;'>
                                        <div class='d-flex flex-column flex-sm-row align-items-center justify-content-center'>
                                            <button type='button' class='btn btn-danger mb-2 mb-sm-0' onclick='openDeleteModal(\"{$file_id}\", \"{$file_path}\")'>Delete</button>";

                                if ($isImage) {
                                    echo "<button type='button' class='btn btn-primary viewbuttonimage mt-2 mt-sm-0 ms-sm-2' onclick='viewImage(\"{$file_path}\", \"{$file_name}\")'>View</button>";
                                }

                                echo "</div></td>";
                                echo "</tr>";
                                $fileCount++; 
                            }
                            echo "<input type='hidden' id='totalFilesize' value='{$totalFilesize}'>";
                        } else {
                            echo "<tr><td colspan='5' style='text-align: center;'>No files uploaded yet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="form-listing2">
                    <div class="form-title-area">
                        <h3 class="form-title2">Comments</h3>
                    </div>
                    <hr class="hr2">

                    <div class="comments-list mt-3">
                    </div>
                </div>

                <form id="commentForm" action="requesttrackermain.php?id=<?php echo $id; ?>&requestor_id=<?php echo $requestor_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="comment_text" class="form-label comment-label">Add a Comment</label>
                        <textarea class="form-control forcomment" id="comment_text" name="comment_text" rows="3"></textarea>
                        <div id="commentError" class="invalid-feedback">Please fill up the textarea</div>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary commentbtn">Submit</button>
                </form>

                <div class="modal fade" id="viewNoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel">View Note</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bodyviewnote">
                                <form id="viewNoteForm">
                                    <div class="mb-3">
                                        <label for="viewNoteText" class="form-label" id="noteLabel">Note for:</label>
                                        <textarea class="form-control" name="viewNoteText" id="viewNoteText" rows="4" readonly></textarea>
                                    </div>
                                    <input type="hidden" id="viewDocId" name="docus_id">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($id); ?>">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="viewImageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewImageModalLabel">View Image</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bodyviewimage">
                                <div class="mb-3">
                                    <label for="viewNoteText" class="form-label" id="imageLabel">Image for:</label>
                                    <img src="" class="img-fluid" id="viewImage" alt="Selected Image">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="viewRegImageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewRegImageModalLabel">View Registrar Image</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bodyviewimage">
                                <div class="mb-3">
                                    <label class="form-label" id="RegImageLabel">Registrar: (name of registrar)</label>
                                    <img src="" class="img-fluid" id="viewRegImage" alt="Selected Image">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmModalLabel">Confirm Deletion</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this file?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmDelete">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const fileError = document.getElementById('fileError');

    function validateFileInput() {
        if (!fileInput.files.length) {
            fileInput.classList.remove('is-valid');
            fileInput.classList.add('is-invalid');
            fileError.style.display = 'block';
            return false;
        } else {
            fileInput.classList.remove('is-invalid');
            fileInput.classList.add('is-valid');
            fileError.style.display = 'none';
            return true;
        }
    }

    fileInput.addEventListener('change', validateFileInput);

    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        if (!validateFileInput()) {
            event.preventDefault(); 
        }
    });
});
</script>

<script>
document.getElementById('file').addEventListener('change', function(event) {
    const fileList = document.getElementById('fileList');
    const fileInput = document.getElementById('file');
    const fileError = document.getElementById('fileError');
    const fileData = document.getElementById('fileData'); 

    fileList.innerHTML = ''; 
    const files = event.target.files;
    const fileCount = files.length;

    fileData.value = '';

    if (fileCount === 0) {
        fileError.style.display = 'block';
        fileInput.classList.add('is-invalid'); 
        fileInput.classList.remove('is-valid'); 
        fileInput.value = ''; 
    } else {
        fileError.style.display = 'none';
        fileInput.classList.remove('is-invalid'); 
        fileInput.classList.add('is-valid'); 

        let fileNames = [];
        for (let i = 0; i < fileCount; i++) {
            const listItem = document.createElement('li');
            listItem.classList.add('file-item');
            
            const fileName = document.createElement('span');
            fileName.classList.add('file-name');
            fileName.textContent = files[i].name;
            
            listItem.appendChild(fileName);
            fileList.appendChild(listItem);
            
            fileNames.push(files[i].name); 
        }
        
        fileData.value = fileNames.join(',');
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('comment_text');
    var errorDiv = document.getElementById('commentError');

    function validateTextarea() {
        if (textarea.value.trim() === '') {
            textarea.classList.remove('is-valid');
            textarea.classList.add('is-invalid');
            errorDiv.style.display = 'block';
        } else {
            textarea.classList.remove('is-invalid');
            textarea.classList.add('is-valid');
            errorDiv.style.display = 'none';
        }
    }

    textarea.addEventListener('input', validateTextarea);

    document.getElementById('commentForm').addEventListener('submit', function(event) {
        if (textarea.value.trim() === '') {
            event.preventDefault();
            textarea.classList.add('is-invalid');
            errorDiv.style.display = 'block';
        } else {
            localStorage.setItem('scrollPosition', window.scrollY);

            localStorage.removeItem('comment_text');
        }
    });

    if (localStorage.getItem('scrollPosition') !== null) {
        window.scrollTo(0, localStorage.getItem('scrollPosition'));
        localStorage.removeItem('scrollPosition'); 
    }
});
</script>

<script>
$(document).ready(function() {
    function updateProgressBar(statusId) {
        $('#progressbar li').each(function() {
            var stepId = $(this).attr('id').replace('step', '');
            if (statusId >= stepId) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    var currentStatusId = <?php echo json_encode($currentStatusId); ?>;
    updateProgressBar(currentStatusId);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', function() {
            const docId = this.getAttribute('data-doc-id');
            const noteText = this.getAttribute('data-note-text');
            const documentName = this.getAttribute('data-document-name');
            
            const noteLabel = document.getElementById('noteLabel');
            noteLabel.textContent = 'Note for: ' + documentName;

            const viewNoteText = document.getElementById('viewNoteText');
            viewNoteText.textContent = noteText;

            const viewDocId = document.getElementById('viewDocId');
            viewDocId.value = docId;

            const viewNoteModal = new bootstrap.Modal(document.getElementById('viewNoteModal'));
            viewNoteModal.show();
        });
    });
});
</script>

<script>
    function openDeleteModal(fileId, filePath) {
        var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();

        document.getElementById('confirmDelete').addEventListener('click', function() {
            window.location.href = 'delete_file.php?file_id=' + fileId + '&file_path=' + encodeURIComponent(filePath);
        });
    }

    document.getElementById('cancelDelete').addEventListener('click', function() {
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const MAX_FILE_SIZE = 25000000; 
    const totalFilesize = parseInt(document.getElementById('totalFilesize').value, 10);

    if (totalFilesize > MAX_FILE_SIZE) {
        document.getElementById('file').disabled = true;
        document.getElementById('uploadfile').disabled = true;
        document.getElementById('fileSizeError').style.display = 'block'; 
    } else {
        document.getElementById('fileSizeError').style.display = 'none'; 
    }
});
</script>

<script>
    function viewImage(filePath, fileName) {
    document.getElementById('viewImage').src = filePath;
    document.getElementById('imageLabel').textContent = "Image for: " + fileName;
    var viewImageModal = new bootstrap.Modal(document.getElementById('viewImageModal'));
    viewImageModal.show();
}
</script>

<script>
function showRegImageModal(imageUrl, registrarName) {
    var modal = new bootstrap.Modal(document.getElementById('viewRegImageModal'));
    document.getElementById('viewRegImage').src = imageUrl;
    document.getElementById('RegImageLabel').textContent = 'Registrar: ' + registrarName;
    modal.show();
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const requestId = "<?php echo $id; ?>";
    const commentsList = document.querySelector('.comments-list');

    function fetchComments() {
        fetch('registrar/fetchComments.php?request_id=' + requestId)
            .then(response => response.json())
            .then(data => {
                commentsList.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(comment => {
                        const commentItem = document.createElement('div');
                        commentItem.className = 'comment-item';

                        const commentor = document.createElement('p');
                        commentor.className = 'comment-submitted';
                        commentor.innerHTML = `<strong style="color: ${comment.color};">${comment.commentor}:</strong> ${comment.comment_text}`;
                        
                        const commentedAt = document.createElement('p');
                        commentedAt.innerHTML = `<small>Posted on: ${comment.commented_at}</small>`;

                        commentItem.appendChild(commentor);
                        commentItem.appendChild(commentedAt);

                        commentsList.appendChild(commentItem);
                    });
                } else {
                    commentsList.innerHTML = '<p class="no-comment">No comments yet.</p>';
                }

            })
            .catch(error => console.error('Error fetching comments:', error));
    }

    fetchComments();
    setInterval(fetchComments, 1000); 
});
</script>

</body>
</html>