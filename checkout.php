<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart item count for navbar badge
$stmt_cart_count = $conn->prepare("SELECT SUM(quantity) AS item_count FROM cart WHERE user_id = ?");
$stmt_cart_count->bind_param("i", $user_id);
$stmt_cart_count->execute();
$cart_count_result = $stmt_cart_count->get_result();
$cart_item_count = $cart_count_result->fetch_assoc()['item_count'] ?? 0;

// Fetch cart for summary using prepared statement
$stmt = $conn->prepare("SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$cart_empty = ($result->num_rows == 0);

// Calculate total before any modifications
if ($result && $result->num_rows > 0) {
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $total += $row['price'] * $row['quantity'];
    }
}

// Fetch user's orders for cancellation (within 30 minutes) using prepared statement
$stmt_orders = $conn->prepare("SELECT id, total, shipping_address, whatsapp_number, status, created_at 
                              FROM orders 
                              WHERE user_id = ? 
                              AND status = 'pending' 
                              AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders_result = $stmt_orders->get_result();

$order_placed = false;
$order_details = ''; // To store order items for confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order']) && !$cart_empty) {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $whatsapp_number = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);

    // Create order using prepared statement
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total, shipping_address, whatsapp_number, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt_order->bind_param("idss", $user_id, $total, $shipping_address, $whatsapp_number);
    if ($stmt_order->execute()) {
        $order_id = $conn->insert_id;

        // Copy cart to order_items and build order details
        $result->data_seek(0);
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt_item->bind_param("iiid", $order_id, $row['product_id'], $row['quantity'], $row['price']);
            $stmt_item->execute();
            $items[] = "- {$row['name']} x{$row['quantity']} @ $" . number_format($row['price'], 2) . " each";
        }
        $order_details = implode("\n", $items);

        // Clear cart using prepared statement
        $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt_clear->bind_param("i", $user_id);
        $stmt_clear->execute();

        // Fetch user details
        $stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $user_result = $stmt_user->get_result();
        $user = $user_result->num_rows > 0 ? $user_result->fetch_assoc() : ['username' => 'Unknown', 'email' => 'unknown@example.com'];
        $username = $user['username'] ?? 'Unknown';

        // Prepare order details for admin and user
        $order_details_full = "New Order Received - Order ID: $order_id\n";
        $order_details_full .= "User: $username ({$user['email']})\n";
        $order_details_full .= "Total: $$total\n";
        $order_details_full .= "Shipping Address: $shipping_address\n";
        $order_details_full .= "WhatsApp Number: $whatsapp_number\n";
        $order_details_full .= "Items:\n$order_details";

        // Send notification to admin (placeholder for Twilio API)
        $admin_whatsapp = "+9471XXXXXXX"; // Replace with admin's WhatsApp number
        $admin_message = $order_details_full;
        // require_once 'vendor/autoload.php';
        // $twilio = new \Twilio\Rest\Client(TWILIO_SID, TWILIO_TOKEN);
        // $twilio->messages->create($admin_whatsapp, [
        //     "from" => "whatsapp:" . TWILIO_NUMBER,
        //     "body" => $admin_message
        // ]);

        // Send notification to user
        $user_message = "Dear $username, your order #$order_id has been placed successfully!\n";
        $user_message .= "Total: $$total\n";
        $user_message .= "Shipping Address: $shipping_address\n";
        $user_message .= "Items:\n$order_details\n";
        $user_message .= "We will notify you when your order is accepted. Thank you!";
        // $twilio->messages->create($whatsapp_number, [
        //     "from" => "whatsapp:" . TWILIO_NUMBER,
        //     "body" => $user_message
        // ]);

        $_SESSION['success'] = "Order placed successfully! Your Order ID is $order_id. Details: Total: $$total, Items: $order_details";
        $order_placed = true;
        $result = $conn->prepare("SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = ?");
        $result->bind_param("i", $user_id);
        $result->execute();
        $result = $result->get_result(); // Re-fetch cart (now empty)
    } else {
        $error = "Error placing order: " . $conn->error;
    }
}

