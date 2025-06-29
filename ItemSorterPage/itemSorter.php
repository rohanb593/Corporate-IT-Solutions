
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
    <link rel="stylesheet" href="../HomePage/homePage.css">
    <link rel="stylesheet" href="itemSorter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>




<body>
    <div class="dashboard-container">
        <!-- Collapsible Sidebar -->
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
                            <?php
                            // Get all uploaded images from all users
                            $uploadDir = '../UploadPage/uploads/';
                            $allImages = [];
                            
                            // Scan the uploads directory for image files
                            if (file_exists($uploadDir)) {
                                $files = scandir($uploadDir);
                                foreach ($files as $file) {
                                    if ($file !== '.' && $file !== '..' && !str_ends_with($file, '.json')) {
                                        $filePath = $uploadDir . $file;
                                        if (@getimagesize($filePath)) { // Check if it's an image
                                            $allImages[] = $filePath;
                                        }
                                    }
                                }
                            }
                            
                            //If no uploaded images, use default slides
                            if (empty($allImages)) {
                                $allImages = [
                                    'images/slide1.jpg',
                                    'images/slide2.jpg',
                                    'images/slide3.jpg'
                                ];
                            }
                            
                            // Display each image in the slideshow
                            foreach ($allImages as $index => $image) {
                                $activeClass = $index === 0 ? 'active' : '';
                                echo '<div class="slide ' . $activeClass . '">';
                                echo '<img src="' . htmlspecialchars($image) . '" alt="Uploaded media">';
                                echo '</div>';
                            }
                            ?>
                        </div>
                        <div class="slideshow-nav">
                            <?php for ($i = 0; $i < count($allImages); $i++): ?>
                                <span class="slideshow-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                            <?php endfor; ?>
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
        const dots = document.querySelectorAll('.slideshow-dot');
        let currentSlide = 0;
        const slideInterval = 5000; // Change slide every 5 seconds
        
        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Show the selected slide
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }
        
        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }
        
        // Start the slideshow
        let slideTimer = setInterval(nextSlide, slideInterval);
        
        // Pause slideshow when hovering
        const slideshow = document.querySelector('.slideshow-container');
        slideshow.addEventListener('mouseenter', () => {
            clearInterval(slideTimer);
        });
        
        slideshow.addEventListener('mouseleave', () => {
            slideTimer = setInterval(nextSlide, slideInterval);
        });
        
        // Dot navigation
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const slideIndex = parseInt(this.getAttribute('data-index'));
                showSlide(slideIndex);
            });
        });
        
        // Video control (existing code)
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