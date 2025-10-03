<?php
require_once '../config.php';
if (!is_logged_teacher()) header('Location: login.php');

$teacher_id = $_SESSION['user_id'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Teacher Dashboard</title>
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
    
    .welcome-text {
      color: #6b7280;
      font-size: 14px;
      font-weight: 500;
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
    
    .action-buttons {
      display: flex;
      gap: 6px;
      flex-wrap: nowrap;
      align-items: center;
      justify-content: flex-start;
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
      }
      
      .action-buttons .button {
        padding: 4px 8px;
        font-size: 10px;
        min-width: 40px;
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
        <h1 class="page-title">Teacher Dashboard</h1>
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

  <!-- Subjects List -->
  <div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Your Subjects</h2>
    </div>
    
    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th>Subject Code</th>
            <th>Title</th>
            <th>Semester</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          try {
            $res = $mysqli->query("SELECT s.id, s.subject_code, s.title, sem.name as semester
                                   FROM subjects s
                                   JOIN semesters sem ON s.semester_id=sem.id
                                   WHERE s.teacher_id=$teacher_id
                                   ORDER BY sem.name, s.subject_code");
            if ($res && $res->num_rows > 0) {
              while ($row = $res->fetch_assoc()):
          ?>
          <tr>
            <td><?= htmlspecialchars($row['subject_code']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['semester']) ?></td>
            <td>
              <div class="action-buttons">
                <a class="button primary" href="manage_grades.php?subject_id=<?= $row['id'] ?>">Manage Grades</a>
              </div>
            </td>
          </tr>
          <?php 
              endwhile;
            } else {
              echo '<tr><td colspan="4" style="text-align: center; color: #6b7280; padding: 40px;">No subjects assigned yet.</td></tr>';
            }
          } catch (Exception $e) {
            echo '<tr><td colspan="4" style="text-align: center; color: #ef4444; padding: 40px;">Error loading subjects: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
