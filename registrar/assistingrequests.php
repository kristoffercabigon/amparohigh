<?php
ob_start();
session_start();

if (empty($_SESSION['registrar_id']) || empty($_SESSION['registrar_name'])) {
    header("Location: login.php");
    exit();
}

include("registrar_header.php");
include("../conn.php");

if (empty($_SESSION['registrar_id']) || empty($_SESSION['registrar_name'])) {
    header("Location: login.php");
    exit();
}

$_SESSION['registrar_id'] = isset($_GET['registrar_id']) ? $_GET['registrar_id'] : '';
$_SESSION['registrar_name'] = isset($_GET['registrar_name']) ? $_GET['registrar_name'] : '';
$_SESSION['status_id'] = isset($_GET['status_id']) ? $_GET['status_id'] : '';

$con = new connec();

$checkedCheckboxes = isset($_SESSION['checkedCheckboxes']) ? $_SESSION['checkedCheckboxes'] : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkedCheckboxes = $_POST['checkedCheckboxes'];
    $_SESSION['checkedCheckboxes'] = $checkedCheckboxes;
}

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchFilter = '';

if (!empty($searchTerm)) {
    $searchTerm = $con->real_escape_string($searchTerm);

    $nameComponents = preg_split('/[\s,]+/', $searchTerm);
    $nameFilters = [];

    foreach ($nameComponents as $component) {
        if (!empty($component)) {
            $nameFilters[] = "(requestor.req_name LIKE '%$component%' OR
                              student.stud_lastname LIKE '%$component%' OR
                              student.stud_firstname LIKE '%$component%' OR
                              student.stud_midname LIKE '%$component%' OR
                              student.stud_suffix LIKE '%$component%')";
        }
    }

    if (!empty($nameFilters)) {
        $searchFilter = '(' . implode(' OR ', $nameFilters) . ')';
    }

    $searchFilter .= " OR registrar.reg_name LIKE '%$searchTerm%' OR 
                      status.status LIKE '%$searchTerm%' OR 
                      reference_no LIKE '%$searchTerm%' OR
                      student.section LIKE '%$searchTerm%' OR
                      request_date LIKE '%$searchTerm%' OR
                      student.grade LIKE '%$searchTerm%' OR
                      student.sylastattended LIKE '%$searchTerm%'";
}

$statusFilter = isset($_GET['status_id']) ? (int)$_GET['status_id'] : 0;
$levelFilter = isset($_GET['level_id']) ? (int)$_GET['level_id'] : 0;

$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : '';
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : '';

$filterConditions = [];

$registrarId = isset($_GET['registrar_id']) ? $_GET['registrar_id'] : $_SESSION['registrar_id'];
if (!empty($registrarId)) {
    $registrarId = $con->real_escape_string($registrarId);
    $filterConditions[] = "request.assisted_by_id = '$registrarId'";
}

if ($statusFilter > 0) {
    $filterConditions[] = "request.status_id = $statusFilter";
}

if ($levelFilter > 0) {
    $filterConditions[] = "request.level_id = $levelFilter";
}

if (!empty($searchFilter)) {
    $filterConditions[] = $searchFilter;
}

if (!empty($dateFrom)) {
    $dateFrom = $con->real_escape_string($dateFrom);
    if (empty($dateTo)) {
        $dateTo = $dateFrom;
    } else {
        $dateTo = $con->real_escape_string($dateTo);
    }
    $dateToEnd = date('Y-m-d H:i:s', strtotime($dateTo . ' 23:59:59'));
    $filterConditions[] = "(request.request_date >= '$dateFrom' AND request.request_date <= '$dateToEnd')";
}

$filterCondition = !empty($filterConditions) ? "WHERE " . implode(' AND ', $filterConditions) : '';

$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

$total_sql = "SELECT COUNT(*) AS total 
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN relationship ON requestor.relationship_id = relationship.id 
JOIN student ON request.student_id = student.id
LEFT JOIN registrar ON request.assisted_by_id = registrar.id
JOIN `status` ON request.status_id = `status`.id
$filterCondition";

$total_result = $con->select_by_query($total_sql);

if (!$total_result) {
    die('SQL Error: ' . $con->error);
}

$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $results_per_page);

