<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - euproxy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f9fafb;
            color: #1f2937;
        }

        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e5e7eb;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            color: #6b7280 !important;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #7c3aed !important;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .header-section {
            margin-bottom: 30px;
        }

        .header-section h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .header-section p {
            color: #6b7280;
            font-size: 1.05rem;
        }

        .welcome-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
        }

        .welcome-card h2 {
            color: #1f2937;
            margin-bottom: 10px;
        }

        .welcome-card p {
            color: #6b7280;
            margin: 0;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #7c3aed;
            transform: translateY(-2px);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(109, 40, 217, 0.1) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .card-icon svg {
            width: 24px;
            height: 24px;
            color: #7c3aed;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .card-description {
            color: #6b7280;
            font-size: 0.95rem;
            margin: 0;
        }

        .user-info {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .user-info h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1f2937;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6b7280;
            font-weight: 500;
        }

        .info-value {
            color: #1f2937;
            font-weight: 600;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(124, 58, 237, 0.3);
            color: white;
        }

        .btn-outline {
            background: white;
            color: #7c3aed;
            border: 1px solid #7c3aed;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline:hover {
            background: #7c3aed;
            color: white;
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 1.5rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">euproxy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit(); return false;">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Logout Form -->
    <form id="logout-form" method="POST" action="{{ route('user.logout') }}" style="display: none;">
        @csrf
    </form>

    <!-- Main Content -->
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header-section">
            <h1>Welcome, {{ explode(' ', $user->name)[0] }}! 👋</h1>
            <p>Here's your euproxy dashboard overview</p>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <h2>Getting Started</h2>
            <p>Your account is ready to use. Manage your proxy settings and monitor your usage from this dashboard.</p>
        </div>

        <!-- Feature Cards -->
        <div class="cards-grid">
            <div class="card">
                <div class="card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="card-title">Manage Proxy Accounts</div>
                <p class="card-description">Create and manage your proxy accounts with ease</p>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div class="card-title">Security Settings</div>
                <p class="card-description">Control access and configure security options</p>
            </div>

            <div class="card">
                <div class="card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="card-title">Usage Analytics</div>
                <p class="card-description">Track your proxy usage and activity logs</p>
            </div>
        </div>

        <!-- User Information -->
        <div class="user-info">
            <h3>Account Information</h3>
            <div class="info-item">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $user->email }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Full Name</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Member Since</span>
                <span class="info-value">{{ $user->created_at->format('F j, Y') }}</span>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <a href="#" class="btn-primary-gradient">Edit Profile</a>
                <a href="#" class="btn-outline">Change Password</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
