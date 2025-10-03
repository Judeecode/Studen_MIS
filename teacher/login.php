<?php
require_once '../config.php';
if (is_logged_teacher()) header('Location: dashboard.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT id, password, full_name FROM teachers WHERE username = ?");
    $stmt->bind_param('s', $u);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if ($row['password'] === md5($p)) { // MD5 for old PHP
            $_SESSION['user_role'] = 'teacher';
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['full_name'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $msg = "Invalid credentials.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Teacher Login</title>
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
      height: 100vh;
      display: flex;
      overflow: hidden;
    }
    
    .login-container {
      display: flex;
      width: 100%;
      height: 100vh;
    }
    
    .logo-section {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      position: relative;
    }
    
    .logo-content {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    
    .logo-icon {
      width: 80px;
      height: 80px;
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .logo-icon::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 20px;
      z-index: 1;
    }
    
    .logo-icon svg {
      position: relative;
      z-index: 2;
      width: 32px;
      height: 32px;
      color: white;
    }
    
    .company-name {
      color: white;
      font-size: 24px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }
    
    .form-section {
      flex: 1;
      background: rgba(230, 232, 236, 0.98);
      backdrop-filter: blur(10px);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      border-left: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
    }
    
    .form-content {
      width: 100%;
      max-width: 400px;
      padding: 40px;
    }
    
    .form-header {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .welcome-text {
      color: #1f2937;
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 8px;
    }
    
    .subtitle-text {
      color: #6b7280;
      font-size: 14px;
      font-weight: 500;
      opacity: 1;
    }
    
    .form-group {
      margin-bottom: 24px;
    }
    
    .form-label {
      display: block;
      color: #1f2937;
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 10px;
    }
    
    .form-input {
      width: 100%;
      height: 52px;
      padding: 0 16px;
      border: 3px solid #6b7280;
      border-radius: 12px;
      background-color: #ffffff !important;
      font-size: 16px;
      font-weight: 700;
      color: #000000 !important;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      -webkit-text-fill-color: #000000 !important;
    }
    
    .form-input:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      background-color: #ffffff !important;
      color: #000000 !important;
    }
    
    .form-input::placeholder {
      color: #9CA3AF !important;
      opacity: 0.6;
    }
    
    .login-button {
      width: 100%;
      height: 48px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-bottom: 16px;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .login-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }
    
    .forgot-password {
      text-align: center;
    }
    
    .forgot-password a {
      color: #667eea;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color 0.2s ease;
    }
    
    .forgot-password a:hover {
      color: #764ba2;
    }
    
    .error-message {
      background-color: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #FCA5A5;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 24px;
      text-align: center;
      font-size: 14px;
    }
    
    .back-link {
      position: absolute;
      top: 20px;
      left: 20px;
      color: #374151;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      opacity: 0.7;
      transition: opacity 0.2s ease;
    }
    
    .back-link:hover {
      opacity: 1;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }
      
      .logo-section {
        flex: 0 0 200px;
        border-bottom: 1px solid #6B7280;
        border-left: none;
      }
      
      .form-section {
        flex: 1;
        border-left: none;
      }
      
      .form-content {
        padding: 20px;
      }
      
      .welcome-text {
        font-size: 28px;
      }
      
      .company-name {
        font-size: 20px;
      }
      
      .logo-icon {
        width: 50px;
        height: 50px;
      }
      
      .logo-icon svg {
        width: 28px;
        height: 28px;
      }
    }
    
    @media (max-width: 480px) {
      .form-content {
        padding: 16px;
      }
      
      .welcome-text {
        font-size: 24px;
      }
      
      .form-input, .login-button {
        height: 44px;
      }
    }
  </style>
</head>
<body>
<div class="login-container">
  <!-- Logo Section -->
  <div class="logo-section">
    <div class="logo-content">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L2 9l10 6 10-6-10-6zm0 8.75L5.04 8.5 12 4.75 18.96 8.5 12 11.75zM4 13v5h2v-3.5l6 3.5 10-6v-2l-10 6-8-4.8V13z"/>
        </svg>
      </div>
      <div class="company-name">Teacher Portal</div>
    </div>
  </div>
  
  <!-- Form Section -->
  <div class="form-section">
    <a href="../index.php" class="back-link">← Back to Home</a>
    
    <div class="form-content">
      <div class="form-header">
        <h1 class="welcome-text">Welcome</h1>
        <p class="subtitle-text">PLEASE LOGIN TO TEACHER DASHBOARD.</p>
      </div>
      
      <?php if($msg): ?>
        <div class="error-message"><?php echo htmlspecialchars($msg); ?></div>
      <?php endif; ?>
      
      <form method="post">
        <div class="form-group">
          <label class="form-label" for="username">Username</label>
          <input type="text" id="username" name="username" class="form-input" required autocomplete="username" style="color: #000000 !important;">
        </div>
        
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password" style="color: #000000 !important;">
        </div>
        
        <button type="submit" class="login-button">Login</button>
      </form>
      
      <div class="forgot-password">
        <a href="#" onclick="alert('Please contact your administrator to reset your password.'); return false;">Forgotten Your Password?</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
