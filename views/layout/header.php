<?php
echo <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haisan Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #1e293b;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            background: #0f172a;
            text-align: center;
        }

        .sidebar-header h3 {
            margin: 0;
            font-weight: 700;
            color: #38bdf8;
            font-size: 1.5rem;
        }

        .sidebar-menu {
            padding: 10px 0;
            list-style: none;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 5px 15px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #334155;
            color: #fff;
        }

        .sidebar-menu a i {
            width: 30px;
            font-size: 1.1rem;
        }

        /* Top Navbar */
        .top-navbar {
            margin-left: 250px;
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #475569;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s;
        }
        
        /* Stats Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #fff;
        }

        .stat-info h5 {
            margin: 0;
            font-size: 0.9rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }

        .stat-info h3 {
            margin: 5px 0 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* Custom Table */
        .table-custom {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.03);
        }
        
        .table-custom thead {
            background: #f8fafc;
        }
        
        .table-custom th {
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 15px;
        }
        
        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .badge-role {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .badge-role-1 { background: #fee2e2; color: #ef4444; } /* Super Admin */
        .badge-role-2 { background: #dbeafe; color: #3b82f6; } /* Admin */
        .badge-role-3 { background: #d1fae5; color: #10b981; } /* User */
    </style>
</head>
<body>
    <div class="wrapper">
HTML;
?>
