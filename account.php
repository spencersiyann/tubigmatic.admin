<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch data from Firebase
$firebase_url = 'https://tubigmatic-admin-default-rtdb.firebaseio.com/Users.json';
$response = @file_get_contents($firebase_url);
$usersData = json_decode($response, true) ?: [];

// Pagination Variables
$usersPerPage = 8; // Users per page
$totalUsers = count($usersData);
$totalPages = ceil($totalUsers / $usersPerPage);
$currentPage = isset($_GET['page']) ? max(1, min($totalPages, intval($_GET['page']))) : 1;
$startIndex = ($currentPage - 1) * $usersPerPage;

// Get only the users for the current page
$usersOnCurrentPage = array_slice($usersData, $startIndex, $usersPerPage, true);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <link rel="stylesheet" href="account.css">
</head>

<body>
    <!-- Navbar Section -->
    <div class="navbar"></div>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="logo">
            <!-- Logo Image and Title -->
            <img src="logo.png" alt="TubigMatic Logo" />
            <h2><span style="color:#006F9E">Tubig</span><span style="color:#3E6606">Matic</span></h2>
            <h4>Admin</h4>
        </div>
        <!-- Sidebar Links -->
        <ul>
            <li><a href="dashboard.php"><img src="icons/dashboard.png" alt="Dashboard Icon"> Dashboard</a></li>
            <li class="active"><a href="account.php"><img src="icons/account.png" alt="Accounts Icon"> Accounts</a></li>
            <li><a href="device.php"><img src="icons/phone.png" alt="Device Icon"> Devices</a></li>
            <li><a href="order.php"><img src="icons/order.png" alt="Device Icon"> Orders</a></li>
            <li><a href="logout.php" id="logout" onclick="return confirmLogout();">
                <img src="icons/logout.png" alt="Logout Icon"> Logout
            </a></li>
        </ul>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <h2>Users</h2>

        <!-- Search Bar Section -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search User's email..." onkeyup="searchUsers()" />
            <img src="icons/search.png" alt="Search" class="search-icon">
        </div>

        <!-- Users Table Section -->
        <div class="users-container">
            <!-- Table for displaying users -->
            <table id="userTable">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact No.</th>
                </tr>

                <!-- PHP Loop to Display Users -->
                <?php if (!empty($usersOnCurrentPage)) : ?>
                    <?php foreach ($usersOnCurrentPage as $userKey => $user) : ?>
                        <?php if (!empty($user['email'])) : // Only display users with an email ?>
                            <tr>
                                <td><?= isset($user['firstName']) && isset($user['lastName']) ? htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) : 'N/A'; ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= isset($user['contactNumber']) ? htmlspecialchars($user['contactNumber']) : 'N/A'; ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- No Users Found -->
                    <tr>
                        <td colspan="3" style="text-align:center;">No users found.</td>
                    </tr>
                <?php endif; ?>

            </table>

            <!-- Pagination Section -->
            <div class="pagination">
                <!-- Previous Page Link -->
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>" class="prev">&laquo; Previous</a>
                <?php endif; ?>

                <!-- Page Number Links -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <!-- Next Page Link -->
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>" class="next">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- External JS File for Account-related JavaScript -->
    <script src="account.js"></script>
</body>

</html>

