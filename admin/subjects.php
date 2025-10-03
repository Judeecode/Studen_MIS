<?php
require_once '../config.php';
if (!is_logged_admin()) header('Location: login.php');

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
$error = '';

// Add subject
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and compose semester name from term + school year
        $term = isset($_POST["semester_term"]) ? trim($_POST["semester_term"]) : '';
        $year = isset($_POST["school_year"]) ? trim($_POST["school_year"]) : '';
        $allowed_terms = ['First Semester', 'Second Semester'];
        
        if (!in_array($term, $allowed_terms, true) || $year === '') {
            $error = 'Invalid semester term or school year.';
        } else {
            $semester_name = $term . ' ' . $year; // e.g., "First Semester 2024-2025"

            // Find or create semester row
            $sem_id = null;
            $stmt = $mysqli->prepare("SELECT id FROM semesters WHERE name = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('s', $semester_name);
                $stmt->execute();
                $stmt->bind_result($sid);
                if ($stmt->fetch()) {
                    $sem_id = (int)$sid;
                }
                $stmt->close();
            }
            
            if ($sem_id === null) {
                $stmt = $mysqli->prepare("INSERT INTO semesters(name) VALUES(?)");
                if ($stmt) {
                    $stmt->bind_param('s', $semester_name);
                    if ($stmt->execute()) {
                        $sem_id = $stmt->insert_id;
                    } else {
                        $error = "Failed to create semester.";
                    }
                    $stmt->close();
                } else {
                    $error = "Database error occurred.";
                }
            }

            if (!$error && $sem_id) {
                // Check for duplicate subject code
                $subject_code = trim($_POST['subject_code']);
                $check_stmt = $mysqli->prepare("SELECT id, title FROM subjects WHERE subject_code = ? LIMIT 1");
                if ($check_stmt) {
                    $check_stmt->bind_param('s', $subject_code);
                    $check_stmt->execute();
                    $check_result = $check_stmt->get_result();
                    
                    if ($check_result->num_rows > 0) {
                        $existing = $check_result->fetch_assoc();
                        $error = "Subject code '" . htmlspecialchars($subject_code) . "' already exists (Title: " . htmlspecialchars($existing['title']) . "). Please use a different subject code.";
                    }
                    $check_stmt->close();
                }
                
                // Insert subject if no duplicate found
                if (!$error) {
                    $stmt = $mysqli->prepare("INSERT INTO subjects(subject_code,title,semester_id,teacher_id) VALUES(?,?,?,?)");
                    if ($stmt) {
                        $title = $_POST['title'];
                        $teacher_id = $_POST['teacher_id'];
                        $stmt->bind_param('ssii', $subject_code, $title, $sem_id, $teacher_id);
                        if ($stmt->execute()) {
                            $message = "Subject added successfully!";
                        } else {
                            $error = "Failed to add subject.";
                        }
                        $stmt->close();
                    } else {
                        $error = "Database error occurred.";
                    }
                }
            }
        }
    } catch (Exception $e) {
        $error = "Error adding subject: " . $e->getMessage();
    }
    
    if (!$error) {
        header("Location: subjects.php?msg=" . urlencode($message));
        exit;
    }
}

// Delete subject
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $mysqli->prepare("DELETE FROM subjects WHERE id=?");
        if ($stmt) {
            $stmt->bind_param('i', $_GET['id']);
            if ($stmt->execute()) {
                $message = "Subject deleted successfully!";
            } else {
                $error = "Failed to delete subject.";
            }
            $stmt->close();
        } else {
            $error = "Database error occurred.";
        }
    } catch (Exception $e) {
        $error = "Error deleting subject: " . $e->getMessage();
    }
    
    if (!$error) {
        header("Location: subjects.php?msg=" . urlencode($message));
        exit;
    }
}

