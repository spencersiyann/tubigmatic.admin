<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Firebase URLs
$firebase_orders_url = 'https://tubigmatic-admin-default-rtdb.firebaseio.com/Orders.json';
$firebase_accepted_url = 'https://tubigmatic-admin-default-rtdb.firebaseio.com/AcceptedOrders.json';
$firebase_rejected_url = 'https://tubigmatic-admin-default-rtdb.firebaseio.com/RejectedOrders.json';

// Fetch orders
$ordersData = json_decode(@file_get_contents($firebase_orders_url), true) ?: [];
$acceptedOrdersData = json_decode(@file_get_contents($firebase_accepted_url), true) ?: [];
$rejectedOrdersData = json_decode(@file_get_contents($firebase_rejected_url), true) ?: [];


function formatContactNumber($contact) {
    // Remove all non-numeric characters
    $digits = preg_replace('/\D/', '', $contact);

   
    if (strlen($digits) === 10 && $digits[0] === '9') {
        return "(+63)9" . substr($digits, 1);
    }

    return "(+63) " . $digits;
}

// Pagination Setup
$perPage = 10; // Number of orders per page
$totalOrders = count($ordersData); // Total number of orders
$totalPages = ceil($totalOrders / $perPage); // Calculate total pages

// Get the current page from the query parameter or set it to 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