$sql = "SELECT request.id, `level`.`level`, registrar.reg_name, registrar.reg_image, requestor.id AS requestor_id, requestor.req_name, student.id AS student_id, student.stud_lastname, student.stud_firstname, student.stud_midname, student.stud_suffix, request_date, status.status, status_id, reference_no, grade, section, sylastattended
FROM request 
JOIN requestor ON request.requestor_id = requestor.id 
JOIN relationship ON requestor.relationship_id = relationship.id 
LEFT JOIN registrar ON request.assisted_by_id = registrar.id
JOIN student ON request.student_id = student.id
JOIN `status` ON request.status_id = `status`.id
LEFT JOIN `level` ON request.level_id = `level`.id
$filterCondition
ORDER BY request.id DESC
LIMIT $offset, $results_per_page";

$result = $con->select_by_query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
}

$sqlstatus = "SELECT DISTINCT id, `status` FROM `status`";
$resultstatus = $con->query($sqlstatus);

$documentsByRequest = [];
$sqlreqdoc = "
    SELECT reqdocu.request_id, docus.documents, reqdocu.document_remarks, reqdocu.docus_id
    FROM reqdocu 
    JOIN docus ON reqdocu.docus_id = docus.id
    JOIN request ON reqdocu.request_id = request.id
";
$resultreqdoc = $con->select_by_query($sqlreqdoc);

