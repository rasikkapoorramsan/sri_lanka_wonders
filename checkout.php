<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart for summary
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

$total = 0;
$cart_empty = ($result->num_rows == 0);

// Fetch user's orders for cancellation (within 30 minutes)
$orders_sql = "SELECT id, total, shipping_address, whatsapp_number, status, created_at 
               FROM orders 
               WHERE user_id = $user_id 
               AND status = 'pending' 
               AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
$orders_result = $conn->query($orders_sql);

$order_placed = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order']) && !$cart_empty) {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $whatsapp_number = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);

    // Create order
    $order_sql = "INSERT INTO orders (user_id, total, shipping_address, whatsapp_number, status) VALUES ($user_id, $total, '$shipping_address', '$whatsapp_number', 'pending')";
    if ($conn->query($order_sql) === TRUE) {
        $order_id = $conn->insert_id;

        // Copy cart to order_items
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, {$row['product_id']}, {$row['quantity']}, {$row['price']})";
            $conn->query($item_sql);
        }

        // Clear cart
        $clear_sql = "DELETE FROM cart WHERE user_id = $user_id";
        $conn->query($clear_sql);

        // Fetch user details
        $user_sql = "SELECT username, email FROM users WHERE id = $user_id";
        $user_result = $conn->query($user_sql);
        $user = $user_result && $user_result->num_rows > 0 ? $user_result->fetch_assoc() : ['username' => 'Unknown', 'email' => 'unknown@example.com'];
        $username = $user['username'] ?? 'Unknown';

        // Prepare order details
        $order_details = "New Order Received - Order ID: $order_id\n";
        $order_details .= "User: $username ({$user['email']})\n";
        $order_details .= "Total: $$total\n";
        $order_details .= "Shipping Address: $shipping_address\n";
        $order_details .= "WhatsApp Number: $whatsapp_number\n";
        $order_details .= "Items:\n";
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $order_details .= "- {$row['name']} x{$row['quantity']} @ $${number_format($row['price'], 2)} each\n";
        }

        // Send notification to admin (placeholder for Twilio API)
        $admin_whatsapp = "+9471XXXXXXX"; // Replace with admin's WhatsApp number
        $admin_message = "New order placed! $order_details";
        // require_once 'vendor/autoload.php';
        // $twilio = new \Twilio\Rest\Client(TWILIO_SID, TWILIO_TOKEN);
        // $twilio->messages->create($admin_whatsapp, [
        //     "from" => "whatsapp:" . TWILIO_NUMBER,
        //     "body" => $admin_message
        // ]);

        $_SESSION['success'] = "Order placed successfully! Your Order ID is $order_id.";
        $order_placed = true;
        $result = $conn->query($sql); // Re-fetch cart (now empty)
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
                $update_sql = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
                $conn->query($update_sql);
            } else {
                $delete_sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
                $conn->query($delete_sql);
            }
        }
        header("Location: checkout.php");
        exit();
    } elseif (isset($_POST['remove']) && !$order_placed) {
        $cart_id = intval($_POST['remove_id']);
        $delete_sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        $conn->query($delete_sql);
        header("Location: checkout.php");
        exit();
    } elseif (isset($_POST['cancel_order'])) {
        $order_id = intval($_POST['order_id']);
        $update_sql = "UPDATE orders SET status = 'canceled' WHERE id = $order_id AND user_id = $user_id AND status = 'pending' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)";
        if ($conn->query($update_sql) === TRUE) {
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

<h1 class="text-center mb-4">Checkout</h1>
<?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
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
                                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <button type="submit" name="remove" value="remove" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to remove this item from your cart?'); this.form.remove_id.value=<?php echo $row['cart_id']; ?>; this.form.submit();">
                                            Remove
                                        </button>
                                        <input type="hidden" name="remove_id" value="">
                                    </td>
                                </tr>
                            <?php $total += $subtotal; } } ?>
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
                <h4 class="mt-4">Total: $<?php echo number_format($total, 2); ?></h4>
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
                            <td>$<?php echo number_format($order['total'], 2); ?></td>
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