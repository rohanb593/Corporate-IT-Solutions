
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Corporate IT Solutions";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from HD_DISPLAY table
$sql = "SELECT * FROM HD_DISPLAY";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Items</title>
    <link rel="stylesheet" href="homePage.css">
    <link rel="stylesheet" href="itemSorter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <h1>Corporate IT Solutions - Inventory</h1>
                <div class="user-info">
                    <span>Welcome, <strong><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin'; ?></strong></span>
                    <?php if(isset($_SESSION['city'])): ?>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($_SESSION['city']); ?></span>
                    <?php endif; ?>
                </div>
            </header>

        
            <!-- Previous code remains the same until the content-section div -->
            <div class="content-section">
                <div class="table-and-slideshow-container">
                    <div class="tables-container">
                        <div class="table-container">
                            <?php if ($result->num_rows > 0): ?>
                                <table class="inventory-table">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Quantity</th>
                                            <th>Discount</th>
                                            <th>Unit Price</th>
                                            <th>Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="live-inventory-body">
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['VC_ITEM_DESC']); ?></td>
                                            <td><?php echo htmlspecialchars($row['NU_QTY']); ?></td>
                                            <td><?php echo htmlspecialchars($row['NU_DISCOUNT']) . '%'; ?></td>
                                            <td>$<?php echo number_format($row['NU_ITEM_PRICE'], 2); ?></td>
                                            <td>$<?php echo number_format($row['NU_LINE_TOTAL'], 2); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No inventory items found.</p>
                            <?php endif; ?>
                        </div>
                        <div class="table-container">
                            <table class="inventory-table">
                                <thead>
                                    <tr>
                                        <th>Total Items</th>
                                        <th>Total Discount</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody id="live-totals-table">
                                    <?php
                                    // Calculate totals
                                    $sql = "SELECT 
                                            SUM(NU_QTY) as total_items,
                                            SUM(NU_LINE_TOTAL * NU_DISCOUNT/100) as total_discount,
                                            SUM(NU_LINE_TOTAL) as total_price
                                            FROM HD_DISPLAY";
                                    
                                    $result = $conn->query($sql);
                                    $totals = $result->fetch_assoc();

                                    if ($totals['total_items'] === null) {
                                        $totals = [
                                            'total_items' => 0,
                                            'total_discount' => 0,
                                            'total_price' => 0
                                        ];
                                    }
                                    
                                    $conn->close();
                                    ?>
                                    <tr>
                                        <td><?php echo number_format($totals['total_items']); ?></td>
                                        <td>$<?php echo number_format($totals['total_discount'], 2); ?></td>
                                        <td>$<?php echo number_format($totals['total_price'], 2); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <aside class="image-slideshow">
                        <div class="slideshow-container">
                            <div class="slide active">
                                <img src="images/slide1.jpg" alt="IT Solution 1">
                            </div>
                            <div class="slide">
                                <img src="images/slide2.jpg" alt="IT Solution 2">
                            </div>
                            <div class="slide">
                                <img src="images/slide3.jpg" alt="IT Solution 3">
                            </div>
                        </div>
                        <div class="video-container">
                            <video controls>
                                <source src="videos/demo.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </aside>
                </div>
            </div>

            <footer>
                <h1>Welcome to Corporate IT Solutions</h1>
            </footer>
        </main>
        
    </div>    

    <script>
        const evtSource = new EventSource("sse_data.php");

        evtSource.onmessage = function(event) {
            const dataParts = event.data.split("|||SSE_SEPARATOR|||");
            if (dataParts.length === 2) {
                // Update items table
                document.querySelector(".table-container:first-child tbody").innerHTML = dataParts[0];
                // Update totals table
                document.querySelector(".table-container:last-child tbody").innerHTML = dataParts[1];
            }
        };
    </script>

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-rotating slideshow
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        const slideInterval = 5000; // Change slide every 3 seconds
        
        function nextSlide() {
            // Remove active class from current slide
            slides[currentSlide].classList.remove('active');
            
            // Move to next slide (loop back to 0 if at end)
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Add active class to new current slide
            slides[currentSlide].classList.add('active');
        }
        
        // Start the slideshow
        let slideTimer = setInterval(nextSlide, slideInterval);
        
        // Pause slideshow when hovering (optional)
        const slideshow = document.querySelector('.slideshow-container');
        slideshow.addEventListener('mouseenter', () => {
            clearInterval(slideTimer);
        });
        
        slideshow.addEventListener('mouseleave', () => {
            slideTimer = setInterval(nextSlide, slideInterval);
        });
        
        // Your existing video control code
        const video = document.querySelector('.video-container video');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting && !video.paused) {
                    video.pause();
                }
            });
        }, {threshold: 0.5});
        
        observer.observe(video);
    });
    </script>


    


    
   


</body>
</html>