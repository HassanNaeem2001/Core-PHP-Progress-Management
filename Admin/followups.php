<?php
include('../connect.php');
include('headeradmin.php');

// === Handle actions ===
if (isset($_GET['action'], $_GET['enrollment_no'])) {
    $action        = $_GET['action'];
    $enrollment_no = $_GET['enrollment_no'];

    // Fetch student + batchcode
    $sql = "
      SELECT 
        s.studentname,
        s.enrollmentno,
        s.studentphoneno,
        s.studentguardianphoneno,
        b.batchcode
      FROM studentprogresssystem.student s
      JOIN studentprogresssystem.batches b 
        ON s.studentbatch = b.batchid
      WHERE s.enrollmentno = ?
    ";
    $stm = $conn->prepare($sql);
    $stm->bind_param("s", $enrollment_no);
    $stm->execute();
    $res = $stm->get_result();
    if ($res->num_rows === 0) {
        echo "<script>alert('Student not found!'); window.close();</script>";
        exit;
    }
    $student = $res->fetch_assoc();

    // Choose phone and message based on action
    switch ($action) {
        case 'send_whatsapp_student':
            $phone_raw = $student['studentphoneno'];
            $toName = $student['studentname'];
            $message = "Dear {$toName},\n\n"
                     . "This is a formal notice from Aptech Scheme 33 Center. Our records show that you were absent in today's class. Kindly explain the reason for your absence. We expect your cooperation.\n\nRegards,\nAptech Scheme 33 Center";
            $recipient = 'student';
            break;

        case 'send_whatsapp_guardian':
            $phone_raw = $student['studentguardianphoneno'];
            $toName = 'Guardian of ' . $student['studentname'];
            $message = "Dear {$toName},\n\n"
                     . "This is a formal notice from Aptech Scheme 33 Center. Our records show that your child, {$student['studentname']}, was absent in today's class. Kindly explain the reason for the absence. We expect your cooperation.\n\nRegards,\nAptech Scheme 33 Center";
            $recipient = 'guardian';
            break;

        case 'send_fee_student':
            $phone_raw = $student['studentphoneno'];
            $toName = $student['studentname'];
            $message = "Dear {$toName},\n\n"
                     . "This message is to officially inform you that , you have still not cleared your tuition fees. Kindly ensure the balance is cleared before the 10th of every month to avoid any interruption in classes.\n\nRegards,\nAptech Scheme 33 Center";
            $recipient = 'student';
            break;

        case 'send_fee_guardian':
            $phone_raw = $student['studentguardianphoneno'];
            $toName = 'Guardian of ' . $student['studentname'];
            $message = "Dear {$toName},\n\n"
                     . "This message is to officially inform you that , {$student['studentname']} have still not cleared the outstanding fees. Kindly ensure the balance is cleared before the 10th of every month to avoid any interruption in classes.\n\nRegards,\nAptech Scheme 33 Center";
            $recipient = 'guardian';
            break;

        default:
            exit;
    }

    // Normalize phone: digits only, Pakistan code 92
    $digits = preg_replace('/\D+/', '', $phone_raw);
    if (substr($digits, 0, 1) === '0') {
        $digits = '92' . substr($digits, 1);
    } elseif (substr($digits, 0, 2) !== '92') {
        $digits = '92' . $digits;
    }
    $phone = $digits;

    // Log message
    $log = $conn->prepare("
      INSERT INTO studentprogresssystem.absent_messages
        (enrollment_no, name, batch, recipient, phone_number, message)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $log->bind_param(
        "ssssss",
        $student['enrollmentno'],
        $student['studentname'],
        $student['batchcode'],
        $recipient,
        $phone,
        $message
    );
    $log->execute();

    // Redirect to WhatsApp
    header("Location: https://wa.me/{$phone}?text=" . urlencode($message));
    exit;
}

// === Pagination ===
$limit = 10;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// === Build search query ===
$where = "WHERE 1=1";
$params = [];
$types  = '';

if (!empty($_GET['batch'])) {
    $where .= " AND b.batchcode = ?";
    $params[] = $_GET['batch'];
    $types    .= 's';
}
if (!empty($_GET['name'])) {
    $where .= " AND s.studentname LIKE ?";
    $params[] = '%' . $_GET['name'] . '%';
    $types    .= 's';
}
if (!empty($_GET['enrollment_no'])) {
    $where .= " AND s.enrollmentno = ?";
    $params[] = $_GET['enrollment_no'];
    $types    .= 's';
}

// Total records
$countSql = "
  SELECT COUNT(*) AS total
  FROM studentprogresssystem.student s
  JOIN studentprogresssystem.batches b ON s.studentbatch = b.batchid
  $where
";
$stmt = $conn->prepare($countSql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// Fetch page data
$dataSql = "
  SELECT s.enrollmentno, s.studentname, b.batchcode
  FROM studentprogresssystem.student s
  JOIN studentprogresssystem.batches b ON s.studentbatch = b.batchid
  $where
  LIMIT ?, ?
";
$params[] = $start;
$params[] = $limit;
$types   .= 'ii';

$stmt = $conn->prepare($dataSql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="container mt-4">
  <h3>Student Absence & Fees Follow‑Ups</h3>

  <!-- Search Form -->
  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-3">
      <input name="batch" class="form-control" placeholder="Batch Code"
        value="<?= htmlspecialchars($_GET['batch'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input name="name" class="form-control" placeholder="Student Name"
        value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input name="enrollment_no" class="form-control" placeholder="Enrollment No"
        value="<?= htmlspecialchars($_GET['enrollment_no'] ?? '') ?>">
    </div>
    <div class="col-md-3">
      <button class="btn btn-dark w-100">Search</button>
    </div>
  </form>

  <?php if ($res->num_rows): ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Enrollment No</th>
        <th>Name</th>
        <th>Batch</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($stu = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($stu['enrollmentno']) ?></td>
        <td><?= htmlspecialchars($stu['studentname']) ?></td>
        <td><?= htmlspecialchars($stu['batchcode']) ?></td>
        <td>
          <a href="?action=send_whatsapp_student&enrollment_no=<?= urlencode($stu['enrollmentno']) ?>"
             class="btn btn-success btn-sm" target="_blank">Absence → Student</a>
          <a href="?action=send_whatsapp_guardian&enrollment_no=<?= urlencode($stu['enrollmentno']) ?>"
             class="btn btn-warning btn-sm" target="_blank">Absence → Guardian</a>
          <a href="?action=send_fee_student&enrollment_no=<?= urlencode($stu['enrollmentno']) ?>"
             class="btn btn-primary btn-sm" target="_blank">Fees → Student</a>
          <a href="?action=send_fee_guardian&enrollment_no=<?= urlencode($stu['enrollmentno']) ?>"
             class="btn btn-info btn-sm" target="_blank">Fees → Guardian</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link" href="?page=1<?= isset($_GET['batch']) ? "&batch={$_GET['batch']}" : '' ?><?= isset($_GET['name']) ? "&name={$_GET['name']}" : '' ?><?= isset($_GET['enrollment_no']) ? "&enrollment_no={$_GET['enrollment_no']}" : '' ?>">« First</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page-1 ?><?= isset($_GET['batch']) ? "&batch={$_GET['batch']}" : '' ?><?= isset($_GET['name']) ? "&name={$_GET['name']}" : '' ?><?= isset($_GET['enrollment_no']) ? "&enrollment_no={$_GET['enrollment_no']}" : '' ?>">‹ Prev</a>
      </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?><?= isset($_GET['batch']) ? "&batch={$_GET['batch']}" : '' ?><?= isset($_GET['name']) ? "&name={$_GET['name']}" : '' ?><?= isset($_GET['enrollment_no']) ? "&enrollment_no={$_GET['enrollment_no']}" : '' ?>"><?= $i ?></a>
      </li>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $page+1 ?><?= isset($_GET['batch']) ? "&batch={$_GET['batch']}" : '' ?><?= isset($_GET['name']) ? "&name={$_GET['name']}" : '' ?><?= isset($_GET['enrollment_no']) ? "&enrollment_no={$_GET['enrollment_no']}" : '' ?>">Next ›</a>
      </li>
      <li class="page-item">
        <a class="page-link" href="?page=<?= $totalPages ?><?= isset($_GET['batch']) ? "&batch={$_GET['batch']}" : '' ?><?= isset($_GET['name']) ? "&name={$_GET['name']}" : '' ?><?= isset($_GET['enrollment_no']) ? "&enrollment_no={$_GET['enrollment_no']}" : '' ?>">Last »</a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>

  <?php else: ?>
    <p class="text-muted">No students found.</p>
  <?php endif; ?>
</div>

<?php include('footeradmin.php'); ?>