while ($rowreqdoc = $resultreqdoc->fetch_assoc()) {
    $requestId = $rowreqdoc['request_id'];
    $document = trim($rowreqdoc['documents']);

    $documentRemark = $rowreqdoc['docus_id'] == 10 ?
        (isset($rowreqdoc['document_remarks']) ? ': ' . htmlspecialchars($rowreqdoc['document_remarks']) : '')
        : '';

    if (!isset($documentsByRequest[$requestId])) {
        $documentsByRequest[$requestId] = [];
    }

    $documentsByRequest[$requestId][] = $document . $documentRemark;
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assisting Requests</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/newrequest.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const selectAllCheckbox = document.getElementById('selectAll');
            const assistButton = document.getElementById('assistButton');
            const unassistButton = document.getElementById('unassistButton');
            const deleteButton = document.getElementById('deleteButton');
            const seeDetailsButton = document.getElementById('seedetailsButton');

            function showAlert(message) {
                const alertModal = new bootstrap.Modal(document.getElementById('alertModalcheckbox'));
                const alertBody = document.getElementById('AlertmodalcheckboxBodyText');

                alertBody.innerHTML = message;
                alertModal.show();
            }

            function updateSelectAllCheckbox() {
                fetchCheckedCheckboxes().then(checkedCheckboxes => {
                    const checkboxes = document.querySelectorAll('.table1 .form-check-input');
                    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                    selectAllCheckbox.checked = (checkedCheckboxes.length > 0 || checkedCount > 0);
                });
            }

            function fetchCheckedCheckboxes() {
                return new Promise((resolve, reject) => {
                    const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                    resolve(checkedCheckboxes);
                });
            }

            function handleCheckboxChange(event) {
                const checkbox = event.target;
                let checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];

                if (checkbox.checked) {
                    if (!checkedCheckboxes.includes(checkbox.id)) {
                        checkedCheckboxes.push(checkbox.id);
                    }
                } else {
                    checkedCheckboxes = checkedCheckboxes.filter(id => id !== checkbox.id);
                }

                localStorage.setItem('checkedCheckboxes', JSON.stringify(checkedCheckboxes));
                updateSelectAllCheckbox();
                checkAssistButtonVisibility();
                checkUnassistButtonVisibility();
                checkDeleteButtonVisibility();
            }

            function applySavedCheckboxStates() {
                fetchCheckedCheckboxes().then(savedStates => {
                    const checkboxes = document.querySelectorAll('.table1 .form-check-input');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = savedStates.includes(checkbox.id);
                    });
                    updateSelectAllCheckbox();
                    checkAssistButtonVisibility();
                    checkUnassistButtonVisibility();
                    checkDeleteButtonVisibility();
                });
            }

            function toggleTableCheckboxes(checked) {
                const checkboxes = document.querySelectorAll('.table1 .form-check-input');
                const checkedCheckboxes = [];

                checkboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                    if (checked) {
                        checkedCheckboxes.push(checkbox.id);
                    }
                });

                localStorage.setItem('checkedCheckboxes', JSON.stringify(checkedCheckboxes));
                checkAssistButtonVisibility();
                checkUnassistButtonVisibility();
                checkDeleteButtonVisibility();
            }

            function checkAssistButtonVisibility() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    const url = `checkAssistButtonVisibility.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error:', data.error);
                                assistButton.style.display = 'none';
                            } else {
                                assistButton.style.display = data.showAssistButton ? 'inline-block' : 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            assistButton.style.display = 'none';
                        });
                } else {
                    assistButton.style.display = 'none';
                }
            }

            function checkUnassistButtonVisibility() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    const url = `checkUnassistButtonVisibility.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error:', data.error);
                                unassistButton.style.display = 'none';
                            } else {
                                unassistButton.style.display = data.showUnassistButton ? 'inline-block' : 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            unassistButton.style.display = 'none';
                        });
                } else {
                    unassistButton.style.display = 'none';
                }
            }

            function checkDeleteButtonVisibility() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    const url = `checkDeleteButtonVisibility.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error:', data.error);
                                deleteButton.style.display = 'none';
                            } else {
                                deleteButton.style.display = data.showDeleteButton ? 'inline-block' : 'none';
                            }
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            deleteButton.style.display = 'none';
                        });
                } else {
                    deleteButton.style.display = 'none';
                }
            }

            function assistCheckedRequests() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    showSpinner(assistButton, 'Assisting...');
                    const url = `assistRequests.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    window.location.href = url;
                    localStorage.removeItem('checkedCheckboxes');
                    localStorage.removeItem('selectAll');
                } else {
                    showAlert('No checkboxes selected');
                }
            }

            function unassistCheckedRequests() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    showSpinner(unassistButton, 'Unassisting...');
                    const url = `unassistRequests.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    window.location.href = url;
                    localStorage.removeItem('checkedCheckboxes');
                    localStorage.removeItem('selectAll');
                } else {
                    showAlert('No checkboxes selected');
                }
            }

            function deleteCheckedRequests() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];
                const checkedIds = checkedCheckboxes.map(id => id.replace('flexCheck_', ''));

                if (checkedIds.length > 0) {
                    showSpinner(deleteButton, 'Deleting...');
                    const url = `deleteRequests.php?ids=${encodeURIComponent(checkedIds.join(','))}`;
                    window.location.href = url;
                    localStorage.removeItem('checkedCheckboxes');
                    localStorage.removeItem('selectAll');
                } else {
                    showAlert('No checkboxes selected');
                }
            }

            function showSpinner(button, text) {
                button.disabled = true;
                button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${text}`;
            }

            function openDetailsInNewTabs() {
                const checkedCheckboxes = JSON.parse(localStorage.getItem('checkedCheckboxes')) || [];

                if (checkedCheckboxes.length > 0) {
                    checkedCheckboxes.forEach(id => {
                        const checkbox = document.getElementById(id);
                        const requestId = checkbox.value;

                        const url = `requesttrackerreg.php?id=${encodeURIComponent(requestId)}&reference_no=${encodeURIComponent(requestId)}&requestor_id=${encodeURIComponent(checkbox.getAttribute('data-requestor-id'))}&registrar_id=${encodeURIComponent('<?php echo $_SESSION['registrar_id']; ?>')}&registrar_name=${encodeURIComponent('<?php echo $_SESSION['registrar_name']; ?>')}`;

                        window.open(url, '_blank');
                    });
                } else {
                    showAlert('No checkboxes selected');
                }
            }

            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = selectAllCheckbox.checked;
                toggleTableCheckboxes(isChecked);
                document.querySelectorAll('.table1 .form-check-input').forEach(cb => {
                    cb.dispatchEvent(new Event('change'));
                });
            });

            assistButton.addEventListener('click', assistCheckedRequests);
            unassistButton.addEventListener('click', unassistCheckedRequests);
            deleteButton.addEventListener('click', deleteCheckedRequests);
            seeDetailsButton.addEventListener('click', openDetailsInNewTabs);

            const checkboxes = document.querySelectorAll('.table1 .form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', handleCheckboxChange);
            });

            applySavedCheckboxStates();
        });
    </script>
</head>

