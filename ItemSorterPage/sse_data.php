<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "Corporate IT Solutions";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

while (true) {
    // Get inventory items
    $sql = "SELECT * FROM HD_DISPLAY";
    $result = $conn->query($sql);

    $items_output = '';
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items_output .= "<tr>
                <td>" . htmlspecialchars($row['VC_ITEM_DESC']) . "</td>
                <td>" . htmlspecialchars($row['NU_QTY']) . "</td>
                <td>" . htmlspecialchars($row['NU_DISCOUNT']) . "%</td>
                <td>$" . number_format($row['NU_ITEM_PRICE'], 2) . "</td>
                <td>$" . number_format($row['NU_LINE_TOTAL'], 2) . "</td>
            </tr>";
        }
    } else {
        $items_output = "<tr><td colspan='5'>No inventory items found.</td></tr>";
    }

    // Calculate totals
    $sql_totals = "SELECT 
            SUM(NU_QTY) as total_items,
            SUM(NU_LINE_TOTAL * NU_DISCOUNT/100) as total_discount,
            SUM(NU_LINE_TOTAL) as total_price
            FROM HD_DISPLAY";
    
    $result_totals = $conn->query($sql_totals);
    $totals = $result_totals->fetch_assoc();
    
    // Check if there are no entries and set totals to 0
    if ($totals['total_items'] === null) {
        $totals = [
            'total_items' => 0,
            'total_discount' => 0,
            'total_price' => 0
        ];
    }

    $totals_output = "<tr>
        <td>" . number_format($totals['total_items']) . "</td>
        <td>$" . number_format($totals['total_discount'], 2) . "</td>
        <td>$" . number_format($totals['total_price'], 2) . "</td>
    </tr>";

    // Combine both outputs with a special separator
    $combined_output = $items_output . "|||SSE_SEPARATOR|||" . $totals_output;

    echo "data: " . str_replace("\n", "", $combined_output) . "\n\n";
    ob_flush();
    flush();
    sleep(1);
}
?>