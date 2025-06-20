<?php
session_start();
// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: loginDisplay.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate IT Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="homePage.css">
    <link rel="stylesheet" href="uploadPage.css">
</head>
<body>
   
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

        <main class="main-content">
            <header>
                <h1>Corporate IT Solutions - Media Upload</h1>
                <div class="user-info">
                    <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                    <?php if(isset($_SESSION['city'])): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($_SESSION['city']); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <div class="content-section">
                <h2><i class="fas fa-upload"></i> Upload Media</h2>
                
                <div class="upload-container">
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="mediaFiles">
                                <i class="fas fa-folder-open"></i> Select Files
                            </label>
                            <input type="file" id="mediaFiles" name="mediaFiles[]" multiple accept="image/*,video/*">
                            <div class="file-info" id="fileInfo">No files selected</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="title"><i class="fas fa-heading"></i> Title</label>
                            <input type="text" id="title" name="title" placeholder="Enter media title">
                        </div>
                        
                        <div class="form-group">
                            <label for="description"><i class="fas fa-align-left"></i> Description</label>
                            <textarea id="description" name="description" placeholder="Enter description"></textarea>
                        </div>
                        
                        <button type="submit" class="upload-btn">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                    </form>

                    <div class="progress-container" id="progressContainer">
                        <div class="progress-bar" id="progressBar"></div>
                        <span class="progress-text" id="progressText">0%</span>
                    </div>

                    <div class="preview-container" id="previewContainer"></div>
                    
                    <div class="message" id="message"></div>
                </div>
            </div>
        </main>
    </div>

    <script src="uploadPage.js"></script>


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