<body>
    <div id="RequestPanel" class="container-fluid RequestPanel">
        <h2 class="titlepanel1 hrba">
            <img src="../images/registration.png" alt="Document Icon" class="icondocu" />
            Assisting Requests
        </h2>
        <hr class="hrrequest1 hrba">

        <div class="row no-gutters d-flex justify-content-center mima2">
            <div class="col-12 col-md-12 box same-height gap-25">
                <form class="search-bar">

                    <div class="input-group searchbararea">
                        <input type="text" id="searchInput" class="form-control searchboxinput1" placeholder="Search here and press Enter" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button" id="searchButton">Search</button>
                            <button class="btn btn-secondary" type="button" id="clearButton">Clear</button>
                        </div>
                    </div>

                    <div class="form-check radiobutton1">
                        <input class="form-check-input" type="radio" name="schoolLevel1" id="allLevel1" value="0" checked>
                        <label class="form-check-label labelradio" for="allLevel1">All Levels</label>
                    </div>
                    <div class="form-check radiobutton1">
                        <input class="form-check-input" type="radio" name="schoolLevel1" id="juniorHigh1" value="1">
                        <label class="form-check-label labelradio" for="juniorHigh1">Junior High School</label>
                    </div>
                    <div class="form-check radiobutton1">
                        <input class="form-check-input" type="radio" name="schoolLevel1" id="seniorHigh1" value="2">
                        <label class="form-check-label labelradio" for="seniorHigh1">Senior High School</label>
                    </div>
                    <div class="areangstatandcal">
                        <div class="dropdown mt-3">
                            <div class="d-flex">
                                <button class="btn btn-primary dropdown-toggle statusbtnreg" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="selectedText">Select Status</span>
                                </button>
                                <input type="text" class="form-control ms-2 selected-status-field" id="selectedStatus" readonly value="">
                                <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                    <li><a class="dropdown-item" href="#" data-id="0" data-status="All Requests">All Requests</a></li>
                                    <?php while ($row = $resultstatus->fetch_assoc()): ?>
                                        <li><a class="dropdown-item" href="#" data-id="<?php echo $row['id']; ?>" data-status="<?php echo $row['status']; ?>"><?php echo $row['status']; ?></a></li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="calendarsorter">
                            <div class="d-flex align-items-center mt-3 areangsorter">
                                <label for="dateFrom" class="me-2">From:</label>
                                <input type="date" id="dateFrom" class="form-control me-3 fromarea">

                                <label for="dateTo" class="me-2">To:</label>
                                <input type="date" id="dateTo" class="form-control me-3 toarea">

                                <div class="buttonareangdate">
                                    <button class="btn btn-primary ms-2 filterdate" type="button" id="filterDateButton">Filter</button>
                                    <button class="btn btn-secondary ms-2 cleardate" type="button" id="clearDateButton">Clear Date</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-wrapper1">
                    <div class="table-container1">
                        <table class="table1 table-striped1" id="resultsTable">
                            <thead>
                                <tr>
                                    <th class="tableheadcolor">
                                        <div class="form-check">
                                            <input class="form-check-input thcheckbox" type="checkbox" id="selectAll" onclick="uncheckCheckboxes(this)">
                                        </div>
                                    </th>
                                    <th class="tableheadcolor">Requested Document</th>
                                    <th class="tableheadcolor">Assisting By</th>
                                    <th class="tableheadcolor">Status</th>
                                    <th class="tableheadcolor">Student Name</th>
                                    <th class="tableheadcolor">Grade</th>
                                    <th class="tableheadcolor">Section</th>
                                    <th class="tableheadcolor">School Year</th>
                                    <th class="tableheadcolor">Requestor Name</th>
                                    <th class="tableheadcolor">Request Date</th>
                                    <th class="tableheadcolor">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($requestData = $result->fetch_assoc()):
                                    if ($requestData):
                                        $statusId = $requestData['status_id'];
                                        $backgroundColor = '#ffffff';
                                        switch ($statusId) {
                                            case 1:
                                                $backgroundColor = '#add8e6';
                                                break;
                                            case 2:
                                                $backgroundColor = '#B1AFFF';
                                                break;
                                            case 3:
                                                $backgroundColor = '#EF9C66';
                                                break;
                                            case 4:
                                                $backgroundColor = '#A5DD9B';
                                                break;
                                            case 5:
                                                $backgroundColor = '#9BCF53';
                                                break;
                                            default:
                                                $backgroundColor = '#ffffff';
                                                break;
                                        }
                                        $deleteButtonVisible = ($_SESSION['registrar_name'] === $requestData['reg_name'] || $requestData['reg_name'] === 'None') || $requestData['reg_name'] === NULL;
                                ?>
                                        <tr data-id="<?php echo htmlspecialchars($requestData['id']); ?>">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input thcheckbox" type="checkbox" name="selected_ids[]" value="<?php echo htmlspecialchars($requestData['id']); ?>" id="flexCheck_<?php echo htmlspecialchars($requestData['id']); ?>">
                                                    <label class="form-check-label" for="flexCheck_<?php echo htmlspecialchars($requestData['id']); ?>"></label>
                                                </div>
                                            </td>
                                            <td class="requested-documents">
                                                <?php
                                                $requestId = $requestData['id'];
                                                if (isset($documentsByRequest[$requestId])) {
                                                    echo implode(', ', $documentsByRequest[$requestId]);
                                                } else {
                                                    echo 'No documents';
                                                }
                                                ?>
                                            </td>
                                            <td class="registrar-name">
                                                <?php
                                                if (isset($requestData['reg_image']) && !empty($requestData['reg_image']) && isset($requestData['reg_name']) && !empty($requestData['reg_name'])) {
                                                    $reg_image = htmlspecialchars($requestData['reg_image'], ENT_QUOTES, 'UTF-8');
                                                    $reg_name = htmlspecialchars($requestData['reg_name'], ENT_QUOTES, 'UTF-8');

                                                    echo '<img src="' . $reg_image . '" alt="Profile Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">' . $reg_name;
                                                } else {
                                                    echo 'None';
                                                }
                                                ?>
                                            </td>
                                            <td class="request-status" style="background-color: <?php echo $backgroundColor; ?>;"><?php echo htmlspecialchars($requestData['status']); ?></td>
                                            <td class="student-name">
                                                <?php
                                                $nameParts = [];
                                                if (!empty($requestData['stud_lastname'])) {
                                                    $nameParts[] = htmlspecialchars($requestData['stud_lastname']);
                                                    $nameParts[] = ',';
                                                }
                                                if (!empty($requestData['stud_firstname'])) {
                                                    $nameParts[] = ' ' . htmlspecialchars($requestData['stud_firstname']);
                                                }
                                                if (!empty($requestData['stud_midname'])) {
                                                    $nameParts[] = ' ' . htmlspecialchars($requestData['stud_midname']);
                                                }
                                                if (!empty($requestData['stud_suffix'])) {
                                                    $nameParts[] = ' ' . htmlspecialchars($requestData['stud_suffix']);
                                                }
                                                $formattedName = implode('', $nameParts);
                                                echo rtrim($formattedName, ', ');
                                                ?>
                                            </td>
                                            <td class="student-grade"><?php echo htmlspecialchars($requestData['grade']); ?></td>
                                            <td class="student-section"><?php echo htmlspecialchars($requestData['section']); ?></td>
                                            <td class="student-sylastattended"><?php echo htmlspecialchars($requestData['sylastattended']); ?></td>
                                            <td class="requestor-name"><?php echo htmlspecialchars($requestData['req_name']); ?></td>
                                            <td class="requested-date">
                                                <?php
                                                $requestDate = $requestData['request_date'];
                                                if ($requestDate) {
                                                    $dateTime = new DateTime($requestDate);
                                                    $formattedDate = $dateTime->format('F j, Y');
                                                    $formattedTime = $dateTime->format('h:i A');
                                                    echo htmlspecialchars($formattedDate . ' at ' . $formattedTime);
                                                } else {
                                                    echo 'No date available';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="requesttrackerreg.php?id=<?php echo urlencode($requestData['id']); ?>&reference_no=<?php echo urlencode($requestData['reference_no']); ?>&requestor_id=<?php echo urlencode($requestData['requestor_id']); ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>" class="btn btn-info btn-sm ml-2 buttonsrp">See Details</a>
                                                <?php if ($deleteButtonVisible): ?>
                                                    <button class="btn btn-danger btn-sm ml-2 buttonsrp" onclick="deleteRequest(<?php echo $requestData['id']; ?>, <?php echo $requestData['requestor_id']; ?>, <?php echo $requestData['student_id']; ?>)">Delete</button>
                                                <?php endif; ?>

                                                <?php if (empty($requestData['reg_name'])): ?>
                                                    <button class="btn btn-primary btn-sm ml-2 buttonsrp" onclick="assistRequest(<?php echo $requestData['id']; ?>)">Assist</button>
                                                <?php endif; ?>

                                                <?php if ($_SESSION['registrar_name'] === $requestData['reg_name']): ?>
                                                    <button class="btn btn-secondary btn-sm ml-2 buttonsrp" onclick="unassistRequest(<?php echo $requestData['id']; ?>)">Unassist</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                <?php endif;
                                endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="buttonsreqpancontainer">
                    <button id="seedetailsButton" class="btn btn-info btn-sm ml-2 buttonsrp seeDetailsButton1">See Details</button>
                    <button id="assistButton" class="btn btn-primary btn-sm ml-2 buttonsrp assistButton1">Assist Selected</button>
                    <button id="unassistButton" class="btn btn-secondary btn-sm ml-2 buttonsrp unassistButton1">Unassist Selected</button>
                    <button id="deleteButton" class="btn btn-danger btn-sm ml-2 buttonsrp deleteButton1">Delete Selected</button>
                </div>
                <div class="pagination-container">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=1<?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>

                            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo ($page > 1 ? max(1, $page - 1) : 1); ?><?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($total_pages, $page + 2);

                            if ($start > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1<?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>">1</a>
                                </li>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end < $total_pages): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>"><?php echo $total_pages; ?></a>
                                </li>
                            <?php endif; ?>

                            <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo min($total_pages, $page + 1); ?><?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>

                            <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo isset($_GET['status_id']) ? '&status_id=' . urlencode($_GET['status_id']) : ''; ?><?php echo isset($_GET['level_id']) ? '&level_id=' . urlencode($_GET['level_id']) : ''; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?><?php echo isset($_GET['dateFrom']) ? '&dateFrom=' . urlencode($_GET['dateFrom']) : ''; ?><?php echo isset($_GET['dateTo']) ? '&dateTo=' . urlencode($_GET['dateTo']) : ''; ?>&registrar_id=<?php echo urlencode($_SESSION['registrar_id']); ?>&registrar_name=<?php echo urlencode($_SESSION['registrar_name']); ?>" aria-label="Last">
                                    <span aria-hidden="true">&raquo;&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="unassistConfirmModal" tabindex="-1" aria-labelledby="unassistConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unassistConfirmModalLabel">Confirm Unassist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to unassist this request?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelUnassist">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmUnassist">Confirm</button>
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
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmDelete">Confirm</button>
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
                <div class="modal-body" id="modalBodyText">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertModalcheckbox" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Alert</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="AlertmodalcheckboxBodyText">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function assistRequest(requestId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_assist.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            const row = document.querySelector(`input[name="selected_ids[]"][value="${requestId}"]`).closest('tr');
            const regNameCell = row.querySelector('td:nth-child(3)');
            const statusCell = row.querySelector('td:nth-child(4)');
            const actionButtonsCell = row.querySelector('td:nth-child(11)');
            const assistButton = row.querySelector('button.btn-primary');

            assistButton.disabled = true;
            assistButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Assisting...';

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === 'success') {
                        regNameCell.innerHTML = `
                    <img src="<?php echo htmlspecialchars($_SESSION['reg_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; margin-right: 10px;">
                    <?php echo htmlspecialchars($_SESSION['registrar_name']); ?>
                `;
                        statusCell.textContent = 'Processing';
                        statusCell.style.backgroundColor = '#B1AFFF';

                        const detailsButtonHTML = row.querySelector('a.btn-info').outerHTML;
                        actionButtonsCell.innerHTML = `
                    ${detailsButtonHTML} 
                    <button class="btn btn-danger btn-sm ml-2 buttonsrp" onclick="deleteRequest(${requestId}, ${row.dataset.requestorId}, ${row.dataset.studentId})">Delete</button>
                    <button class="btn btn-secondary btn-sm ml-2 buttonsrp" onclick="unassistRequest(${requestId})">Unassist</button>
                `;
                    } else {
                        assistButton.disabled = false;
                        assistButton.innerHTML = 'Assist';
                        alert('An error occurred while assisting the request.');
                    }
                }
            };

            xhr.send(`assist=true&request_id=${requestId}`);
        }
    </script>

    <script>
        function unassistRequest(requestId) {
            $('#unassistConfirmModal').modal('show');

            document.getElementById('confirmUnassist').onclick = function() {
                fetch('unassist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            unassist: true,
                            request_id: requestId
                        })
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.trim() === 'success') {
                            showAlertModal('You have successfully unassisted the request.');

                            const row = document.querySelector(`input[name="selected_ids[]"][value="${requestId}"]`).closest('tr');
                            const regNameCell = row.querySelector('td:nth-child(3)');
                            const statusCell = row.querySelector('td:nth-child(4)');
                            const actionButtons = row.querySelector('td:nth-child(11)');

                            regNameCell.textContent = 'None';
                            statusCell.textContent = 'In queue';
                            statusCell.style.backgroundColor = '#add8e6';

                            actionButtons.innerHTML = `
                    <a href="requesttrackerreg.php?id=${requestId}" class="btn btn-info btn-sm ml-2 buttonsrp">See Details</a>
                    <button class="btn btn-danger btn-sm ml-2 buttonsrp" onclick="deleteRequest(${requestId}, ${row.dataset.requestorId}, ${row.dataset.studentId})">Delete</button>
                    <button class="btn btn-primary btn-sm ml-2 buttonsrp" onclick="assistRequest(${requestId})">Assist</button>
                `;

                        } else {
                            showAlertModal('Failed to unassist the request. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlertModal('An error occurred. Please try again.');
                    });

                $('#unassistConfirmModal').modal('hide');
            };

            document.getElementById('cancelUnassist').onclick = function() {
                $('#unassistConfirmModal').modal('hide');
            };
        }

        function showAlertModal(message) {
            document.getElementById('alertModalBodyText').textContent = message;
            $('#alertModal').modal('show');
        }
    </script>

    <script>
        function deleteRequest(id, requestorId, studentId) {
            $('#confirmModal').modal('show');

            document.getElementById('confirmDelete').onclick = function() {
                fetch('delete_request.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            delete: true,
                            request_id: id,
                            requestor_id: requestorId,
                            student_id: studentId
                        })
                    })
                    .then(response => response.text())
                    .then(result => {
                        if (result.trim() === 'success') {
                            showAlertModal('The request has been successfully deleted.');

                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) {
                                row.remove();
                            }
                        } else {
                            showAlertModal('Failed to delete the request: ' + result);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlertModal('An error occurred: ' + error.message);
                    });

                $('#confirmModal').modal('hide');
            };

            document.getElementById('cancelDelete').onclick = function() {
                $('#confirmModal').modal('hide');
            };
        }

        function showAlertModal(message) {
            document.getElementById('modalBodyText').textContent = message;
            $('#alertModal').modal('show');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusDropdown = document.getElementById('statusDropdown');
            const selectedStatus = document.getElementById('selectedStatus');

            const colors = {
                0: 'white',
                1: '#add8e6',
                2: '#B1AFFF',
                3: '#EF9C66',
                4: '#A5DD9B',
                5: '#9BCF53'
            };

            const urlParams = new URLSearchParams(window.location.search);
            let savedStatusId = urlParams.get('status_id');
            let savedStatusLabel = '';

            if (!savedStatusId || savedStatusId === "0") {
                savedStatusId = "0";
                savedStatusLabel = "All Requests";
                selectedStatus.value = savedStatusLabel;
                selectedStatus.style.backgroundColor = colors[savedStatusId];

                const registrarId = "<?php echo isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : ''; ?>";
                const registrarName = "<?php echo isset($_SESSION['registrar_name']) ? $_SESSION['registrar_name'] : ''; ?>";
                const url = new URL(window.location.href);

                url.searchParams.set('status_id', savedStatusId);
                url.searchParams.set('registrar_id', registrarId);
                url.searchParams.set('registrar_name', registrarName);

                window.history.replaceState({}, '', url.toString());
            } else {
                document.querySelectorAll('.dropdown-item').forEach(item => {
                    if (item.getAttribute('data-id') === savedStatusId) {
                        savedStatusLabel = item.getAttribute('data-status');
                        selectedStatus.style.backgroundColor = colors[savedStatusId] || 'white';
                    }
                });
                selectedStatus.value = savedStatusLabel;
            }

            document.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function() {
                    const statusLabel = this.getAttribute('data-status');
                    const statusId = this.getAttribute('data-id');

                    selectedStatus.value = statusLabel;
                    selectedStatus.style.backgroundColor = colors[statusId] || 'white';

                    const registrarId = "<?php echo isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : ''; ?>";
                    const registrarName = "<?php echo isset($_SESSION['registrar_name']) ? $_SESSION['registrar_name'] : ''; ?>";
                    const url = new URL(window.location.href);

                    url.searchParams.set('status_id', statusId);
                    url.searchParams.set('page', '1');
                    url.searchParams.set('registrar_id', registrarId);
                    url.searchParams.set('registrar_name', registrarName);

                    url.searchParams.delete('search');

                    window.location.href = url.toString();
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const schoolLevelRadios = document.querySelectorAll('input[name="schoolLevel1"]');
            const urlParams = new URLSearchParams(window.location.search);
            const savedLevelId = urlParams.get('level_id') || "0";

            schoolLevelRadios.forEach(radio => {
                if (radio.value === savedLevelId) {
                    radio.checked = true;
                }

                radio.addEventListener('change', function() {
                    const selectedLevelId = this.value;

                    const registrarId = "<?php echo isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : ''; ?>";
                    const registrarName = "<?php echo isset($_SESSION['registrar_name']) ? $_SESSION['registrar_name'] : ''; ?>";
                    const statusId = urlParams.get('status_id') || "0";
                    const url = new URL(window.location.href);

                    url.searchParams.set('level_id', selectedLevelId);
                    url.searchParams.set('status_id', statusId);
                    url.searchParams.set('page', '1');
                    url.searchParams.set('registrar_id', registrarId);
                    url.searchParams.set('registrar_name', registrarName);

                    url.searchParams.delete('search');

                    window.location.href = url.toString();
                });
            });
        });
    </script>

    <script>
        document.getElementById('searchInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                handleSearch();
                event.preventDefault();
            }
        });

        document.getElementById('searchButton').addEventListener('click', function() {
            handleSearch();
        });

        document.getElementById('clearButton').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            handleSearch();
        });

        function handleSearch() {
            const searchValue = document.getElementById('searchInput').value.trim();
            const urlParams = new URLSearchParams(window.location.search);

            if (searchValue === '') {
                urlParams.delete('search');
            } else {
                urlParams.set('search', searchValue);
            }

            window.location.search = urlParams.toString();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateFromInput = document.getElementById('dateFrom');
            const dateToInput = document.getElementById('dateTo');
            const filterDateButton = document.getElementById('filterDateButton');
            const clearDateButton = document.getElementById('clearDateButton');

            const urlParams = new URLSearchParams(window.location.search);
            const savedDateFrom = urlParams.get('dateFrom') || '';
            const savedDateTo = urlParams.get('dateTo') || '';

            dateFromInput.value = savedDateFrom;
            dateToInput.value = savedDateTo;

            filterDateButton.addEventListener('click', function() {
                const dateFrom = dateFromInput.value;
                let dateTo = dateToInput.value;

                if (!dateTo) {
                    dateTo = dateFrom;
                }

                const registrarId = "<?php echo isset($_SESSION['registrar_id']) ? $_SESSION['registrar_id'] : ''; ?>";
                const registrarName = "<?php echo isset($_SESSION['registrar_name']) ? $_SESSION['registrar_name'] : ''; ?>";
                const statusId = urlParams.get('status_id') || "0";
                const page = urlParams.get('page') || "1";

                const url = new URL(window.location.href);

                url.searchParams.set('dateFrom', dateFrom);
                url.searchParams.set('dateTo', dateTo);
                url.searchParams.set('registrar_id', registrarId);
                url.searchParams.set('registrar_name', registrarName);
                url.searchParams.set('status_id', statusId);
                url.searchParams.set('page', page);

                url.searchParams.delete('search');

                window.location.href = url.toString();
            });

            clearDateButton.addEventListener('click', function() {
                dateFromInput.value = '';
                dateToInput.value = '';

                const url = new URL(window.location.href);
                url.searchParams.delete('dateFrom');
                url.searchParams.delete('dateTo');

                window.location.href = url.toString();
            });
        });
    </script>

</body>

</html>