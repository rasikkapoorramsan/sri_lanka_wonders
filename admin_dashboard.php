<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit();
}

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $sql = "INSERT INTO products (name, description, price, image) VALUES ('$name', '$description', $price, '$image')";
    $conn->query($sql);
}

// Handle product removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_product'])) {
    $product_id = intval($_POST['product_id']);
    
    $check_cart = $conn->query("SELECT COUNT(*) as count FROM cart WHERE product_id = $product_id");
    $check_orders = $conn->query("SELECT COUNT(*) as count FROM order_items WHERE product_id = $product_id");
    $cart_count = $check_cart ? $check_cart->fetch_assoc()['count'] : 0;
    $order_count = $check_orders ? $check_orders->fetch_assoc()['count'] : 0;
    
    if ($cart_count > 0 || $order_count > 0) {
        // Silently fail if product is in cart or orders
    } else {
        $sql = "DELETE FROM products WHERE id = $product_id";
        $conn->query($sql);
    }
}

// Handle product update (display form)
$edit_product = null;
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['edit'])) {
    $product_id = intval($_GET['edit']);
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $edit_product = $result->fetch_assoc();
    }
}

// Handle product update (submit)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $product_id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);

    $sql = "UPDATE products SET name = '$name', description = '$description', price = $price, image = '$image' WHERE id = $product_id";
    $conn->query($sql);
    $edit_product = null;
}

// Handle order acceptance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept_order'])) {
    $order_id = intval($_POST['order_id']);
    $sql = "UPDATE orders SET status = 'accepted' WHERE id = $order_id";
    if ($conn->query($sql) === TRUE) {
        // Fetch order details for user notification with error handling
        $order_sql = "SELECT o.whatsapp_number, u.username, o.total 
                      FROM orders o 
                      JOIN users u ON o.user_id = u.id 
                      WHERE o.id = $order_id";
        $order_result = $conn->query($order_sql);
        $order = $order_result && $order_result->num_rows > 0 ? $order_result->fetch_assoc() : ['whatsapp_number' => '', 'username' => 'Unknown', 'total' => 0];
        $user_whatsapp = $order['whatsapp_number'] ?? '';
        $username = $order['username'] ?? 'Unknown';
        $total = $order['total'] ?? 0;

        // Send acceptance message to user
        $user_message = "Dear $username, your order #$order_id has been accepted! Total: $$total. Thank you for shopping with us.";
        // Placeholder for Twilio API
        // require_once 'vendor/autoload.php';
        // $twilio = new \Twilio\Rest\Client(TWILIO_SID, TWILIO_TOKEN);
        // $twilio->messages->create($user_whatsapp, [
        //     "from" => "whatsapp:" . TWILIO_NUMBER,
        //     "body" => $user_message
        // ]);
    }
}

// Fetch existing products
$product_sql = "SELECT * FROM products";
$product_result = $conn->query($product_sql);

// Fetch orders (excluding canceled ones)
$order_sql = "SELECT o.id AS order_id, o.user_id, o.total, o.shipping_address, o.whatsapp_number, o.status, u.username, u.email 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              WHERE o.status != 'canceled'";
$order_result = $conn->query($order_sql);

// Fetch user details
$user_sql = "SELECT id, username, email, created_at FROM users";
$user_result = $conn->query($user_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Discover the Wonders of Sri Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .order-items { max-height: 100px; overflow-y: auto; word-break: break-word; }
        .product-actions { white-space: nowrap; }
        .order-actions { white-space: nowrap; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Admin Dashboard</h2>

        <!-- Add Product Form -->
        <h3>Add New Product</h3>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image URL</label>
                <input type="url" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>

        <!-- Existing Products List -->
        <h3 class="mt-5">Existing Products</h3>
        <?php if ($product_result && $product_result->num_rows > 0) { ?>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $product_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($row['price'] ?? 0, 2); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($row['name'] ?? ''); ?>" style="width: 100px;" onerror="this.src='https://via.placeholder.com/100';"></td>
                            <td class="product-actions">
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this product? Related cart items will be cleared.');">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="remove_product" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm ms-2">Update</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { echo "<p>No products available.</p>"; } ?>

        <!-- Update Product Form (if in edit mode) -->
        <?php if ($edit_product) { ?>
            <h3 class="mt-5">Update Product</h3>
            <form method="POST" class="mt-3">
                <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo floatval($edit_product['price'] ?? 0); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="image" name="image" value="<?php echo htmlspecialchars($edit_product['image'] ?? ''); ?>" required>
                </div>
                <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                <a href="admin_dashboard.php" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        <?php } ?>

        <!-- User Details -->
        <h3 class="mt-5">User Details</h3>
        <?php if ($user_result && $user_result->num_rows > 0) { ?>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $user_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { echo "<p>No users registered yet.</p>"; } ?>

        <!-- List Orders (excluding canceled) -->
        <h3 class="mt-5">Orders</h3>
        <?php if ($order_result && $order_result->num_rows > 0) { ?>
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Total</th>
                        <th>Shipping & WhatsApp</th>
                        <th>Items</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $order_result->fetch_assoc()) {
                        $order_id = $order['order_id'];
                        $item_sql = "SELECT p.name, oi.quantity, oi.price 
                                    FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.id 
                                    WHERE oi.order_id = $order_id";
                        $item_result = $conn->query($item_sql);
                        if ($item_result === false) {
                            $items_list = "Error fetching items";
                        } else {
                            $items = [];
                            while ($item = $item_result->fetch_assoc()) {
                                $items[] = htmlspecialchars("{$item['name']} x{$item['quantity']} @ $" . number_format($item['price'], 2));
                            }
                            $items_list = !empty($items) ? implode('<br>', $items) : 'No items';
                        }
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></td>
                            <td>$<?php echo number_format($order['total'] ?? 0, 2); ?></td>
                            <td>
                                <strong>Shipping:</strong> <?php echo htmlspecialchars($order['shipping_address'] ?? 'N/A'); ?><br>
                                <strong>WhatsApp:</strong> <?php echo htmlspecialchars($order['whatsapp_number'] ?? 'N/A'); ?>
                            </td>
                            <td class="order-items"><?php echo $items_list; ?></td>
                            <td class="order-actions">
                                <?php 
                                $status = $order['status'] ?? 'unknown';
                                if ($status === 'pending') { ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" name="accept_order" class="btn btn-success btn-sm">Accept</button>
                                    </form>
                                <?php } elseif ($status === 'accepted') { ?>
                                    <span class="text-success">Accepted</span>
                                <?php } else { ?>
                                    <span class="text-warning">Unknown Status</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { echo "<p>No orders available.</p>"; } ?>

        <a href="admin_logout.php" class="btn btn-danger mt-4">Logout</a>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
// Admin logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}
?>