<?php
include 'header.php';
include 'config.php';

// Fetch all categories and their products
$categories_sql = "SELECT id, name FROM categories";
$categories_result = $conn->query($categories_sql);

$search_term = isset($_GET['search']) ? trim($_GET['search']) : ''; // Get search term from URL
?>

<style>
    body {
        background: #f7e4d4; /* Warm cream background */
        font-family: 'Playfair Display', serif;
        color: #3c2f2f; /* Dark brown text */
    }
    h1 {
        font-size: 3em;
        font-weight: 700;
        color: #8b4513; /* Rich brown */
        text-transform: uppercase;
        letter-spacing: 2px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    h2 {
        font-size: 2em;
        color: #8b4513;
        margin-top: 40px;
        margin-bottom: 20px;
        text-transform: capitalize;
    }
    p.text-center {
        font-size: 1.2em;
        color: #6b4e31; /* Muted brown */
        max-width: 800px;
        margin: 0 auto 40px;
        line-height: 1.8;
    }
    .search-container {
        max-width: 600px;
        margin: 0 auto 30px;
    }
    .search-input {
        border-radius: 25px;
        padding: 12px 20px;
        border: 2px solid #d4a017; /* Golden border */
        font-size: 1.1em;
    }
    .search-input:focus {
        border-color: #b8860b; /* Darker gold on focus */
        box-shadow: 0 0 0 0.2rem rgba(212, 160, 23, 0.25);
    }
    .no-results {
        text-align: center;
        color: #6b4e31;
        font-size: 1.2em;
        margin-top: 40px;
    }
    .alert-success {
        background-color: #d4e4d2; /* Soft green for success */
        border: 2px solid #4a704a;
        color: #2f4f2f;
        font-size: 1.1em;
        padding: 15px;
        margin-bottom: 30px;
    }
    .row {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .card {
        border: none;
        background: #fffaf0; /* Off-white with warm tone */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        position: relative;
        margin-bottom: 30px;
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 24px rgba(139, 69, 19, 0.2);
    }
    .card-img-top {
        height: 250px;
        object-fit: cover;
        filter: brightness(95%) contrast(110%);
        border-bottom: 4px solid #d4a017; /* Golden border */
        position: relative;
    }
    .card-img-top::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.3), transparent);
    }
    .card-body {
        padding: 20px;
        background: #fff5e6; /* Warm peach */
        text-align: center;
    }
    .card-title {
        font-size: 1.8em;
        color: #8b4513;
        font-weight: 600;
        margin-bottom: 10px;
        text-transform: capitalize;
    }
    .card-text {
        color: #6b4e31;
        font-size: 1.1em;
        line-height: 1.6;
        min-height: 60px;
    }
    .btn-primary {
        background-color: #d4a017; /* Golden yellow */
        border: none;
        padding: 12px 25px;
        font-size: 1.1em;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 25px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #b8860b; /* Darker gold */
        transform: scale(1.05);
        color: #fff;
    }
    @media (max-width: 768px) {
        h1 {
            font-size: 2em;
        }
        h2 {
            font-size: 1.5em;
        }
        p.text-center {
            font-size: 1em;
        }
        .card-img-top {
            height: 200px;
        }
        .card-title {
            font-size: 1.5em;
        }
    }
</style>

<h1 class="text-center mb-4">Food & Culinary Culture</h1>
<p class="text-center">Explore a variety of authentic Sri Lankan dishes, rich in spices and flavors. Click on a card to learn more and purchase.</p>

<!-- Search Box -->
<div class="search-container">
    <form method="GET" class="d-flex">
        <input type="text" class="form-control search-input" name="search" placeholder="Search by cultural name (e.g., Sinhalese, Tamil, Moor)" value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn btn-primary ms-2">Search</button>
        <?php if ($search_term) { ?>
            <a href="food_culinary_culture.php" class="btn btn-secondary ms-2">Clear</a>
        <?php } ?>
    </form>
</div>

<?php if (isset($_GET['order_success']) && $_GET['order_success'] == 1 && isset($_SESSION['success'])) { ?>
    <div class="alert alert-success text-center" role="alert">
        <?php echo $_SESSION['success']; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php } ?>

<div class="container mt-4">
    <?php if ($search_term) { ?>
        <p class="text-center">Searching for "<?php echo htmlspecialchars($search_term); ?>". Showing matching cultural foods in order.</p>
    <?php } ?>
    
    <?php
    $all_categories = [];
    if ($categories_result && $categories_result->num_rows > 0) {
        $categories_result->data_seek(0);
        while ($category = $categories_result->fetch_assoc()) {
            $all_categories[] = $category;
        }
    }

    if ($search_term) {
        // Filter and sort matching categories alphabetically
        $matching_categories = array_filter($all_categories, function($category) use ($search_term) {
            return stripos($category['name'], $search_term) !== false;
        });
        usort($matching_categories, function($a, $b) {
            return strnatcmp($a['name'], $b['name']);
        });

        $search_matched = !empty($matching_categories);
        if ($search_matched) {
            foreach ($matching_categories as $category) {
                echo "<h2>" . htmlspecialchars($category['name']) . "</h2>";
                echo "<div class='row'>";
                
                $products_sql = "SELECT * FROM products WHERE category_id = " . $category['id'];
                $products_result = $conn->query($products_sql);
                
                if ($products_result && $products_result->num_rows > 0) {
                    while ($row = $products_result->fetch_assoc()) { ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $row['name']; ?></h5>
                                    <p class="card-text"><?php echo substr($row['description'], 0, 100) . '...'; ?></p>
                                    <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                                    <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-success">Add to to Cart</a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } else {
                    echo "<p>No products in this category.</p>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='no-results'>No cultural foods found for '" . htmlspecialchars($search_term) . "'. Please try a different search term.</div>";
        }
    } else {
        // Display all categories and products in default order
        foreach ($all_categories as $category) {
            echo "<h2>" . htmlspecialchars($category['name']) . "</h2>";
            echo "<div class='row'>";
            
            $products_sql = "SELECT * FROM products WHERE category_id = " . $category['id'];
            $products_result = $conn->query($products_sql);
            
            if ($products_result && $products_result->num_rows > 0) {
                while ($row = $products_result->fetch_assoc()) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="<?php echo $row['image']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['name']; ?></h5>
                                <p class="card-text"><?php echo substr($row['description'], 0, 100) . '...'; ?></p>
                                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php }
            } else {
                echo "<p>No products in this category.</p>";
            }
            echo "</div>";
        }
    }
    ?>
</div>

<?php include 'footer.php'; ?>