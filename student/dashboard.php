<?php
require_once '../config.php';
if (!is_logged_student()) header('Location: login.php');

$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// term/year filters (separate Semester and School Year)
$allowed_terms = ['First Semester', 'Second Semester'];
$term_filter = isset($_GET['term']) ? trim($_GET['term']) : '';
if (!in_array($term_filter, $allowed_terms, true)) {
    $term_filter = '';
}
$year_filter = isset($_GET['school_year']) ? trim($_GET['school_year']) : '';

// fetch enrollments and grades with optional filters
$sql = "
    SELECT e.id AS enroll_id, s.subject_code, s.title, sem.name AS semester, e.grade
    FROM enrollments e
    JOIN subjects s ON e.subject_id_fk = s.id
    JOIN semesters sem ON e.semester_id_fk = sem.id
    WHERE e.student_id_fk = ?
";

if ($term_filter !== '' && $year_filter !== '') {
    // Exact match e.g., "First Semester 2024-2025"
    $sql .= " AND sem.name = ?";
} elseif ($term_filter !== '') {
    // Any year of the selected term
    $sql .= " AND sem.name LIKE CONCAT(?, ' %')";
} elseif ($year_filter !== '') {
    // Any term for the entered year
    $sql .= " AND sem.name LIKE CONCAT('% ', ?)";
}

$sql .= " ORDER BY sem.name, s.subject_code";

$stmt = $mysqli->prepare($sql);
if ($term_filter !== '' && $year_filter !== '') {
    $full = $term_filter . ' ' . $year_filter;
    $stmt->bind_param('is', $student_id, $full);
} elseif ($term_filter !== '') {
    $stmt->bind_param('is', $student_id, $term_filter);
} elseif ($year_filter !== '') {
    $stmt->bind_param('is', $student_id, $year_filter);
} else {
    $stmt->bind_param('i', $student_id);
}
$stmt->execute();
$res = $stmt->get_result();

$grades = array();
$complete = true;
while ($row = $res->fetch_assoc()) {
    $grades[] = $row;
    if (!isset($row['grade']) || $row['grade'] === null || $row['grade'] === '') {
        $complete = false;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #1f2937;
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .page-title {
      font-size: 32px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 8px;
    }
    
    .page-subtitle {
      color: #6b7280;
      font-size: 16px;
    }
    
    .header-actions {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    
    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 14px;
      gap: 8px;
    }
    
    .button.primary {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .button.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }
    
    .button.secondary {
      background: #6b7280;
      color: white;
    }
    
    .button.secondary:hover {
      background: #4b5563;
      transform: translateY(-1px);
    }
    
    .button.danger {
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white;
      box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }
    
    .button.danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    }
    
    .content-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      margin-bottom: 30px;
    }
    
    .table-container {
      overflow-x: auto;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 16px;
      overflow: hidden;
    }
    
    .table th {
      background: linear-gradient(135deg, #f8fafc, #e2e8f0);
      color: #374151;
      font-weight: 600;
      padding: 16px 12px;
      text-align: left;
      border-bottom: 2px solid #e5e7eb;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .table td {
      padding: 16px 12px;
      border-bottom: 1px solid #f3f4f6;
      color: #374151;
      vertical-align: top;
    }
    
    .table tr:hover {
      background: #f8fafc;
    }
    
    .table tr:last-child td {
      border-bottom: none;
    }
    
    .filter-form {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    
    .filter-form label {
      font-weight: 600;
      color: #374151;
      font-size: 14px;
    }
    
    .filter-form select,
    .filter-form input[type="text"] {
      padding: 8px 12px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      background: white;
      color: #374151;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .filter-form select:focus,
    .filter-form input[type="text"]:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .filter-form button {
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 14px;
    }
    
    .filter-form button[type="submit"] {
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
    }
    
    .filter-form button[type="submit"]:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .filter-form a {
      padding: 8px 16px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      background: #6b7280;
      color: white;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .filter-form a:hover {
      background: #4b5563;
      transform: translateY(-1px);
    }
    
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 16px;
    }
    
    .status-badge.success {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #065f46;
    }
    
    .status-badge.warn {
      background: rgba(245, 158, 11, 0.1);
      border: 1px solid rgba(245, 158, 11, 0.3);
      color: #78350f;
    }
    
    .grade-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
    }
    
    .grade-badge.na {
      background: rgba(245, 158, 11, 0.1);
      border: 1px solid rgba(245, 158, 11, 0.3);
      color: #78350f;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 15px;
      }
      
      .header-content {
        flex-direction: column;
        text-align: center;
      }
      
      .header-actions {
        justify-content: center;
      }
      
      .page-title {
        font-size: 28px;
      }
      
      .table-container {
        font-size: 14px;
      }
      
      .table th,
      .table td {
        padding: 12px 8px;
      }
      
      .filter-form {
        flex-direction: column;
        align-items: stretch;
      }
      
      .filter-form select,
      .filter-form input[type="text"],
      .filter-form button,
      .filter-form a {
        width: 100%;
      }
    }
    
    @media (max-width: 480px) {
      .header,
      .content-card {
        padding: 20px;
      }
      
      .page-title {
        font-size: 24px;
      }
      
      .button {
        padding: 10px 16px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Header -->
  <div class="header">
    <div class="header-content">
      <div>
        <h1 class="page-title">Student Dashboard</h1>
        <p class="page-subtitle">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></p>
      </div>
      <div class="header-actions">
        <a class="button danger" href="../logout.php">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Logout
        </a>
      </div>
    </div>
  </div>

  <!-- Subjects & Grades -->
  <div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Your Subjects & Grades</h2>
    </div>
    
    <!-- Filter Form -->
    <form method="get" action="dashboard.php" class="filter-form">
      <label>Filter by:</label>
      <select name="term" aria-label="Semester term">
        <option value="" <?= $term_filter === '' ? 'selected' : '' ?>>All Semesters</option>
        <option value="First Semester" <?= $term_filter === 'First Semester' ? 'selected' : '' ?>>First Semester</option>
        <option value="Second Semester" <?= $term_filter === 'Second Semester' ? 'selected' : '' ?>>Second Semester</option>
      </select>
      <input type="text" name="school_year" aria-label="School Year" placeholder="School Year (e.g., 2024-2025)" value="<?= htmlspecialchars($year_filter) ?>" style="width:200px;" />
      <button type="submit">Apply Filter</button>
      <?php if ($term_filter !== '' || $year_filter !== ''): ?>
        <a href="dashboard.php">Reset</a>
      <?php endif; ?>
    </form>
    
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th>Subject Code</th>
            <th>Title</th>
            <th>Semester</th>
            <th>Grade</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($grades) === 0): ?>
            <tr><td colspan="4" style="text-align: center; color: #6b7280; padding: 40px;">You are not enrolled in any subjects.</td></tr>
          <?php else: ?>
            <?php foreach ($grades as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['subject_code']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td>
                  <?php
                    if (isset($row['grade']) && $row['grade'] !== null && $row['grade'] !== '') {
                        echo '<strong>' . htmlspecialchars($row['grade']) . '</strong>';
                    } else {
                        echo '<span class="grade-badge na">N/A</span>';
                    }
                  ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Status Card -->
  <div class="content-card">
    <h3 style="margin: 0 0 16px; color: #1f2937; font-size: 20px;">Grade Status</h3>
    <?php if ($complete && count($grades) > 0): ?>
      <div class="status-badge success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Complete - All grades have been recorded
      </div>
    <?php else: ?>
      <div class="status-badge warn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Incomplete - Some grades are pending
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
