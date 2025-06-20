<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate IT Solutions</title>
    <link rel="stylesheet" href="homePage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php
    session_start();
    // Redirect to login if not authenticated
    if (!isset($_SESSION['username'])) {
        header("Location: loginDisplay.php");
        exit();
    }
    ?>
    
    <div class="dashboard-container">
        <!-- Collapsible Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li data-content="dashboard">
                        <a href="homePage.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li data-content="View Items" class="active">
                        <a href="itemSorter.php">
                            <i class="fas fa-tasks"></i>
                            <span class="menu-text">View Items</span>
                        </a>
                    </li>

                    <li data-content="dashboard">
                        <a href="uploadPage.php">
                            <i class="fas fa-upload"></i>
                            <span class="menu-text">Upload</span>
                        </a>

                    </li>
                
                    <li class="logout">
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="menu-text">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header>
                <h1>Corporate IT Solutions</h1>
                <div class="user-info">
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <span>
                        <i class="fas fa-map-marker-alt"></i> 
                        <?php echo htmlspecialchars($_SESSION['city']); ?>
                    </span>
                </div>
            </header>

            <!-- Dynamic Content Sections -->
            <div class="content-section" id="dashboard-content">
                <h2><i class="fas fa-chart-line">

                </i> Dashboard Overview</h2>
            </div>
        </main>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.createElement('button');
            
            toggleBtn.className = 'sidebar-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.onclick = function() {
                sidebar.classList.toggle('collapsed');
            };
            
            sidebar.appendChild(toggleBtn);
        });
    </script>
</body>
</html>