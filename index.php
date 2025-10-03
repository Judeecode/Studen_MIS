<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Management System</title>
  <meta name="description" content="Student Management System – manage students, teachers, subjects, enrollments, and grades.">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    html, body {
      height: 100%;
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #1f2937;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .container {
      max-width: 1000px;
      width: 100%;
      animation: fadeInUp 0.6s ease both;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .hero-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 24px;
      padding: 50px;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      text-align: center;
      margin-bottom: 30px;
    }
    
    .logo-container {
      width: 80px;
      height: 80px;
      margin: 0 auto 24px;
      display: grid;
      place-items: center;
      border-radius: 20px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }
    
    .logo-container svg {
      width: 40px;
      height: 40px;
      color: white;
    }
    
    .hero-title {
      font-size: 42px;
      font-weight: 700;
      color: #1f2937;
      margin-bottom: 16px;
      line-height: 1.2;
    }
    
    .hero-subtitle {
      font-size: 18px;
      color: #6b7280;
      margin-bottom: 40px;
      line-height: 1.6;
    }
    
    .login-buttons {
      display: flex;
      gap: 16px;
      justify-content: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    
    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 16px 32px;
      border-radius: 14px;
      font-weight: 600;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 16px;
      gap: 10px;
      min-width: 180px;
    }
    
    .button.admin {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    
    .button.admin:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(102, 126, 234, 0.5);
    }
    
    .button.teacher {
      background: linear-gradient(135deg, #f093fb, #f5576c);
      color: white;
      box-shadow: 0 8px 20px rgba(240, 147, 251, 0.4);
    }
    
    .button.teacher:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(240, 147, 251, 0.5);
    }
    
    .button.student {
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      color: white;
      box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
    }
    
    .button.student:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 30px rgba(79, 172, 254, 0.5);
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }
    
    .feature-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease both;
    }
    
    .feature-card:nth-child(1) { animation-delay: 0.1s; }
    .feature-card:nth-child(2) { animation-delay: 0.2s; }
    .feature-card:nth-child(3) { animation-delay: 0.3s; }
    
    .feature-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .feature-icon {
      width: 60px;
      height: 60px;
      display: grid;
      place-items: center;
      margin: 0 auto 16px;
      border-radius: 16px;
      font-size: 28px;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
      border: 2px solid rgba(102, 126, 234, 0.2);
    }
    
    .feature-title {
      font-size: 20px;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 10px;
      text-align: center;
    }
    
    .feature-desc {
      font-size: 15px;
      color: #6b7280;
      line-height: 1.6;
      text-align: center;
    }
    
    .footer-text {
      text-align: center;
      color: rgba(255, 255, 255, 0.9);
      font-size: 14px;
      margin-top: 30px;
      font-weight: 500;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .hero-card {
        padding: 35px 25px;
      }
      
      .hero-title {
        font-size: 32px;
      }
      
      .hero-subtitle {
        font-size: 16px;
      }
      
      .login-buttons {
        flex-direction: column;
        align-items: stretch;
      }
      
      .button {
        width: 100%;
      }
      
      .features-grid {
        grid-template-columns: 1fr;
      }
    }
    
    @media (max-width: 480px) {
      body {
        padding: 15px;
      }
      
      .hero-card {
        padding: 30px 20px;
      }
      
      .hero-title {
        font-size: 28px;
      }
      
      .logo-container {
        width: 70px;
        height: 70px;
      }
      
      .logo-container svg {
        width: 35px;
        height: 35px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Hero Section -->
    <div class="hero-card">
      <div class="logo-container">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z" fill="currentColor" opacity="0.9"/>
          <path d="M7 12.29V16c0 1.1 2.69 2 6 2s6-.9 6-2v-3.71l-6 3.27-6-3.27z" fill="currentColor" opacity="0.7"/>
        </svg>
      </div>
      
      <h1 class="hero-title">Student Management System</h1>
      <p class="hero-subtitle">
        Manage students, teachers, subjects, enrollments, and grades<br>
        from a unified, secure dashboard.
      </p>
      
      <div class="login-buttons">
        <a class="button admin" href="admin/login.php">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
          </svg>
          Admin Login
        </a>
        <a class="button teacher" href="teacher/login.php">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
          </svg>
          Teacher Login
        </a>
        <a class="button student" href="student/login.php">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
          Student Login
        </a>
      </div>
    </div>
    
    <!-- Features Section -->
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">📚</div>
        <div class="feature-title">Unified Records</div>
        <div class="feature-desc">
          All student, teacher, subject, and grade records managed in one centralized system.
        </div>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <div class="feature-title">Fast & Efficient</div>
        <div class="feature-desc">
          Streamlined access for admins, teachers, and students with instant updates.
        </div>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">🔒</div>
        <div class="feature-title">Secure & Reliable</div>
        <div class="feature-desc">
          Role-based access control with secure authentication and audit trails.
        </div>
      </div>
    </div>
    
    <div class="footer-text">
      Choose your role above to continue
    </div>
  </div>
</body>
</html>
