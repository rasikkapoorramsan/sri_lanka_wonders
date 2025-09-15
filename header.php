<?php
session_start();
include 'config.php'; // Ensure database connection for cart query
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discover the Wonders of Sri Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            padding-top: 70px; /* Adjust based on navbar height */
        }
        .navbar {
            background-color: #000; /* Black background */
        }
        .navbar .navbar-brand, .navbar .nav-link, .navbar .dropdown-item {
            color: #fff; /* White text for all navbar elements */
        }
        .navbar .nav-link:hover, .navbar .dropdown-item:hover {
            color: #ffff00; /* Yellow on hover */
        }
        .navbar .dropdown-menu {
            background-color: #000; /* Match navbar background */
        }
        .navbar .dropdown-item:hover {
            background-color: #1a1a1a; /* Darker background on hover */
        }
        .btn-success {
            background-color: #28a745; /* Green for checkout button */
            color: #fff; /* White text for button */
            border: none;
        }
        .btn-success:hover {
            background-color: #218838;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Sri Lanka Wonders</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tourist_guide.php">Tourist Guide</a></li>
                    <li class="nav-item"><a class="nav-link" href="supplier_services.php">Supplier & Services</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="culturalExchangeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cultural Exchange
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="culturalExchangeDropdown">
                            <li><a class="dropdown-item" href="festivals_celebrations.php">Festivals & Celebrations</a></li>
                            <li><a class="dropdown-item" href="arts_crafts.php">Arts & Crafts</a></li>
                            <li><a class="dropdown-item" href="remembrance_places.php">Remembrance Places</a></li>
                            <li><a class="dropdown-item" href="dance_traditional_music.php">Dance & Traditional Music</a></li>
                            <li><a class="dropdown-item" href="food_culinary_culture.php">Food & Culinary Culture</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['username'])) { ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo $_SESSION['username']; ?></span>
                        </li>
                        <?php if (isset($_SESSION['admin_logged_in'])) { ?>
                            <!-- Admin dashboard link removed from navbar -->
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cart.php">Cart</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="checkout.php">Checkout</a>
                            </li>
                            <?php
                            $cart_count = 0;
                            if (isset($_SESSION['user_id'])) {
                                $cart_sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = " . $_SESSION['user_id'];
                                $cart_result = $conn->query($cart_sql);
                                if ($cart_result) {
                                    $cart_count = $cart_result->fetch_assoc()['count'];
                                }
                            }
                            if ($cart_count > 0) {
                                echo '<span class="badge bg-danger text-white ms-1">' . $cart_count . '</span>'; // Optional badge on Cart
                            }
                            ?>
                        <?php } ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php">Sign Up</a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">