// Handle update or remove actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update']) && !$order_placed) {
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            $quantity = intval($quantity);
            $cart_id = intval($cart_id);
            if ($quantity > 0) {
                $stmt_update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt_update->bind_param("iii", $quantity, $cart_id, $user_id);
                $stmt_update->execute();
            } else {
                $stmt_delete = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt_delete->bind_param("ii", $cart_id, $user_id);
                $stmt_delete->execute();
            }
        }
        header("Location: checkout.php");
        exit();
    } elseif (isset($_POST['remove']) && !$order_placed) {
        $cart_id = intval($_POST['remove_id']);
        $stmt_delete = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt_delete->bind_param("ii", $cart_id, $user_id);
        $stmt_delete->execute();
        header("Location: checkout.php");
        exit();
    } elseif (isset($_POST['cancel_order'])) {
        $order_id = intval($_POST['order_id']);
        $stmt_cancel = $conn->prepare("UPDATE orders SET status = 'canceled' WHERE id = ? AND user_id = ? AND status = 'pending' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
        $stmt_cancel->bind_param("ii", $order_id, $user_id);
        if ($stmt_cancel->execute()) {
            $_SESSION['success'] = "Order #$order_id has been canceled.";
        } else {
            $error = "Error canceling order: " . $conn->error;
        }
        header("Location: checkout.php");
        exit();
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> <!-- Added Bootstrap JS -->
    <style>
        .navbar {
            background-color: #8b4513; /* Rich brown to match your theme */
        }
        .navbar-brand, .nav-link {
            color: #fffaf0 !important; /* Off-white text */
        }
        .nav-link:hover {
            color: #d4a017 !important; /* Golden hover effect */
        }
        .navbar-toggler {
            border-color: #fffaf0;
        }
        .navbar-toggler-icon {
            background-color: #fffaf0;
        }
        .cart-badge {
            background-color: #d4a017; /* Golden badge */
            color: #fffaf0;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.8em;
            position: absolute;
            top: -5px;
            right: -10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Discover Sri Lanka</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="food_culinary_culture.php">Food & Culinary</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            Cart
                            <?php if ($cart_item_count > 0) { ?>
                                <span class="cart-badge"><?php echo $cart_item_count; ?></span>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="checkout.php">Checkout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($error)) { echo "<div class='alert alert-danger text-center'>$error</div>"; } ?>
    <?php if (isset($_SESSION['success'])) { ?>
        <div class="alert alert-success text-center" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php } ?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h4>Order Summary</h4>
                <?php if ($cart_empty && !$order_placed) { ?>
                    <p class="text-center">Your cart is empty. <a href="cart.php">Go to Cart</a> to add items.</p>
                <?php } else { ?>
                    <form method="POST">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($result && $result->num_rows > 0) {
                                    $result->data_seek(0);
                                    while($row = $result->fetch_assoc()) {
                                        $subtotal = $row['price'] * $row['quantity'];
                                ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><input type="number" name="quantity[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" class="form-control" style="width: 80px;"></td>
                                        <td>$<?php echo number_format($row['price']); ?></td>
                                        <td>$<?php echo number_format($subtotal); ?></td>
                                        <td>
                                            <button type="submit" name="remove" value="remove" class="btn btn-danger btn-sm" 
                                                    onclick="return confirm('Are you sure you want to remove this item from your cart?'); this.form.remove_id.value=<?php echo $row['cart_id']; ?>; this.form.submit();">
                                                Remove
                                            </button>
                                            <input type="hidden" name="remove_id" value="">
                                        </td>
                                    </tr>
                                <?php  $subtotal; } } ?>
                            </tbody>
                        </table>
                        <?php if ($result && $result->num_rows > 0) { ?>
                            <div class="text-end">
                                <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
                                <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
                            </div>
                        <?php } ?>
                    </form>
                <?php } ?>
            </div>
            <div class="col-md-4">
                <h4>Shipping Information</h4>
                <?php if (!$cart_empty || $order_placed) { ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" required <?php echo $order_placed ? 'disabled' : ''; ?>><?php echo $order_placed ? '' : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                            <input type="tel" class="form-control" id="whatsapp_number" name="whatsapp_number" placeholder="+9471-XXX-XXXX" pattern="\+94[0-9]{9}" title="Enter a valid Sri Lankan WhatsApp number (e.g., +94711234567)" required <?php echo $order_placed ? 'disabled' : ''; ?>>
                        </div>
                        <?php if (!$order_placed) { ?>
                            <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
                        <?php } ?>
                    </form>
                    <p class="mt-3">Note: This is a simulated checkout. No real payment is processed.</p>
                    <h4 class="mt-4">Total: $<?php echo number_format($total); ?></h4>
                <?php } ?>
            </div>
        </div>

        <!-- Order History with Cancel Option (30-minute limit) -->
        <?php if ($orders_result && $orders_result->num_rows > 0) { ?>
            <div class="mt-5">
                <h4>My Orders</h4>
                <div class="alert alert-warning text-center" role="alert">
                    You have 30 minutes to cancel your order.
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Total</th>
                            <th>Shipping Address</th>
                            <th>WhatsApp Number</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td>$<?php echo number_format($order['total'], ); ?></td>
                                <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                <td><?php echo htmlspecialchars($order['whatsapp_number']); ?></td>
                                <td><?php echo $order['status']; ?></td>
                                <td><?php echo $order['created_at']; ?></td>
                                <td>
                                    <?php if ($order['status'] === 'pending') { ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel order #<?php echo $order['id']; ?>?');">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" name="cancel_order" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    <?php } else { echo "N/A"; } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } elseif (!$cart_empty || $order_placed) { echo "<p class='mt-5'>No recent orders available for cancellation.</p>"; } ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>