// Get message from URL
if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Subjects</title>
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
      background: linear-gradient(135deg, #f59e0b, #f97316);
      color: white;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    
    .button.primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
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
    
    .action-buttons {
      display: flex;
      gap: 6px;
      flex-wrap: nowrap;
      align-items: center;
      justify-content: flex-start;
      min-width: 100px;
    }
    
    .action-buttons .button {
      padding: 6px 10px;
      font-size: 11px;
      border-radius: 6px;
      white-space: nowrap;
      flex-shrink: 0;
      min-width: 50px;
      text-align: center;
    }
    
    .form-container {
      max-width: 600px;
      margin: 0 auto;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
    }
    
    .form-group label {
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      font-size: 14px;
    }
    
    .form-group input,
    .form-group select {
      padding: 12px 16px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      background: white;
      color: #374151;
      font-size: 14px;
      transition: all 0.3s ease;
    }
    
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #f59e0b;
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .form-group.full-width {
      grid-column: 1 / -1;
    }
    
    .form-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      margin-top: 24px;
    }
    
    .help-text {
      font-size: 12px;
      color: #6b7280;
      margin-top: 8px;
    }
    
    .semester-info {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      border: 1px solid #f59e0b;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 20px;
    }
    
    .semester-info h4 {
      color: #92400e;
      font-size: 16px;
      margin-bottom: 8px;
    }
    
    .semester-info p {
      color: #a16207;
      font-size: 14px;
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
      
      .action-buttons {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 4px;
        min-width: auto;
      }
      
      .action-buttons .button {
        padding: 4px 8px;
        font-size: 10px;
        min-width: 40px;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
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
        <h1 class="page-title">Manage Subjects</h1>
        <p class="page-subtitle">Add, edit, and manage course subjects</p>
      </div>
      <div class="header-actions">
        <a class="button secondary" href="dashboard.php">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 12H5m7-7l-7 7 7 7"/>
          </svg>
          Back to Dashboard
        </a>
        <a class="button danger" href="../logout.php">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Logout
        </a>
      </div>
    </div>
  </div>

  <!-- Messages -->
  <?php if ($message): ?>
    <div class="content-card">
      <div class="alert success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= $message ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="content-card">
      <div class="alert error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= $error ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($action === 'list'): ?>
    <!-- Subjects List -->
    <div class="content-card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Subjects List</h2>
        <a class="button primary" href="subjects.php?action=addform">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          Add New Subject
        </a>
      </div>
      
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Subject Code</th>
              <th>Title</th>
              <th>Semester</th>
              <th>Teacher</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
              $res = $mysqli->query("SELECT s.*, sem.name as semester, t.full_name as teacher
                                     FROM subjects s
                                     JOIN semesters sem ON s.semester_id=sem.id
                                     LEFT JOIN teachers t ON s.teacher_id=t.id
                                     ORDER BY s.id DESC");
              if ($res) {
                while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['subject_code']) ?></td>
              <td><?= htmlspecialchars($row['title']) ?></td>
              <td><?= htmlspecialchars($row['semester']) ?></td>
              <td><?= htmlspecialchars(isset($row['teacher']) && $row['teacher'] ? $row['teacher'] : 'Not assigned') ?></td>
              <td>
                <div class="action-buttons">
                  <a class="button danger" href="subjects.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this subject?')">Delete</a>
                </div>
              </td>
            </tr>
            <?php 
                endwhile;
              } else {
                echo '<tr><td colspan="6" style="text-align: center; color: #6b7280; padding: 40px;">No subjects found or database error occurred.</td></tr>';
              }
            } catch (Exception $e) {
              echo '<tr><td colspan="6" style="text-align: center; color: #ef4444; padding: 40px;">Error loading subjects: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php elseif ($action === 'addform'): ?>
    <!-- Add Subject Form -->
    <div class="content-card">
      <h2 style="margin: 0 0 24px; color: #1f2937; font-size: 24px;">Add New Subject</h2>
      
      <div class="semester-info">
        <h4>Semester Information</h4>
        <p>Subjects are organized by semester and school year. The system will automatically create or find the appropriate semester record.</p>
      </div>
      
      <div class="form-container">
        <form method="post" action="subjects.php?action=add">
          <div class="form-grid">
            <div class="form-group">
              <label for="subject_code">Subject Code *</label>
              <input type="text" id="subject_code" name="subject_code" required placeholder="e.g., MATH101">
            </div>
            <div class="form-group full-width">
              <label for="title">Subject Title *</label>
              <input type="text" id="title" name="title" required placeholder="e.g., Mathematics 101">
            </div>
            <div class="form-group">
              <label for="semester_term">Semester *</label>
              <select id="semester_term" name="semester_term" required>
                <option value="">Select Semester</option>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
              </select>
              <div class="help-text">Choose First or Second Semester</div>
            </div>
            <div class="form-group">
              <label for="school_year">School Year *</label>
              <input type="text" id="school_year" name="school_year" required placeholder="e.g., 2024-2025">
              <div class="help-text">Enter school year as YYYY-YYYY</div>
            </div>
            <div class="form-group full-width">
              <label for="teacher_id">Assigned Teacher *</label>
              <select id="teacher_id" name="teacher_id" required>
                <option value="">Select Teacher</option>
                <?php
                try {
                  $res = $mysqli->query("SELECT * FROM teachers ORDER BY full_name");
                  if ($res) {
                    while ($t = $res->fetch_assoc()):
                ?>
                <option value="<?= htmlspecialchars($t['id']) ?>"><?= htmlspecialchars($t['full_name']) ?></option>
                <?php 
                    endwhile;
                  } else {
                    echo '<option disabled>No teachers available</option>';
                  }
                } catch (Exception $e) {
                  echo '<option disabled>Error loading teachers</option>';
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <a class="button secondary" href="subjects.php">Cancel</a>
            <button type="submit" class="button primary">Add Subject</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