// Calculate the starting index for the orders to display on this page
$startIndex = ($currentPage - 1) * $perPage;
$ordersDataForPage = array_slice($ordersData, $startIndex, $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders</title>
    <style>

    /* Body */
    body {
      font-family: Arial, sans-serif;
      display: flex;
      margin: 0;
      flex-direction: column;
    }
    /* Top Navbar */
    .navbar {
    background-color: #3E6606;
    height: 50px;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000; 
    }

    li a {
      text-decoration: none;
      color: #000000;
    }
    /* Sidebar */
    .sidebar {
    width: 250px;
    background: #ffffff;
    height: 100vh; 
    padding: 0px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 2);
    position: fixed;
    top: 0; 
    left: 0;
    overflow-y: auto;
    }
    .sidebar ul li a {
        display: block;
        color: #000000;
        text-decoration: none;
        width: 100%;
        height: 100%;
    }
    .sidebar .logo img {
        width: 50px;
        margin-left: 10px;
        margin-top: 70px;
    }
    .sidebar h2 {
        margin-top: -40px;
        margin-left: 60px;
        margin-bottom: 50px;
    }
    .sidebar h4 {
        margin-top: -30px;
        margin-left: 80px;
        margin-bottom: 50px;
        color: #000000;
    }
    .sidebar ul {
        list-style: none;
        margin-left: 10px;
        padding: 0;
    }
    .sidebar ul li {
        padding: 15px;
        cursor: pointer;
        color: #000000;
        font-weight: bold;
        font-size: 15px;
        margin-top: 20px;
        display: flex;
        align-items: center;
    }
    .sidebar ul .active {
        background: #9FB383;
    }
    .sidebar ul li img {
        width: 20px;
        margin-right: 10px;
    }
    .sidebar ul li:hover {
        background: #c2d6a6;
        color: rgb(0, 0, 0);
        transition: 0.3s;
        border-radius: 5px;
    }
    /* Main content */
    .main-content {
        flex: 1;
        padding: 20px;
        margin-left: calc(var(--sidebar-width, 280px));
    height: 100vh;
    overflow-y: auto; 
        margin-top: 60px;
    }
    
    /* Search Bar Styling */
    .search-bar {
        position: relative;
        width: 40%;
        max-width: 200px;
        margin: auto;
        margin-right: 60px;
    }
    .search-bar input {
        width: 85%;
        padding: 12px 40px 12px 15px; 
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        outline: none;
        transition: 0.3s;
    }
    .search-bar input:focus {
        border-color: #3E6606;
        box-shadow: 0 0 5px rgba(62, 102, 6, 0.5);
    }
    .search-bar button {
        padding: 0px 0px;
        border: 1px solid #ccc;
        background: #ffffff;
        color: white;
        cursor: pointer;
        border-radius: 0 3px 3px 0;
    }
    .search-icon {
        position: absolute;
        right: 0px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        cursor: pointer;
        opacity: 0.6;
    }
  
    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        font-size: 14px;
        border-radius: 10px;
    }
    th, td {
        padding: 15px;
        text-align: left;
    }
    th {
        background-color:#2E4A21;
        color: white;
        text-transform: uppercase;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
    .delete-btn {
        background-color: red;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 3px;
    }
    .delete-btn:hover {
        background-color: darkred;
    }
    
    /* Modal Styling */
    .modal {
        display: none; /* Hidden by default */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        background-color: white;
        padding: 20px;
        width: 300px;
        text-align: center;
        margin: 20% auto;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    #messageModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999;
    }
    button {
        padding: 8px 12px;
        border: none;
        margin: 10px;
        cursor: pointer;
    }
    #confirmButton{
        background-color: red;
        border-radius: 5px;
        color: white;
    }
    #confirmButton:hover {
        background-color:rgb(214, 21, 21);
    }

    #cancelButton {
        background-color: gray;
        border-radius: 5px;
        color: white;
    }
    #cancelButton:hover {
        background-color:rgb(109, 109, 109);
    }

    .confirm-delete {
        background-color: red;
        color: white;
    }

    .cancel-delete {
        background-color: gray;
        color: white;
    }

    /* Pagination Styling */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
    }
    .pagination a {
        padding: 8px 12px;
        text-decoration: none;
        color: white;
        background-color: #ccc; 
        border-radius: 5px;
        transition: 0.3s;
    }
    .pagination a.active {
        background-color: #3E6032; 
    }
    .pagination a:hover {
        background-color: #3E6032;
        color: white;
    }
    .pagination .prev,
    .pagination .next {
        background-color: #ccc; 
        color: white;
    }
    .pagination .prev:hover,
    .pagination .next:hover {
        background-color: #3E6032; 
    }
    .pagination .next-btn:hover {
        background: #9FB383;
    }

    /* Buttons */
    .accept-btn {
        background-color:rgb(12, 83, 6) ; 
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
    }
    .accept-btn:hover {
        background-color:rgb(8, 95, 27);
    }
    .reject-btn {
        background-color:rgb(133, 19, 30); /* Red */
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
    }

    .reject-btn:hover {
        background-color: #c82333;
    }

    /* Order Table */
    .table-wrapper {
        width: 100%;
        overflow-x: auto; 
    }
    #orderTable {
        width: 100%;
        min-width: 700px; /* Adjust as needed */
        border-collapse: collapse;
    }
    #orderTable th, #orderTable td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }
    #orderTable th {
        background-color: #f4f4f4;
    }
    .status-btn {
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        font-size: 15px;    
        border-radius: 5px;
        background-color: #f0f0f0;
        transition: 0.3s;
        }
    .status-btn.active {
        background-color:rgb(29, 83, 33);
        color: white;
        }
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        max-width: 100%;
    }
    table {
        width: 100%;
        min-width: 800px; 
        border-collapse: collapse;
        white-space: nowrap; 
    }
    td:nth-child(5), th:nth-child(5) {
        max-width: 250px; 
        word-break: break-word;
    }
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
    }

        </style>
</head>

