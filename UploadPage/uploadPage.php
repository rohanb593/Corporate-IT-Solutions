<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: loginDisplay.php");
    exit();
}

// Get existing media files
$mediaFiles = [];
$metadataFile = 'uploads/' . session_id() . '_metadata.json';

if (file_exists($metadataFile)) {
    $mediaFiles = json_decode(file_get_contents($metadataFile), true) ?: [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corporate IT Solutions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../HomePage/homePage.css">
    <link rel="stylesheet" href="uploadPage.css">
</head>
<body>
   
    <div class="dashboard-container">
        <!-- Collapsible Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <ul>
                    <li data-content="dashboard">
                        <a href="../HomePage/homePage.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                

                    <li data-content="dashboard">
                        <a href="uploadPage.php">
                            <i class="fas fa-upload"></i>
                            <span class="menu-text">Upload</span>
                        </a>

                    </li>
                
                    <li class="logout">
                        <a href="../HomePage/logout.php">
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
                                <i class="fas fa-folder-open"></i> Select Files (Max 5)
                            </label>
                            <input type="file" id="mediaFiles" name="mediaFiles[]" multiple accept="image/*">
                            <div class="file-info" id="fileInfo">No files selected (0/5)</div>
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

                    <!-- Media Management Section -->
                    <h3 style="margin-top: 30px;"><i class="fas fa-images"></i> Your Uploaded Media</h3>
                    <div class="media-management">
                        <?php if (!empty($mediaFiles)): ?>
                            <div class="media-grid">
                                <?php foreach ($mediaFiles as $index => $file): ?>
                                    <div class="media-item" data-filename="<?php echo htmlspecialchars($file['name']); ?>">
                                        <img src="uploads/<?php echo htmlspecialchars($file['name']); ?>" alt="Uploaded media">
                                        <div class="media-actions">
                                            <button class="delete-btn" data-filename="<?php echo htmlspecialchars($file['name']); ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No media files uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="uploadPage.js"></script>
    <script>
        // Handle delete actions
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const filename = this.getAttribute('data-filename');
                if (confirm('Are you sure you want to delete this file?')) {
                    fetch('deleteMedia.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `filename=${encodeURIComponent(filename)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Refresh to show updated list
                        } else {
                            alert('Error deleting file: ' + data.message);
                        }
                    });
                }
            });
        });
    </script>

    <!-- Include sidebar toggle script -->
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