<?php
ob_start(); 



if (isset($_POST['add_to_cart'])) {
   if ($user_id == 0) {
       header("location: index.php?message=" . urlencode("Please log in to add items to the cart"));
       exit();
   }

   $product_name = mysqli_real_escape_string($connection, $_POST['product_name']);
   $product_price = mysqli_real_escape_string($connection, $_POST['product_price']);
   $product_image = mysqli_real_escape_string($connection, $_POST['product_image']);
   $product_quantity = (int)$_POST['product_quantity'];

   $check_cart = mysqli_query($connection, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'");

   if (!$check_cart) {
       header("location: index.php?message=" . urlencode("Database error"));
       exit();
   }

   if (mysqli_num_rows($check_cart) > 0) {
       $cart_item = mysqli_fetch_assoc($check_cart);
       $new_quantity = $cart_item['quantity'] + $product_quantity;

       $update = mysqli_query($connection, "UPDATE `cart` SET quantity = '$new_quantity' WHERE id = '{$cart_item['id']}'");
       
       if ($update) {
           header("location: index.php?update_msg=" . urlencode("{$product_quantity} item(s) added. Total quantity: {$new_quantity}"));
       } else {
           header("location: index.php?message=" . urlencode("Update failed"));
       }
       exit();
   } else {
       $insert = mysqli_query($connection, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')");
       
       if ($insert) {
           header("location: index.php?insert_msg=" . urlencode("{$product_quantity} item(s) added to cart!"));
       } else {
           header("location: index.php?message=" . urlencode("Insert failed"));
       }
       exit();
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <link rel="stylesheet" href="home.css">
   <style>
    .add-to-cart-btn {
        background-color: #8e44ad !important; 
        color: white !important; 
        width: 50%; 
        transition: transform 0.3s ease-in-out;
    }
    
    .add-to-cart-btn:hover {
        transform: scale(1.1);
    }

   
</style>
</head>
<body>
   

<section class="home">

   <div class="content">
      <h3>Hand Picked Book to your door.</h3>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Excepturi, quod? Reiciendis ut porro iste totam.</p>
      <a href="about.php" class="white-btn">discover more</a>
   </div>

</section>

<section class="container py-5 w-75">
    <h1 class="text-center mb-5 fs-10">LATEST PRODUCTS</h1>
    <div class="row row-cols-lg-3 row-cols-md-2 row-cols-1 g-4 ">
        <?php
        $upload_dir = 'uploaded_img';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $select_products = mysqli_query($connection, "SELECT * FROM products LIMIT 6") or die('Query failed: ' . mysqli_error($connection));
        if (mysqli_num_rows($select_products) > 0) {
            while ($product = mysqli_fetch_assoc($select_products)) {
        ?>
        <div class="col">
            <div class="card shadow-sm text-center border-0 h-100">
                <span class="badge bg-danger position-absolute top-0 start-0 m-2">
                    $<?php echo htmlspecialchars($product['price']); ?>/-
                </span>
                <div class="d-flex align-items-center" style="height: 300px;">
                    <img src="<?php echo $upload_dir . '/' . htmlspecialchars($product['image']); ?>" 
                         class="card-img-top mx-auto p-2 w-75" alt="Product Image" 
                         style="max-height: 100%; width: auto;"
                         onerror="this.src='<?php echo $upload_dir; ?>/default.jpg';">
                </div>
                <div class="card-body p-3">
                    <h6 class="card-title text-truncate"> 
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h6>
                    <form action="" method="POST">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                        <input type="number" name="product_quantity" value="1" min="1" class="form-control mb-2 text-center w-50 mx-auto">
                        <button type="submit" name="add_to_cart" class="btn add-to-cart-btn">
    Add to Cart
</button>                    </form>
                </div>
            </div>
        </div>
        <?php 
            }
        } else {
            echo '<p class="text-center text-muted">No products available!</p>';
        }
        ?>
    </div>
    <div class="text-center mt-4">
        <a href="shop.php" class="btn btn-primary">Load More</a>
    </div>
</section>

   <section class="container py-5" id="about">
      <div class="row align-items-center">
         <div class="col-md-6">
            <img src="images/about-img.jpg" class="img-fluid rounded" alt="About Us">
         </div>
         <div class="col-md-6">
            <h3>About Us</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia corporis ratione saepe sed adipisci?</p>
            <a href="about.php" class="btn btn-outline-dark">Read More</a>
         </div>
      </div>
   </section>

   <section class="bg-light py-5 text-center">
      <div class="container">
         <h3>Have any questions?</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Atque cumque exercitationem repellendus, amet ullam voluptatibus?</p>
         <a href="contact.php" class="btn btn-dark">Contact Us</a>
      </div>
   </section>


<?php include 'footer.php'; ?> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



</body>
</html>

<?php 
ob_end_flush();  
?>