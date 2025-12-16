<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="Hệ thống luyện thi THPT Quốc gia môn Tin học - Ôn tập trực tuyến với hàng ngàn câu hỏi và đề thi">
    <meta name="keywords" content="luyện thi THPT, tin học, THPT Quốc gia, ôn thi online">
    <meta name="author" content="Exam System">
    <meta name="theme-color" content="#6366f1">
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Luyện thi THPT">
    
    <!-- Security Headers (Client-side hints) -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    
    <title>🎓 Luyện thi THPT Quốc gia - Tin học</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            /* Modern Color Palette for Education */
            --primary-blue: #2563eb;
            --primary-blue-dark: #1e40af;
            --primary-blue-light: #3b82f6;
            --secondary-purple: #7c3aed;
            --secondary-purple-dark: #6d28d9;
            --accent-orange: #f59e0b;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --bg-gradient-start: #1e3a8a;
            --bg-gradient-end: #312e81;
            --text-dark: #1f2937;
            --text-light: #f9fafb;
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            --card-shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 50%, #1e293b 100%);
            background-attachment: fixed;
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text-dark);
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated background pattern */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(124, 58, 237, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(245, 158, 11, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .screen {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .screen.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .main-container {
            padding-top: 120px;
            padding-bottom: 60px;
            position: relative;
            z-index: 1;
        }
        
        /* Enhanced Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.5rem 0;
            transition: all 0.3s;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            width: 100%;
        }
        
        .navbar .container-fluid {
            max-width: 100%;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .navbar-collapse {
            flex-grow: 1;
        }
        
        .navbar-nav {
            flex-direction: row !important;
            align-items: center;
            gap: 0.25rem;
        }
        
        .navbar-nav .nav-link {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.9rem;
            white-space: nowrap;
        }
        
        .navbar-nav .nav-link i {
            font-size: 1rem;
        }
        
        .navbar-text {
            white-space: nowrap;
            margin-left: 0.5rem;
        }
        
        @media (max-width: 991px) {
            .navbar-nav {
                flex-direction: column !important;
                align-items: flex-start;
                width: 100%;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem !important;
                width: 100%;
            }
        }
        
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-blue) !important;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .navbar-brand:hover {
            color: var(--secondary-purple) !important;
            transform: scale(1.05);
        }
        
        .navbar-brand i {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-dark) !important;
            padding: 8px 16px !important;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .nav-link:hover {
            background: linear-gradient(135deg, var(--primary-blue-light), var(--secondary-purple));
            color: white !important;
            transform: translateY(-2px);
        }
        
        .nav-link i {
            font-size: 1.1rem;
        }
        
        .navbar-text {
            color: var(--text-dark) !important;
            font-weight: 600;
            padding: 8px 16px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(124, 58, 237, 0.1));
            border-radius: 20px;
            border: 2px solid var(--primary-blue-light);
        }
        
        /* Enhanced Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--secondary-purple), var(--accent-orange));
            transform: scaleX(0);
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--card-shadow-hover);
        }
        
        .card:hover::before {
            transform: scaleX(1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-purple));
            color: white;
            border: none;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .card-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        /* Enhanced Buttons */
        .btn {
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 12px;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
            z-index: -1;
        }
        
        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-blue-dark), var(--primary-blue));
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.6);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--accent-green), #059669);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        
        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--accent-orange), #d97706);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--accent-red), #dc2626);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
        }
        
        .btn-info {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4);
        }
        
        .btn-custom {
            border-radius: 25px;
            padding: 12px 32px;
            font-size: 1.05rem;
        }
        
        /* Enhanced Form Controls */
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        /* Enhanced Badges */
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .badge-custom {
            padding: 10px 20px;
            border-radius: 25px;
        }
        
        .result-badge {
            font-size: 1.3rem;
            padding: 12px 24px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Login Card */
        .login-card {
            max-width: 480px;
            margin: 50px auto;
            border-radius: 30px;
            overflow: hidden;
        }
        
        .login-card .card-body {
            padding: 3rem;
        }
        
        .login-card .display-4 {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Exam Cards */
        .exam-card {
            transition: all 0.4s;
            border: 2px solid transparent;
            height: 100%;
        }
        
        .exam-card:hover {
            border-color: var(--primary-blue) !important;
            transform: translateY(-10px) rotate(1deg);
        }
        
        /* Loading */
        .loading {
            text-align: center;
            padding: 60px;
        }
        
        .spinner-border {
            width: 4rem;
            height: 4rem;
            border-width: 4px;
        }
        
        /* Alert Float */
        .alert-float {
            position: fixed;
            top: 90px;
            right: 30px;
            z-index: 9999;
            min-width: 350px;
            animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(400px) rotate(10deg);
                opacity: 0;
            }
            to {
                transform: translateX(0) rotate(0);
                opacity: 1;
            }
        }
        
        /* Question Item */
        .question-item {
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-blue);
            transition: all 0.3s;
        }
        
        .question-item:hover {
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            transform: translateX(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Timer */
        .timer {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent-red);
            background: rgba(239, 68, 68, 0.1);
            padding: 10px 20px;
            border-radius: 15px;
            display: inline-block;
            animation: timerPulse 1s ease-in-out infinite;
        }
        
        @keyframes timerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Table Enhancements */
        .table {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table thead {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-purple));
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
        }
        
        .table tbody tr {
            transition: all 0.3s;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .table tbody tr:hover {
            background: rgba(37, 99, 235, 0.05);
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Home Hero Section */
        .display-3 {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            background: linear-gradient(135deg, white, rgba(255, 255, 255, 0.8));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: titleFloat 3s ease-in-out infinite;
        }
        
        @keyframes titleFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .lead {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 1.3rem;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-purple));
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-blue-dark), var(--secondary-purple-dark));
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .display-3 {
                font-size: 2rem;
            }
            
            .lead {
                font-size: 1rem;
            }
            
            .alert-float {
                min-width: 280px;
                right: 15px;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }
        
        /* CSS cho màn hình chọn đề thi */
        .exam-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            height: 100%;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-left-color: #0d6efd;
        }

        .exam-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .exam-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0.5rem 0;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .exam-info i {
            color: #0d6efd;
        }
        
        /* CSS cho màn hình làm bài thi */
        .question-nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
        }
        
        .question-nav-btn {
            aspect-ratio: 1;
            border: 2px solid #dee2e6;
            background: white;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .question-nav-btn:hover {
            background: #e7f3ff;
            border-color: #0d6efd;
        }
        
        .question-nav-btn.active {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        
        .question-nav-btn.answered {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .timer-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
            padding: 10px;
            background: #fff3cd;
            border-radius: 5px;
        }
        
        .timer-large {
            font-size: 1.8rem;
            font-weight: bold;
            color: #dc3545;
        }
        
        .question-container {
            padding: 20px;
        }
        
        .question-text {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .answer-option {
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .answer-option:hover {
            background: #f8f9fa;
            border-color: #0d6efd;
        }
        
        .answer-option input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 15px;
            cursor: pointer;
        }
        
        .answer-option.selected {
            background: #e7f3ff;
            border-color: #0d6efd;
            font-weight: 500;
        }
        
        .answer-label {
            font-weight: 600;
            margin-right: 10px;
            color: #0d6efd;
            font-size: 1.1rem;
        }
        
        /* CSS cho màn hình kết quả thi */
        .result-card {
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .score-display {
            padding: 20px;
        }
        
        .score-circle {
            width: 200px;
            height: 200px;
            position: relative;
            margin: 0 auto;
        }
        
        .score-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .score-text h1 {
            font-size: 3rem;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
        }
        
        .score-text p {
            margin: 0;
            color: #6c757d;
        }
        
        .stat-box {
            padding: 20px;
            border-radius: 10px;
            margin: 10px 0;
        }
        
        .stat-box i {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .stat-box h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .stat-box p {
            margin: 0;
            color: #6c757d;
        }
        
        .stat-correct {
            background: #d1f0dd;
            color: #28a745;
        }
        
        .stat-wrong {
            background: #ffc0c0;
            color: #dc3545;
        }
        
        .stat-time {
            background: #fff3cd;
            color: #ffc107;
        }
        
        .stat-percent {
            background: #e7f3ff;
            color: #0d6efd;
        }
        
        .question-review {
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #dee2e6;
            background: #f8f9fa;
        }
        
        .question-review.correct {
            border-left-color: #28a745;
            background: #d1f0dd;
        }
        
        .question-review.wrong {
            border-left-color: #dc3545;
            background: #ffc0c0;
        }
        
        .answer-review {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        
        .answer-review.correct-answer {
            background: #d1f0dd;
            border: 2px solid #28a745;
            font-weight: bold;
        }
        
        .answer-review.user-wrong {
            background: #ffc0c0;
            border: 2px solid #dc3545;
        }
        
        /* Dashboard Stat Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.7;
        }
        
        .stat-card.stat-users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-card.stat-exams {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .stat-card.stat-submissions {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .stat-card.stat-questions {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
            }
            
            .navbar, .btn, .alert-float {
                display: none;
            }
        }
        
        /* Modal Fix - Ensure proper z-index and scrolling */
        .modal {
            z-index: 9999 !important;
        }
        
        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.5) !important;
        }
        
        .modal-backdrop {
            display: none !important; /* Hide default backdrop */
        }
        
        .modal-dialog {
            margin: 1.75rem auto;
            z-index: 10000 !important;
            position: relative;
        }
        
        .modal-content {
            position: relative;
            z-index: 10001 !important;
        }
        
        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        /* Fix for modal scroll */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }
        
        /* Ensure modal hides completely */
        .modal:not(.show) {
            display: none !important;
        }
        
        /* Answer option styling */
        .answer-option {
            transition: all 0.3s ease;
            cursor: default;
        }
        
        .answer-option:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        /* Answer box styling - nền sáng */
        .answer-box {
            transition: all 0.3s ease;
            cursor: default;
        }
        
        .answer-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .answer-box.correct-answer {
            animation: pulse-green 2s infinite;
        }
        
        @keyframes pulse-green {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
            }
        }
        
        /* Exam Card Hover Effect */
        .exam-card-hover {
            cursor: pointer;
        }
        
        .exam-card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        }
        
        .exam-card-hover .card-header {
            transition: all 0.3s ease;
            /* Hiển thị gradient màu đẹp mặc định, không cần hover */
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%) !important;
        }
        
        .exam-card-hover:hover .card-header {
            /* Khi hover, làm sáng hơn một chút */
            background: linear-gradient(135deg, #8b5fc7 0%, #7b8ef5 100%) !important;
        }
    </style>
</head>
<body>
    <!-- Global Loading Spinner -->
    <div id="globalLoader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div class="spinner-border text-light" role="status" style="width:4rem; height:4rem;">
            <span class="visually-hidden">Đang tải...</span>
        </div>
    </div>

    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:10000;">
        <div id="globalToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-info-circle me-2" id="toastIcon"></i>
                <strong class="me-auto" id="toastTitle">Thông báo</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="toastBody">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#" onclick="app.showScreen('home')">
                <i class="bi bi-mortarboard-fill"></i>
                <span>Luyện thi THPT Quốc gia</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Guest Menu -->
                <ul class="navbar-nav ms-auto" id="guestMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('dethimau')">
                            <i class="bi bi-file-earmark-text"></i> Đề thi mẫu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('register')">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('login')">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </a>
                    </li>
                </ul>
                
                <!-- Student Menu -->
                <ul class="navbar-nav ms-auto d-none" id="studentMenu">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('chondetthi'); app.closeNavbar();">
                            <i class="bi bi-grid-3x3-gap"></i> Danh sách đề thi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('lichsuthi'); app.closeNavbar();">
                            <i class="bi bi-clock-history"></i> Lịch sử thi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.showScreen('thongkecanhan'); app.closeNavbar();">
                            <i class="bi bi-bar-chart"></i> Thống kê cá nhân
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="app.logout(); app.closeNavbar();">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
                
                <!-- Teacher Menu -->
<ul class="navbar-nav ms-auto d-none" id="teacherMenu">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('quanlycauhoi')">
                            <i class="bi bi-bank"></i> Ngân hàng câu hỏi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('danhsachdetthi')">
                            <i class="bi bi-list-task"></i> Danh sách đề thi
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-nowrap dropdown-toggle" href="#" id="createExamDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-file-earmark-plus"></i> Tạo đề thi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="createExamDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#taoDeNgauNhienModal">
                                <i class="bi bi-shuffle"></i> Tạo đề ngẫu nhiên
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="app.showScreen('taodethucong')">
                                <i class="bi bi-ui-checks"></i> Tạo đề thủ công
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('thongkelop')">
                            <i class="bi bi-graph-up-arrow"></i> Thống kê lớp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.logout()">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
                
                <!-- Admin Menu -->
                <ul class="navbar-nav ms-auto d-none" id="adminMenu">
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('dashboard')">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('quanlynguoidung')">
                            <i class="bi bi-people"></i> Quản lý người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('backup')">
                            <i class="bi bi-database"></i> Backup
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.showScreen('monitoring')">
                            <i class="bi bi-speedometer2"></i> Giám sát
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-nowrap" href="#" onclick="app.logout()">
                            <i class="bi bi-box-arrow-right"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid main-container">
        <!-- Home Screen - Modern Hero -->
        <div id="homeScreen" class="screen active">
            <div class="text-center text-white px-4">
                <div class="mb-5">
                    <h1 class="display-3 mb-3" style="font-size: 3.5rem;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </h1>
                    <h2 class="display-3 mb-4">
                        Hệ thống Luyện thi THPT Quốc gia
                    </h2>
                    <p class="lead mb-2">📚 Môn Tin học - Ôn luyện và Kiểm tra trực tuyến</p>
                    <p class="text-white-50" style="font-size: 1.1rem;">
                        Chuẩn bị tốt nhất cho kỳ thi THPT Quốc gia với hệ thống bài tập đa dạng và phong phú
                    </p>
                </div>
                
                <div class="row justify-content-center g-4">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <i class="bi bi-file-earmark-text" style="font-size: 4rem; background: linear-gradient(135deg, #2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-3">📝 Đề thi mẫu</h5>
                                <p class="card-text text-muted">Truy cập kho đề thi mẫu phong phú, cập nhật liên tục theo cấu trúc mới nhất</p>
                                <button class="btn btn-primary btn-custom mt-3 w-100" onclick="app.showScreen('dethimau')">
                                    <i class="bi bi-arrow-right-circle"></i> Xem ngay
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <i class="bi bi-person-check" style="font-size: 4rem; background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-3">🔐 Đăng nhập</h5>
                                <p class="card-text text-muted">Đăng nhập để làm bài thi, xem kết quả và theo dõi tiến trình học tập</p>
                                <button class="btn btn-success btn-custom mt-3 w-100" onclick="app.showScreen('login')">
                                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập ngay
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <i class="bi bi-graph-up-arrow" style="font-size: 4rem; background: linear-gradient(135deg, #f59e0b, #d97706); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-3">📊 Thống kê</h5>
                                <p class="card-text text-muted">Theo dõi quá trình học tập, phân tích điểm mạnh và cải thiện điểm yếu</p>
                                <button class="btn btn-warning btn-custom mt-3 w-100" onclick="app.showScreen('login')">
                                    <i class="bi bi-bar-chart"></i> Xem thống kê
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center p-4">
                                <div class="mb-4">
                                    <i class="bi bi-trophy" style="font-size: 4rem; background: linear-gradient(135deg, #ef4444, #dc2626); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-3">🏆 Thành tích</h5>
                                <p class="card-text text-muted">Theo dõi thành tích, xếp hạng và nhận phần thưởng khi hoàn thành mục tiêu</p>
                                <button class="btn btn-danger btn-custom mt-3 w-100" onclick="app.showScreen('login')">
                                    <i class="bi bi-award"></i> Xem thành tích
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Section -->
                <div class="row justify-content-center mt-5 pt-4">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white">
                            <h2 class="display-4 fw-bold mb-0">1000+</h2>
                            <p class="text-white-50">Câu hỏi</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white">
                            <h2 class="display-4 fw-bold mb-0">50+</h2>
                            <p class="text-white-50">Đề thi</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white">
                            <h2 class="display-4 fw-bold mb-0">5000+</h2>
                            <p class="text-white-50">Học sinh</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-white">
                            <h2 class="display-4 fw-bold mb-0">98%</h2>
                            <p class="text-white-50">Hài lòng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Screen (Admin) - NEW -->
        <div id="dashboardScreen" class="screen">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-center mb-4 text-white">
                            <i class="bi bi-speedometer2"></i> Bảng điều khiển
                        </h2>
                    </div>
                </div>
                
                <!-- Summary Stats Row -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card stat-users">
                            <div class="card-body text-center">
                                <i class="bi bi-people-fill stat-icon"></i>
                                <h4 class="mt-3" id="totalUsersCount">0</h4>
                                <p class="text-muted mb-0">Tổng người dùng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card stat-exams">
                            <div class="card-body text-center">
                                <i class="bi bi-file-earmark-text-fill stat-icon"></i>
                                <h4 class="mt-3" id="totalExamsCount">0</h4>
                                <p class="text-muted mb-0">Tổng đề thi</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card stat-submissions">
                            <div class="card-body text-center">
                                <i class="bi bi-send-check-fill stat-icon"></i>
                                <h4 class="mt-3" id="totalSubmissionsCount">0</h4>
                                <p class="text-muted mb-0">Tổng bài nộp</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card stat-questions">
                            <div class="card-body text-center">
                                <i class="bi bi-question-circle-fill stat-icon"></i>
                                <h4 class="mt-3" id="totalQuestionsCount">0</h4>
                                <p class="text-muted mb-0">Tổng câu hỏi</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <div class="col-lg-8 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-graph-up"></i> Hoạt động theo tháng
                            </div>
                            <div class="card-body">
                                <canvas id="activityChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-pie-chart"></i> Phân bố người dùng
                            </div>
                            <div class="card-body">
                                <canvas id="userRoleChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <i class="bi bi-clock-history"></i> Bài thi gần đây
                            </div>
                            <div class="card-body">
                                <div id="recentSubmissionsTable" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <i class="bi bi-exclamation-triangle"></i> Cảnh báo hệ thống
                            </div>
                            <div class="card-body">
                                <div id="systemAlertsTable" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Statistics Row (UR-04.3 Enhancement) -->
                <div class="row mb-4">
                    <div class="col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-trophy"></i> Top 5 Học sinh xuất sắc
                            </div>
                            <div class="card-body">
                                <div id="topStudentsTable" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-graph-up-arrow"></i> Thống kê nhanh
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                        <strong>Điểm trung bình:</strong>
                                        <span id="avgScore" class="text-primary">0</span>/10
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-percent text-info"></i>
                                        <strong>Tỷ lệ hoàn thành:</strong>
                                        <span id="completionRate" class="text-primary">0%</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-person-check text-success"></i>
                                        <strong>Học sinh đạt ≥ 5 điểm:</strong>
                                        <span id="passCount" class="text-primary">0</span>
                                    </li>
                                    <li class="mb-2">
                                        <i class="bi bi-alarm text-warning"></i>
                                        <strong>Thời gian TB/bài:</strong>
                                        <span id="avgTime" class="text-primary">0 phút</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-octagon"></i> Phát hiện gian lận
                            </div>
                            <div class="card-body">
                                <div id="cheatingDetectionTable" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Will be populated by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button class="btn btn-primary me-2" onclick="app.showScreen('quanlynguoidung')">
                            <i class="bi bi-people"></i> Quản lý người dùng
                        </button>
                        <button class="btn btn-success me-2" onclick="app.showScreen('quanlycauhoi')">
                            <i class="bi bi-bank"></i> Quản lý Ngân hàng câu hỏi
                        </button>
                        <button class="btn btn-info me-2" onclick="app.loadDashboard()">
                            <i class="bi bi-arrow-clockwise"></i> Làm mới dữ liệu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Screen - Enhanced -->
        <div id="loginScreen" class="screen">
            <div class="card login-card">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">
                        <i class="bi bi-person-circle display-4 d-block mb-3"></i>
                        Đăng nhập
                    </h3>
                    <form id="loginForm" onsubmit="app.login(event)">
                        <div class="mb-3">
                            <label class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="loginUsername" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="loginPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-custom">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </button>
                        <div class="mt-3 text-center">
                            <a href="#" onclick="app.showScreen('forgotPassword')" class="text-muted">
                                <i class="bi bi-key"></i> Quên mật khẩu?
                            </a>
                            <span class="mx-2 text-muted">|</span>
                            <a href="#" onclick="app.showScreen('register')" class="text-primary">
                                <i class="bi bi-person-plus"></i> Đăng ký tài khoản
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Register Screen (UR-01.2) - NEW -->
        <div id="registerScreen" class="screen">
            <div class="card login-card">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">
                        <i class="bi bi-person-plus-fill display-4 d-block mb-3"></i>
                        Đăng ký tài khoản
                    </h3>
                    <form id="registerForm" onsubmit="app.register(event)">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên đăng nhập *</label>
                                <input type="text" class="form-control" name="TenDangNhap" required minlength="3">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="Email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mật khẩu *</label>
                                <input type="password" class="form-control" name="MatKhau" required minlength="6">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ tên *</label>
                                <input type="text" class="form-control" name="HoTen" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lớp (tùy chọn)</label>
                                <input type="text" class="form-control" name="Lop" placeholder="VD: 12A1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trường (tùy chọn)</label>
                                <input type="text" class="form-control" name="Truong" placeholder="VD: THPT Nguyễn Huệ">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-custom">
                            <i class="bi bi-check-circle"></i> Đăng ký
                        </button>
                        <div class="mt-3 text-center">
                            <a href="#" onclick="app.showScreen('login')" class="text-muted">
                                <i class="bi bi-arrow-left"></i> Đã có tài khoản? Đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Forgot Password Screen (UR-01.3) - NEW -->
        <div id="forgotPasswordScreen" class="screen">
            <div class="card login-card">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">
                        <i class="bi bi-key-fill display-4 d-block mb-3"></i>
                        Quên mật khẩu
                    </h3>
                    <p class="text-muted text-center mb-4">
                        Nhập email đã đăng ký, chúng tôi sẽ gửi mã khôi phục cho bạn
                    </p>
                    <form id="forgotPasswordForm" onsubmit="app.forgotPassword(event)">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="Email" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-custom">
                            <i class="bi bi-send"></i> Gửi mã khôi phục
                        </button>
                        <div class="mt-3 text-center">
                            <a href="#" onclick="app.showScreen('login')" class="text-muted">
                                <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reset Password Screen (UR-01.3) - NEW -->
        <div id="resetPasswordScreen" class="screen">
            <div class="card login-card">
                <div class="card-body p-5">
                    <h3 class="card-title text-center mb-4">
                        <i class="bi bi-shield-lock-fill display-4 d-block mb-3"></i>
                        Đặt lại mật khẩu
                    </h3>
                    <form id="resetPasswordForm" onsubmit="app.resetPassword(event)">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="Email" id="resetEmail" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mã khôi phục (6 chữ số)</label>
                            <input type="text" class="form-control" name="ResetCode" required pattern="\d{6}" placeholder="123456">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" name="MatKhauMoi" id="newPassword" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" name="XacNhanMatKhau" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-custom">
                            <i class="bi bi-check-circle"></i> Đặt lại mật khẩu
                        </button>
                        <div class="mt-3 text-center">
                            <a href="#" onclick="app.showScreen('login')" class="text-muted">
                                <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Đề thi mẫu Screen (Guest) -->
        <div id="dethimauScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-file-earmark-text"></i> Đề thi mẫu
                </h2>
                <div id="dethimauContent" class="row"></div>
            </div>
        </div>

        <!-- Lịch sử thi Screen (Student) -->
        <div id="lichsuthiScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-clock-history"></i> Lịch sử làm bài
                </h2>
                <div class="card">
                    <div class="card-body">
                        <div id="lichsuthiContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê cá nhân Screen (Student) -->
        <div id="thongkecanhanScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-bar-chart-fill"></i> Thống kê tiến độ cá nhân
                </h2>
                
                <!-- Tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-file-earmark-check" style="font-size: 2rem; color: #667eea;"></i>
                                <h3 class="mt-2 mb-0" id="tongSoBaiLam">0</h3>
                                <p class="text-muted mb-0">Tổng bài làm</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-trophy-fill" style="font-size: 2rem; color: #f59e0b;"></i>
                                <h3 class="mt-2 mb-0" id="diemTrungBinh">0</h3>
                                <p class="text-muted mb-0">Điểm trung bình</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-check-circle-fill" style="font-size: 2rem; color: #10b981;"></i>
                                <h3 class="mt-2 mb-0" id="tiLeDung">0%</h3>
                                <p class="text-muted mb-0">Tỷ lệ đúng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-star-fill" style="font-size: 2rem; color: #ef4444;"></i>
                                <h3 class="mt-2 mb-0" id="diemCaoNhat">0</h3>
                                <p class="text-muted mb-0">Điểm cao nhất</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Biểu đồ -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Biểu đồ điểm số theo thời gian</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartDiemSo" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Tỷ lệ đúng/sai</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartTyLe" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Phân tích theo chuyên đề -->
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-book-half"></i> Phân tích theo chuyên đề</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartChuyenDe" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chọn đề thi Screen (Student) - UR-02.1 -->
        <div id="chondetthiScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-grid-3x3-gap"></i> Danh sách đề thi
                </h2>
                
                <!-- Bộ lọc -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-search"></i> Tìm kiếm</label>
                                <input type="text" class="form-control" id="searchExam" placeholder="Nhập tên đề thi...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><i class="bi bi-sort-down"></i> Sắp xếp</label>
                                <select class="form-select" id="sortExam">
                                    <option value="newest">Mới nhất</option>
                                    <option value="oldest">Cũ nhất</option>
                                    <option value="name">Tên A-Z</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button class="btn btn-primary w-100" onclick="app.loadDanhSachDeThi()">
                                    <i class="bi bi-arrow-clockwise"></i> Làm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách đề thi dạng card -->
                <div id="examListContent" class="row g-4">
                    <!-- Sẽ được load bằng JavaScript -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <p class="text-white mt-3">Đang tải danh sách đề thi...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Làm bài thi Screen (Student) - UR-02.2 FULL IMPLEMENTATION -->
        <div id="lambaithiScreen" class="screen">
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar: Question Navigation -->
                    <div class="col-md-3">
                        <div class="card sticky-top" style="top: 90px;">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-list-ol"></i> Danh sách câu hỏi
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <div id="questionNavigator" class="question-nav-grid">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                            <div class="card-footer">
                                <div id="examTimer" class="timer-display text-center">
                                    <i class="bi bi-clock"></i>
                                    <span id="timerText">00:00:00</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div id="progressBar" class="progress-bar bg-success" style="width: 0%"></div>
                                </div>
                                <small class="text-muted d-block text-center mt-1">
                                    <span id="answeredCount">0</span>/<span id="totalQuestions">0</span> câu
                                </small>
                            </div>
                        </div>
                        
                        <!-- Auto-save indicator -->
                        <div id="autoSaveIndicator" class="alert alert-sm mt-2 d-none">
                            <i class="bi bi-check-circle"></i> Đã lưu tự động
                        </div>
                    </div>

                    <!-- Main: Question Content -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0" id="examTitleDisplay">
                                        <i class="bi bi-file-earmark-text"></i> 
                                        <span id="examTitle"></span>
                                    </h5>
                                    <div id="timerDisplay" class="timer-large">
                                        <i class="bi bi-clock-fill text-danger"></i>
                                        <span id="mainTimer" class="fw-bold">00:00:00</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body" id="questionContent" style="min-height: 400px;">
                                <!-- Question will be displayed here -->
                                <div class="text-center text-muted py-5">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                    <p class="mt-3">Đang tải câu hỏi...</p>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-secondary" onclick="app.prevQuestion()" id="btnPrev">
                                        <i class="bi bi-chevron-left"></i> Câu trước
                                    </button>
                                    <button class="btn btn-danger" onclick="app.showSubmitConfirm()">
                                        <i class="bi bi-send"></i> Nộp bài
                                    </button>
                                    <button class="btn btn-secondary" onclick="app.nextQuestion()" id="btnNext">
                                        Câu sau <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warning Alert for Cheating -->
                        <div id="cheatingWarning" class="alert alert-warning mt-3 d-none">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Cảnh báo:</strong> Hệ thống đã ghi nhận <span id="cheatingCount">0</span> lần vi phạm (chuyển tab/cửa sổ).
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Xác nhận nộp bài -->
        <div class="modal fade" id="submitConfirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle"></i> Xác nhận nộp bài
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-info-circle"></i> Bạn có chắc chắn muốn nộp bài?
                        </div>
                        <div id="submitSummary">
                            <p><strong>Thống kê:</strong></p>
                            <ul>
                                <li>Đã làm: <span id="submitAnswered">0</span> câu</li>
                                <li>Chưa làm: <span id="submitUnanswered">0</span> câu</li>
                                <li>Thời gian còn lại: <span id="submitTimeLeft">00:00</span></li>
                            </ul>
                        </div>
                        <p class="text-danger"><strong>Lưu ý:</strong> Sau khi nộp bài, bạn không thể sửa đổi câu trả lời!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" onclick="app.submitExam()">
                            <i class="bi bi-send"></i> Xác nhận nộp bài
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kết quả thi Screen (Student) - UR-02.3 & UR-02.4 -->
        <div id="ketquaScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4 text-center">
                    <i class="bi bi-trophy"></i> Kết quả bài thi
                </h2>

                <div class="row">
                    <!-- Score Card -->
                    <div class="col-md-4">
                        <div class="card text-center result-card">
                            <div class="card-body">
                                <div class="score-display">
                                    <div class="score-circle">
                                        <canvas id="scoreChart"></canvas>
                                        <div class="score-text">
                                            <h1 id="finalScore">0</h1>
                                            <p>điểm</p>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mt-3" id="resultTitle">Kết quả</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Thống kê chi tiết</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <div class="stat-box stat-correct">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <h3 id="correctCount">0</h3>
                                            <p>Đúng</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-box stat-wrong">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <h3 id="wrongCount">0</h3>
                                            <p>Sai</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-box stat-time">
                                            <i class="bi bi-clock-fill"></i>
                                            <h3 id="timeTaken">0</h3>
                                            <p>Thời gian</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-box stat-percent">
                                            <i class="bi bi-percent"></i>
                                            <h3 id="percentCorrect">0%</h3>
                                            <p>Tỷ lệ đúng</p>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="mt-3">
                                    <h6>Thông tin bài thi</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="150"><strong>Đề thi:</strong></td>
                                            <td id="resultExamName">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày làm:</strong></td>
                                            <td id="resultDate">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Thời gian làm:</strong></td>
                                            <td id="resultDuration">-</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button class="btn btn-primary btn-lg" onclick="app.showDetailedResults()">
                                        <i class="bi bi-eye"></i> Xem chi tiết đáp án
                                    </button>
                                    <button class="btn btn-success" onclick="app.showScreen('chonDeThi')">
                                        <i class="bi bi-arrow-repeat"></i> Làm bài khác
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Chi tiết đáp án -->
        <div class="modal fade" id="detailedResultModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-list-check"></i> Chi tiết đáp án
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="detailedResultContent" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Will be populated by JavaScript -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê tiến độ Screen (Student) - UR-02.5 -->
        <div id="thongkeScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4 text-center">
                    <i class="bi bi-graph-up"></i> Thống kê tiến độ học tập
                </h2>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center stats-card">
                            <div class="card-body">
                                <i class="bi bi-trophy-fill text-warning" style="font-size: 2rem;"></i>
                                <h3 id="totalExamsDone">0</h3>
                                <p class="text-muted">Bài thi đã làm</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center stats-card">
                            <div class="card-body">
                                <i class="bi bi-star-fill text-success" style="font-size: 2rem;"></i>
                                <h3 id="avgScore">0</h3>
                                <p class="text-muted">Điểm trung bình</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center stats-card">
                            <div class="card-body">
                                <i class="bi bi-graph-up-arrow text-primary" style="font-size: 2rem;"></i>
                                <h3 id="highestScore">0</h3>
                                <p class="text-muted">Điểm cao nhất</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center stats-card">
                            <div class="card-body">
                                <i class="bi bi-percent text-info" style="font-size: 2rem;"></i>
                                <h3 id="avgAccuracy">0%</h3>
                                <p class="text-muted">Độ chính xác</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Biểu đồ điểm số theo thời gian</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="scoreTimeChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Phân bố kết quả</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="resultPieChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 2 -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Phân tích theo chủ đề</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="subjectBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Lịch sử làm bài gần đây</h5>
                            </div>
                            <div class="card-body">
                                <div id="recentExamsTable" style="max-height: 300px; overflow-y: auto;">
                                    <!-- Table will be populated -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <button class="btn btn-primary btn-lg" onclick="app.showScreen('chonDeThi')">
                        <i class="bi bi-pencil-square"></i> Làm bài thi mới
                    </button>
                    <button class="btn btn-success btn-lg" onclick="app.loadThongKe()">
                        <i class="bi bi-arrow-clockwise"></i> Làm mới dữ liệu
                    </button>
                </div>
            </div>
        </div>

        <!-- Quản lý Ngân hàng câu hỏi Screen (Teacher) -->
        <div id="quanlycauhoiScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-bank"></i> Quản lý Ngân hàng câu hỏi
                </h2>
                
                <!-- Action Buttons -->
                <div class="mb-3">
                    <button class="btn btn-success me-2" onclick="app.showAddQuestionForm()">
                        <i class="bi bi-plus-circle"></i> Thêm câu hỏi mới
                    </button>
                    <button class="btn btn-primary me-2" onclick="app.toggleImportForm()">
                        <i class="bi bi-upload"></i> Import từ file
                    </button>
                    <div class="btn-group me-2" role="group">
                        <button class="btn btn-info" onclick="app.exportQuestions('csv')">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Xuất CSV
                        </button>
                        <button class="btn btn-danger" onclick="app.exportQuestions('pdf')">
                            <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                        </button>
                    </div>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#taoDeNgauNhienModal">
                        <i class="bi bi-shuffle"></i> Tạo đề ngẫu nhiên
                    </button>
                </div>
                
                <!-- Thêm câu hỏi thủ công Card -->
                <div class="card mb-3 d-none" id="addQuestionCard">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle"></i> Thêm câu hỏi mới
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="addQuestionForm" onsubmit="app.addQuestion(event)">
                            <div class="mb-3">
                                <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="questionContent" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đáp án A <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="answerA" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đáp án B <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="answerB" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đáp án C <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="answerC" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Đáp án D <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="answerD" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                    <select class="form-select" id="correctAnswer" required>
                                        <option value="">-- Chọn --</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Môn học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="questionSubject" value="TIN" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Độ khó <span class="text-danger">*</span></label>
                                    <select class="form-select" id="questionDifficulty" required>
                                        <option value="de">Dễ</option>
                                        <option value="trungbinh" selected>Trung bình</option>
                                        <option value="kho">Khó</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Lưu câu hỏi
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="app.hideAddQuestionForm()">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Import từ file Card -->
                <div class="card mb-3 d-none" id="importCard">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-upload"></i> Import câu hỏi từ file
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="importForm" onsubmit="app.importQuestions(event)">
                            <div class="mb-3">
                                <label class="form-label">Chọn file Excel/CSV</label>
                                <input type="file" class="form-control" id="importFile" accept=".xlsx,.xls,.csv" required>
                                <div class="form-text">
                                    Định dạng file: NoiDung, DapAn1, DapAn2, DapAn3, DapAn4, DapAnDung (A/B/C/D), DoKho, MaMon
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Import câu hỏi
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="app.toggleImportForm()">
                                    <i class="bi bi-x-circle"></i> Đóng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Danh sách câu hỏi -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Danh sách câu hỏi</h5>
                        <div id="questionListContent">
                            <p class="text-muted">Đang tải danh sách câu hỏi...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tạo đề thi Screen (Teacher) -->
        <div id="taodetthiScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-file-earmark-plus"></i> Tạo đề thi mới
                </h2>
                <div class="card">
                    <div class="card-body">
                        <form id="createExamForm" onsubmit="app.createExam(event)">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên đề thi</label>
                                    <input type="text" class="form-control" id="examName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Môn học</label>
                                    <input type="text" class="form-control" id="examSubject" value="Tin học" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Thời gian (phút)</label>
                                    <input type="number" class="form-control" id="examDuration" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số câu hỏi</label>
                                    <input type="number" class="form-control" id="examQuestions" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Độ khó</label>
                                    <select class="form-select" id="examDifficulty">
                                        <option value="de">Dễ</option>
                                        <option value="trungbinh" selected>Trung bình</option>
                                        <option value="kho">Khó</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-custom">
                                <i class="bi bi-plus-circle"></i> Tạo đề thi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tạo đề thi THỦ CÔNG Screen (Teacher) -->
        <div id="taodethucongScreen" class="screen">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-white mb-4">
                            <i class="bi bi-ui-checks"></i> Tạo đề thi thủ công
                        </h2>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Danh sách câu hỏi (bên trái) -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-check"></i> Chọn câu hỏi 
                                    <span class="badge bg-light text-dark ms-2" id="totalQuestionsAvailable">0</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Bộ lọc -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Ngân hàng câu hỏi</label>
                                        <select class="form-select" id="filterQuestionBank" onchange="app.filterManualQuestions()">
                                            <option value="">Tất cả</option>
                                            <option value="NH001">Tin học đại cương</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Độ khó</label>
                                        <select class="form-select" id="filterDifficulty" onchange="app.filterManualQuestions()">
                                            <option value="">Tất cả</option>
                                            <option value="De">Dễ</option>
                                            <option value="TB">Trung bình</option>
                                            <option value="Kho">Khó</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-info w-100" onclick="app.loadManualQuestions()">
                                            <i class="bi bi-arrow-clockwise"></i> Làm mới
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Danh sách câu hỏi với checkbox -->
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-hover table-sm">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 5%">
                                                    <input type="checkbox" id="selectAllQuestions" onchange="app.toggleSelectAll()">
                                                </th>
                                                <th style="width: 10%">Mã</th>
                                                <th style="width: 50%">Nội dung</th>
                                                <th style="width: 10%">Đáp án</th>
                                                <th style="width: 15%">Độ khó</th>
                                                <th style="width: 10%">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="manualQuestionList">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                                    Đang tải...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar: Câu đã chọn & Form (bên phải) -->
                    <div class="col-md-4">
                        <!-- Câu đã chọn -->
                        <div class="card mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-check2-square"></i> Câu đã chọn 
                                    <span class="badge bg-light text-dark" id="selectedCount">0</span>
                                </h6>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <div id="selectedQuestionsList" class="list-group list-group-flush">
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mb-0">Chưa chọn câu nào</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form thông tin đề thi -->
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle"></i> Thông tin đề thi
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="manualExamForm" onsubmit="app.createManualExam(event)">
                                    <div class="mb-3">
                                        <label class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="manualExamName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Môn học <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="manualExamSubject" value="Tin học" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Thời gian (phút) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="manualExamDuration" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea class="form-control" id="manualExamDescription" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" id="createManualExamBtn" disabled>
                                        <i class="bi bi-check-circle"></i> Tạo đề thi (<span id="btnSelectedCount">0</span> câu)
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danh sách đề thi Screen (Teacher) -->
        <div id="danhsachdetthiScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-list-task"></i> Danh sách đề thi của tôi
                </h2>

                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đề</th>
                                        <th>Tên đề thi</th>
                                        <th>Chủ đề</th>
                                        <th>Số câu</th>
                                        <th>Thời gian</th>
                                        <th>Ngày tạo</th>
                                        <th>Lượt làm</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="examListTable">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Đang tải...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết đề thi Modal -->
        <div class="modal fade" id="examDetailModal" tabindex="-1" aria-labelledby="examDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="examDetailModalLabel">
                            <i class="bi bi-file-earmark-text"></i> Chi tiết đề thi
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="examDetailBody" style="max-height: 70vh; overflow-y: auto;">
                        <!-- Nội dung chi tiết sẽ được load bằng JavaScript -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Đang tải...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thống kê lớp học Screen (Teacher) - UR-03.5 -->
        <div id="thongkelopScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-graph-up-arrow"></i> Thống kê lớp học
                </h2>

                <!-- Cards tổng quan -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0" id="statTotalStudents">0</h3>
                                <p class="text-muted mb-0">Tổng học sinh</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-info">
                            <div class="card-body">
                                <i class="bi bi-star-fill text-info" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0" id="statAverageScore">0</h3>
                                <p class="text-muted mb-0">Điểm trung bình</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-success">
                            <div class="card-body">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0" id="statPassRate">0%</h3>
                                <p class="text-muted mb-0">Tỷ lệ đạt</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center border-warning">
                            <div class="card-body">
                                <i class="bi bi-clipboard-check-fill text-warning" style="font-size: 2rem;"></i>
                                <h3 class="mt-2 mb-0" id="statTotalExams">0</h3>
                                <p class="text-muted mb-0">Tổng bài thi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Top 5 học sinh giỏi -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <i class="bi bi-trophy-fill"></i> Top 5 học sinh giỏi nhất
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên học sinh</th>
                                                <th>Điểm TB</th>
                                                <th>Số bài thi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="topStudentsTable">
                                            <tr><td colspan="4" class="text-center">Chưa có dữ liệu</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top 5 học sinh yếu -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-exclamation-triangle-fill"></i> Top 5 học sinh cần hỗ trợ
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tên học sinh</th>
                                                <th>Điểm TB</th>
                                                <th>Số bài thi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="weakStudentsTable">
                                            <tr><td colspan="4" class="text-center">Chưa có dữ liệu</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biểu đồ phân bố điểm -->
                <div class="row g-4 mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-bar-chart-fill"></i> Phân bố điểm số
                            </div>
                            <div class="card-body">
                                <canvas id="scoreDistributionChart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng chi tiết tất cả học sinh -->
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <i class="bi bi-table"></i> Chi tiết toàn bộ học sinh
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên học sinh</th>
                                                <th>Email</th>
                                                <th>Điểm TB</th>
                                                <th>Điểm cao nhất</th>
                                                <th>Điểm thấp nhất</th>
                                                <th>Số bài thi</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody id="allStudentsTable">
                                            <tr><td colspan="8" class="text-center">Đang tải dữ liệu...</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quản lý người dùng Screen (Admin) -->
        <div id="quanlynguoidungScreen" class="screen">
            <div class="container">
                <h2 class="text-white mb-4">
                    <i class="bi bi-people"></i> Quản lý người dùng
                </h2>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Danh sách người dùng</h5>
                            <button class="btn btn-primary btn-sm" onclick="app.showCreateUserModal()">
                                <i class="bi bi-plus-circle"></i> Thêm người dùng
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lọc theo vai trò</label>
                            <select class="form-select" id="roleFilter" onchange="app.loadUsers()">
                                <option value="">Tất cả</option>
                                <option value="hocsinh">Học sinh</option>
                                <option value="giaovien">Giáo viên</option>
                                <option value="admin">Quản trị viên</option>
                            </select>
                        </div>
                        <div id="usersContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backup Screen (Admin) -->
        <div id="backupScreen" class="screen">
            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-12">
                        <h2 class="text-center mb-4 text-white">
                            <i class="bi bi-database"></i> Quản lý Backup & Restore
                        </h2>
                    </div>
                </div>
                
                <!-- Action Cards -->
                <div class="row mb-4">
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-download"></i> Backup Database
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    Tạo bản sao lưu đầy đủ của toàn bộ dữ liệu hệ thống, bao gồm:
                                </p>
                                <ul>
                                    <li>Người dùng và tài khoản</li>
                                    <li>Câu hỏi và đề thi</li>
                                    <li>Lịch sử thi và kết quả</li>
                                    <li>Cấu hình hệ thống</li>
                                </ul>
                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#backupModal">
                                    <i class="bi bi-download"></i> Tạo Backup ngay
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-danger text-white">
                                <i class="bi bi-upload"></i> Restore Database
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    Khôi phục dữ liệu từ file backup đã lưu trước đó.
                                </p>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Cảnh báo:</strong> Dữ liệu hiện tại sẽ bị ghi đè!
                                </div>
                                <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#restoreModal">
                                    <i class="bi bi-upload"></i> Restore từ file
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Backup History -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <i class="bi bi-clock-history"></i> Lịch sử Backup
                            </div>
                            <div class="card-body">
                                <div id="backupHistoryTable">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Thời gian</th>
                                                <th>Dung lượng</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="backupHistoryBody">
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    Đang tải lịch sử...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCREEN: System Monitoring (Giám sát hệ thống) -->
    <div id="monitoringScreen" class="screen">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-white"><i class="bi bi-speedometer2"></i> Giám Sát Hệ Thống</h2>
                        <div>
                            <span class="badge bg-success me-2" id="lastUpdateTime">Đang tải...</span>
                            <button class="btn btn-primary" onclick="app.loadMonitoring()">
                                <i class="bi bi-arrow-clockwise"></i> Làm mới
                            </button>
                        </div>
                    </div>

                    <!-- Metrics Cards Row 1 -->
                    <div class="row g-4 mb-4">
                        <!-- Card: Người dùng trực tuyến -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Online</h6>
                                            <h2 class="mb-0 text-success" id="onlineUsers">-</h2>
                                        </div>
                                        <div class="fs-1 text-success">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted">Đang trực tuyến</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Tổng người dùng -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Tổng Users</h6>
                                            <h2 class="mb-0 text-primary" id="totalUsers">-</h2>
                                        </div>
                                        <div class="fs-1 text-primary">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted" id="activeUsersText">-</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Bài thi hôm nay -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Bài thi hôm nay</h6>
                                            <h2 class="mb-0 text-warning" id="todaySubmissions">-</h2>
                                        </div>
                                        <div class="fs-1 text-warning">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted" id="totalSubmissionsText">-</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Điểm trung bình -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-2">Điểm TB</h6>
                                            <h2 class="mb-0 text-info" id="avgScore">-</h2>
                                        </div>
                                        <div class="fs-1 text-info">
                                            <i class="bi bi-star-fill"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted">Điểm trung bình chung</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics Cards Row 2 -->
                    <div class="row g-4 mb-4">
                        <!-- Card: Tổng đề thi -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-journal-text fs-1 text-primary mb-2"></i>
                                    <h3 class="mb-0" id="totalExams">-</h3>
                                    <small class="text-muted">Đề thi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Tổng câu hỏi -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-question-circle fs-1 text-success mb-2"></i>
                                    <h3 class="mb-0" id="totalQuestions">-</h3>
                                    <small class="text-muted">Câu hỏi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Học sinh -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-mortarboard fs-1 text-info mb-2"></i>
                                    <h3 class="mb-0" id="totalStudents">-</h3>
                                    <small class="text-muted">Học sinh</small>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Giáo viên -->
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-workspace fs-1 text-warning mb-2"></i>
                                    <h3 class="mb-0" id="totalTeachers">-</h3>
                                    <small class="text-muted">Giáo viên</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info & Recent Activities -->
                    <div class="row g-4">
                        <!-- System Information -->
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-dark text-white">
                                    <i class="bi bi-info-circle"></i> Thông tin Hệ thống
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>PHP Version:</strong></td>
                                            <td id="phpVersion">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Laravel:</strong></td>
                                            <td id="laravelVersion">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Database:</strong></td>
                                            <td id="database">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Server Time:</strong></td>
                                            <td id="serverTime">-</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Uptime:</strong></td>
                                            <td id="serverUptime">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activities -->
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <i class="bi bi-activity"></i> Hoạt động Gần đây
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-hover table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Người dùng</th>
                                                    <th>Đề thi</th>
                                                    <th>Điểm</th>
                                                    <th>Thời gian</th>
                                                </tr>
                                            </thead>
                                            <tbody id="recentActivitiesTable">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        Đang tải...
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Thêm người dùng -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus"></i> Thêm người dùng mới
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="TenDangNhap" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="Email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="MatKhau" required minlength="6">
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                                <select class="form-select" name="Role" id="userRole" required onchange="app.toggleRoleFields()">
                                    <option value="">-- Chọn vai trò --</option>
                                    <option value="hocsinh">Học sinh</option>
                                    <option value="giaovien">Giáo viên</option>
                                    <option value="admin">Quản trị viên</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Thông tin học sinh -->
                        <div id="hocSinhFields" class="role-fields" style="display:none;">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Thông tin học sinh</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="HoTen" data-role="hocsinh">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lớp</label>
                                    <input type="text" class="form-control" name="Lop" placeholder="VD: 12A1" data-role="hocsinh">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trường</label>
                                <input type="text" class="form-control" name="Truong" placeholder="VD: THPT Nguyễn Huệ" data-role="hocsinh">
                            </div>
                        </div>

                        <!-- Thông tin giáo viên -->
                        <div id="giaoVienFields" class="role-fields" style="display:none;">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Thông tin giáo viên</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="HoTen" data-role="giaovien">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="SoDienThoai" placeholder="VD: 0912345678" data-role="giaovien">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chuyên môn</label>
                                <input type="text" class="form-control" name="ChuyenMon" placeholder="VD: Tin học" data-role="giaovien">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="app.createUser()">
                        <i class="bi bi-check-circle"></i> Tạo tài khoản
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Sửa người dùng -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> Sửa thông tin người dùng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" id="editMaTK" name="MaTK">
                        <input type="hidden" id="editRole" name="Role">
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Lưu ý:</strong> Không thể thay đổi tên đăng nhập và vai trò
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="editTenDangNhap" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="Email" id="editEmail" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" name="MatKhau" id="editMatKhau" minlength="6">
                                <small class="text-muted">Để trống nếu không đổi mật khẩu</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vai trò</label>
                                <input type="text" class="form-control" id="editRoleDisplay" disabled>
                            </div>
                        </div>
                        
                        <!-- Thông tin học sinh -->
                        <div id="editHocSinhFields" class="role-edit-fields" style="display:none;">
                            <h6 class="text-warning border-bottom pb-2 mb-3">Thông tin học sinh</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" name="HoTen" id="editHoTenHS">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lớp</label>
                                    <input type="text" class="form-control" name="Lop" id="editLop" placeholder="VD: 12A1">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Trường</label>
                                <input type="text" class="form-control" name="Truong" id="editTruong" placeholder="VD: THPT Nguyễn Huệ">
                            </div>
                        </div>

                        <!-- Thông tin giáo viên -->
                        <div id="editGiaoVienFields" class="role-edit-fields" style="display:none;">
                            <h6 class="text-warning border-bottom pb-2 mb-3">Thông tin giáo viên</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" name="HoTen" id="editHoTenGV">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" class="form-control" name="SoDienThoai" id="editSoDienThoai" placeholder="VD: 0912345678">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Chuyên môn</label>
                                <input type="text" class="form-control" name="ChuyenMon" id="editChuyenMon" placeholder="VD: Tin học">
                            </div>
                        </div>
                        
                        <!-- Phân quyền chi tiết (UR-04.2) -->
                        <div id="editPermissionsSection" class="mt-4" style="display:none;">
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="bi bi-shield-lock"></i> Phân quyền chi tiết
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permViewUsers" name="permissions[]" value="view_users">
                                        <label class="form-check-label" for="permViewUsers">
                                            Xem danh sách người dùng
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permManageUsers" name="permissions[]" value="manage_users">
                                        <label class="form-check-label" for="permManageUsers">
                                            Quản lý người dùng
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permManageQuestions" name="permissions[]" value="manage_questions">
                                        <label class="form-check-label" for="permManageQuestions">
                                            Quản lý Ngân hàng câu hỏi
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permCreateExams" name="permissions[]" value="create_exams">
                                        <label class="form-check-label" for="permCreateExams">
                                            Tạo đề thi
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permViewStats" name="permissions[]" value="view_statistics">
                                        <label class="form-check-label" for="permViewStats">
                                            Xem thống kê
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permBackup" name="permissions[]" value="backup_restore">
                                        <label class="form-check-label" for="permBackup">
                                            Backup & Restore
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permExportData" name="permissions[]" value="export_data">
                                        <label class="form-check-label" for="permExportData">
                                            Xuất dữ liệu
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="permSystemSettings" name="permissions[]" value="system_settings">
                                        <label class="form-check-label" for="permSystemSettings">
                                            Cài đặt hệ thống
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="bi bi-info-circle"></i>
                                <small>Lưu ý: Quyền này chỉ áp dụng cho Admin và Giáo viên. Học sinh có quyền mặc định là làm bài và xem kết quả.</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-warning" onclick="app.updateUser()">
                        <i class="bi bi-check-circle"></i> Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Xác nhận bắt đầu làm bài -->
    <div class="modal fade" id="confirmStartExamModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-play-circle"></i> Xác nhận bắt đầu làm bài
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Lưu ý quan trọng:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Thời gian làm bài sẽ được tính ngay khi bắt đầu</li>
                            <li>Bài làm sẽ tự động lưu mỗi 60 giây</li>
                            <li>Không được chuyển tab hoặc thoát trình duyệt</li>
                            <li>Khi hết thời gian, bài thi sẽ tự động nộp</li>
                        </ul>
                    </div>
                    <div id="examInfoPreview">
                        <!-- Thông tin đề thi sẽ được load vào đây -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" onclick="app.confirmStartExam()">
                        <i class="bi bi-play-fill"></i> Bắt đầu làm bài
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Chi tiết bài làm -->
    <div class="modal fade" id="chiTietBaiLamModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-file-earmark-text-fill"></i> Chi tiết bài làm
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Nội dung sẽ được load bằng JavaScript -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3">Đang tải...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tạo đề thi ngẫu nhiên (Giáo viên) - NEW -->
    <div class="modal fade" id="taoDeNgauNhienModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-shuffle"></i> Tạo đề thi ngẫu nhiên
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="randomExamForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="TenDe" required 
                                       placeholder="VD: Đề thi thử lần 1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian (phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="ThoiGianLamBai" required 
                                       min="30" max="180" value="60" placeholder="60">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chủ đề <span class="text-danger">*</span></label>
                                <select class="form-select" name="ChuDe" required>
                                    <option value="">-- Chọn chủ đề --</option>
                                    <option value="Tin học đại cương">Tin học đại cương</option>
                                    <option value="Lập trình Pascal">Lập trình Pascal</option>
                                    <option value="Lập trình C++">Lập trình C++</option>
                                    <option value="Cấu trúc dữ liệu">Cấu trúc dữ liệu</option>
                                    <option value="Giải thuật">Giải thuật</option>
                                    <option value="Tổng hợp">Tổng hợp</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số câu hỏi <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="SoLuongCauHoi" required 
                                       min="5" max="50" value="8" placeholder="8">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Độ khó</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="DoKho" id="doKhoDe" value="De" checked>
                                <label class="btn btn-outline-success" for="doKhoDe">Dễ</label>
                                
                                <input type="radio" class="btn-check" name="DoKho" id="doKhoTrungBinh" value="Trung binh">
                                <label class="btn btn-outline-warning" for="doKhoTrungBinh">Trung bình</label>
                                
                                <input type="radio" class="btn-check" name="DoKho" id="doKhoKho" value="Kho">
                                <label class="btn btn-outline-danger" for="doKhoKho">Khó</label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Lưu ý:</strong> Hệ thống sẽ tự động chọn ngẫu nhiên các câu hỏi phù hợp với yêu cầu.
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Cảnh báo:</strong> Nếu không đủ câu hỏi theo yêu cầu, vui lòng giảm số câu hoặc chọn độ khó khác.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" onclick="app.generateRandomExam()">
                        <i class="bi bi-shuffle"></i> Tạo đề thi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Sửa câu hỏi (Giáo viên) -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil"></i> Sửa câu hỏi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editQuestionForm" onsubmit="app.updateQuestion(event)">
                        <input type="hidden" id="editQuestionId">
                        
                        <div class="mb-3">
                            <label class="form-label">Nội dung câu hỏi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editQuestionContent" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đáp án A <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editAnswerA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đáp án B <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editAnswerB" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đáp án C <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editAnswerC" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Đáp án D <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="editAnswerD" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Đáp án đúng <span class="text-danger">*</span></label>
                                <select class="form-select" id="editCorrectAnswer" required>
                                    <option value="">-- Chọn --</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ngân hàng câu hỏi</label>
                                <input type="text" class="form-control" id="editQuestionBank" value="NH001">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Độ khó <span class="text-danger">*</span></label>
                                <select class="form-select" id="editQuestionDifficulty" required>
                                    <option value="De">Dễ</option>
                                    <option value="TB">Trung bình</option>
                                    <option value="Kho">Khó</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-warning" onclick="app.updateQuestion(event)">
                        <i class="bi bi-check-circle"></i> Cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Backup Database (Admin) - NEW -->
    <div class="modal fade" id="backupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-download"></i> Backup Database
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Cảnh báo:</strong> Quá trình backup có thể mất vài phút tùy thuộc vào dung lượng dữ liệu.
                    </div>
                    <div id="backupProgress" style="display:none;">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" style="width: 100%"></div>
                        </div>
                        <p class="text-center text-muted">Đang thực hiện backup...</p>
                    </div>
                    <div id="backupSuccess" style="display:none;" class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Backup thành công!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="btnStartBackup" onclick="app.startBackup()">
                        <i class="bi bi-download"></i> Bắt đầu Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Sửa đề thi (Teacher) -->
    <div class="modal fade" id="editExamModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square"></i> Sửa thông tin đề thi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editExamForm">
                        <input type="hidden" id="editExamMaDe">
                        
                        <div class="mb-3">
                            <label class="form-label">Tên đề thi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editExamTenDe" required 
                                   placeholder="Nhập tên đề thi">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chủ đề</label>
                                <select class="form-select" id="editExamChuDe">
                                    <option value="">-- Chọn chủ đề --</option>
                                    <option value="Tin học đại cương">Tin học đại cương</option>
                                    <option value="Lập trình Pascal">Lập trình Pascal</option>
                                    <option value="Lập trình C++">Lập trình C++</option>
                                    <option value="Cấu trúc dữ liệu">Cấu trúc dữ liệu</option>
                                    <option value="Giải thuật">Giải thuật</option>
                                    <option value="Tổng hợp">Tổng hợp</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Thời gian làm bài (phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="editExamThoiGian" required 
                                       min="10" max="180" placeholder="60">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" id="editExamMoTa" rows="3" 
                                      placeholder="Nhập mô tả về đề thi (không bắt buộc)"></textarea>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="editExamTrangThai">
                            <label class="form-check-label" for="editExamTrangThai">
                                Kích hoạt đề thi (học sinh có thể làm bài)
                            </label>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Lưu ý:</strong> Số lượng câu hỏi không thể thay đổi sau khi tạo. Để thay đổi câu hỏi, vui lòng tạo đề thi mới.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-warning" onclick="app.updateExam()">
                        <i class="bi bi-check-circle"></i> Cập nhật đề thi
                    </button>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Modal: Restore Database (Admin) - NEW -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-upload"></i> Restore Database
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <strong>Nguy hiểm:</strong> Restore sẽ ghi đè toàn bộ dữ liệu hiện tại. Hãy chắc chắn bạn đã backup trước đó.
                    </div>
                    <form id="restoreForm">
                        <div class="mb-3">
                            <label class="form-label">Chọn file backup (.sql) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="restoreFile" accept=".sql" required>
                        </div>
                    </form>
                    <div id="restoreProgress" style="display:none;">
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" 
                                 role="progressbar" style="width: 100%"></div>
                        </div>
                        <p class="text-center text-muted">Đang restore...</p>
                    </div>
                    <div id="restoreSuccess" style="display:none;" class="alert alert-success">
                        <i class="bi bi-check-circle"></i> Restore thành công!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="btnStartRestore" onclick="app.startRestore()">
                        <i class="bi bi-upload"></i> Bắt đầu Restore
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const app = {
            apiUrl: '/api',
            token: localStorage.getItem('token'),
            user: JSON.parse(localStorage.getItem('user') || 'null'),
            requestQueue: [], // For rate limiting
            currentScreen: null, // Track current active screen
            monitoringInterval: null, // For auto-refresh monitoring
            
            /**
             * ===================================
             * UTILITY FUNCTIONS (Security & UX)
             * ===================================
             */
            
            // Show global loading spinner
            showLoader() {
                const loader = document.getElementById('globalLoader');
                if (loader) {
                    loader.style.display = 'flex';
                }
            },
            
            // Hide global loading spinner
            hideLoader() {
                const loader = document.getElementById('globalLoader');
                if (loader) {
                    loader.style.display = 'none';
                }
            },
            
            // Show toast notification
            showToast(title, message, type = 'info') {
                const toast = document.getElementById('globalToast');
                const toastTitle = document.getElementById('toastTitle');
                const toastBody = document.getElementById('toastBody');
                const toastIcon = document.getElementById('toastIcon');
                const toastHeader = toast.querySelector('.toast-header');
                
                // Set icon and color based on type
                const icons = {
                    success: { icon: 'bi-check-circle-fill', color: 'text-success' },
                    error: { icon: 'bi-x-circle-fill', color: 'text-danger' },
                    warning: { icon: 'bi-exclamation-triangle-fill', color: 'text-warning' },
                    info: { icon: 'bi-info-circle-fill', color: 'text-primary' }
                };
                
                const config = icons[type] || icons.info;
                toastIcon.className = `bi ${config.icon} me-2 ${config.color}`;
                toastTitle.textContent = title;
                toastBody.textContent = message;
                
                // Show toast
                const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 5000 });
                bsToast.show();
            },
            
            // Debounce function for search optimization
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },
            
            // Sanitize HTML to prevent XSS
            sanitizeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str;
                return div.innerHTML;
            },
            
            // Check rate limit (simple client-side check)
            checkRateLimit() {
                const now = Date.now();
                this.requestQueue = this.requestQueue.filter(time => now - time < 60000); // Last minute
                
                if (this.requestQueue.length >= 60) { // Max 60 requests per minute
                    this.showToast('Cảnh báo', 'Bạn đang gửi request quá nhanh. Vui lòng chờ!', 'warning');
                    return false;
                }
                
                this.requestQueue.push(now);
                return true;
            },
            
            // Validate CSRF token exists
            getCsrfToken() {
                const token = document.querySelector('meta[name="csrf-token"]');
                return token ? token.getAttribute('content') : '';
            },
            
            // Global error handler
            handleGlobalError(error, context = '') {
                console.error(`[${context}] Error:`, error);
                
                let message = 'Đã xảy ra lỗi không xác định';
                
                if (error.message) {
                    message = error.message;
                } else if (typeof error === 'string') {
                    message = error;
                }
                
                // Network errors
                if (error.name === 'TypeError' && message.includes('fetch')) {
                    message = 'Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng.';
                }
                
                this.showToast('Lỗi', message, 'error');
                this.hideLoader();
            },
            
            init() {
                this.updateNavigation();
                
                // Setup debounced search for exam selection
                this.setupDebouncedSearch();
                
                // Setup keyboard shortcuts
                this.setupKeyboardShortcuts();
                
                if (this.user) {
                    // Nếu user đã login nhưng chưa có detail (MaHS, MaGV), refresh user info
                    const needsRefresh = !this.user.detail || 
                                       (this.user.Role === 'hocsinh' && !this.user.detail.MaHS) ||
                                       (this.user.Role === 'giaovien' && !this.user.detail.MaGV);
                    
                    if (needsRefresh && this.token) {
                        console.log('User missing detail info, refreshing...');
                        this.refreshUserInfo();
                    } else {
                        console.log('User detail already loaded:', this.user.detail);
                    }
                    this.showDefaultScreen();
                } else {
                    this.showScreen('home');
                }
            },
            
            setupDebouncedSearch() {
                // Debounced search for exam list
                const searchInput = document.getElementById('examSearchInput');
                if (searchInput) {
                    const debouncedSearch = this.debounce(() => {
                        this.loadDanhSachDeThi();
                    }, 500);
                    
                    searchInput.addEventListener('input', debouncedSearch);
                }
                
                // Debounced search for questions (teacher)
                const questionSearch = document.getElementById('questionSearchInput');
                if (questionSearch) {
                    const debouncedQuestionSearch = this.debounce(() => {
                        this.loadQuestionList();
                    }, 500);
                    
                    questionSearch.addEventListener('input', debouncedQuestionSearch);
                }
            },
            
            setupKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    // ESC to close modals
                    if (e.key === 'Escape') {
                        const openModals = document.querySelectorAll('.modal.show');
                        openModals.forEach(modal => {
                            const bsModal = bootstrap.Modal.getInstance(modal);
                            if (bsModal) bsModal.hide();
                        });
                    }
                    
                    // Ctrl+S to save (prevent default and trigger auto-save in exam)
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        if (this.currentExam && document.getElementById('lambaithiScreen').classList.contains('active')) {
                            this.luuBaiLam();
                        }
                    }
                });
            },
            
            async refreshUserInfo() {
                try {
                    const response = await this.apiCall('/me', {
                        method: 'GET'
                    });
                    
                    if (response && response.success) {
                        this.user = response.data.user;
                        this.user.detail = response.data.detail;
                        localStorage.setItem('user', JSON.stringify(this.user));
                        console.log('✅ User info refreshed successfully:', this.user.detail);
                    }
                } catch (error) {
                    console.error('❌ Error refreshing user info:', error);
                }
            },
            
            updateNavigation() {
                console.log('🔄 updateNavigation called, user:', this.user);
                
                // Get menu elements with null checks
                const guestMenu = document.getElementById('guestMenu');
                const studentMenu = document.getElementById('studentMenu');
                const teacherMenu = document.getElementById('teacherMenu');
                const adminMenu = document.getElementById('adminMenu');
                
                if (!guestMenu || !studentMenu || !teacherMenu || !adminMenu) {
                    console.error('❌ Menu elements not found!');
                    return;
                }
                
                // Hide all menus
                guestMenu.classList.add('d-none');
                studentMenu.classList.add('d-none');
                teacherMenu.classList.add('d-none');
                adminMenu.classList.add('d-none');
                
                if (!this.user) {
                    console.log('👤 No user - showing guest menu');
                    guestMenu.classList.remove('d-none');
                } else {
                    console.log('👤 User logged in, role:', this.user.Role);
                    
                    if (this.user.Role === 'hocsinh') {
                        studentMenu.classList.remove('d-none');
                    } else if (this.user.Role === 'giaovien') {
                        teacherMenu.classList.remove('d-none');
                    } else if (this.user.Role === 'admin') {
                        adminMenu.classList.remove('d-none');
                    }
                }
            },
            
            showScreen(screenName) {
                console.log('🔄 showScreen called with:', screenName);
                
                // Stop monitoring auto-refresh when leaving monitoring screen
                if (this.currentScreen === 'monitoring') {
                    this.stopMonitoringAutoRefresh();
                }
                
                // Hide all screens
                document.querySelectorAll('.screen').forEach(screen => {
                    screen.classList.remove('active');
                });
                
                // Show selected screen
                const screen = document.getElementById(screenName + 'Screen');
                console.log('🔍 Looking for element:', screenName + 'Screen', 'Found:', screen);
                
                if (screen) {
                    screen.classList.add('active');
                    this.currentScreen = screenName;
                    
                    console.log('✅ Screen activated:', screenName);
                    
                    // Load screen data
                    if (screenName === 'dethimau') {
                        this.loadDeThiMau();
                    } else if (screenName === 'lichsuthi') {
                        this.loadLichSuThi();
                    } else if (screenName === 'chondethi' || screenName === 'chonDeThi' || screenName === 'chondetthi') {
                        console.log('📋 Calling loadDanhSachDeThi...');
                        this.loadDanhSachDeThi();
                    } else if (screenName === 'thongkecanhan') {
                        this.loadThongKeCanhan();
                    } else if (screenName === 'thongke') {
                        this.loadThongKe();
                    } else if (screenName === 'dashboard') {
                        this.loadDashboard();
                    } else if (screenName === 'backup') {
                        this.loadBackupHistory();
                    } else if (screenName === 'quanlynguoidung') {
                        this.loadUsers();
                    } else if (screenName === 'quanlycauhoi') {
                        this.loadQuestionList();
                    } else if (screenName === 'taodethucong') {
                        this.loadManualQuestions();
                    } else if (screenName === 'danhsachdethi' || screenName === 'danhsachdetthi') {
                        this.loadTeacherExams();
                    } else if (screenName === 'thongkelop') {
                        this.loadClassStatistics();
                    } else if (screenName === 'monitoring') {
                        this.loadMonitoring();
                        this.startMonitoringAutoRefresh();
                    }
                }
            },
            
            showDefaultScreen() {
                if (this.user.Role === 'hocsinh') {
                    this.showScreen('chondetthi'); // FIXED: Đúng là chondetthi không phải chondethi
                } else if (this.user.Role === 'giaovien') {
                    this.showScreen('quanlycauhoi');
                } else if (this.user.Role === 'admin') {
                    this.showScreen('dashboard');
                } else {
                    this.showScreen('home');
                }
            },
            
            // Đóng navbar mobile
            closeNavbar() {
                const navbar = document.getElementById('navbarNav');
                if (navbar && navbar.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbar, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            },
            
            async apiCall(endpoint, options = {}) {
                // Check rate limit
                if (!this.checkRateLimit()) {
                    return null;
                }
                
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    ...options.headers
                };
                
                if (this.token) {
                    headers['Authorization'] = `Bearer ${this.token}`;
                }
                
                try {
                    const response = await fetch(this.apiUrl + endpoint, {
                        ...options,
                        headers,
                        credentials: 'same-origin' // Include cookies for CSRF
                    });
                    
                    console.log('API Response:', {
                        url: this.apiUrl + endpoint,
                        status: response.status,
                        contentType: response.headers.get('content-type')
                    });
                    
                    // CHỈ logout nếu đang ở route cần authentication và không phải route login
                    if (response.status === 401 && this.token && !endpoint.includes('/login')) {
                        this.showToast('Phiên hết hạn', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'warning');
                        this.logout();
                        return null;
                    }
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text.substring(0, 200));
                        throw new Error(`Server trả về HTML thay vì JSON. Status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Có lỗi xảy ra');
                    }
                    
                    return data;
                } catch (error) {
                    // Enhanced error handling
                    if (error.name === 'TypeError' && error.message.includes('fetch')) {
                        this.showToast('Lỗi kết nối', 'Không thể kết nối đến server. Kiểm tra mạng!', 'error');
                    } else {
                        this.showToast('Lỗi', error.message || 'Có lỗi xảy ra', 'error');
                    }
                    console.error('API Call Error:', error);
                    return null;
                }
            },
            
            async login(event) {
                event.preventDefault();
                
                const username = document.getElementById('loginUsername').value;
                const password = document.getElementById('loginPassword').value;
                
                const data = await this.apiCall('/login', {
                    method: 'POST',
                    body: JSON.stringify({
                        TenDangNhap: username,
                        MatKhau: password
                    })
                });
                
                if (data && data.success) {
                    this.token = data.data.token;
                    this.user = data.data.user;
                    this.user.detail = data.data.detail; // Lưu thông tin chi tiết (MaHS, MaGV, etc.)
                    
                    localStorage.setItem('token', this.token);
                    localStorage.setItem('user', JSON.stringify(this.user));
                    
                    this.showAlert('Đăng nhập thành công!', 'success');
                    this.updateNavigation();
                    this.showDefaultScreen();
                    
                    document.getElementById('loginForm').reset();
                }
            },
            
            // Modern alert using toast (backward compatible)
            showAlert(message, type = 'info') {
                const typeMap = {
                    success: 'success',
                    danger: 'error',
                    error: 'error',
                    warning: 'warning',
                    info: 'info'
                };
                
                const title = {
                    success: 'Thành công',
                    error: 'Lỗi',
                    warning: 'Cảnh báo',
                    info: 'Thông báo'
                }[typeMap[type]] || 'Thông báo';
                
                this.showToast(title, message, typeMap[type]);
            },
            
            logout() {
                this.token = null;
                this.user = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                
                this.updateNavigation();
                this.showScreen('home');
                this.showAlert('Đã đăng xuất', 'info');
            },

            // UR-01.2: Đăng ký tài khoản (Register)
            async register(event) {
                event.preventDefault();
                
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData);
                
                const response = await this.apiCall('/register', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                if (response && response.success) {
                    // Auto-login after register
                    this.token = response.data.token;
                    this.user = response.data.user;
                    this.user.detail = response.data.detail; // Lưu thông tin chi tiết (MaHS, MaGV, etc.)
                    
                    localStorage.setItem('token', this.token);
                    localStorage.setItem('user', JSON.stringify(this.user));
                    
                    this.showAlert('Đăng ký thành công! Chào mừng đến với hệ thống', 'success');
                    this.updateNavigation();
                    this.showDefaultScreen();
                    
                    document.getElementById('registerForm').reset();
                }
            },

            // UR-01.3: Quên mật khẩu (Forgot Password)
            async forgotPassword(event) {
                event.preventDefault();
                
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData);
                
                const response = await this.apiCall('/forgot-password', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                if (response && response.success) {
                    // Save email for reset password screen
                    sessionStorage.setItem('resetEmail', data.Email);
                    
                    this.showAlert('Mã khôi phục đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư!', 'success');
                    
                    // Show reset password screen
                    this.showScreen('resetPassword');
                    document.getElementById('resetEmail').value = data.Email;
                    
                    document.getElementById('forgotPasswordForm').reset();
                }
            },

            // UR-01.3: Đặt lại mật khẩu (Reset Password)
            async resetPassword(event) {
                event.preventDefault();
                
                const formData = new FormData(event.target);
                const data = Object.fromEntries(formData);
                
                // Verify password match
                if (data.MatKhauMoi !== data.XacNhanMatKhau) {
                    this.showAlert('Mật khẩu xác nhận không khớp!', 'danger');
                    return;
                }
                
                const response = await this.apiCall('/reset-password', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                if (response && response.success) {
                    sessionStorage.removeItem('resetEmail');
                    
                    this.showAlert('Đặt lại mật khẩu thành công! Bạn có thể đăng nhập với mật khẩu mới', 'success');
                    this.showScreen('login');
                    
                    document.getElementById('resetPasswordForm').reset();
                }
            },
            
            async loadDeThiMau() {
                const content = document.getElementById('dethimauContent');
                content.innerHTML = '<div class="loading"><div class="spinner-border text-white" role="status"></div></div>';
                
                const data = await this.apiCall('/de-thi-mau');
                
                if (data && data.success) {
                    const exams = data.data;
                    
                    if (exams.length === 0) {
                        content.innerHTML = '<div class="col-12"><div class="alert alert-info">Chưa có đề thi mẫu nào</div></div>';
                        return;
                    }
                    
                    content.innerHTML = exams.map(exam => `
                        <div class="col-md-4 mb-4">
                            <div class="card exam-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${exam.TenDe || 'Đề thi'}</h5>
                                    <p class="card-text">
                                        <i class="bi bi-book"></i> ${exam.MaMon || 'Tin học'}<br>
                                        <i class="bi bi-clock"></i> ${exam.ThoiGianLamBai || 0} phút<br>
                                        <i class="bi bi-list-ol"></i> ${exam.SoCauHoi || 0} câu hỏi
                                    </p>
                                    <span class="badge bg-info">${exam.MucDo || 'Trung bình'}</span>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <small class="text-muted">Mã đề: ${exam.MaDe}</small>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    content.innerHTML = '<div class="col-12"><div class="alert alert-danger">Không thể tải dữ liệu</div></div>';
                }
            },
            
            async loadLichSuThi() {
                const content = document.getElementById('lichsuthiContent');
                content.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div><p class="mt-3">Đang tải lịch sử...</p></div>';
                
                const data = await this.apiCall('/lich-su-thi');
                
                if (data && data.success) {
                    const history = data.data;
                    
                    if (history.length === 0) {
                        content.innerHTML = `
                            <div class="alert alert-warning text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Chưa có lịch sử làm bài</h5>
                                <p class="mb-0">Bạn chưa làm bài thi nào. Hãy bắt đầu làm bài từ danh sách đề thi!</p>
                            </div>
                        `;
                        return;
                    }
                    
                    content.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th><i class="bi bi-hash"></i> Mã bài làm</th>
                                        <th><i class="bi bi-file-earmark-text"></i> Đề thi</th>
                                        <th><i class="bi bi-calendar-event"></i> Ngày thi</th>
                                        <th><i class="bi bi-trophy"></i> Điểm số</th>
                                        <th><i class="bi bi-check-circle"></i> Kết quả</th>
                                        <th class="text-center"><i class="bi bi-gear"></i> Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${history.map(item => {
                                        const diem = parseFloat(item.Diem || 0);
                                        const badgeClass = diem >= 8 ? 'bg-success' : diem >= 5 ? 'bg-warning' : 'bg-danger';
                                        const tiLeDung = item.TongSoCau > 0 ? Math.round((item.SoCauDung / item.TongSoCau) * 100) : 0;
                                        
                                        return `
                                        <tr>
                                            <td><code>${item.MaBaiLam}</code></td>
                                            <td><strong>${item.de_thi?.TenDe || 'N/A'}</strong></td>
                                            <td>${new Date(item.created_at).toLocaleString('vi-VN')}</td>
                                            <td>
                                                <h5 class="mb-0">
                                                    <span class="badge ${badgeClass} rounded-pill">
                                                        ${diem.toFixed(2)}/10
                                                    </span>
                                                </h5>
                                            </td>
                                            <td>
                                                <span class="text-success"><strong>${item.SoCauDung}</strong> đúng</span> / 
                                                <span class="text-danger"><strong>${item.SoCauSai}</strong> sai</span>
                                                <br>
                                                <small class="text-muted">(${tiLeDung}%)</small>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary" onclick="app.viewResultDetail('${item.MaBaiLam}')">
                                                    <i class="bi bi-eye-fill"></i> Xem chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    content.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Không thể tải dữ liệu lịch sử thi</div>';
                }
            },
            
            async viewResultDetail(maBaiLam) {
                try {
                    // Hiển thị loading
                    const modal = document.getElementById('chiTietBaiLamModal');
                    if (!modal) {
                        this.showAlert('Lỗi: Không tìm thấy modal', 'danger');
                        return;
                    }
                    
                    const modalBody = modal.querySelector('.modal-body');
                    modalBody.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                            <p class="mt-3">Đang tải chi tiết bài làm...</p>
                        </div>
                    `;
                    
                    // Mở modal
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                    
                    // Gọi API
                    const data = await this.apiCall(`/bai-lam/${maBaiLam}/chi-tiet`);
                    
                    if (data && data.success) {
                        const result = data.data;
                        
                        // Hiển thị thông tin bài làm
                        let html = `
                            <!-- Thông tin tổng quan -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-info-circle-fill"></i> Thông tin bài làm</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Đề thi:</strong> ${result.baiLam.TenDe}</p>
                                            <p><strong>Mã bài làm:</strong> <code>${result.baiLam.MaBaiLam}</code></p>
                                            <p><strong>Thời gian làm bài:</strong> ${result.baiLam.ThoiGianLamBai} phút</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Điểm số:</strong> 
                                                <span class="badge ${result.ketQua.Diem >= 8 ? 'bg-success' : result.ketQua.Diem >= 5 ? 'bg-warning' : 'bg-danger'} fs-5">
                                                    ${result.ketQua.Diem}/10
                                                </span>
                                            </p>
                                            <p><strong>Số câu đúng:</strong> <span class="text-success fw-bold">${result.ketQua.SoCauDung}/${result.ketQua.TongSoCau}</span></p>
                                            <p><strong>Tỷ lệ đúng:</strong> ${result.ketQua.TiLeDung}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Danh sách câu hỏi -->
                            <h5 class="mb-3"><i class="bi bi-list-check"></i> Chi tiết từng câu hỏi</h5>
                        `;
                        
                        result.cauHoi.forEach((cau, index) => {
                            const isDung = cau.IsDung;
                            const borderClass = isDung ? 'border-success' : 'border-danger';
                            const bgClass = isDung ? 'bg-success' : 'bg-danger';
                            
                            html += `
                                <div class="card mb-3 ${borderClass}" style="border-width: 2px;">
                                    <div class="card-header ${bgClass} bg-opacity-10">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <span class="badge ${bgClass} me-2">${index + 1}</span>
                                                ${isDung ? '<i class="bi bi-check-circle-fill text-success"></i> Đúng' : '<i class="bi bi-x-circle-fill text-danger"></i> Sai'}
                                            </h6>
                                            <small class="badge bg-secondary">${cau.ChuyenDe || 'N/A'} - Độ khó: ${cau.DoKho || 'N/A'}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="fw-bold mb-3">${cau.NoiDung}</p>
                                        
                                        <div class="row g-2 mb-3">
                                            ${['A', 'B', 'C', 'D'].map(opt => {
                                                const isChosen = cau.DapAnChon === opt;
                                                const isCorrect = cau.DapAnDung === opt;
                                                let classes = 'border p-3 rounded';
                                                let icon = '';
                                                
                                                if (isCorrect) {
                                                    classes += ' border-success bg-success bg-opacity-10';
                                                    icon = '<i class="bi bi-check-circle-fill text-success"></i> ';
                                                } else if (isChosen && !isCorrect) {
                                                    classes += ' border-danger bg-danger bg-opacity-10';
                                                    icon = '<i class="bi bi-x-circle-fill text-danger"></i> ';
                                                }
                                                
                                                return `
                                                    <div class="col-md-6">
                                                        <div class="${classes}">
                                                            ${icon}<strong>${opt}.</strong> ${cau['DapAn' + opt]}
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                        
                                        ${cau.GiaiThich ? `
                                            <div class="alert alert-info mb-0">
                                                <strong><i class="bi bi-lightbulb-fill"></i> Giải thích:</strong>
                                                <p class="mb-0 mt-2">${cau.GiaiThich}</p>
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        
                        modalBody.innerHTML = html;
                    } else {
                        modalBody.innerHTML = '<div class="alert alert-danger">Không thể tải chi tiết bài làm</div>';
                    }
                    
                } catch (error) {
                    console.error('Error:', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                }
            },
            
            async viewResult(maBaiLam) {
                const data = await this.apiCall(`/baithi/${maBaiLam}/ketqua`);
                
                if (data && data.success) {
                    const result = data.data;
                    alert(`Chi tiết kết quả:\n\nĐiểm: ${result.Diem}/10\nSố câu đúng: ${result.SoCauDung}/${result.TongSoCau}\nThời gian: ${result.ThoiGianLamBai}`);
                    // TODO: Implement modal to show detailed results
                }
            },
            
            /**
             * Load thống kê cá nhân
             */
            async loadThongKeCanhan() {
                try {
                    const data = await this.apiCall('/thong-ke/ca-nhan');
                    
                    if (data && data.success) {
                        const stats = data.data;
                        
                        // Cập nhật các thẻ tổng quan (cấu trúc API mới: thongTinChung)
                        const info = stats.thongTinChung || stats; // Hỗ trợ cả 2 format
                        document.getElementById('tongSoBaiLam').textContent = info.tongSoBaiLam || 0;
                        document.getElementById('diemTrungBinh').textContent = (info.diemTrungBinh || 0).toFixed(2);
                        document.getElementById('tiLeDung').textContent = (info.tiLeDungTrungBinh || info.tiLeDung || 0).toFixed(0) + '%';
                        document.getElementById('diemCaoNhat').textContent = (info.diemCaoNhat || 0).toFixed(2);
                        
                        // Biểu đồ điểm số theo thời gian
                        const ctxDiem = document.getElementById('chartDiemSo');
                        if (!ctxDiem) {
                            console.error('chartDiemSo element not found');
                            return;
                        }
                        
                        // Destroy previous chart safely
                        if (window.chartDiemSo && typeof window.chartDiemSo.destroy === 'function') {
                            window.chartDiemSo.destroy();
                        }
                        
                        const lichSuDiem = stats.lichSuDiem || [];
                        window.chartDiemSo = new Chart(ctxDiem.getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: lichSuDiem.map(item => item.ngayRutGon || item.ngay),
                                datasets: [{
                                    label: 'Điểm số',
                                    data: lichSuDiem.map(item => item.diem),
                                    borderColor: '#667eea',
                                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            title: function(context) {
                                                return stats.lichSuDiem[context[0].dataIndex].tenDe;
                                            },
                                            label: function(context) {
                                                return 'Điểm: ' + context.parsed.y + '/10';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 10,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                        
                        // Biểu đồ tỷ lệ đúng/sai (hỗ trợ cả khongLam)
                        const ctxTyLe = document.getElementById('chartTyLe');
                        if (!ctxTyLe) {
                            console.error('chartTyLe element not found');
                            return;
                        }
                        
                        // Destroy previous chart safely
                        if (window.chartTyLe && typeof window.chartTyLe.destroy === 'function') {
                            window.chartTyLe.destroy();
                        }
                        
                        const tyLe = stats.tyLeDungSai || {};
                        window.chartTyLe = new Chart(ctxTyLe.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: ['Đúng', 'Sai', 'Không làm'],
                                datasets: [{
                                    data: [tyLe.dung || 0, tyLe.sai || 0, tyLe.khongLam || 0],
                                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const total = (tyLe.dung || 0) + (tyLe.sai || 0) + (tyLe.khongLam || 0);
                                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                                return context.label + ': ' + context.parsed + ' câu (' + percentage + '%)';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        
                        // Biểu đồ phân tích theo chuyên đề (API mới: phanTichChuyenDe)
                        const ctxChuyenDe = document.getElementById('chartChuyenDe');
                        if (!ctxChuyenDe) {
                            console.error('chartChuyenDe element not found');
                            return;
                        }
                        
                        // Destroy previous chart safely
                        if (window.chartChuyenDe && typeof window.chartChuyenDe.destroy === 'function') {
                            window.chartChuyenDe.destroy();
                        }
                        
                        const chuyenDe = stats.phanTichChuyenDe || stats.chuyenDe || [];
                        window.chartChuyenDe = new Chart(ctxChuyenDe.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: chuyenDe.map(cd => cd.tenChuyenDe),
                                datasets: [{
                                    label: 'Tỷ lệ đúng (%)',
                                    data: chuyenDe.map(cd => cd.tyLeDung),
                                    backgroundColor: chuyenDe.map(cd => {
                                        if (cd.tyLeDung >= 80) return '#10b981';
                                        if (cd.tyLeDung >= 60) return '#f59e0b';
                                        return '#ef4444';
                                    }),
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const cd = chuyenDe[context.dataIndex];
                                                if (!cd) return '';
                                                return [
                                                    'Tỷ lệ đúng: ' + (cd.tyLeDung || 0).toFixed(1) + '%',
                                                    'Số câu đúng: ' + (cd.soCauDung || 0) + '/' + (cd.tongSoCau || 0),
                                                    'Xếp loại: ' + (cd.xepLoai || 'N/A')
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '%';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        
                    } else {
                        this.showAlert('Không thể tải thống kê cá nhân: ' + (data.message || 'Unknown error'), 'danger');
                    }
                    
                } catch (error) {
                    console.error('=== THONG KE CA NHAN ERROR ===');
                    console.error('Error:', error);
                    console.error('Stack:', error.stack);
                    this.showAlert('Lỗi khi tải thống kê: ' + error.message, 'danger');
                }
            },
            
            async importQuestions(event) {
                event.preventDefault();
                
                const fileInput = document.getElementById('importFile');
                const file = fileInput.files[0];
                
                if (!file) {
                    this.showAlert('Vui lòng chọn file', 'warning');
                    return;
                }
                
                const formData = new FormData();
                formData.append('file', file);
                
                try {
                    const response = await fetch(this.apiUrl + '/cau-hoi/import', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showAlert('Import câu hỏi thành công!', 'success');
                        fileInput.value = '';
                        this.toggleImportForm(); // Hide form
                        this.loadQuestionList(); // Reload list
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    this.showAlert(error.message, 'danger');
                }
            },
            
            showAddQuestionForm() {
                // Hide import form if visible
                document.getElementById('importCard').classList.add('d-none');
                
                // Show add question form
                const card = document.getElementById('addQuestionCard');
                card.classList.remove('d-none');
                
                // Scroll to form
                card.scrollIntoView({ behavior: 'smooth', block: 'start' });
            },
            
            hideAddQuestionForm() {
                document.getElementById('addQuestionCard').classList.add('d-none');
                document.getElementById('addQuestionForm').reset();
            },
            
            toggleImportForm() {
                // Hide add question form if visible
                document.getElementById('addQuestionCard').classList.add('d-none');
                
                const card = document.getElementById('importCard');
                card.classList.toggle('d-none');
                
                if (!card.classList.contains('d-none')) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },
            
            async addQuestion(event) {
                event.preventDefault();
                
                const questionData = {
                    NoiDung: document.getElementById('questionContent').value,
                    DapAn1: document.getElementById('answerA').value,
                    DapAn2: document.getElementById('answerB').value,
                    DapAn3: document.getElementById('answerC').value,
                    DapAn4: document.getElementById('answerD').value,
                    DapAnDung: document.getElementById('correctAnswer').value,
                    MaMon: document.getElementById('questionSubject').value,
                    MucDo: document.getElementById('questionDifficulty').value
                };
                
                const data = await this.apiCall('/cau-hoi', {
                    method: 'POST',
                    body: JSON.stringify(questionData)
                });
                
                if (data && data.success) {
                    this.showAlert('Thêm câu hỏi thành công!', 'success');
                    this.hideAddQuestionForm();
                    this.loadQuestionList(); // Reload list
                }
            },
            
            async loadQuestionList() {
                const content = document.getElementById('questionListContent');
                if (!content) {
                    console.error('questionListContent not found');
                    return;
                }
                
                content.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div></div>';
                
                try {
                    console.log('Loading question list...');
                    const data = await this.apiCall('/cau-hoi', {
                        method: 'GET'
                    });
                    
                    console.log('Question list response:', data);
                    
                    if (!data || !data.success) {
                        content.innerHTML = '<div class="alert alert-danger">Không thể tải danh sách câu hỏi</div>';
                        return;
                    }
                    
                    // API trả về paginated data
                    const questions = data.data.data || data.data || [];
                    
                    console.log('Questions array:', questions);
                    
                    if (questions.length === 0) {
                        content.innerHTML = '<div class="alert alert-info">Chưa có câu hỏi nào. Hãy thêm câu hỏi mới!</div>';
                        return;
                    }
                    
                    content.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Mã CH</th>
                                        <th style="width: 40%">Nội dung</th>
                                        <th style="width: 10%">Đáp án</th>
                                        <th style="width: 10%">Ngân hàng</th>
                                        <th style="width: 15%">Độ khó</th>
                                        <th style="width: 15%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${questions.map(q => {
                                        const doKho = (q.DoKho || '').toLowerCase();
                                        return `
                                        <tr>
                                            <td><code>${q.MaCH || 'N/A'}</code></td>
                                            <td>${(q.NoiDung || '').substring(0, 80)}${(q.NoiDung || '').length > 80 ? '...' : ''}</td>
                                            <td><span class="badge bg-success">${q.DapAn || 'N/A'}</span></td>
                                            <td><small>${q.MaNH || 'N/A'}</small></td>
                                            <td>
                                                <span class="badge ${
                                                    doKho === 'de' ? 'bg-info' : 
                                                    doKho === 'kho' ? 'bg-danger' : 'bg-warning'
                                                }">
                                                    ${doKho === 'de' ? 'Dễ' : doKho === 'kho' ? 'Khó' : 'Trung bình'}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="app.viewQuestion('${q.MaCH}')" title="Xem chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" onclick="app.editQuestion('${q.MaCH}')" title="Sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="app.deleteQuestion('${q.MaCH}')" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `}).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } catch (error) {
                    console.error('Load question list error:', error);
                    content.innerHTML = '<div class="alert alert-danger">Không thể tải danh sách câu hỏi: ' + error.message + '</div>';
                }
            },
            
            async viewQuestion(maCH) {
                const data = await this.apiCall(`/cau-hoi/${maCH}`);
                
                if (data && data.success) {
                    const q = data.data;
                    alert(`CHI TIẾT CÂU HỎI\n\nMã: ${q.MaCH}\nNội dung: ${q.NoiDung}\n\nA. ${q.DapAn1}\nB. ${q.DapAn2}\nC. ${q.DapAn3}\nD. ${q.DapAn4}\n\nĐáp án đúng: ${q.DapAn}\nĐộ khó: ${q.DoKho}\nMôn: ${q.MaMon}`);
                    // TODO: Replace with a proper modal
                }
            },
            
            async deleteQuestion(maCH) {
                if (!confirm('Bạn có chắc muốn xóa câu hỏi này?')) {
                    return;
                }
                
                const data = await this.apiCall(`/cau-hoi/${maCH}`, {
                    method: 'DELETE'
                });
                
                if (data && data.success) {
                    this.showAlert('Đã xóa câu hỏi', 'success');
                    this.loadQuestionList();
                }
            },
            
            exportQuestions(format) {
                // Tạo URL với token trong query string
                const url = `${this.apiUrl}/cau-hoi/export?format=${format}&token=${this.token}`;
                
                // Tạo thông báo
                this.showAlert(`Đang xuất file ${format.toUpperCase()}...`, 'info');
                
                // Tạo link ẩn và click để download
                const link = document.createElement('a');
                link.href = url;
                link.download = `cau-hoi-${Date.now()}.${format}`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Thông báo thành công sau 1 giây
                setTimeout(() => {
                    this.showAlert(`Đã xuất file ${format.toUpperCase()} thành công!`, 'success');
                }, 1000);
            },
            
            async editQuestion(maCH) {
                try {
                    // Load dữ liệu câu hỏi
                    const data = await this.apiCall(`/cau-hoi/${maCH}`, { method: 'GET' });
                    
                    if (!data || !data.success) {
                        this.showAlert('Không thể tải câu hỏi', 'danger');
                        return;
                    }
                    
                    const q = data.data;
                    
                    // Fill dữ liệu vào form
                    document.getElementById('editQuestionId').value = q.MaCH;
                    document.getElementById('editQuestionContent').value = q.NoiDung || '';
                    document.getElementById('editAnswerA').value = q.DapAnA || '';
                    document.getElementById('editAnswerB').value = q.DapAnB || '';
                    document.getElementById('editAnswerC').value = q.DapAnC || '';
                    document.getElementById('editAnswerD').value = q.DapAnD || '';
                    document.getElementById('editCorrectAnswer').value = q.DapAn || '';
                    document.getElementById('editQuestionBank').value = q.MaNH || 'NH001';
                    document.getElementById('editQuestionDifficulty').value = q.DoKho || 'TB';
                    
                    // Hiển thị modal
                    const modal = new bootstrap.Modal(document.getElementById('editQuestionModal'));
                    modal.show();
                    
                } catch (error) {
                    console.error('Edit question error:', error);
                    this.showAlert('Lỗi khi tải câu hỏi: ' + error.message, 'danger');
                }
            },
            
            async updateQuestion(event) {
                if (event) event.preventDefault();
                
                try {
                    const maCH = document.getElementById('editQuestionId').value;
                    
                    const questionData = {
                        NoiDung: document.getElementById('editQuestionContent').value,
                        DapAnA: document.getElementById('editAnswerA').value,
                        DapAnB: document.getElementById('editAnswerB').value,
                        DapAnC: document.getElementById('editAnswerC').value,
                        DapAnD: document.getElementById('editAnswerD').value,
                        DapAn: document.getElementById('editCorrectAnswer').value,
                        MaNH: document.getElementById('editQuestionBank').value,
                        DoKho: document.getElementById('editQuestionDifficulty').value
                    };
                    
                    const data = await this.apiCall(`/cau-hoi/${maCH}`, {
                        method: 'PUT',
                        body: JSON.stringify(questionData)
                    });
                    
                    if (data && data.success) {
                        this.showAlert('Cập nhật câu hỏi thành công!', 'success');
                        
                        // Đóng modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editQuestionModal'));
                        if (modal) modal.hide();
                        
                        // Reload list
                        this.loadQuestionList();
                    }
                } catch (error) {
                    console.error('Update question error:', error);
                    this.showAlert('Lỗi khi cập nhật: ' + error.message, 'danger');
                }
            },
            
            /**
             * ====================================
             * TẠO ĐỀ THI THỦ CÔNG (Manual Exam Creation)
             * ====================================
             */
            
            selectedQuestions: [], // Array lưu câu đã chọn
            allQuestions: [], // Array lưu tất cả câu hỏi
            
            async loadManualQuestions() {
                try {
                    const data = await this.apiCall('/cau-hoi', { method: 'GET' });
                    
                    if (data && data.success) {
                        this.allQuestions = data.data.data || data.data || [];
                        document.getElementById('totalQuestionsAvailable').textContent = this.allQuestions.length;
                        this.renderManualQuestionList();
                    }
                } catch (error) {
                    console.error('Load manual questions error:', error);
                }
            },
            
            filterManualQuestions() {
                const bank = document.getElementById('filterQuestionBank').value;
                const difficulty = document.getElementById('filterDifficulty').value;
                
                let filtered = [...this.allQuestions];
                
                if (bank) {
                    filtered = filtered.filter(q => q.MaNH === bank);
                }
                
                if (difficulty) {
                    filtered = filtered.filter(q => q.DoKho === difficulty);
                }
                
                this.renderManualQuestionList(filtered);
            },
            
            renderManualQuestionList(questions = null) {
                const tbody = document.getElementById('manualQuestionList');
                const list = questions || this.allQuestions;
                
                if (!list || list.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Không có câu hỏi nào</td></tr>';
                    return;
                }
                
                let html = '';
                list.forEach(q => {
                    const isSelected = this.selectedQuestions.some(sq => sq.MaCH === q.MaCH);
                    const doKho = (q.DoKho || '').toLowerCase();
                    
                    html += `
                        <tr class="${isSelected ? 'table-success' : ''}">
                            <td>
                                <input type="checkbox" 
                                       class="question-checkbox" 
                                       value="${q.MaCH}" 
                                       ${isSelected ? 'checked' : ''}
                                       onchange="app.toggleQuestionSelection('${q.MaCH}')">
                            </td>
                            <td><code>${q.MaCH}</code></td>
                            <td>${(q.NoiDung || '').substring(0, 60)}...</td>
                            <td><span class="badge bg-success">${q.DapAn}</span></td>
                            <td>
                                <span class="badge ${doKho === 'de' ? 'bg-info' : doKho === 'kho' ? 'bg-danger' : 'bg-warning'}">
                                    ${doKho === 'de' ? 'Dễ' : doKho === 'kho' ? 'Khó' : 'Trung bình'}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" onclick="app.viewQuestionDetail('${q.MaCH}')" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                tbody.innerHTML = html;
            },
            
            toggleQuestionSelection(maCH) {
                const question = this.allQuestions.find(q => q.MaCH === maCH);
                if (!question) return;
                
                const index = this.selectedQuestions.findIndex(q => q.MaCH === maCH);
                
                if (index > -1) {
                    // Bỏ chọn
                    this.selectedQuestions.splice(index, 1);
                } else {
                    // Chọn
                    this.selectedQuestions.push(question);
                }
                
                this.updateSelectedQuestionsSidebar();
                this.renderManualQuestionList();
            },
            
            toggleSelectAll() {
                const checkbox = document.getElementById('selectAllQuestions');
                const checkboxes = document.querySelectorAll('.question-checkbox');
                
                if (checkbox.checked) {
                    // Select all visible questions
                    this.selectedQuestions = [...this.allQuestions];
                    checkboxes.forEach(cb => cb.checked = true);
                } else {
                    // Deselect all
                    this.selectedQuestions = [];
                    checkboxes.forEach(cb => cb.checked = false);
                }
                
                this.updateSelectedQuestionsSidebar();
                this.renderManualQuestionList();
            },
            
            updateSelectedQuestionsSidebar() {
                const count = this.selectedQuestions.length;
                const container = document.getElementById('selectedQuestionsList');
                const btn = document.getElementById('createManualExamBtn');
                
                // Update counts
                document.getElementById('selectedCount').textContent = count;
                document.getElementById('btnSelectedCount').textContent = count;
                
                // Enable/disable button
                btn.disabled = count === 0;
                
                if (count === 0) {
                    container.innerHTML = `
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mb-0">Chưa chọn câu nào</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                this.selectedQuestions.forEach((q, index) => {
                    html += `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <small>
                                <strong>${index + 1}.</strong> ${(q.NoiDung || '').substring(0, 40)}...
                            </small>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="app.removeSelectedQuestion('${q.MaCH}')" 
                                    title="Xóa">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            },
            
            removeSelectedQuestion(maCH) {
                this.selectedQuestions = this.selectedQuestions.filter(q => q.MaCH !== maCH);
                this.updateSelectedQuestionsSidebar();
                this.renderManualQuestionList();
            },
            
            viewQuestionDetail(maCH) {
                const q = this.allQuestions.find(question => question.MaCH === maCH);
                if (!q) return;
                
                alert(`CHI TIẾT CÂU HỎI\n\nMã: ${q.MaCH}\nNội dung: ${q.NoiDung}\n\nA. ${q.DapAnA}\nB. ${q.DapAnB}\nC. ${q.DapAnC}\nD. ${q.DapAnD}\n\nĐáp án đúng: ${q.DapAn}\nĐộ khó: ${q.DoKho}`);
            },
            
            async createManualExam(event) {
                event.preventDefault();
                
                if (this.selectedQuestions.length === 0) {
                    this.showAlert('Vui lòng chọn ít nhất 1 câu hỏi!', 'warning');
                    return;
                }
                
                const examData = {
                    TenDe: document.getElementById('manualExamName').value,
                    ChuDe: document.getElementById('manualExamSubject').value,
                    ThoiGianLamBai: parseInt(document.getElementById('manualExamDuration').value),
                    MoTa: document.getElementById('manualExamDescription').value || '',
                    DanhSachCauHoi: this.selectedQuestions.map(q => q.MaCH)
                };
                
                try {
                    this.showLoader();
                    const data = await this.apiCall('/de-thi/manual', {
                        method: 'POST',
                        body: JSON.stringify(examData)
                    });
                    
                    if (data && data.success) {
                        this.showAlert(`Tạo đề thi thành công với ${this.selectedQuestions.length} câu hỏi!`, 'success');
                        
                        // Reset form
                        document.getElementById('manualExamForm').reset();
                        this.selectedQuestions = [];
                        this.updateSelectedQuestionsSidebar();
                        this.renderManualQuestionList();
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Create manual exam error:', error);
                    this.showAlert('Lỗi khi tạo đề thi: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },
            
            // ============================================
            // ============================================
            // DANH SÁCH ĐỀ THI CỦA GIÁO VIÊN
            // ============================================
            async loadTeacherExams() {
                try {
                    console.log('🔍 Loading teacher exams...');
                    this.showLoader();
                    const data = await this.apiCall('/de-thi/teacher');
                    
                    console.log('📊 API Response:', data);
                    
                    if (data && data.success) {
                        console.log('✅ Exams data:', data.data);
                        this.renderExamList(data.data);
                    } else {
                        console.error('❌ API returned error or null:', data);
                        const tableBody = document.getElementById('examListTable');
                        if (tableBody) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="9" class="text-center text-danger">
                                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Không thể tải danh sách đề thi. Vui lòng thử lại!</p>
                                    </td>
                                </tr>
                            `;
                        }
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('💥 Load exams error:', error);
                    this.showAlert('Lỗi khi tải danh sách đề thi: ' + error.message, 'danger');
                    const tableBody = document.getElementById('examListTable');
                    if (tableBody) {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="9" class="text-center text-danger">
                                    <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Lỗi: ${error.message}</p>
                                </td>
                            </tr>
                        `;
                    }
                    this.hideLoader();
                }
            },

            renderExamList(exams) {
                console.log('🎨 Rendering exam list...', exams);
                const tableBody = document.getElementById('examListTable');
                console.log('📊 Table body element:', tableBody);
                
                if (!tableBody) {
                    console.error('❌ examListTable element NOT FOUND!');
                    return;
                }
                
                if (exams.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">Chưa có đề thi nào. Hãy tạo đề thi mới!</p>
                            </td>
                        </tr>
                    `;
                    console.log('ℹ️ No exams to display');
                    return;
                }

                const html = exams.map(exam => `
                    <tr>
                        <td><span class="badge bg-primary">${exam.MaDe}</span></td>
                        <td><strong>${exam.TenDe}</strong></td>
                        <td>${exam.ChuDe}</td>
                        <td>
                            <span class="badge bg-info">${exam.SoCauHoiThucTe}</span>
                            ${exam.SoCauHoiThucTe !== exam.SoLuongCauHoi ? 
                                `<small class="text-warning">(Khai báo: ${exam.SoLuongCauHoi})</small>` : ''}
                        </td>
                        <td>${exam.ThoiGianLamBai} phút</td>
                        <td>${new Date(exam.NgayTao).toLocaleString('vi-VN')}</td>
                        <td><span class="badge bg-success">${exam.SoLuotLam}</span></td>
                        <td>
                            ${exam.TrangThai == 1 ? 
                                '<span class="badge bg-success">Kích hoạt</span>' : 
                                '<span class="badge bg-secondary">Vô hiệu</span>'}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" onclick="app.viewExamDetail('${exam.MaDe}')" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="app.editExam('${exam.MaDe}')" title="Sửa đề thi">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="app.deleteExam('${exam.MaDe}', '${exam.TenDe}')" title="Xóa đề thi">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
                
                console.log('✅ Setting innerHTML with', exams.length, 'exams');
                tableBody.innerHTML = html;
                console.log('✅ Render complete!');
            },

            async viewExamDetail(maDe) {
                try {
                    this.showLoader();
                    const data = await this.apiCall(`/de-thi/${maDe}/detail`);
                    
                    if (data && data.success) {
                        this.renderExamDetail(data.data);
                        const modalElement = document.getElementById('examDetailModal');
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: false,
                            keyboard: true
                        });
                        
                        // Add event listener for when modal is hidden
                        modalElement.addEventListener('hidden.bs.modal', function () {
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }, { once: true });
                        
                        modal.show();
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('View exam detail error:', error);
                    this.showAlert('Lỗi khi xem chi tiết đề thi', 'danger');
                    this.hideLoader();
                }
            },

            renderExamDetail(data) {
                const exam = data.deThi || data.exam || data;
                const questions = data.cauHoi || data.questions || [];
                const detailBody = document.getElementById('examDetailBody');
                
                console.log('📝 Rendering exam detail:', { exam, questions });
                
                if (!exam) {
                    detailBody.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Không tìm thấy thông tin đề thi!
                        </div>
                    `;
                    return;
                }
                
                detailBody.innerHTML = `
                    <!-- Thông tin đề thi với nền sáng -->
                    <div class="card mb-4 border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="card-body text-white p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">
                                    <i class="bi bi-file-earmark-text-fill"></i> ${exam.TenDe}
                                </h4>
                                <span class="badge bg-light text-dark fs-6">${exam.MaDe}</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-list-ol fs-4 me-2"></i>
                                        <div>
                                            <small class="opacity-75">Số câu</small>
                                            <h5 class="mb-0">${exam.SoLuongCauHoi || questions.length}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock-fill fs-4 me-2"></i>
                                        <div>
                                            <small class="opacity-75">Thời gian</small>
                                            <h5 class="mb-0">${exam.ThoiGianLamBai} phút</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-tags-fill fs-4 me-2"></i>
                                        <div>
                                            <small class="opacity-75">Chủ đề</small>
                                            <h5 class="mb-0">${exam.ChuDe || 'Tin học'}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar-check fs-4 me-2"></i>
                                        <div>
                                            <small class="opacity-75">Ngày tạo</small>
                                            <h6 class="mb-0">${new Date(exam.NgayTao).toLocaleDateString('vi-VN')}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách câu hỏi với nền sáng -->
                    <div class="mb-3">
                        ${questions.length === 0 ? `
                            <div class="alert alert-warning shadow-sm border-0">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Chưa có câu hỏi!</strong> Đề thi này chưa có câu hỏi nào. Vui lòng thêm câu hỏi.
                            </div>
                        ` : questions.map((q, index) => {
                            const correctAnswer = q.DapAn || q.DapAnDung;
                            const difficulty = q.DoKho || 'TB';
                            const difficultyConfig = {
                                'De': { color: '#10b981', bg: '#d1fae5', text: 'Dễ' },
                                'TB': { color: '#f59e0b', bg: '#fef3c7', text: 'Trung bình' },
                                'Kho': { color: '#ef4444', bg: '#fee2e2', text: 'Khó' }
                            };
                            const diffStyle = difficultyConfig[difficulty] || difficultyConfig['TB'];
                            
                            return `
                                <div class="card shadow-sm border-0 mb-3" style="background: #ffffff;">
                                    <div class="card-header border-0 d-flex justify-content-between align-items-center py-3" 
                                         style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                        <div>
                                            <span class="badge me-2" style="background: #667eea; font-size: 0.9rem;">
                                                Câu ${q.ThuTu || (index + 1)}
                                            </span>
                                            <span class="badge bg-secondary" style="font-size: 0.85rem;">${q.MaCH}</span>
                                        </div>
                                        <span class="badge" style="background: ${diffStyle.color}; font-size: 0.85rem;">
                                            ${diffStyle.text}
                                        </span>
                                    </div>
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 1.1rem; line-height: 1.6;">
                                            <i class="bi bi-chat-square-text-fill text-primary me-2"></i>
                                            ${q.NoiDung}
                                        </h6>
                                        
                                        <div class="row g-3">
                                            ${['A', 'B', 'C', 'D'].map(option => {
                                                const answerText = q['DapAn' + option] || '';
                                                const isCorrect = correctAnswer === option;
                                                
                                                return `
                                                    <div class="col-md-6">
                                                        <div class="answer-box p-3 rounded-3 h-100 ${isCorrect ? 'correct-answer' : ''}"
                                                             style="background: ${isCorrect ? '#d1fae5' : '#f8fafc'}; 
                                                                    border: 2px solid ${isCorrect ? '#10b981' : '#e2e8f0'};
                                                                    transition: all 0.3s ease;">
                                                            <div class="d-flex align-items-start">
                                                                <div class="me-3">
                                                                    ${isCorrect ? 
                                                                        '<i class="bi bi-check-circle-fill fs-4" style="color: #10b981;"></i>' : 
                                                                        '<span class="badge bg-secondary">' + option + '</span>'}
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <strong style="color: ${isCorrect ? '#10b981' : '#64748b'}; font-size: 1rem;">
                                                                        ${option}.
                                                                    </strong>
                                                                    <span style="color: ${isCorrect ? '#1e293b' : '#64748b'}; margin-left: 0.5rem;">
                                                                        ${answerText}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                        
                                        <div class="mt-4 p-3 rounded-3 d-flex align-items-center" 
                                             style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-left: 4px solid #10b981;">
                                            <i class="bi bi-check-circle-fill fs-5 me-2" style="color: #059669;"></i>
                                            <strong style="color: #065f46; font-size: 1rem;">
                                                Đáp án đúng: ${correctAnswer}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;
            },
            
            // Sửa đề thi
            async editExam(maDe) {
                try {
                    this.showLoader();
                    
                    // Lấy thông tin đề thi từ API
                    const data = await this.apiCall(`/de-thi/${maDe}/detail`);
                    
                    if (data && data.success) {
                        const exam = data.data.exam;
                        
                        // Fill dữ liệu vào form
                        document.getElementById('editExamMaDe').value = exam.MaDe;
                        document.getElementById('editExamTenDe').value = exam.TenDe;
                        document.getElementById('editExamChuDe').value = exam.ChuDe || '';
                        document.getElementById('editExamThoiGian').value = exam.ThoiGianLamBai;
                        document.getElementById('editExamMoTa').value = exam.MoTa || '';
                        document.getElementById('editExamTrangThai').checked = exam.TrangThai == 1;
                        
                        // Hiển thị modal
                        const modal = new bootstrap.Modal(document.getElementById('editExamModal'));
                        modal.show();
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Load exam error:', error);
                    this.showAlert('Lỗi khi tải thông tin đề thi: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },
            
            // Cập nhật đề thi
            async updateExam() {
                try {
                    const maDe = document.getElementById('editExamMaDe').value;
                    const tenDe = document.getElementById('editExamTenDe').value;
                    const chuDe = document.getElementById('editExamChuDe').value;
                    const thoiGian = document.getElementById('editExamThoiGian').value;
                    const moTa = document.getElementById('editExamMoTa').value;
                    const trangThai = document.getElementById('editExamTrangThai').checked;
                    
                    // Validate
                    if (!tenDe || !thoiGian) {
                        this.showAlert('Vui lòng điền đầy đủ thông tin bắt buộc', 'warning');
                        return;
                    }
                    
                    if (thoiGian < 10 || thoiGian > 180) {
                        this.showAlert('Thời gian làm bài phải từ 10 đến 180 phút', 'warning');
                        return;
                    }
                    
                    this.showLoader();
                    
                    const data = await this.apiCall(`/de-thi/${maDe}`, {
                        method: 'PUT',
                        body: JSON.stringify({
                            TenDe: tenDe,
                            ChuDe: chuDe,
                            ThoiGianLamBai: parseInt(thoiGian),
                            MoTa: moTa,
                            TrangThai: trangThai ? 1 : 0
                        })
                    });
                    
                    if (data && data.success) {
                        this.showAlert('Cập nhật đề thi thành công!', 'success');
                        
                        // Đóng modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editExamModal'));
                        modal.hide();
                        
                        // Reload danh sách
                        this.loadTeacherExams();
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Update exam error:', error);
                    this.showAlert('Lỗi khi cập nhật đề thi: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },
            
            // Xóa đề thi
            async deleteExam(maDe, tenDe) {
                if (!confirm(`⚠️ Bạn có chắc chắn muốn XÓA đề thi "${tenDe}"?\n\nHành động này KHÔNG THỂ HOÀN TÁC!`)) {
                    return;
                }
                
                try {
                    this.showLoader();
                    const data = await this.apiCall(`/de-thi/${maDe}`, {
                        method: 'DELETE'
                    });
                    
                    if (data && data.success) {
                        this.showAlert('Xóa đề thi thành công!', 'success');
                        this.loadTeacherExams(); // Reload danh sách
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Delete exam error:', error);
                    this.showAlert('Lỗi khi xóa đề thi: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },

            // ============================================
            // UR-03.5: THỐNG KÊ LỚP HỌC
            // ============================================
            async loadClassStatistics() {
                try {
                    this.showLoader();
                    const data = await this.apiCall('/thong-ke/lop-hoc');
                    
                    if (data && data.success) {
                        const stats = data.data;
                        
                        // Cập nhật cards tổng quan
                        document.getElementById('statTotalStudents').textContent = stats.summary.totalStudents;
                        document.getElementById('statAverageScore').textContent = stats.summary.averageScore.toFixed(2);
                        document.getElementById('statPassRate').textContent = stats.summary.passRate.toFixed(1) + '%';
                        document.getElementById('statTotalExams').textContent = stats.summary.totalExams;
                        
                        // Render top 5 học sinh giỏi
                        const topTable = document.getElementById('topStudentsTable');
                        if (stats.topStudents.length > 0) {
                            topTable.innerHTML = stats.topStudents.map((student, index) => `
                                <tr>
                                    <td><span class="badge bg-warning">${index + 1}</span></td>
                                    <td>${student.TenTK}</td>
                                    <td><strong class="text-success">${student.avg_score}</strong></td>
                                    <td>${student.total_exams}</td>
                                </tr>
                            `).join('');
                        } else {
                            topTable.innerHTML = '<tr><td colspan="4" class="text-center">Chưa có dữ liệu</td></tr>';
                        }
                        
                        // Render top 5 học sinh yếu
                        const weakTable = document.getElementById('weakStudentsTable');
                        if (stats.weakStudents.length > 0) {
                            weakTable.innerHTML = stats.weakStudents.map((student, index) => `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${student.TenTK}</td>
                                    <td><strong class="text-danger">${student.avg_score}</strong></td>
                                    <td>${student.total_exams}</td>
                                </tr>
                            `).join('');
                        } else {
                            weakTable.innerHTML = '<tr><td colspan="4" class="text-center">Chưa có dữ liệu</td></tr>';
                        }
                        
                        // Render biểu đồ phân bố điểm
                        this.renderScoreDistributionChart(stats.scoreDistribution);
                        
                        // Render bảng chi tiết học sinh
                        const allTable = document.getElementById('allStudentsTable');
                        if (stats.studentDetails.length > 0) {
                            allTable.innerHTML = stats.studentDetails.map((student, index) => {
                                const statusBadge = student.status === 'Đạt' 
                                    ? '<span class="badge bg-success">Đạt</span>' 
                                    : student.status === 'Chưa đạt' 
                                        ? '<span class="badge bg-danger">Chưa đạt</span>'
                                        : '<span class="badge bg-secondary">Chưa thi</span>';
                                
                                return `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${student.TenTK}</td>
                                        <td>${student.Email}</td>
                                        <td><strong>${student.avg_score}</strong></td>
                                        <td>${student.max_score}</td>
                                        <td>${student.min_score}</td>
                                        <td>${student.total_exams}</td>
                                        <td>${statusBadge}</td>
                                    </tr>
                                `;
                            }).join('');
                        } else {
                            allTable.innerHTML = '<tr><td colspan="8" class="text-center">Chưa có dữ liệu</td></tr>';
                        }
                    }
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Load class statistics error:', error);
                    this.showAlert('Lỗi khi tải thống kê: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },
            
            renderScoreDistributionChart(distribution) {
                const ctx = document.getElementById('scoreDistributionChart');
                
                // Destroy existing chart if exists
                if (window.classStatsChart) {
                    window.classStatsChart.destroy();
                }
                
                window.classStatsChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: distribution.map(d => d.label + ' (' + d.range + ')'),
                        datasets: [{
                            label: 'Số học sinh',
                            data: distribution.map(d => d.count),
                            backgroundColor: [
                                'rgba(220, 53, 69, 0.7)',   // Kém - Red
                                'rgba(255, 193, 7, 0.7)',   // Yếu - Yellow
                                'rgba(108, 117, 125, 0.7)', // TB - Gray
                                'rgba(13, 202, 240, 0.7)',  // Khá - Cyan
                                'rgba(32, 201, 151, 0.7)',  // Khá Giỏi - Teal
                                'rgba(25, 135, 84, 0.7)'    // Giỏi - Green
                            ],
                            borderColor: [
                                'rgba(220, 53, 69, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(108, 117, 125, 1)',
                                'rgba(13, 202, 240, 1)',
                                'rgba(32, 201, 151, 1)',
                                'rgba(25, 135, 84, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Số HS: ' + context.parsed.y;
                                    }
                                }
                            }
                        }
                    }
                });
            },
            
            async createExam(event) {
                event.preventDefault();
                
                const examData = {
                    TenDe: document.getElementById('examName').value,
                    ChuDe: document.getElementById('examSubject').value,
                    ThoiGianLamBai: parseInt(document.getElementById('examDuration').value),
                    SoLuongCauHoi: parseInt(document.getElementById('examQuestions').value),
                    MoTa: 'Độ khó: ' + document.getElementById('examDifficulty').value
                };
                
                const data = await this.apiCall('/tao-de-thi', {
                    method: 'POST',
                    body: JSON.stringify(examData)
                });
                
                if (data && data.success) {
                    this.showAlert('Tạo đề thi thành công!', 'success');
                    document.getElementById('createExamForm').reset();
                    this.showScreen('danhsachdetthi');
                    this.loadTeacherExams();
                }
            },
            
            async loadUsers() {
                const content = document.getElementById('usersContent');
                content.innerHTML = '<div class="loading"><div class="spinner-border" role="status"></div></div>';
                
                const role = document.getElementById('roleFilter').value;
                const endpoint = role ? `/users?Role=${role}` : '/users';
                
                const data = await this.apiCall(endpoint);
                
                if (data && data.success) {
                    const users = data.data;
                    
                    if (users.length === 0) {
                        content.innerHTML = '<div class="alert alert-info">Không có người dùng nào</div>';
                        return;
                    }
                    
                    content.innerHTML = `
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã TK</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${users.map(user => `
                                        <tr>
                                            <td>${user.MaTK}</td>
                                            <td>${user.TenDangNhap}</td>
                                            <td>${user.Email}</td>
                                            <td>
                                                <span class="badge ${
                                                    user.Role === 'admin' ? 'bg-danger' : 
                                                    user.Role === 'giaovien' ? 'bg-primary' : 'bg-success'
                                                }">
                                                    ${user.Role}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge ${user.TrangThai ? 'bg-success' : 'bg-secondary'}">
                                                    ${user.TrangThai ? 'Hoạt động' : 'Đã khóa'}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="app.toggleUserStatus('${user.MaTK}')" title="${user.TrangThai ? 'Khóa tài khoản' : 'Mở khóa tài khoản'}">
                                                    <i class="bi bi-lock"></i> ${user.TrangThai ? 'Khóa' : 'Mở'}
                                                </button>
                                                ${user.Role !== 'admin' ? `
                                                    <button class="btn btn-sm btn-danger ms-1" onclick="app.deleteUser('${user.MaTK}', '${user.TenDangNhap}')" title="Xóa tài khoản">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                ` : ''}
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    content.innerHTML = '<div class="alert alert-danger">Không thể tải dữ liệu</div>';
                }
            },
            
            async toggleUserStatus(maTK) {
                if (!confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?')) {
                    return;
                }
                
                console.log('Toggle user status:', maTK);
                
                try {
                    const data = await this.apiCall(`/users/${maTK}/toggle`, {
                        method: 'PATCH'
                    });
                    
                    console.log('Toggle response:', data);
                    
                    if (!data) {
                        this.showAlert('Không nhận được phản hồi từ server', 'danger');
                        return;
                    }
                    
                    if (data.success) {
                        this.showAlert(data.message, 'success');
                        this.loadUsers();
                    } else {
                        this.showAlert(data.message || 'Có lỗi xảy ra', 'danger');
                    }
                } catch (error) {
                    console.error('Toggle status error:', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                }
            },
            
            async deleteUser(maTK, tenDangNhap) {
                if (!confirm(`⚠️ CẢNH BÁO: Bạn có chắc chắn muốn XÓA VĨNH VIỄN tài khoản "${tenDangNhap}"?\n\nHành động này KHÔNG THỂ HOÀN TÁC!`)) {
                    return;
                }
                
                // Xác nhận lần 2
                if (!confirm(`Xác nhận lần cuối: Xóa tài khoản "${tenDangNhap}"?`)) {
                    return;
                }
                
                try {
                    const data = await this.apiCall(`/users/${maTK}`, {
                        method: 'DELETE'
                    });
                    
                    if (data && data.success) {
                        this.showAlert(data.message, 'success');
                        this.loadUsers(); // Reload danh sách
                    }
                } catch (error) {
                    console.error('Delete user error:', error);
                    this.showAlert('Không thể xóa tài khoản: ' + error.message, 'danger');
                }
            },
            
            showCreateUserModal() {
                const modal = new bootstrap.Modal(document.getElementById('createUserModal'));
                document.getElementById('createUserForm').reset();
                // Hide all role-specific fields
                document.querySelectorAll('.role-fields').forEach(el => el.style.display = 'none');
                modal.show();
            },
            
            toggleRoleFields() {
                const role = document.getElementById('userRole').value;
                
                // Hide all role fields and disable their inputs
                document.querySelectorAll('.role-fields').forEach(el => {
                    el.style.display = 'none';
                    // Disable all inputs in hidden fields
                    el.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = true;
                        input.value = ''; // Clear value
                    });
                });
                
                // Show relevant fields based on role and enable their inputs
                if (role === 'hocsinh') {
                    const hocSinhFields = document.getElementById('hocSinhFields');
                    hocSinhFields.style.display = 'block';
                    hocSinhFields.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = false;
                    });
                } else if (role === 'giaovien') {
                    const giaoVienFields = document.getElementById('giaoVienFields');
                    giaoVienFields.style.display = 'block';
                    giaoVienFields.querySelectorAll('input, select, textarea').forEach(input => {
                        input.disabled = false;
                    });
                }
            },
            
            async createUser() {
                const form = document.getElementById('createUserForm');
                const formData = new FormData(form);
                const role = formData.get('Role');
                
                // Validate required fields
                if (!formData.get('TenDangNhap') || !formData.get('Email') || !formData.get('MatKhau') || !role) {
                    this.showAlert('Vui lòng điền đầy đủ thông tin bắt buộc', 'warning');
                    return;
                }
                
                // Validate HoTen for hocsinh and giaovien
                if (role === 'hocsinh' || role === 'giaovien') {
                    const hoTen = formData.get('HoTen');
                    if (!hoTen || hoTen.trim() === '') {
                        this.showAlert('Vui lòng nhập họ tên', 'warning');
                        return;
                    }
                }
                
                // Convert FormData to object
                const userData = {};
                formData.forEach((value, key) => {
                    if (value) userData[key] = value;
                });
                
                try {
                    console.log('Creating user with data:', userData);
                    const response = await this.apiCall('/users', {
                        method: 'POST',
                        body: JSON.stringify(userData)
                    });
                    console.log('Create user response:', response);
                    
                    if (response && response.success) {
                        this.showAlert('Tạo người dùng thành công!', 'success');
                        
                        // Đóng modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createUserModal'));
                        if (modal) modal.hide();
                        
                        // Reload user list sau khi đóng modal
                        setTimeout(() => {
                            this.loadUsers();
                        }, 300);
                    } else {
                        this.showAlert(response?.message || 'Có lỗi xảy ra', 'danger');
                    }
                } catch (error) {
                    console.error('Error creating user:', error);
                    this.showAlert('Không thể tạo người dùng: ' + error.message, 'danger');
                }
            },
            
            async loadUsers() {
                const roleFilter = document.getElementById('roleFilter')?.value || '';
                console.log('Loading users with role filter:', roleFilter);
                try {
                    const url = roleFilter ? `/users?Role=${roleFilter}` : '/users';
                    const response = await this.apiCall(url, {
                        method: 'GET'
                    });
                    console.log('Load users response:', response);
                    
                    if (response && response.success) {
                        console.log('Users data:', response.data);
                        this.displayUsers(response.data);
                    } else {
                        this.showAlert('Không thể tải danh sách người dùng', 'danger');
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    this.showAlert('Lỗi khi tải người dùng: ' + error.message, 'danger');
                }
            },
            
            displayUsers(users) {
                console.log('Displaying users:', users);
                const container = document.getElementById('usersContent');
                if (!container) {
                    console.error('usersContent container not found!');
                    return;
                }
                
                if (!users || users.length === 0) {
                    container.innerHTML = '<p class="text-muted">Không có người dùng nào</p>';
                    return;
                }
                
                let html = '<div class="table-responsive"><table class="table table-hover">';
                html += '<thead><tr>';
                html += '<th>Tên đăng nhập</th><th>Email</th><th>Vai trò</th><th>Họ tên</th><th>Trạng thái</th><th>Thao tác</th>';
                html += '</tr></thead><tbody>';
                
                users.forEach(user => {
                    let hoTen = '-';
                    if (user.ThongTinHocSinh) hoTen = user.ThongTinHocSinh.HoTen;
                    else if (user.ThongTinGiaoVien) hoTen = user.ThongTinGiaoVien.HoTen;
                    
                    // Role badge colors
                    const roleBadge = user.Role === 'admin' ? 'danger' : 
                                     user.Role === 'giaovien' ? 'primary' : 'success';
                    const roleText = user.Role === 'admin' ? 'ADMIN' :
                                    user.Role === 'giaovien' ? 'GIAOVIEN' : 'HOCSINH';
                    
                    // Status check - TrangThai có thể là boolean (true/false) hoặc số (1/0)
                    const isActive = user.TrangThai === true || user.TrangThai === 1 || user.TrangThai === '1';
                    const statusBadge = isActive ? 'success' : 'secondary';
                    const statusText = isActive ? 'HOẠT ĐỘNG' : 'KHÓA';
                    
                    html += '<tr>';
                    html += `<td>${user.TenDangNhap}</td>`;
                    html += `<td>${user.Email}</td>`;
                    html += `<td><span class="badge bg-${roleBadge}">${roleText}</span></td>`;
                    html += `<td>${hoTen}</td>`;
                    html += `<td><span class="badge bg-${statusBadge}">${statusText}</span></td>`;
                    html += `<td>
                        <button class="btn btn-sm btn-warning" onclick="app.editUser('${user.MaTK}')" title="Sửa">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-${isActive ? 'secondary' : 'success'}" 
                                onclick="app.toggleUserStatus('${user.MaTK}')" 
                                title="${isActive ? 'Khóa tài khoản' : 'Mở khóa tài khoản'}">
                            <i class="bi bi-${isActive ? 'lock' : 'unlock'}"></i>
                        </button>
                        ${user.Role !== 'admin' ? `
                            <button class="btn btn-sm btn-danger" 
                                    onclick="app.deleteUser('${user.MaTK}', '${user.TenDangNhap}')" 
                                    title="Xóa tài khoản">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    </td>`;
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            },
            
            async toggleUserStatus(maTK) {
                if (!confirm('Bạn có chắc muốn thay đổi trạng thái người dùng này?')) return;
                
                console.log('Toggle user status:', maTK);
                
                try {
                    const response = await this.apiCall(`/users/${maTK}/toggle`, {
                        method: 'PATCH'
                    });
                    
                    console.log('Toggle response:', response);
                    
                    if (!response) {
                        this.showAlert('Không nhận được phản hồi từ server', 'danger');
                        return;
                    }
                    
                    if (response.success) {
                        this.showAlert('Cập nhật trạng thái thành công', 'success');
                        this.loadUsers();
                    } else {
                        this.showAlert(response.message || 'Có lỗi xảy ra', 'danger');
                    }
                } catch (error) {
                    console.error('Toggle status error:', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                }
            },
            
            async editUser(maTK) {
                try {
                    // Lấy thông tin người dùng
                    const response = await this.apiCall('/users');
                    if (!response || !response.success) {
                        this.showAlert('Không thể tải thông tin người dùng', 'danger');
                        return;
                    }
                    
                    const user = response.data.find(u => u.MaTK === maTK);
                    if (!user) {
                        this.showAlert('Không tìm thấy người dùng', 'danger');
                        return;
                    }
                    
                    // Điền thông tin vào form
                    document.getElementById('editMaTK').value = user.MaTK;
                    document.getElementById('editTenDangNhap').value = user.TenDangNhap;
                    document.getElementById('editEmail').value = user.Email;
                    document.getElementById('editRole').value = user.Role;
                    document.getElementById('editMatKhau').value = '';
                    
                    // Hiển thị role
                    const roleText = user.Role === 'admin' ? 'Quản trị viên' :
                                   user.Role === 'giaovien' ? 'Giáo viên' : 'Học sinh';
                    document.getElementById('editRoleDisplay').value = roleText;
                    
                    // Ẩn tất cả các fields role
                    document.querySelectorAll('.role-edit-fields').forEach(el => el.style.display = 'none');
                    
                    // Hiển thị section phân quyền cho admin và giaovien
                    const permSection = document.getElementById('editPermissionsSection');
                    if (user.Role === 'admin' || user.Role === 'giaovien') {
                        permSection.style.display = 'block';
                        // TODO: Load và check permissions của user từ database
                        // Hiện tại để mặc định unchecked
                        document.querySelectorAll('#editPermissionsSection input[type="checkbox"]').forEach(cb => {
                            cb.checked = false;
                        });
                    } else {
                        permSection.style.display = 'none';
                    }
                    
                    // Hiển thị và điền thông tin theo role
                    if (user.Role === 'hocsinh' && user.ThongTinHocSinh) {
                        document.getElementById('editHocSinhFields').style.display = 'block';
                        document.getElementById('editHoTenHS').value = user.ThongTinHocSinh.HoTen || '';
                        document.getElementById('editLop').value = user.ThongTinHocSinh.Lop || '';
                        document.getElementById('editTruong').value = user.ThongTinHocSinh.Truong || '';
                    } else if (user.Role === 'giaovien' && user.ThongTinGiaoVien) {
                        document.getElementById('editGiaoVienFields').style.display = 'block';
                        document.getElementById('editHoTenGV').value = user.ThongTinGiaoVien.HoTen || '';
                        document.getElementById('editSoDienThoai').value = user.ThongTinGiaoVien.SoDienThoai || '';
                        document.getElementById('editChuyenMon').value = user.ThongTinGiaoVien.ChuyenMon || '';
                    }
                    
                    // Hiển thị modal
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                    
                } catch (error) {
                    this.showAlert('Lỗi khi tải thông tin: ' + error.message, 'danger');
                }
            },
            
            async updateUser() {
                const form = document.getElementById('editUserForm');
                const formData = new FormData(form);
                const maTK = document.getElementById('editMaTK').value;
                
                // Validate email
                if (!formData.get('Email')) {
                    this.showAlert('Vui lòng nhập email', 'warning');
                    return;
                }
                
                // Convert FormData to object (chỉ lấy fields có giá trị)
                const updateData = {};
                formData.forEach((value, key) => {
                    if (value && key !== 'MaTK' && key !== 'Role') {
                        updateData[key] = value;
                    }
                });
                
                // Nếu không có MatKhau, xóa khỏi update data
                if (!updateData.MatKhau) {
                    delete updateData.MatKhau;
                }
                
                console.log('Updating user:', maTK, updateData);
                
                try {
                    const response = await this.apiCall(`/users/${maTK}`, {
                        method: 'PUT',
                        body: JSON.stringify(updateData)
                    });
                    
                    console.log('Update response:', response);
                    
                    // Kiểm tra response
                    if (!response) {
                        this.showAlert('Không nhận được phản hồi từ server', 'danger');
                        return;
                    }
                    
                    if (response.success) {
                        this.showAlert('Cập nhật người dùng thành công!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                        this.loadUsers(); // Reload user list
                    } else {
                        this.showAlert(response.message || 'Có lỗi xảy ra', 'danger');
                    }
                } catch (error) {
                    console.error('Update user error:', error);
                    this.showAlert('Không thể cập nhật người dùng: ' + error.message, 'danger');
                }
            },
            
            async createBackup() {
                if (!confirm('Bạn có chắc chắn muốn tạo bản sao lưu database?')) {
                    return;
                }

                try {
                    this.showAlert('Đang tạo backup...', 'info');
                    
                    const response = await this.apiCall('/backup', {
                        method: 'POST'
                    });

                    if (response.success) {
                        this.showAlert('✅ Backup thành công: ' + response.data.TenFile, 'success');
                        // Refresh backup list
                        if (typeof this.loadBackupHistory === 'function') {
                            this.loadBackupHistory();
                        }
                    } else {
                        throw new Error(response.message || 'Không thể tạo backup');
                    }
                } catch (error) {
                    console.error('Backup error:', error);
                    this.showAlert('Không thể tạo file backup: ' + error.message, 'danger');
                }
            },
            
            showRestoreModal() {
                const modal = document.createElement('div');
                modal.className = 'modal fade';
                modal.id = 'restoreModal';
                modal.innerHTML = `
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    Khôi phục Database
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-danger">
                                    <strong>⚠️ CẢNH BÁO NGHIÊM TRỌNG:</strong><br>
                                    • Restore sẽ GHI ĐÈ toàn bộ dữ liệu hiện tại<br>
                                    • Hệ thống sẽ tự động tạo backup an toàn trước khi restore<br>
                                    • Quá trình có thể mất 10-30 giây<br>
                                    • <strong>Không tắt trình duyệt trong quá trình restore!</strong>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Chọn file backup (.sql) *</label>
                                    <input type="file" class="form-control" id="restoreFileInput" accept=".sql,.txt" required>
                                    <small class="text-muted">File phải là .sql được export từ hệ thống này</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="confirmRestoreCheck" required>
                                    <label class="form-check-label fw-bold text-danger" for="confirmRestoreCheck">
                                        Tôi hiểu rủi ro và muốn tiếp tục restore
                                    </label>
                                </div>

                                <div class="alert alert-info mb-0">
                                    <strong>💡 Khuyến nghị:</strong><br>
                                    • Test trên database riêng trước khi restore production<br>
                                    • Đọc file HUONG_DAN_TEST_RESTORE_AN_TOAN.md<br>
                                    • Có sẵn kế hoạch rollback nếu cần
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Hủy
                                </button>
                                <button type="button" class="btn btn-danger" id="executeRestoreBtn">
                                    <i class="bi bi-arrow-clockwise"></i> Bắt đầu Restore
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modal);
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
                
                // Bind button event
                document.getElementById('executeRestoreBtn').addEventListener('click', () => {
                    this.executeRestore();
                });
                
                // Cleanup after close
                modal.addEventListener('hidden.bs.modal', () => {
                    document.body.removeChild(modal);
                });
            },

            async executeRestore() {
                const fileInput = document.getElementById('restoreFileInput');
                const confirmCheck = document.getElementById('confirmRestoreCheck');
                
                // Validation
                if (!fileInput.files || !fileInput.files[0]) {
                    this.showAlert('⚠️ Vui lòng chọn file backup', 'warning');
                    return;
                }
                
                if (!confirmCheck.checked) {
                    this.showAlert('⚠️ Vui lòng xác nhận bạn hiểu rủi ro', 'warning');
                    return;
                }
                
                // Confirm lần cuối
                const confirmed = confirm(
                    '⚠️⚠️⚠️ XÁC NHẬN LẦN CUỐI ⚠️⚠️⚠️\n\n' +
                    'BẠN CHẮC CHẮN MUỐN RESTORE?\n\n' +
                    '• Toàn bộ dữ liệu hiện tại sẽ bị THAY THẾ!\n' +
                    '• Users đang online có thể bị ngắt kết nối!\n' +
                    '• Hệ thống sẽ tạo backup an toàn trước khi restore!\n\n' +
                    'Nhấn OK để tiếp tục, Cancel để hủy.'
                );
                
                if (!confirmed) {
                    return;
                }
                
                try {
                    // Disable button to prevent double-click
                    const restoreBtn = document.getElementById('executeRestoreBtn');
                    restoreBtn.disabled = true;
                    restoreBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang restore...';
                    
                    this.showAlert('🔄 Đang tạo backup an toàn và restore database... Vui lòng đợi!', 'info');
                    
                    const formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    
                    const response = await fetch('/api/restore', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + this.token,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showAlert(
                            '✅ Khôi phục database thành công!\n' +
                            '📁 Backup an toàn: ' + data.safety_backup + '\n' +
                            '🔄 Trang sẽ tự động tải lại sau 3 giây...',
                            'success'
                        );
                        
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('restoreModal'));
                        if (modal) modal.hide();
                        
                        // Reload page sau 3 giây
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        throw new Error(data.message || 'Restore failed');
                    }
                    
                } catch (error) {
                    console.error('Restore error:', error);
                    this.showAlert(
                        '❌ Không thể restore database!\n\n' +
                        'Lỗi: ' + error.message + '\n\n' +
                        '💡 Kiểm tra:\n' +
                        '• File backup có đúng định dạng không?\n' +
                        '• File có bị corrupt không?\n' +
                        '• Xem log tại storage/logs/laravel.log',
                        'danger'
                    );
                    
                    // Re-enable button
                    const restoreBtn = document.getElementById('executeRestoreBtn');
                    if (restoreBtn) {
                        restoreBtn.disabled = false;
                        restoreBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Bắt đầu Restore';
                    }
                }
            },
            
            showAlert(message, type = 'info') {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-float`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            },

            // ================================================
            // FUNCTIONS CHO CHỨC NĂNG CHỌN ĐỀ THI - UR-02.1
            // ================================================
            
            // Biến để lưu đề thi đang chọn
            selectedExam: null,

            /**
             * Load danh sách đề thi
             */
            async loadDanhSachDeThi() {
                try {
                    console.log('🔍 Loading danh sách đề thi...');
                    const searchTerm = document.getElementById('searchExam')?.value || '';
                    const sortBy = document.getElementById('sortExam')?.value || 'newest';
                    
                    const response = await this.apiCall('/de-thi', {
                        method: 'GET'
                    });
                    
                    console.log('📊 API Response:', response);
                    
                    if (response && response.success) {
                        // API trả về data trong response.data.data (pagination)
                        let exams = response.data.data || response.data || [];
                        
                        console.log('✅ Số đề thi:', exams.length);
                        
                        // Lọc theo từ khóa tìm kiếm
                        if (searchTerm) {
                            exams = exams.filter(exam => 
                                exam.TenDe.toLowerCase().includes(searchTerm.toLowerCase())
                            );
                        }
                        
                        // Sắp xếp
                        if (sortBy === 'newest') {
                            exams.sort((a, b) => new Date(b.NgayTao) - new Date(a.NgayTao));
                        } else if (sortBy === 'oldest') {
                            exams.sort((a, b) => new Date(a.NgayTao) - new Date(b.NgayTao));
                        } else if (sortBy === 'name') {
                            exams.sort((a, b) => a.TenDe.localeCompare(b.TenDe));
                        }
                        
                        this.displayDanhSachDeThi(exams);
                    } else {
                        console.error('❌ API failed:', response);
                        this.showAlert('Không thể tải danh sách đề thi', 'danger');
                        // Hiển thị thông báo trống
                        this.displayDanhSachDeThi([]);
                    }
                } catch (error) {
                    console.error('❌ Error:', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                    // Hiển thị thông báo trống
                    this.displayDanhSachDeThi([]);
                }
            },

            /**
             * Hiển thị danh sách đề thi
             */
            displayDanhSachDeThi(exams) {
                const container = document.getElementById('examListContent');
                
                if (!exams || exams.length === 0) {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-warning shadow-sm text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Chưa có đề thi nào</h5>
                                <p class="mb-0">Hiện tại chưa có đề thi nào được công bố. Vui lòng quay lại sau!</p>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                let html = '';
                exams.forEach(exam => {
                    const date = new Date(exam.NgayTao);
                    const formattedDate = date.toLocaleDateString('vi-VN');
                    
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0 exam-card-hover" style="transition: all 0.3s;">
                                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h5 class="mb-0">
                                        <i class="bi bi-file-earmark-text-fill"></i> ${exam.TenDe}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3" style="min-height: 40px;">
                                        ${exam.MoTa || '<em>Không có mô tả</em>'}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-primary rounded-pill">
                                            <i class="bi bi-list-ol"></i> ${exam.SoLuongCauHoi} câu hỏi
                                        </span>
                                        <span class="badge bg-info rounded-pill">
                                            <i class="bi bi-clock-fill"></i> ${exam.ThoiGianLamBai} phút
                                        </span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="small text-muted mb-3">
                                        <div class="mb-1">
                                            <i class="bi bi-person-fill"></i> <strong>Giáo viên:</strong> ${exam.TenGiaoVien || 'N/A'}
                                        </div>
                                        <div>
                                            <i class="bi bi-calendar-event"></i> <strong>Ngày tạo:</strong> ${formattedDate}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-0">
                                    <button class="btn btn-success w-100 btn-lg" onclick="app.showConfirmStartModal('${exam.MaDe}')" style="font-weight: 600;">
                                        <i class="bi bi-play-circle-fill"></i> Làm bài
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            },

            /**
             * Hiển thị modal xác nhận bắt đầu làm bài
             */
            async showConfirmStartModal(maDe) {
                try {
                    console.log('🔍 Fetching exam details for:', maDe);
                    
                    // Lấy chi tiết đề thi
                    const response = await this.apiCall(`/de-thi/${maDe}`, {
                        method: 'GET'
                    });
                    
                    console.log('📊 API Response:', response);
                    
                    // Kiểm tra response có hợp lệ không
                    if (!response) {
                        console.error('❌ Response is null or undefined');
                        this.showAlert('Không thể kết nối đến server', 'danger');
                        return;
                    }
                    
                    if (!response.success) {
                        console.error('❌ API returned error:', response);
                        this.showAlert(response.message || 'Không thể tải thông tin đề thi', 'danger');
                        return;
                    }
                    
                    this.selectedExam = response.data;
                    
                    console.log('✅ Selected Exam:', this.selectedExam);
                    
                    // Hiển thị thông tin đề thi
                    const infoDiv = document.getElementById('examInfoPreview');
                    if (!infoDiv) {
                        console.error('❌ Element examInfoPreview not found!');
                        this.showAlert('Lỗi: Không tìm thấy element hiển thị', 'danger');
                        return;
                    }
                    
                    infoDiv.innerHTML = `
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">${this.selectedExam.TenDe}</h6>
                                <p class="mb-2"><strong>Số câu hỏi:</strong> ${this.selectedExam.SoLuongCauHoi}</p>
                                <p class="mb-2"><strong>Thời gian:</strong> ${this.selectedExam.ThoiGianLamBai} phút</p>
                                <p class="mb-0"><strong>Giáo viên:</strong> ${this.selectedExam.TenGiaoVien || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    
                    // Hiển thị modal
                    const modalEl = document.getElementById('confirmStartExamModal');
                    if (!modalEl) {
                        console.error('❌ Modal confirmStartExamModal not found!');
                        this.showAlert('Lỗi: Không tìm thấy modal', 'danger');
                        return;
                    }
                    
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    
                    console.log('✅ Modal displayed successfully');
                    
                } catch (error) {
                    console.error('❌ Exception in showConfirmStartModal:', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                }
            },

            // ========================================
            // CHỌN ĐỀ THI FUNCTIONS - UR-02.1 COMPLETE
            // ========================================

            selectedExam: null,

            /**
             * Xác nhận và bắt đầu làm bài
             */
            async confirmStartExam() {
                console.log('=== confirmStartExam() CALLED ===');
                
                if (!this.selectedExam) {
                    console.error('ERROR: No exam selected');
                    this.showAlert('Lỗi: Chưa chọn đề thi', 'danger');
                    return;
                }
                
                console.log('Selected Exam:', this.selectedExam);
                
                // Show loader
                this.showLoader();
                
                try {
                    console.log('Calling API: /de-thi/' + this.selectedExam.MaDe + '/bat-dau');
                    console.log('Token available:', !!this.token);
                    console.log('User info:', this.user);
                    
                    // Gọi API bắt đầu làm bài
                    const response = await this.apiCall(`/de-thi/${this.selectedExam.MaDe}/bat-dau`, {
                        method: 'POST'
                    });
                    
                    console.log('API Response:', response);
                    
                    // Kiểm tra response null
                    if (!response) {
                        console.error('ERROR: Response is null or undefined');
                        this.showAlert('Lỗi: Không nhận được phản hồi từ server', 'danger');
                        return;
                    }
                    
                    if (response.success) {
                        console.log('SUCCESS: Starting exam with data:', response.data);
                        
                        // Lưu thông tin bài làm vào sessionStorage
                        sessionStorage.setItem('currentExam', JSON.stringify(response.data));
                        
                        // Đóng modal
                        const modalEl = document.getElementById('confirmStartExamModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
                        
                        // Chuyển sang màn hình làm bài
                        this.showScreen('lambaithi');
                        this.startExam(response.data);
                    } else {
                        console.error('ERROR: API returned success=false:', response.message);
                        this.showAlert(response.message || 'Không thể bắt đầu làm bài', 'danger');
                    }
                } catch (error) {
                    console.error('EXCEPTION in confirmStartExam():', error);
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                } finally {
                    this.hideLoader();
                }
            },

            // ========================================
            // EXAM TAKING FUNCTIONS - UR-02.2
            // ========================================
            examData: null,
            currentQuestionIndex: 0,
            answers: {},
            timeRemaining: 0,
            timerInterval: null,
            autoSaveInterval: null,
            cheatingAttempts: 0,

            /**
             * Bắt đầu làm bài thi
             */
            startExam(data) {
                console.log('=== START EXAM ===');
                console.log('Exam data:', data);
                console.log('data.MaHS:', data.MaHS);
                console.log('this.user:', this.user);
                console.log('this.user.detail:', this.user.detail);
                
                this.examData = data;
                this.currentQuestionIndex = 0;
                this.answers = {};
                this.cheatingAttempts = 0;
                this.timeRemaining = data.ThoiGianLamBai * 60; // Convert to seconds

                // Lưu thông tin học sinh để dùng khi nộp bài
                // Lấy MaHS từ backend response hoặc từ user detail
                const maHS = data.MaHS || this.user.detail?.MaHS;
                console.log('Determined MaHS:', maHS);
                
                if (maHS) {
                    sessionStorage.setItem('hocSinhInfo', JSON.stringify({
                        MaHS: maHS,
                        HoTen: this.user.detail?.HoTen || this.user.TenDangNhap
                    }));
                    console.log('✅ Saved student info to sessionStorage:', maHS);
                } else {
                    console.error('❌ ERROR: MaHS not found in response or user detail!');
                    console.log('Backend response:', data);
                    console.log('User object:', this.user);
                }

                // Display exam info
                document.getElementById('examTitle').textContent = data.TenDe;
                document.getElementById('totalQuestions').textContent = data.CauHoi.length;

                // Render question navigator
                this.renderQuestionNavigator();

                // Display first question
                this.displayQuestion(0);

                // Start timer
                this.startTimer();

                // Start auto-save (every 60 seconds)
                this.startAutoSave();

                // Enable cheating detection
                this.enableCheatingDetection();

                console.log('Exam started successfully');
            },

            /**
             * Render question navigator (số câu hỏi bên sidebar)
             */
            renderQuestionNavigator() {
                const container = document.getElementById('questionNavigator');
                let html = '';

                this.examData.CauHoi.forEach((q, index) => {
                    const isActive = index === this.currentQuestionIndex ? 'active' : '';
                    const isAnswered = this.answers[q.MaCauHoi] ? 'answered' : '';
                    html += `
                        <button class="question-nav-btn ${isActive} ${isAnswered}" 
                                onclick="app.goToQuestion(${index})"
                                title="Câu ${index + 1}">
                            ${index + 1}
                        </button>
                    `;
                });

                container.innerHTML = html;
                this.updateProgress();
            },

            /**
             * Hiển thị câu hỏi
             */
            displayQuestion(index) {
                if (!this.examData || index < 0 || index >= this.examData.CauHoi.length) {
                    return;
                }

                this.currentQuestionIndex = index;
                const question = this.examData.CauHoi[index];

                const html = `
                    <div class="question-container">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="text-primary">Câu ${index + 1}/${this.examData.CauHoi.length}</h5>
                            <span class="badge bg-info">${question.DoKho || 'Trung bình'}</span>
                        </div>
                        
                        <div class="question-text">
                            ${question.NoiDung}
                        </div>

                        <div class="answers-container mt-4">
                            ${['A', 'B', 'C', 'D'].map(option => {
                                const isSelected = this.answers[question.MaCauHoi] === option;
                                return `
                                    <div class="answer-option ${isSelected ? 'selected' : ''}" 
                                         onclick="app.selectAnswer('${question.MaCauHoi}', '${option}')">
                                        <input type="radio" 
                                               name="answer_${question.MaCauHoi}" 
                                               value="${option}"
                                               ${isSelected ? 'checked' : ''}
                                               onchange="app.selectAnswer('${question.MaCauHoi}', '${option}')">
                                        <span class="answer-label">${option}.</span>
                                        <span>${question['DapAn' + option]}</span>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;

                document.getElementById('questionContent').innerHTML = html;

                // Update navigator
                this.renderQuestionNavigator();

                // Update prev/next buttons
                document.getElementById('btnPrev').disabled = (index === 0);
                document.getElementById('btnNext').disabled = (index === this.examData.CauHoi.length - 1);
            },

            /**
             * Chọn đáp án
             */
            selectAnswer(maCauHoi, answer) {
                this.answers[maCauHoi] = answer;
                this.displayQuestion(this.currentQuestionIndex);
            },

            /**
             * Đi đến câu hỏi cụ thể
             */
            goToQuestion(index) {
                this.displayQuestion(index);
            },

            /**
             * Câu trước
             */
            prevQuestion() {
                if (this.currentQuestionIndex > 0) {
                    this.displayQuestion(this.currentQuestionIndex - 1);
                }
            },

            /**
             * Câu sau
             */
            nextQuestion() {
                if (this.currentQuestionIndex < this.examData.CauHoi.length - 1) {
                    this.displayQuestion(this.currentQuestionIndex + 1);
                }
            },

            /**
             * Bắt đầu đếm ngược thời gian
             */
            startTimer() {
                this.updateTimerDisplay();

                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    this.updateTimerDisplay();

                    // Auto-submit when time is up
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        this.showAlert('Hết giờ! Bài thi sẽ được nộp tự động.', 'warning');
                        setTimeout(() => {
                            this.submitExam();
                        }, 2000);
                    }

                    // Warning when 5 minutes left
                    if (this.timeRemaining === 300) {
                        this.showAlert('Còn 5 phút! Hãy kiểm tra lại bài làm.', 'warning');
                    }
                }, 1000);
            },

            /**
             * Cập nhật hiển thị timer
             */
            updateTimerDisplay() {
                const hours = Math.floor(this.timeRemaining / 3600);
                const minutes = Math.floor((this.timeRemaining % 3600) / 60);
                const seconds = this.timeRemaining % 60;

                const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                document.getElementById('timerText').textContent = timeString;
                document.getElementById('mainTimer').textContent = timeString;

                // Change color when time is running out
                if (this.timeRemaining < 300) { // Less than 5 minutes
                    document.getElementById('mainTimer').className = 'fw-bold text-danger';
                }
            },

            /**
             * Cập nhật progress bar
             */
            updateProgress() {
                const answered = Object.keys(this.answers).length;
                const total = this.examData.CauHoi.length;
                const percentage = (answered / total) * 100;

                document.getElementById('progressBar').style.width = percentage + '%';
                document.getElementById('answeredCount').textContent = answered;
            },

            /**
             * Hiển thị modal xác nhận nộp bài
             */
            showSubmitConfirm() {
                const answered = Object.keys(this.answers).length;
                const unanswered = this.examData.CauHoi.length - answered;

                document.getElementById('submitAnswered').textContent = answered;
                document.getElementById('submitUnanswered').textContent = unanswered;
                
                const hours = Math.floor(this.timeRemaining / 3600);
                const minutes = Math.floor((this.timeRemaining % 3600) / 60);
                document.getElementById('submitTimeLeft').textContent = `${hours}:${minutes.toString().padStart(2, '0')}`;

                const modal = new bootstrap.Modal(document.getElementById('submitConfirmModal'));
                modal.show();
            },

            /**
             * Nộp bài thi - UR-02.2
             */
            async submitExam() {
                try {
                    console.log('=== SUBMIT EXAM START ===');
                    
                    // Close modal if open
                    const modalElement = document.getElementById('submitConfirmModal');
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();

                    // Stop timer and auto-save
                    clearInterval(this.timerInterval);
                    clearInterval(this.autoSaveInterval);
                    this.disableCheatingDetection();

                    // Get student info
                    const hocSinhInfo = JSON.parse(sessionStorage.getItem('hocSinhInfo') || '{}');
                    
                    if (!hocSinhInfo.MaHS) {
                        this.showAlert('Không tìm thấy thông tin học sinh. Vui lòng đăng nhập lại.', 'danger');
                        return;
                    }

                    // Prepare answers array
                    const cauTraLoi = [];
                    for (const [maCH, dapAnChon] of Object.entries(this.answers)) {
                        if (dapAnChon) {  // Only include answered questions
                            cauTraLoi.push({
                                MaCH: maCH,
                                DapAnChon: dapAnChon
                            });
                        }
                    }

                    // Prepare submission data
                    const submissionData = {
                        MaDe: this.examData.MaDe,
                        MaHS: hocSinhInfo.MaHS,
                        CauTraLoi: cauTraLoi,
                        ThoiGianBatDau: this.examData.ThoiGianBatDau
                    };

                    console.log('Submitting exam:', submissionData);

                    // Call API
                    const response = await this.apiCall('/bai-lam/nop-bai', {
                        method: 'POST',
                        body: JSON.stringify(submissionData)
                    });

                    console.log('Submit response:', response);

                    if (response && response.success) {
                        this.showAlert('Nộp bài thành công! Đang chuyển đến kết quả...', 'success');
                        
                        // Save result to sessionStorage
                        sessionStorage.setItem('examResult', JSON.stringify(response.data));
                        
                        // Redirect to result screen after 1 second
                        setTimeout(() => {
                            this.showScreen('ketqua');
                            this.displayExamResult(response.data);
                        }, 1000);
                    } else {
                        this.showAlert(response?.message || 'Không thể nộp bài', 'danger');
                    }
                } catch (error) {
                    console.error('=== SUBMIT ERROR ===');
                    console.error('Error:', error);
                    this.showAlert('Lỗi khi nộp bài: ' + error.message, 'danger');
                }
            },

            // ========================================
            // AUTO-SAVE FUNCTIONALITY - UR-05.2
            // ========================================

            /**
             * Bắt đầu auto-save
             */
            startAutoSave() {
                this.autoSaveInterval = setInterval(async () => {
                    await this.saveProgress();
                }, 60000); // Every 60 seconds
            },

            /**
             * Lưu tiến độ làm bài
             */
            async saveProgress() {
                if (!this.examData || !this.examData.MaBaiLam) return;

                try {
                    const indicator = document.getElementById('autoSaveIndicator');
                    indicator.classList.remove('d-none', 'alert-success', 'alert-danger');
                    indicator.classList.add('alert-info');
                    indicator.innerHTML = '<i class="bi bi-arrow-repeat"></i> Đang lưu...';

                    const data = {
                        MaBaiLam: this.examData.MaBaiLam,
                        CauTraLoi: this.answers
                    };

                    const response = await this.apiCall('/luu-nhap', {
                        method: 'POST',
                        body: JSON.stringify(data)
                    });

                    if (response.success) {
                        indicator.classList.remove('alert-info');
                        indicator.classList.add('alert-success');
                        indicator.innerHTML = '<i class="bi bi-check-circle"></i> Đã lưu tự động';
                        
                        setTimeout(() => {
                            indicator.classList.add('d-none');
                        }, 3000);
                    }
                } catch (error) {
                    const indicator = document.getElementById('autoSaveIndicator');
                    indicator.classList.remove('alert-info');
                    indicator.classList.add('alert-danger');
                    indicator.innerHTML = '<i class="bi bi-exclamation-circle"></i> Lỗi khi lưu';
                    console.error('Auto-save error:', error);
                }
            },

            // ========================================
            // CHEATING DETECTION - UR-05.1
            // ========================================

            cheatingHandlers: {},

            /**
             * Bật cheating detection
             */
            enableCheatingDetection() {
                // Visibility change (switch tab/minimize window)
                this.cheatingHandlers.visibilityChange = () => {
                    if (document.hidden && this.examData) {
                        this.logCheatingAttempt('SWITCH_TAB');
                    }
                };
                document.addEventListener('visibilitychange', this.cheatingHandlers.visibilityChange);

                // Window blur (click outside)
                this.cheatingHandlers.blur = () => {
                    if (this.examData) {
                        this.logCheatingAttempt('LEAVE_WINDOW');
                    }
                };
                window.addEventListener('blur', this.cheatingHandlers.blur);

                // Prevent right-click
                this.cheatingHandlers.contextmenu = (e) => {
                    if (this.examData) {
                        e.preventDefault();
                        this.showAlert('Không được nhấp chuột phải khi làm bài!', 'warning');
                    }
                };
                document.addEventListener('contextmenu', this.cheatingHandlers.contextmenu);

                // Prevent copy
                this.cheatingHandlers.copy = (e) => {
                    if (this.examData) {
                        e.preventDefault();
                        this.showAlert('Không được sao chép nội dung!', 'warning');
                    }
                };
                document.addEventListener('copy', this.cheatingHandlers.copy);
            },

            /**
             * Tắt cheating detection
             */
            disableCheatingDetection() {
                if (this.cheatingHandlers.visibilityChange) {
                    document.removeEventListener('visibilitychange', this.cheatingHandlers.visibilityChange);
                }
                if (this.cheatingHandlers.blur) {
                    window.removeEventListener('blur', this.cheatingHandlers.blur);
                }
                if (this.cheatingHandlers.contextmenu) {
                    document.removeEventListener('contextmenu', this.cheatingHandlers.contextmenu);
                }
                if (this.cheatingHandlers.copy) {
                    document.removeEventListener('copy', this.cheatingHandlers.copy);
                }
            },

            /**
             * Ghi nhận gian lận
             */
            async logCheatingAttempt(loaiGianLan) {
                if (!this.examData || !this.examData.MaBaiLam) return;

                this.cheatingAttempts++;

                // Update UI
                const warning = document.getElementById('cheatingWarning');
                warning.classList.remove('d-none');
                document.getElementById('cheatingCount').textContent = this.cheatingAttempts;

                try {
                    await this.apiCall('/ghi-nhan-gian-lan', {
                        method: 'POST',
                        body: JSON.stringify({
                            MaBaiLam: this.examData.MaBaiLam,
                            LoaiGianLan: loaiGianLan
                        })
                    });

                    // Show warning
                    if (this.cheatingAttempts === 1) {
                        this.showAlert('Cảnh báo: Đã phát hiện hành vi chuyển tab/cửa sổ. Vui lòng tập trung làm bài!', 'warning');
                    } else if (this.cheatingAttempts >= 3) {
                        this.showAlert('Cảnh báo nghiêm trọng: Bạn đã vi phạm ' + this.cheatingAttempts + ' lần. Giáo viên sẽ được thông báo!', 'danger');
                    }
                } catch (error) {
                    console.error('Error logging cheating attempt:', error);
                }
            },

            // ========================================
            // RESULT DISPLAY FUNCTIONS - UR-02.3 & UR-02.4
            // ========================================

            examResult: null,

            /**
             * Hiển thị kết quả thi - UR-02.3
             */
            displayExamResult(result) {
                this.examResult = result;

                // Display score
                document.getElementById('finalScore').textContent = result.Diem.toFixed(1);

                // Display statistics
                document.getElementById('correctCount').textContent = result.SoCauDung;
                document.getElementById('wrongCount').textContent = result.SoCauSai;
                document.getElementById('percentCorrect').textContent = ((result.SoCauDung / result.TongSoCau) * 100).toFixed(1) + '%';

                // Calculate time taken (duration in seconds from exam start)
                let timeInSeconds = 0;
                if (this.examData && this.examData.ThoiGianLamBai) {
                    // Tính từ thời gian bắt đầu đến giờ (hoặc từ timeRemaining)
                    timeInSeconds = (this.examData.ThoiGianLamBai * 60) - this.timeRemaining;
                }
                
                // Display time taken
                const minutes = Math.floor(timeInSeconds / 60);
                const seconds = timeInSeconds % 60;
                document.getElementById('timeTaken').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                // Display exam info
                document.getElementById('resultExamName').textContent = result.TenDe || '-';
                document.getElementById('resultDate').textContent = new Date().toLocaleDateString('vi-VN');
                document.getElementById('resultDuration').textContent = `${minutes} phút ${seconds} giây`;

                // Update result title
                let title = 'Xuất sắc!';
                if (result.Diem < 5) title = 'Cần cố gắng thêm!';
                else if (result.Diem < 7) title = 'Khá tốt!';
                else if (result.Diem < 9) title = 'Rất tốt!';
                document.getElementById('resultTitle').textContent = title;

                // Create circular progress chart (optional - simple version)
                this.drawScoreCircle(result.Diem);
            },

            /**
             * Vẽ biểu đồ tròn điểm số (simple version)
             */
            drawScoreCircle(score) {
                // Simple percentage display (can be enhanced with Chart.js later)
                const percentage = (score / 10) * 100;
                // For now, just show the score - can add Chart.js circle chart later
            },

            /**
             * Hiển thị chi tiết đáp án - UR-02.4
             */
            async showDetailedResults() {
                if (!this.examResult || !this.examResult.MaBaiLam) {
                    this.showAlert('Không có thông tin kết quả', 'warning');
                    return;
                }

                try {
                    // Get detailed result from API
                    const response = await this.apiCall(`/bai-lam/${this.examResult.MaBaiLam}/chi-tiet`);

                    if (response && response.success) {
                        this.renderDetailedResults(response.data);
                        
                        // Show modal
                        const modal = new bootstrap.Modal(document.getElementById('detailedResultModal'));
                        modal.show();
                    }
                } catch (error) {
                    this.showAlert('Không thể tải chi tiết đáp án: ' + (error.message || 'Lỗi không xác định'), 'danger');
                    console.error('showDetailedResults error:', error);
                }
            },

            /**
             * Render chi tiết từng câu hỏi
             */
            renderDetailedResults(data) {
                const container = document.getElementById('detailedResultContent');
                let html = '';

                // API trả về data.cauHoi thay vì data.ChiTiet
                const cauHoiList = data.cauHoi || data.ChiTiet || [];

                cauHoiList.forEach((item, index) => {
                    const isCorrect = item.DapAnChon === item.DapAnDung || item.IsDung;
                    const questionClass = isCorrect ? 'correct' : 'wrong';

                    html += `
                        <div class="question-review ${questionClass}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">
                                    <span class="badge ${isCorrect ? 'bg-success' : 'bg-danger'}">
                                        ${isCorrect ? '✓ Đúng' : '✗ Sai'}
                                    </span>
                                    Câu ${index + 1}
                                </h6>
                            </div>
                            
                            <p class="mb-3"><strong>${item.NoiDung}</strong></p>

                            <div class="answers-review">
                                ${['A', 'B', 'C', 'D'].map(option => {
                                    let className = 'answer-review';
                                    let icon = '';
                                    
                                    if (option === item.DapAnDung) {
                                        className += ' correct-answer';
                                        icon = '<i class="bi bi-check-circle-fill text-success"></i> ';
                                    }
                                    if (option === item.DapAnChon && option !== item.DapAnDung) {
                                        className += ' user-wrong';
                                        icon = '<i class="bi bi-x-circle-fill text-danger"></i> ';
                                    }
                                    
                                    return `
                                        <div class="${className}">
                                            ${icon}<strong>${option}.</strong> ${item['DapAn' + option]}
                                            ${option === item.DapAnChon ? ' <span class="badge bg-primary">Bạn chọn</span>' : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>

                            ${item.GiaiThich ? `
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong><i class="bi bi-lightbulb"></i> Giải thích:</strong> ${item.GiaiThich}
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                container.innerHTML = html || '<p class="text-center text-muted">Không có dữ liệu chi tiết</p>';
            },

            // ========================================
            // STATISTICS & CHARTS FUNCTIONS - UR-02.5
            // ========================================

            chartInstances: {},

            /**
             * Load thống kê tiến độ
             */
            async loadThongKe() {
                try {
                    // Get exam history
                    const response = await this.apiCall('/lich-su-thi');
                    
                    if (response && response.success) {
                        const data = response.data;
                        this.displayThongKe(data);
                    } else {
                        this.showAlert('Không thể tải thống kê', 'warning');
                    }
                } catch (error) {
                    this.showAlert('Lỗi: ' + error.message, 'danger');
                    console.error('Load stats error:', error);
                }
            },

            /**
             * Hiển thị thống kê
             */
            displayThongKe(data) {
                if (!data || data.length === 0) {
                    document.getElementById('totalExamsDone').textContent = '0';
                    document.getElementById('avgScore').textContent = '0';
                    document.getElementById('highestScore').textContent = '0';
                    document.getElementById('avgAccuracy').textContent = '0%';
                    return;
                }

                // Calculate summary stats
                const totalExams = data.length;
                const avgScore = (data.reduce((sum, item) => sum + item.Diem, 0) / totalExams).toFixed(1);
                const highestScore = Math.max(...data.map(item => item.Diem)).toFixed(1);
                const avgAccuracy = (data.reduce((sum, item) => sum + (item.SoCauDung / item.TongSoCau * 100), 0) / totalExams).toFixed(1);

                // Update summary cards
                document.getElementById('totalExamsDone').textContent = totalExams;
                document.getElementById('avgScore').textContent = avgScore;
                document.getElementById('highestScore').textContent = highestScore;
                document.getElementById('avgAccuracy').textContent = avgAccuracy + '%';

                // Render charts
                this.renderScoreTimeChart(data);
                this.renderResultPieChart(data);
                this.renderSubjectBarChart(data);
                this.renderRecentExamsTable(data);
            },

            /**
             * Biểu đồ điểm số theo thời gian (Line Chart)
             */
            renderScoreTimeChart(data) {
                const ctx = document.getElementById('scoreTimeChart');
                if (!ctx) return;

                // Destroy previous chart
                if (this.chartInstances.scoreTime) {
                    this.chartInstances.scoreTime.destroy();
                }

                // Sort by date
                const sortedData = [...data].sort((a, b) => new Date(a.ThoiGianNop) - new Date(b.ThoiGianNop));

                this.chartInstances.scoreTime = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: sortedData.map(item => new Date(item.ThoiGianNop).toLocaleDateString('vi-VN')),
                        datasets: [{
                            label: 'Điểm số',
                            data: sortedData.map(item => item.Diem),
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Điểm: ' + context.parsed.y.toFixed(1);
                                    }
                                }
                            }
                        }
                    }
                });
            },

            /**
             * Biểu đồ tròn phân bố kết quả (Pie Chart)
             */
            renderResultPieChart(data) {
                const ctx = document.getElementById('resultPieChart');
                if (!ctx) return;

                if (this.chartInstances.resultPie) {
                    this.chartInstances.resultPie.destroy();
                }

                // Count by result categories
                const excellent = data.filter(item => item.Diem >= 9).length;
                const good = data.filter(item => item.Diem >= 7 && item.Diem < 9).length;
                const average = data.filter(item => item.Diem >= 5 && item.Diem < 7).length;
                const poor = data.filter(item => item.Diem < 5).length;

                this.chartInstances.resultPie = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Xuất sắc (9-10)', 'Giỏi (7-8.9)', 'Khá (5-6.9)', 'Yếu (<5)'],
                        datasets: [{
                            data: [excellent, good, average, poor],
                            backgroundColor: [
                                '#28a745',
                                '#0d6efd',
                                '#ffc107',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            },

            /**
             * Biểu đồ cột phân tích theo chủ đề (Bar Chart)
             */
            renderSubjectBarChart(data) {
                const ctx = document.getElementById('subjectBarChart');
                if (!ctx) return;

                if (this.chartInstances.subjectBar) {
                    this.chartInstances.subjectBar.destroy();
                }

                // Group by subject (assuming TenDe contains subject info)
                const subjectStats = {};
                data.forEach(item => {
                    // Extract subject from exam name (simple logic)
                    const subject = item.TenDe || 'Khác';
                    if (!subjectStats[subject]) {
                        subjectStats[subject] = { total: 0, count: 0 };
                    }
                    subjectStats[subject].total += item.Diem;
                    subjectStats[subject].count++;
                });

                const subjects = Object.keys(subjectStats);
                const avgScores = subjects.map(subject => 
                    (subjectStats[subject].total / subjectStats[subject].count).toFixed(1)
                );

                this.chartInstances.subjectBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: subjects,
                        datasets: [{
                            label: 'Điểm trung bình',
                            data: avgScores,
                            backgroundColor: 'rgba(13, 110, 253, 0.7)',
                            borderColor: '#0d6efd',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 10,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            },

            /**
             * Render bảng lịch sử gần đây
             */
            renderRecentExamsTable(data) {
                const container = document.getElementById('recentExamsTable');
                if (!container) return;

                // Get 10 most recent
                const recentData = [...data]
                    .sort((a, b) => new Date(b.ThoiGianNop) - new Date(a.ThoiGianNop))
                    .slice(0, 10);

                let html = `
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Đề thi</th>
                                <th>Điểm</th>
                                <th>Đúng/Tổng</th>
                                <th>Ngày làm</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                recentData.forEach(item => {
                    const scoreClass = item.Diem >= 7 ? 'text-success' : (item.Diem >= 5 ? 'text-warning' : 'text-danger');
                    const date = new Date(item.ThoiGianNop).toLocaleDateString('vi-VN');
                    
                    html += `
                        <tr>
                            <td>${item.TenDe}</td>
                            <td class="${scoreClass} fw-bold">${item.Diem.toFixed(1)}</td>
                            <td>${item.SoCauDung}/${item.TongSoCau}</td>
                            <td><small>${date}</small></td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                container.innerHTML = html;
            },

            /**
             * ===================================
             * DASHBOARD FUNCTIONS (Admin)
             * ===================================
             */
            
            async loadDashboard() {
                try {
                    console.log('Loading dashboard...');
                    // Call multiple endpoints in parallel
                    const [usersRes, examsRes, submissionsRes, questionsRes] = await Promise.all([
                        this.apiCall('/users'),  // FIXED: đổi từ /nguoi-dung sang /users
                        this.apiCall('/de-thi'),
                        this.apiCall('/lich-su-thi'),
                        this.apiCall('/cau-hoi')
                    ]);

                    console.log('Dashboard data:', { usersRes, examsRes, submissionsRes, questionsRes });

                    // Display summary stats (xử lý cả array và paginated data)
                    document.getElementById('totalUsersCount').textContent = 
                        usersRes?.total || usersRes?.data?.length || 0;
                    
                    document.getElementById('totalExamsCount').textContent = 
                        examsRes?.total || examsRes?.data?.length || 0;
                    
                    document.getElementById('totalSubmissionsCount').textContent = 
                        submissionsRes?.total || submissionsRes?.data?.length || 0;
                    
                    // FIXED: câu hỏi là paginated, lấy từ data.total
                    document.getElementById('totalQuestionsCount').textContent = 
                        questionsRes?.data?.total || questionsRes?.data?.data?.length || questionsRes?.total || 0;

                    // Render charts
                    this.renderActivityChart(submissionsRes?.data || []);
                    this.renderUserRoleChart(usersRes?.data || []);
                    
                    // Render tables
                    this.renderRecentSubmissionsTable(submissionsRes?.data || []);
                    this.renderSystemAlerts();
                    
                    // Render advanced statistics (UR-04.3 Enhancement)
                    this.renderTopStudents(submissionsRes?.data || []);
                    this.renderQuickStats(submissionsRes?.data || []);
                    this.renderCheatingDetection();

                } catch (error) {
                    console.error('Load dashboard error:', error);
                    this.showAlert('Không thể tải dashboard: ' + error.message, 'danger');
                }
            },

            /**
             * Biểu đồ hoạt động theo tháng (Line Chart)
             */
            renderActivityChart(submissions) {
                const ctx = document.getElementById('activityChart');
                if (!ctx) return;

                if (this.chartInstances.activity) {
                    this.chartInstances.activity.destroy();
                }

                // Group by month
                const monthlyData = {};
                submissions.forEach(item => {
                    const date = new Date(item.ThoiGianNop);
                    const monthKey = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
                    monthlyData[monthKey] = (monthlyData[monthKey] || 0) + 1;
                });

                // Get last 6 months
                const months = [];
                const counts = [];
                const today = new Date();
                for (let i = 5; i >= 0; i--) {
                    const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
                    const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    months.push(`Tháng ${d.getMonth() + 1}/${d.getFullYear()}`);
                    counts.push(monthlyData[key] || 0);
                }

                this.chartInstances.activity = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Số bài thi',
                            data: counts,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 10
                                }
                            }
                        }
                    }
                });
            },

            /**
             * Biểu đồ phân bố người dùng theo vai trò (Pie Chart)
             */
            renderUserRoleChart(users) {
                const ctx = document.getElementById('userRoleChart');
                if (!ctx) return;

                if (this.chartInstances.userRole) {
                    this.chartInstances.userRole.destroy();
                }

                // Count by role
                const roleCounts = {
                    'Học sinh': users.filter(u => u.Role === 'hocsinh').length,
                    'Giáo viên': users.filter(u => u.Role === 'giaovien').length,
                    'Admin': users.filter(u => u.Role === 'admin').length
                };

                this.chartInstances.userRole = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(roleCounts),
                        datasets: [{
                            data: Object.values(roleCounts),
                            backgroundColor: [
                                '#0d6efd',
                                '#28a745',
                                '#dc3545'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            },

            /**
             * Bảng bài thi gần đây (Dashboard version)
             */
            renderRecentSubmissionsTable(submissions) {
                const container = document.getElementById('recentSubmissionsTable');
                if (!container) return;

                if (!submissions || submissions.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center">Chưa có dữ liệu</p>';
                    return;
                }

                // Sort and take 10 most recent
                const recentData = [...submissions]
                    .sort((a, b) => new Date(b.ThoiGianNop) - new Date(a.ThoiGianNop))
                    .slice(0, 10);

                let html = `
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Đề thi</th>
                                <th>Điểm</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                recentData.forEach(item => {
                    const scoreClass = item.Diem >= 7 ? 'text-success' : (item.Diem >= 5 ? 'text-warning' : 'text-danger');
                    const date = new Date(item.ThoiGianNop).toLocaleDateString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    html += `
                        <tr>
                            <td><small>${item.MaNguoiDung || 'N/A'}</small></td>
                            <td><small>${item.TenDe}</small></td>
                            <td class="${scoreClass} fw-bold">${item.Diem.toFixed(1)}</td>
                            <td><small>${date}</small></td>
                        </tr>
                    `;
                });

                html += `
                        </tbody>
                    </table>
                `;

                container.innerHTML = html;
            },

            /**
             * Hiển thị cảnh báo hệ thống
             */
            renderSystemAlerts() {
                const container = document.getElementById('systemAlertsTable');
                if (!container) return;

                // Mock system alerts
                const alerts = [
                    { type: 'success', message: 'Hệ thống đang hoạt động bình thường', time: 'Vừa xong' },
                    { type: 'info', message: 'Database được backup tự động mỗi ngày', time: '2 giờ trước' },
                    { type: 'warning', message: 'Có 5 học sinh chưa hoàn thành bài thi', time: '3 giờ trước' }
                ];

                let html = '<div class="list-group list-group-flush">';
                alerts.forEach(alert => {
                    const iconClass = alert.type === 'success' ? 'bi-check-circle text-success' : 
                                     (alert.type === 'warning' ? 'bi-exclamation-triangle text-warning' : 
                                      'bi-info-circle text-info');
                    
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">
                                    <i class="bi ${iconClass}"></i> ${alert.message}
                                </h6>
                                <small class="text-muted">${alert.time}</small>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';

                container.innerHTML = html;
            },
            
            /**
             * Render Top 5 học sinh xuất sắc (UR-04.3 Enhancement)
             */
            renderTopStudents(submissionsData) {
                const container = document.getElementById('topStudentsTable');
                if (!container) return;
                
                // Group by student and calculate average score
                const studentScores = {};
                submissionsData.forEach(sub => {
                    if (!studentScores[sub.MaHS]) {
                        studentScores[sub.MaHS] = {
                            name: sub.HoTen || 'N/A',
                            scores: [],
                            total: 0
                        };
                    }
                    studentScores[sub.MaHS].scores.push(sub.Diem);
                    studentScores[sub.MaHS].total += sub.Diem;
                });
                
                // Calculate average and sort
                const topStudents = Object.entries(studentScores)
                    .map(([id, data]) => ({
                        name: data.name,
                        avgScore: (data.total / data.scores.length).toFixed(2),
                        count: data.scores.length
                    }))
                    .sort((a, b) => b.avgScore - a.avgScore)
                    .slice(0, 5);
                
                if (topStudents.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center">Chưa có dữ liệu</p>';
                    return;
                }
                
                let html = '<ol class="list-group list-group-numbered">';
                topStudents.forEach((student, index) => {
                    const medalClass = index === 0 ? 'text-warning' : (index === 1 ? 'text-secondary' : (index === 2 ? 'text-danger' : ''));
                    const medal = index < 3 ? `<i class="bi bi-trophy-fill ${medalClass}"></i>` : '';
                    
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${medal} ${student.name}</div>
                                <small class="text-muted">${student.count} bài thi</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">${student.avgScore}</span>
                        </li>
                    `;
                });
                html += '</ol>';
                
                container.innerHTML = html;
            },
            
            /**
             * Render thống kê nhanh (UR-04.3 Enhancement)
             */
            renderQuickStats(submissionsData) {
                if (submissionsData.length === 0) {
                    document.getElementById('avgScore').textContent = '0';
                    document.getElementById('completionRate').textContent = '0%';
                    document.getElementById('passCount').textContent = '0';
                    document.getElementById('avgTime').textContent = '0 phút';
                    return;
                }
                
                // Điểm trung bình
                const totalScore = submissionsData.reduce((sum, sub) => sum + sub.Diem, 0);
                const avgScore = (totalScore / submissionsData.length).toFixed(1);
                document.getElementById('avgScore').textContent = avgScore;
                
                // Tỷ lệ hoàn thành (giả sử là % bài thi đã nộp)
                const completionRate = 100; // Mock - trong thực tế cần tính từ số học sinh
                document.getElementById('completionRate').textContent = completionRate + '%';
                
                // Số học sinh đạt >= 5 điểm
                const passCount = submissionsData.filter(sub => sub.Diem >= 5).length;
                document.getElementById('passCount').textContent = passCount;
                
                // Thời gian trung bình (mock)
                const avgTime = Math.floor(Math.random() * 30) + 15; // 15-45 phút
                document.getElementById('avgTime').textContent = avgTime + ' phút';
            },
            
            /**
             * Render cảnh báo gian lận (UR-04.3 Enhancement)
             */
            renderCheatingDetection() {
                const container = document.getElementById('cheatingDetectionTable');
                if (!container) return;
                
                // Mock data - trong thực tế lấy từ API
                const cheatingCases = [
                    { student: 'Nguyễn Văn A', type: 'Tab switch', count: 5, time: '10 phút trước' },
                    { student: 'Trần Thị B', type: 'Copy/Paste', count: 3, time: '30 phút trước' }
                ];
                
                if (cheatingCases.length === 0) {
                    container.innerHTML = '<p class="text-success text-center"><i class="bi bi-shield-check"></i> Không phát hiện gian lận</p>';
                    return;
                }
                
                let html = '<div class="list-group list-group-flush">';
                cheatingCases.forEach(c => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>${c.student}</strong>
                                <span class="badge bg-danger">${c.count}</span>
                            </div>
                            <small class="text-muted">${c.type} - ${c.time}</small>
                        </div>
                    `;
                });
                html += '</div>';
                
                container.innerHTML = html;
            },

            /**
             * ===================================
             * BACKUP & RESTORE FUNCTIONS
             * ===================================
             */
            
            async startBackup() {
                const progressDiv = document.getElementById('backupProgress');
                const successDiv = document.getElementById('backupSuccess');
                const btnStart = document.getElementById('btnStartBackup');
                
                try {
                    btnStart.disabled = true;
                    progressDiv.style.display = 'block';
                    successDiv.style.display = 'none';
                    
                    const result = await this.apiCall('/backup', {
                        method: 'POST'
                    });
                    
                    if (result && result.success) {
                        progressDiv.style.display = 'none';
                        successDiv.style.display = 'block';
                        successDiv.innerHTML = `
                            <i class="bi bi-check-circle"></i> 
                            Backup thành công! 
                            <br><small>File: ${result.file || 'backup.sql'}</small>
                        `;
                        
                        // Reload backup history
                        this.loadBackupHistory();
                        
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('backupModal')).hide();
                        }, 2000);
                    }
                } catch (error) {
                    console.error('Backup error:', error);
                    progressDiv.style.display = 'none';
                    this.showAlert('Backup thất bại: ' + error.message, 'danger');
                } finally {
                    btnStart.disabled = false;
                }
            },

            async startRestore() {
                const fileInput = document.getElementById('restoreFile');
                const progressDiv = document.getElementById('restoreProgress');
                const successDiv = document.getElementById('restoreSuccess');
                const btnStart = document.getElementById('btnStartRestore');
                
                if (!fileInput.files || fileInput.files.length === 0) {
                    this.showAlert('Vui lòng chọn file backup', 'warning');
                    return;
                }
                
                if (!confirm('BẠN CHẮC CHẮN MUỐN RESTORE? Dữ liệu hiện tại sẽ BỊ GHI ĐÈ!')) {
                    return;
                }
                
                try {
                    btnStart.disabled = true;
                    progressDiv.style.display = 'block';
                    successDiv.style.display = 'none';
                    
                    const formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    
                    // QUAN TRỌNG: Dùng fetch trực tiếp cho FormData
                    // KHÔNG dùng apiCall vì nó set Content-Type: application/json
                    const response = await fetch(`${this.apiUrl}/restore`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${this.token}`
                            // KHÔNG set Content-Type! Browser tự động set multipart/form-data
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(result.message || `HTTP ${response.status}`);
                    }
                    
                    if (result && result.success) {
                        progressDiv.style.display = 'none';
                        successDiv.style.display = 'block';
                        
                        setTimeout(() => {
                            this.showAlert('Restore thành công! Vui lòng đăng nhập lại.', 'success');
                            bootstrap.Modal.getInstance(document.getElementById('restoreModal')).hide();
                            this.logout();
                        }, 2000);
                    }
                } catch (error) {
                    console.error('Restore error:', error);
                    progressDiv.style.display = 'none';
                    this.showAlert('Restore thất bại: ' + error.message, 'danger');
                } finally {
                    btnStart.disabled = false;
                }
            },

            async loadBackupHistory() {
                try {
                    const result = await this.apiCall('/backups');
                    const tbody = document.getElementById('backupHistoryBody');
                    
                    if (!result || !result.data || result.data.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    Chưa có backup nào
                                </td>
                            </tr>
                        `;
                        return;
                    }
                    
                    let html = '';
                    result.data.forEach(backup => {
                        // API trả về: ThoiGian, KichThuoc (already formatted), TrangThai, MaSaoLuu, TenFile
                        const statusClass = backup.TrangThai === 'ThanhCong' ? 'success' : 'danger';
                        const statusText = backup.TrangThai === 'ThanhCong' ? 'THÀNH CÔNG' : 'THẤT BẠI';
                        
                        html += `
                            <tr>
                                <td>${backup.ThoiGian}</td>
                                <td>${backup.KichThuoc}</td>
                                <td><span class="badge bg-${statusClass}">${statusText}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="app.downloadBackup(${backup.MaSaoLuu})">
                                        <i class="bi bi-download"></i> Tải về
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                } catch (error) {
                    console.error('Load backup history error:', error);
                    const tbody = document.getElementById('backupHistoryBody');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-danger">
                                <i class="bi bi-exclamation-triangle"></i> Lỗi tải danh sách backup: ${error.message}
                            </td>
                        </tr>
                    `;
                }
            },

            formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            },

            async downloadBackup(maSaoLuu) {
                try {
                    if (!this.token) {
                        this.showAlert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 'danger');
                        this.logout();
                        return;
                    }
                    
                    // Download file backup với fetch + Authorization header
                    const url = `${this.apiUrl}/backups/${maSaoLuu}/download`;
                    
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${this.token}`,
                            'Accept': 'application/octet-stream'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Lấy filename từ Content-Disposition header
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = `backup_${maSaoLuu}.sql`;
                    if (contentDisposition) {
                        const matches = /filename="(.+)"/.exec(contentDisposition);
                        if (matches) filename = matches[1];
                    }
                    
                    // Download file
                    const blob = await response.blob();
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(downloadUrl);
                    
                    this.showAlert('Đã tải backup thành công!', 'success');
                } catch (error) {
                    console.error('Download backup error:', error);
                    this.showAlert('Không thể tải backup: ' + error.message, 'danger');
                }
            },

            /**
             * ====================================
             * SYSTEM MONITORING (Giám sát hệ thống)
             * ====================================
             */
            
            async loadMonitoring() {
                try {
                    this.showLoader();
                    
                    const result = await this.apiCall('/system/monitor', { method: 'GET' });
                    
                    if (!result || !result.success) {
                        this.showAlert('Không thể tải dữ liệu giám sát', 'danger');
                        return;
                    }
                    
                    const data = result.data;
                    
                    // Update User Metrics
                    document.getElementById('onlineUsers').textContent = data.users.online;
                    document.getElementById('totalUsers').textContent = data.users.total;
                    document.getElementById('activeUsersText').textContent = 
                        `${data.users.active} đang hoạt động`;
                    
                    // Update Exam Metrics
                    document.getElementById('todaySubmissions').textContent = data.exams.today_submissions;
                    document.getElementById('totalSubmissionsText').textContent = 
                        `Tổng: ${data.exams.total_submissions} bài`;
                    document.getElementById('avgScore').textContent = data.exams.avg_score;
                    
                    // Update Content Metrics
                    document.getElementById('totalExams').textContent = data.exams.total;
                    document.getElementById('totalQuestions').textContent = data.questions.total;
                    document.getElementById('totalStudents').textContent = data.users.students;
                    document.getElementById('totalTeachers').textContent = data.users.teachers;
                    
                    // Update System Info
                    document.getElementById('phpVersion').textContent = data.system.php_version;
                    document.getElementById('laravelVersion').textContent = data.system.laravel_version;
                    document.getElementById('database').textContent = data.system.database;
                    document.getElementById('serverTime').textContent = data.system.server_time;
                    document.getElementById('serverUptime').textContent = data.system.uptime;
                    
                    // Update Recent Activities
                    this.renderRecentActivities(data.recent_activities);
                    
                    // Update last refresh time
                    const now = new Date().toLocaleTimeString('vi-VN');
                    document.getElementById('lastUpdateTime').textContent = `Cập nhật: ${now}`;
                    
                    this.hideLoader();
                } catch (error) {
                    console.error('Load monitoring error:', error);
                    this.showAlert('Lỗi khi tải dữ liệu giám sát: ' + error.message, 'danger');
                    this.hideLoader();
                }
            },
            
            renderRecentActivities(activities) {
                const tbody = document.getElementById('recentActivitiesTable');
                
                if (!activities || activities.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Chưa có hoạt động nào
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                let html = '';
                activities.forEach(activity => {
                    const scoreClass = activity.Diem >= 5 ? 'text-success' : 'text-danger';
                    html += `
                        <tr>
                            <td><strong>${activity.TenDangNhap}</strong></td>
                            <td>${activity.TenDe}</td>
                            <td class="${scoreClass}"><strong>${activity.Diem !== null ? activity.Diem : 'N/A'}</strong></td>
                            <td><small class="text-muted">${activity.ThoiGianNopFormatted}</small></td>
                        </tr>
                    `;
                });
                
                tbody.innerHTML = html;
            },
            
            startMonitoringAutoRefresh() {
                // Auto refresh every 30 seconds
                if (this.monitoringInterval) {
                    clearInterval(this.monitoringInterval);
                }
                
                this.monitoringInterval = setInterval(() => {
                    // Only refresh if monitoring screen is active
                    const screen = document.getElementById('monitoringScreen');
                    if (screen && screen.classList.contains('active')) {
                        this.loadMonitoring();
                    }
                }, 30000); // 30 seconds
            },
            
            stopMonitoringAutoRefresh() {
                if (this.monitoringInterval) {
                    clearInterval(this.monitoringInterval);
                    this.monitoringInterval = null;
                }
            },

            /**
             * ===================================
             * RANDOM EXAM GENERATION (Teacher)
             * ===================================
             */
            
            async generateRandomExam() {
                const form = document.getElementById('randomExamForm');
                const formData = new FormData(form);
                
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                
                try {
                    const data = {
                        TenDe: formData.get('TenDe'),
                        ThoiGianLamBai: parseInt(formData.get('ThoiGianLamBai')),
                        ChuDe: formData.get('ChuDe'),
                        SoLuongCauHoi: parseInt(formData.get('SoLuongCauHoi')),
                        DoKho: formData.get('DoKho')
                    };
                    
                    this.showAlert('Đang tạo đề thi ngẫu nhiên...', 'info');
                    
                    const result = await this.apiCall('/tao-de-thi-ngau-nhien', {
                        method: 'POST',
                        body: JSON.stringify(data)
                    });
                    
                    if (result && result.success) {
                        this.showAlert('Tạo đề thi thành công!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('taoDeNgauNhienModal')).hide();
                        form.reset();
                        
                        // Reload exam list if on teacher exam list screen
                        if (document.getElementById('danhsachdetthiScreen') && document.getElementById('danhsachdetthiScreen').classList.contains('active')) {
                            this.loadTeacherExams();
                        }
                    }
                } catch (error) {
                    console.error('Generate random exam error:', error);
                    this.showAlert('Tạo đề thi thất bại: ' + error.message, 'danger');
                }
            }
        };
        
        // Make app globally accessible for onclick handlers
        window.app = app;
        
        // Initialize app when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            app.init();
            
            // Add click event for modal background to close
            const examModal = document.getElementById('examDetailModal');
            if (examModal) {
                examModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        const modal = bootstrap.Modal.getInstance(this);
                        if (modal) {
                            modal.hide();
                        }
                    }
                });
            }
        });
    </script>

</body>
</html>
<?php /**PATH D:\Hệ thống luyện thi THPT môn Tin học (mới)\Hệ thống luyện thi THPT môn Tin học\resources\views/app.blade.php ENDPATH**/ ?>