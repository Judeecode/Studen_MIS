<?php
require_once '../config.php';
if (!is_logged_admin()) header('Location: login.php');

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
$error = '';

// ✅ Add Teacher
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $mysqli->prepare("INSERT INTO teachers(full_name,username,password) VALUES(?,?,?)");
        if ($stmt) {
            $full_name = $_POST['full_name'];
            $username = $_POST['username'];
            $password = md5($_POST['password']); // MD5 password
            $stmt->bind_param('sss', $full_name, $username, $password);
            if ($stmt->execute()) {
                $message = "Teacher added successfully!";
            } else {
                $error = "Failed to add teacher.";
            }
        } else {
            $error = "Database error occurred.";
        }
    } catch (Exception $e) {
        $error = "Error adding teacher: " . $e->getMessage();
    }
    if (!$error) {
        header("Location: teachers.php?msg=" . urlencode($message));
        exit;
    }
}

// ✅ Delete Teacher
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        $stmt = $mysqli->prepare("DELETE FROM teachers WHERE id=?");
        if ($stmt) {
            $stmt->bind_param('i', $_GET['id']);
            if ($stmt->execute()) {
                $message = "Teacher deleted successfully!";
            } else {
                $error = "Failed to delete teacher.";
            }
        } else {
            $error = "Database error occurred.";
        }
    } catch (Exception $e) {
        $error = "Error deleting teacher: " . $e->getMessage();
    }
    if (!$error) {
        header("Location: teachers.php?msg=" . urlencode($message));
        exit;
    }
}

// ✅ Edit Teacher
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!empty($_POST['password'])) {
            $stmt = $mysqli->prepare("UPDATE teachers SET full_name=?, username=?, password=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param('sssi',
                    $_POST['full_name'],
                    $_POST['username'],
                    md5($_POST['password']),
                    $_POST['id']
                );
            }
        } else {
            $stmt = $mysqli->prepare("UPDATE teachers SET full_name=?, username=? WHERE id=?");
            if ($stmt) {
                $stmt->bind_param('ssi',
                    $_POST['full_name'],
                    $_POST['username'],
                    $_POST['id']
                );
            }
        }
        if ($stmt && $stmt->execute()) {
            $message = "Teacher updated successfully!";
        } else {
            $error = "Failed to update teacher.";
        }
    } catch (Exception $e) {
        $error = "Error updating teacher: " . $e->getMessage();
    }
    if (!$error) {
        header("Location: teachers.php?msg=" . urlencode($message));
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
  <title>Manage Teachers</title>
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
      min-width: 150px;
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
      border-color: #10b981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
        <h1 class="page-title">Manage Teachers</h1>
        <p class="page-subtitle">Add, edit, and manage teacher records</p>
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
    <!-- Teachers List -->
    <div class="content-card">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Teachers List</h2>
        <a class="button primary" href="teachers.php?action=addform">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 4v16m8-8H4"/>
          </svg>
          Add New Teacher
        </a>
      </div>
      
      <div class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Username</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
              $res = $mysqli->query("SELECT * FROM teachers ORDER BY id DESC");
              if ($res) {
                while ($row = $res->fetch_assoc()):
            ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td>
                <div class="action-buttons">
                  <a class="button secondary" href="teachers.php?action=editform&id=<?= $row['id'] ?>">Edit</a>
                  <a class="button danger" href="teachers.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this teacher?')">Delete</a>
                </div>
              </td>
            </tr>
            <?php 
                endwhile;
              } else {
                echo '<tr><td colspan="4" style="text-align: center; color: #6b7280; padding: 40px;">No teachers found or database error occurred.</td></tr>';
              }
            } catch (Exception $e) {
              echo '<tr><td colspan="4" style="text-align: center; color: #ef4444; padding: 40px;">Error loading teachers: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php elseif ($action === 'addform'): ?>
    <!-- Add Teacher Form -->
    <div class="content-card">
      <h2 style="margin: 0 0 24px; color: #1f2937; font-size: 24px;">Add New Teacher</h2>
      <div class="form-container">
        <form method="post" action="teachers.php?action=add">
          <div class="form-grid">
            <div class="form-group full-width">
              <label for="full_name">Full Name *</label>
              <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
              <label for="username">Username *</label>
              <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
              <label for="password">Password *</label>
              <input type="password" id="password" name="password" required>
            </div>
          </div>
          <div class="form-actions">
            <a class="button secondary" href="teachers.php">Cancel</a>
            <button type="submit" class="button primary">Add Teacher</button>
          </div>
        </form>
      </div>
    </div>

  <?php elseif ($action === 'editform' && isset($_GET['id'])):
      $id = intval($_GET['id']);
      try {
        $result = $mysqli->query("SELECT * FROM teachers WHERE id=$id");
        if ($result && $row = $result->fetch_assoc()):
  ?>
    <!-- Edit Teacher Form -->
    <div class="content-card">
      <h2 style="margin: 0 0 24px; color: #1f2937; font-size: 24px;">Edit Teacher</h2>
      <div class="form-container">
        <form method="post" action="teachers.php?action=edit">
          <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
          <div class="form-grid">
            <div class="form-group full-width">
              <label for="full_name">Full Name *</label>
              <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" required>
            </div>
            <div class="form-group">
              <label for="username">Username *</label>
              <input type="text" id="username" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
            </div>
            <div class="form-group">
              <label for="password">Password (leave blank to keep current)</label>
              <input type="password" id="password" name="password">
            </div>
          </div>
          <div class="form-actions">
            <a class="button secondary" href="teachers.php">Cancel</a>
            <button type="submit" class="button primary">Update Teacher</button>
          </div>
        </form>
      </div>
    </div>
    <?php 
        else:
          echo '<div class="content-card"><div class="alert error">Teacher not found.</div></div>';
        endif;
      } catch (Exception $e) {
        echo '<div class="content-card"><div class="alert error">Error loading teacher: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
      }
    ?>
  <?php endif; ?>
</div>
</body>
</html>
