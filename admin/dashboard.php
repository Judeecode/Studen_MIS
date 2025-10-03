<?php
require_once '../config.php';
if (!is_logged_admin()) header('Location: login.php');

// Get statistics with error handling
$stats = [];
try {
    // Check if tables exist and get counts
    $tables = ['students', 'teachers', 'subjects', 'grades'];
    foreach ($tables as $table) {
        $result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        if ($result) {
            $stats[$table] = $result->fetch_assoc()['count'];
        } else {
            $stats[$table] = 0; // Default to 0 if table doesn't exist or query fails
        }
    }
} catch (Exception $e) {
    // If any error occurs, set all stats to 0
    $stats = ['students' => 0, 'teachers' => 0, 'subjects' => 0, 'grades' => 0];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../styles.css">
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
    
    .dashboard-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .dashboard-header {
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
    
    .welcome-section h1 {
      font-size: 32px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 8px;
    }
    
    .welcome-section p {
      color: #6b7280;
      font-size: 16px;
    }
    
    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    
    .user-avatar {
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #f59e0b, #f97316);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 18px;
    }
    
    .user-details {
      text-align: right;
    }
    
    .user-name {
      font-weight: 600;
      color: #1f2937;
      font-size: 16px;
    }
    
    .user-role {
      color: #6b7280;
      font-size: 14px;
    }
    
    .logout-btn {
      background: #ef4444;
      color: white;
      padding: 8px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .logout-btn:hover {
      background: #dc2626;
      transform: translateY(-1px);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .stat-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 16px;
    }
    
    .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    
    .stat-icon.students { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-icon.teachers { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .stat-icon.subjects { background: linear-gradient(135deg, #f59e0b, #f97316); }
    .stat-icon.grades { background: linear-gradient(135deg, #ef4444, #dc2626); }
    
    .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 4px;
    }
    
    .stat-label {
      color: #6b7280;
      font-size: 14px;
      font-weight: 500;
    }
    
    .modules-section {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .modules-title {
      font-size: 24px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 20px;
      text-align: center;
    }
    
    .modules-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .module-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: 1px solid #e5e7eb;
      transition: all 0.3s ease;
      text-decoration: none;
      color: inherit;
    }
    
    .module-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      border-color: #d1d5db;
    }
    
    .module-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 16px;
    }
    
    .module-icon {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
    
    .module-icon.students { background: linear-gradient(135deg, #10b981, #059669); }
    .module-icon.teachers { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .module-icon.subjects { background: linear-gradient(135deg, #f59e0b, #f97316); }
    
    .module-info h3 {
      font-size: 18px;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 4px;
    }
    
    .module-info p {
      color: #6b7280;
      font-size: 14px;
    }
    
    .module-description {
      color: #6b7280;
      font-size: 14px;
      line-height: 1.5;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .dashboard-container {
        padding: 15px;
      }
      
      .header-content {
        flex-direction: column;
        text-align: center;
      }
      
      .user-info {
        flex-direction: column;
        text-align: center;
      }
      
      .user-details {
        text-align: center;
      }
      
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .modules-grid {
        grid-template-columns: 1fr;
      }
      
      .welcome-section h1 {
        font-size: 28px;
      }
    }
    
    @media (max-width: 480px) {
      .dashboard-header,
      .modules-section {
        padding: 20px;
      }
      
      .stat-card,
      .module-card {
        padding: 20px;
      }
      
      .welcome-section h1 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
<div class="dashboard-container">
  <!-- Header Section -->
  <div class="dashboard-header fade-in">
    <div class="header-content">
      <div class="welcome-section">
        <h1>Admin Dashboard</h1>
        <p>Welcome back! Here's an overview of your system.</p>
      </div>
      <div class="user-info">
        <div class="user-avatar">
          <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
        </div>
        <div class="user-details">
          <div class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
          <div class="user-role">Administrator</div>
        </div>
        <a href="../logout.php" class="logout-btn">Logout</a>
      </div>
    </div>
  </div>
  
  <!-- Statistics Cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon students">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12a5 5 0 100-10 5 5 0 000 10zM4 22v-1a7 7 0 0116 0v1H4z"/>
          </svg>
        </div>
      </div>
      <div class="stat-value"><?= $stats['students'] ?></div>
      <div class="stat-label">Total Students</div>
    </div>
    
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon teachers">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3L2 9l10 6 10-6-10-6zm0 8.75L5.04 8.5 12 4.75 18.96 8.5 12 11.75zM4 13v5h2v-3.5l6 3.5 10-6v-2l-10 6-8-4.8V13z"/>
          </svg>
        </div>
      </div>
      <div class="stat-value"><?= $stats['teachers'] ?></div>
      <div class="stat-label">Total Teachers</div>
    </div>
    
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon subjects">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
          </svg>
        </div>
      </div>
      <div class="stat-value"><?= $stats['subjects'] ?></div>
      <div class="stat-label">Total Subjects</div>
    </div>
    
    <div class="stat-card">
      <div class="stat-header">
        <div class="stat-icon grades">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9 11H7v6h2v-6zm4 0h-2v6h2v-6zm4 0h-2v6h2v-6zm2-7H3v2h18V4zm0 4H3v2h18V8zm0 4H3v2h18v-2z"/>
          </svg>
        </div>
      </div>
      <div class="stat-value"><?= $stats['grades'] ?></div>
      <div class="stat-label">Total Grades</div>
    </div>
  </div>
  
  <!-- Management Modules -->
  <div class="modules-section">
    <h2 class="modules-title">Management Modules</h2>
    <div class="modules-grid">
      <a href="students.php" class="module-card">
        <div class="module-header">
          <div class="module-icon students">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 12a5 5 0 100-10 5 5 0 000 10zM4 22v-1a7 7 0 0116 0v1H4z"/>
            </svg>
          </div>
          <div class="module-info">
            <h3>Manage Students</h3>
            <p>Student Records</p>
          </div>
        </div>
        <div class="module-description">
          Add, edit, and manage student information, enrollment status, and academic records.
        </div>
      </a>
      
      <a href="teachers.php" class="module-card">
        <div class="module-header">
          <div class="module-icon teachers">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 3L2 9l10 6 10-6-10-6zm0 8.75L5.04 8.5 12 4.75 18.96 8.5 12 11.75zM4 13v5h2v-3.5l6 3.5 10-6v-2l-10 6-8-4.8V13z"/>
            </svg>
          </div>
          <div class="module-info">
            <h3>Manage Teachers</h3>
            <p>Faculty Records</p>
          </div>
        </div>
        <div class="module-description">
          Manage teacher profiles, subject assignments, and faculty information.
        </div>
      </a>
      
      <a href="subjects.php" class="module-card">
        <div class="module-header">
          <div class="module-icon subjects">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
            </svg>
          </div>
          <div class="module-info">
            <h3>Manage Subjects</h3>
            <p>Course Catalog</p>
          </div>
        </div>
        <div class="module-description">
          Create and manage subject offerings, course descriptions, and academic programs.
        </div>
      </a>
    </div>
  </div>
</div>
</body>
</html>
