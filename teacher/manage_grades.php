<?php
require_once '../config.php';
if (!is_logged_teacher()) header('Location: login.php');

$teacher_id = $_SESSION['user_id'];
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// ✅ verify teacher owns this subject
$stmt = $mysqli->prepare("SELECT s.*, sem.name as semester 
                          FROM subjects s 
                          JOIN semesters sem ON s.semester_id=sem.id 
                          WHERE s.id=? AND s.teacher_id=?");
$stmt->bind_param('ii', $subject_id, $teacher_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();
if (!$subject) die("Invalid subject or unauthorized access.");

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updated = 0;
        foreach ($_POST['grades'] as $enroll_id => $grade) {
            if ($grade === '') continue; // skip empty fields
            $stmt = $mysqli->prepare("UPDATE enrollments SET grade=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param('di', $grade, $enroll_id);
                if ($stmt->execute()) {
                    $updated++;
                }
            }
        }
        if ($updated > 0) {
            $message = "Grades updated successfully! ($updated record(s) updated)";
        } else {
            $error = "No grades were updated.";
        }
    } catch (Exception $e) {
        $error = "Error updating grades: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Grades</title>
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
    
    .content-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      margin-bottom: 30px;
    }
    
    .alert {
      padding: 16px 20px;
      border-radius: 12px;
      margin-bottom: 24px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .alert.success {
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #065f46;
    }
    
    .alert.error {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #7f1d1d;
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
    
    .table input[type="number"] {
      width: 100%;
      max-width: 150px;
      padding: 8px 12px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      background: white;
      color: #374151;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .table input[type="number"]:focus {
      outline: none;
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    
    .subject-info {
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border: 1px solid #0ea5e9;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 24px;
    }
    
    .subject-info h4 {
      color: #0c4a6e;
      font-size: 18px;
      margin-bottom: 8px;
    }
    
    .subject-info p {
      color: #075985;
      font-size: 14px;
      margin: 4px 0;
    }
    
    .form-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      margin-top: 24px;
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
      
      .form-actions {
        justify-content: center;
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
        <h1 class="page-title">Manage Grades</h1>
        <p class="page-subtitle">Update student grades for this subject</p>
      </div>
      <div class="header-actions">
        <a class="button secondary" href="dashboard.php">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 12H5m7-7l-7 7 7 7"/>
          </svg>
          Back to Dashboard
        </a>
      </div>
    </div>
  </div>

  <!-- Subject Info & Grades Form -->
  <div class="content-card">
    <div class="subject-info">
      <h4><?= htmlspecialchars($subject['title']) ?></h4>
      <p><strong>Subject Code:</strong> <?= htmlspecialchars($subject['subject_code']) ?></p>
      <p><strong>Semester:</strong> <?= htmlspecialchars($subject['semester']) ?></p>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
      <div class="alert success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alert error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Student Name</th>
              <th>Grade</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
              $res = $mysqli->query("SELECT e.id as enroll_id, st.full_name, e.grade
                                     FROM enrollments e
                                     JOIN students st ON e.student_id_fk=st.id
                                     WHERE e.subject_id_fk=$subject_id
                                     ORDER BY st.full_name");
              if ($res && $res->num_rows > 0) {
                while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td>
                <input type="number" step="0.01" name="grades[<?= $row['enroll_id'] ?>]" 
                       value="<?= htmlspecialchars($row['grade']) ?>" 
                       placeholder="Enter grade">
              </td>
            </tr>
            <?php 
                endwhile;
              } else {
                echo '<tr><td colspan="2" style="text-align: center; color: #6b7280; padding: 40px;">No students enrolled in this subject.</td></tr>';
              }
            } catch (Exception $e) {
              echo '<tr><td colspan="2" style="text-align: center; color: #ef4444; padding: 40px;">Error loading students: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="form-actions">
        <button type="submit" class="button primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5 13l4 4L19 7"/>
          </svg>
          Save Grades
        </button>
      </div>
    </form>
  </div>
</div>
</body>
</html>
