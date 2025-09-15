<?php
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: food_culinary_culture.php");
    exit();
}

$product_id = intval($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: food_culinary_culture.php");
    exit();
}

// Handle add to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $quantity = intval($_POST['quantity']);

    // Check if already in cart
    $check_sql = "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update quantity
        $cart_item = $check_result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = $new_quantity WHERE id = " . $cart_item['id'];
        $conn->query($update_sql);
    } else {
        // Insert new
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $conn->query($insert_sql);
    }

    $_SESSION['success'] = "Item added to cart successfully!";
    header("Location: food_culinary_culture.php"); // Redirect back to food page instead of checkout
    exit();
}

include 'header.php';
?>

<h1 class="text-center mb-4"><?php echo $product['name']; ?></h1>
<?php if (isset($_SESSION['success'])) { ?>
    <div class="alert alert-success text-center" role="alert">
        <?php echo $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php } ?>
<div class="row">
    <div class="col-md-6">
        <img src="<?php echo $product['image']; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>">
    </div>
    <div class="col-md-6">
        <p><?php echo $product['description']; ?></p>
        <h4>Price: $<?php echo number_format($product['price'], 2); ?></h4>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                </div>
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>
        <?php } else { ?>
            <p>Please <a href="login.php">log in</a> to add this to your cart.</p>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>