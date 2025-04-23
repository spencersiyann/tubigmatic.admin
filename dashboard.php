<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Function to fetch data from Firebase Realtime Database
function fetchFirebaseData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true); // Convert JSON to PHP array
}

// Firebase Realtime Database URL
$firebase_url = "https://tubigmatic-admin-default-rtdb.firebaseio.com/";

// Fetch users and devices
$users = fetchFirebaseData($firebase_url . "Users.json");
$devices = fetchFirebaseData($firebase_url . "Devices.json");
$orders = fetchFirebaseData($firebase_url . "Orders.json");
$acceptedOrders = fetchFirebaseData($firebase_url . "AcceptedOrders.json");
$rejectedOrders = fetchFirebaseData($firebase_url . "RejectedOrders.json");


// Ensure data is an array before using it
$users = is_array($users) ? $users : [];
$devices = is_array($devices) ? $devices : [];
$orders = is_array($orders) ? $orders : [];
$acceptedOrders = is_array($acceptedOrders) ? $acceptedOrders : [];
$rejectedOrders = is_array($rejectedOrders) ? $rejectedOrders : [];

// Count orders based on their status
$pendingOrdersCount = count($orders);
$acceptedOrdersCount = count($acceptedOrders);
$rejectedOrdersCount = count($rejectedOrders);

$totalUsers = count($users);

// Debugging: Check if data is retrieved properly
if (empty($users)) {
    echo "<p style='color:red;'>Error: Unable to fetch users from Firebase.</p>";
}

if (empty($devices)) {
    echo "<p style='color:red;'>Error: Unable to fetch devices from Firebase.</p>";
}

// Format users
$users_list = [];
foreach ($users as $user) {
    if (isset($user['firstName'], $user['lastName'], $user['contactNumber'])) {
        $users_list[] = [
            "fullname" => htmlspecialchars($user['firstName'] . " " . $user['lastName']),
            "contact" => htmlspecialchars($user['contactNumber']),
        ];
    }
}

// Format devices
$devices_list = [];
foreach ($devices as $device) {
    if (isset($device['deviceID'], $device['email'])) {
        $devices_list[] = [
            "id" => htmlspecialchars($device['deviceID']),
            "email" => htmlspecialchars($device['email']),
        ];
    }
}

// Pagination Settings (User's Table)
$usersPerPage = 3;
$totalUsers = count($users_list);
$totalUserPages = ceil($totalUsers / $usersPerPage);
$userPage = isset($_GET['user_page']) ? (int)$_GET['user_page'] : 1;
$userPage = max(1, min($totalUserPages, $userPage));
$startUserIndex = ($userPage - 1) * $usersPerPage;
$usersOnPage = array_slice($users_list, $startUserIndex, $usersPerPage);