<body>
    <!--Navbar -->
    <div class="navbar"></div>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="TubigMatic Logo" />
            <h2><span style="color:#006F9E">Tubig</span><span style="color:#3E6606">Matic</span></h2>
            <h4>Admin</h4>
        </div>
        <ul>
            <li><a href="dashboard.php"><img src="icons/dashboard.png" alt="Dashboard Icon"> Dashboard</a></li>
            <li><a href="account.php"><img src="icons/account.png" alt="Accounts Icon"> Accounts</a></li>
            <li><a href="device.php"><img src="icons/phone.png" alt="Device Icon"> Devices</a></li>
            <li class="active"><a href="order.php"><img src="icons/order.png" alt="Order Icon"> Orders</a></li>
            <li><a href="logout.php" id="logout" onclick="return confirmLogout();">
                <img src="icons/logout.png" alt="Logout Icon"> Logout
            </a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Orders</h2>

        <!-- Status Buttons (active) -->
        <div class="status-buttons">
            <button onclick="showOrders('all')" class="status-btn active" id="all-btn">Pending Orders</button>
            <button onclick="showOrders('accepted')" class="status-btn" id="accepted-btn">Accepted</button>
            <button onclick="showOrders('rejected')" class="status-btn" id="rejected-btn">Rejected</button>
            <button onclick="showOrders('history')" class="status-btn" id="history-btn">History</button>
        </div>

        <!-- Search Orders -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search..." onkeyup="searchOrders()" />
            <img src="icons/search.png" alt="Search" class="search-icon">
        </div>
        <br>
        <!-- Order Container -->
        <div class="orders-container">
            <div class="table-wrapper">
                <!-- Orders Table -->
                <table id="allOrdersTable">
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                        <th>Reference No.</th>
                        <th>Action</th>
                    </tr>
                    <!-- Fetch from firebase -->
                    <?php foreach ($ordersData as $orderKey => $order) : ?>
                        <tr class="order-row">
                            <td><?= htmlspecialchars($order['orderID'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['email'] ?? 'N/A') ?></td>
                            <td><?= isset($order['contactNumber']) ? formatContactNumber($order['contactNumber']) : 'N/A' ?></td>
                            <td style="white-space: normal; word-break: break-word; max-width: 250px;">
                    <?= htmlspecialchars($order['address'] ?? 'N/A') ?>
                            </td>
                            <td><?= htmlspecialchars($order['referenceNumber'] ?? 'N/A') ?></td>
                            <td>
                                <button class="accept-btn" onclick="updateStatus('<?= $orderKey ?>', 'accepted')">Accept</button>
                                <button class="reject-btn" onclick="updateStatus('<?= $orderKey ?>', 'rejected')">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Accepted Orders Table -->
                <table id="acceptedOrdersTable" style="display:none;">
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                        <th>Reference No.</th>
                    </tr>
                    <?php foreach ($acceptedOrdersData as $order) : ?>
                        <tr>
                            <td><?= htmlspecialchars($order['orderID'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['email'] ?? 'N/A') ?></td>
                            <td><?= isset($order['contactNumber']) ? formatContactNumber($order['contactNumber']) : 'N/A' ?></td>
                            <td style="white-space: normal; word-break: break-word; max-width: 250px;">
                            <?= htmlspecialchars($order['address'] ?? 'N/A') ?>
                            </td>
                            <td><?= htmlspecialchars($order['referenceNumber'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Rejected Orders Table -->
                <table id="rejectedOrdersTable" style="display:none;">
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Address</th>
                        <th>Reference No.</th>
                    </tr>
                    <?php foreach ($rejectedOrdersData as $order) : ?>
                        <tr>
                            <td><?= htmlspecialchars($order['orderID'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['email'] ?? 'N/A') ?></td>
                            <td><?= isset($order['contactNumber']) ? formatContactNumber($order['contactNumber']) : 'N/A' ?></td>
                            <td style="white-space: normal; word-break: break-word; max-width: 250px;">
                     <?= htmlspecialchars($order['address'] ?? 'N/A') ?>
                            </td>
                            <td><?= htmlspecialchars($order['referenceNumber'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- History Orders Table -->
                <table id="historyOrdersTable" style="display:none;">
                    <tr>
                        <th>Order ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Reference No.</th>
                        <th>Status</th>
                    </tr>

                    <?php 
                    // Merge accepted and rejected orders
                    $historyOrders = [];
                    foreach ($acceptedOrdersData as $order) {
                        $order['status'] = 'Accepted';
                        $historyOrders[] = $order;
                    }
                    foreach ($rejectedOrdersData as $order) {
                        $order['status'] = 'Rejected';
                        $historyOrders[] = $order;
                    }
                    // Sort orders by orderID (ascending order)
                    usort($historyOrders, function ($a, $b) {
                        return ($a['orderID'] ?? 0) <=> ($b['orderID'] ?? 0);
                    });
                    // Display sorted history orders
                    foreach ($historyOrders as $order) : ?>
                        <tr>
                            <td><?= htmlspecialchars($order['orderID'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['email'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($order['referenceNumber'] ?? 'N/A') ?></td>
                            <td style="font-weight: bold; color: <?= $order['status'] === 'Accepted' ? 'green' : 'red' ?>;">
                                <?= $order['status'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
         
                 <!-- Pagination -->
            <div class="pagination">
                <?php if ($currentPage > 1) : ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="prev">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages) : ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="next">Next &raquo;</a>
                <?php endif; ?>
            </div>

            </div>
        </div>
    </div>

            <!-- Message Modal Structure -->
            <div id="messageModal" style="display:none;">
            <div class="modal-content">
                <p id="modalMessage"></p> 
                <button id="cancelButton" onclick="closeMessageModal()">Close</button>
                <button id="confirmButton">Confirm</button> 
            </div>
            </div>

    <script src="account.js"></script>

    <script>

    function searchOrders() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let tables = ["allOrdersTable", "acceptedOrdersTable", "rejectedOrdersTable", "historyOrdersTable"];

        tables.forEach(tableID => {
            let table = document.getElementById(tableID);
            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip table header
                let cells = rows[i].getElementsByTagName("td");
                let match = false;

                for (let cell of cells) {
                    if (cell) {
                        let text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().includes(input)) {
                            match = true;
                            break; // Stop checking other columns if match found
                        }
                    }
                }

                rows[i].style.display = match ? "" : "none";
            }
        });
    }

    function showOrders(status) {
        let allOrdersTable = document.getElementById("allOrdersTable");
        let acceptedOrdersTable = document.getElementById("acceptedOrdersTable");
        let rejectedOrdersTable = document.getElementById("rejectedOrdersTable");
        let historyOrdersTable = document.getElementById("historyOrdersTable");

        allOrdersTable.style.display = (status === "all") ? "" : "none";
        acceptedOrdersTable.style.display = (status === "accepted") ? "" : "none";
        rejectedOrdersTable.style.display = (status === "rejected") ? "" : "none";
        historyOrdersTable.style.display = (status === "history") ? "" : "none";

        // Remove 'active' class from all buttons
        document.querySelectorAll(".status-btn").forEach(btn => btn.classList.remove("active"));

        // Add 'active' class to the clicked button
        document.getElementById(status + "-btn").classList.add("active");
    }

    // Function to show the modal with a custom message
    function showMessageModal(message) {
        const modal = document.getElementById('messageModal');
        const modalMessage = document.getElementById('modalMessage');
        
        modalMessage.textContent = message; // Set the message dynamically
        
        modal.style.display = 'block'; // Show the modal
    }

    // Function to close the modal
    function closeMessageModal() {
        const modal = document.getElementById('messageModal');
        modal.style.display = 'none'; // Hide the modal
    }

    // Example of updating order status and using the modal
    function updateStatus(orderKey, status) {
        let confirmationMsg = status === 'accepted' 
            ? `Are you sure you want to accept this order?` 
            : `Are you sure you want to reject this order?`;

        showMessageModal(confirmationMsg); // Show the confirmation message in the modal

        // Assuming you would have a confirm button that would handle the fetch request
        document.getElementById('confirmButton').addEventListener('click', function() {
            fetch("update_order_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ orderKey: orderKey, status: status }) // Send JSON data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessageModal(`Order ${status} successfully!`);
                    setTimeout(() => location.reload(), 2000); // Optionally reload the page after a delay
                } else {
                    showMessageModal("Failed to update order status: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showMessageModal("An error occurred while updating the order status.");
            });
        });
    }

    </script>

</body>
</html>
