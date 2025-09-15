<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle remove item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $delete_sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
    $conn->query($delete_sql);
    header("Location: cart.php");
    exit();
}

// Handle update quantity
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $update_sql = "UPDATE cart SET quantity = $quantity WHERE id = " . intval($cart_id) . " AND user_id = $user_id";
            $conn->query($update_sql);
        }
    }
    header("Location: cart.php");
    exit();
}

// Fetch cart items
$sql = "SELECT c.id, c.quantity, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = $user_id";
$result = $conn->query($sql);

$total = 0;

include 'header.php';
?>

<h1 class="text-center mb-4">Your Cart</h1>
<?php if (isset($_SESSION['success'])) { echo "<div class='alert alert-success'>{$_SESSION['success']}</div>"; unset($_SESSION['success']); } ?>

<form method="POST">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) { 
                while($row = $result->fetch_assoc()) {
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
            ?>
                <tr>
                    <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px;"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>$<?php echo number_format($row['price'], 2); ?></td>
                    <td><input type="number" name="quantity[<?php echo $row['id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" class="form-control" style="width: 80px;"></td>
                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Remove</a></td>
                </tr>
            <?php } } else { ?>
                <tr><td colspan="6" class="text-center">Your cart is empty.</td></tr>
            <?php } ?>
        </tbody>
    </table>
    <?php if ($result->num_rows > 0) { ?>
        <h4 class="text-end">Total: $<?php echo number_format($total, 2); ?></h4>
        <div class="text-end">
            <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>
    <?php } ?>
</form>

<?php include 'footer.php'; ?>