//Pagination Settings (Device's Table)
$devicesPerPage = 3;
$totalDevices = count($devices_list);
$totalDevicePages = ceil($totalDevices / $devicesPerPage);
$devicePage = isset($_GET['device_page']) ? (int)$_GET['device_page'] : 1;
$devicePage = max(1, min($totalDevicePages, $devicePage));
$startDeviceIndex = ($devicePage - 1) * $devicesPerPage;
$devicesOnPage = array_slice($devices_list, $startDeviceIndex, $devicesPerPage);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Navbar -->
    <div class="navbar"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="TubigMatic Logo">
            <h2><span style="color:#006F9E">Tubig</span><span style="color:#3E6606">Matic</span></h2>
            <h4>Admin</h4>
        </div>
        <ul>
            <!-- Sidebar Links -->
            <li class="active">
                <a href="dashboard.php">
                    <img src="icons/dashboard.png" alt="Dashboard Icon"> Dashboard
                </a>
            </li>
            <li>
                <a href="account.php">
                    <img src="icons/account.png" alt="Accounts Icon"> Accounts
                </a>
            </li>
            <li>
                <a href="device.php">
                    <img src="icons/phone.png" alt="Device Icon"> Devices
                </a>
            </li>
            <li>
                <a href="order.php">
                    <img src="icons/order.png" alt="Device Icon"> Orders
                </a>
            </li>
            <!-- Logout with confirmation -->
            <li>
                <a href="logout.php" id="logout" onclick="return confirmLogout();">
                    <img src="icons/logout.png" alt="Logout Icon"> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Overview</h2>

        <div class="stats">
            <!-- Displaying Number of Users -->
            <div class="stat-box">
                <h4>Number of Users</h4>
                <p><?= count($users_list) ?></p>
            </div>

            <!-- Displaying Total Devices -->
            <div class="stat-box">
                <h4>Total Devices</h4>
                <p><?= count(array_filter($devices_list, fn($d) => $d['email'])) ?></p>
            </div>

            <!-- Displaying Pending Orders -->
            <div class="stat-box">
                <h4>Pending Orders</h4>
                <p><?= $pendingOrdersCount ?><br></p>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Data Containers (Users and Devices) -->
        <div class="data-container">
            <!-- Users Card -->
            <div class="card">
                <h3>Users</h3>
                <table>
                    <tr>
                        <th>Fullname</th>
                        <th>Contact No.</th>
                    </tr>
                    <!-- Loop through users and display their details -->
                    <?php foreach ($usersOnPage as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['fullname']) ?></td>
                            <td><?= htmlspecialchars($user['contact']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Pagination for Users -->
                <div class="pagination">
                    <!-- Previous page link (if not the first page) -->
                    <?php if ($userPage > 1): ?>
                        <a href="?user_page=<?= $userPage - 1 ?>">Previous</a>
                    <?php endif; ?>

                    <!-- Loop to display page numbers -->
                    <?php for ($i = 1; $i <= $totalUserPages; $i++): ?>
                        <a href="?user_page=<?= $i ?>" class="<?= ($i == $userPage) ? 'active' : '' ?>"> <?= $i ?> </a>
                    <?php endfor; ?>

                    <!-- Next page link (if not the last page) -->
                    <?php if ($userPage < $totalUserPages): ?>
                        <a href="?user_page=<?= $userPage + 1 ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Devices Card -->
            <div class="card">
                <h3>Devices</h3>
                <table>
                    <tr>
                        <th>Device ID</th>
                        <th>Email</th>
                    </tr>
                    <!-- Loop through devices and display their details -->
                    <?php foreach ($devicesOnPage as $device): ?>
                        <tr>
                            <td><?= htmlspecialchars($device['id']) ?></td>
                            <td><?= htmlspecialchars($device['email']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Pagination for Devices -->
                <div class="pagination">
                    <!-- Previous page link (if not the first page) -->
                    <?php if ($devicePage > 1): ?>
                        <a href="?device_page=<?= $devicePage - 1 ?>">Previous</a>
                    <?php endif; ?>

                    <!-- Loop to display page numbers -->
                    <?php for ($i = 1; $i <= $totalDevicePages; $i++): ?>
                        <a href="?device_page=<?= $i ?>" class="<?= ($i == $devicePage) ? 'active' : '' ?>"> <?= $i ?> </a>
                    <?php endfor; ?>

                    <!-- Next page link (if not the last page) -->
                    <?php if ($devicePage < $totalDevicePages): ?>
                        <a href="?device_page=<?= $devicePage + 1 ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to confirm logout -->
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>


    <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      margin: 0;
      flex-direction: column;
    }

    /* Top Navbar Style */
    .navbar {
      background-color: #3E6606;
      height: 50px;
      width: 100%;
    }

    li a {
      text-decoration: none;
      color: #000000;
    }

    /* Sidebar Style */
    .sidebar {
      width: 250px;
      background: #ffffff;
      color: white;
      height: calc(100vh - 50px);
      padding: 0px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 2);
      position: fixed;
      top: 50px;
      left: 0;
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
      margin-top: 20px;
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

    /* Main Content Style */
    .main-content {
      flex: 1;
      padding: 20px;
      margin-left: 270px;
      margin-top: 5px;
    }

    /* Status Style */
    .stats {
    display: flex;
    margin-left: 100px;
    width: 800px;
    height: 120px;
    margin-top: 25px;
    gap: 80px;
    }
    .stat-box {
    background: #ffffff;
    box-shadow: 0 4px 3px #3E6606;
    padding: 8px;
    font-size: 18px;
    border-radius: 10px;
    text-align: center;
    flex: 1;
    }

    /* Divider Style */
    .divider {
    border-top: 1px solid #3E6606;
    margin-top: 50px;
    margin-bottom: 40px;
    }

    /* Data Container Style */
    .data-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    width: 990px;
    }
    
    /* Card Style */
    .card {
    background: white;
    padding: 20px;
    border-radius: 3px;
    font-size: 12px;
    box-shadow: 0 1px 1px 1.2px #61862c;
    flex: 1;
    }

    /* Table Style */
    table {
    width: 100%;
    border-collapse: collapse;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
    margin-top: -10px;
    }
    table, th, td {
    border: 1px solid #cfcfcf;
    }
    th, td {
    padding: 10px;
    text-align: left;
    }
    
    /* Pagination Style */
    .pagination {
        margin-top: 30px;
        text-align: right;
    }
    .pagination button {
    background-color: #a3a3a3;
    color: white;
    border: none;
    padding: 5px 10px;
    margin: 0 3px;
    cursor: pointer;
    border-radius: 3px;
    transition: background-color 0.3s;
    }
    .pagination button.active {
    background-color: #1d1d1d; 
    color: #ffffff; 
    }
    .pagination button:hover {
    background-color: #3a3a3a;
    }
    .pagination a {
    padding: 8px 12px;
    margin: 5px;
    text-decoration: none;
    background: #ddd;
    color: black;
    border-radius: 5px;
    }
    .pagination a.active {
    background: #3E6606;
    color: white;
    }
    .pagination a:hover {
    background: #9FB383;
    }
    </style>

    
</body>
</html>
