<?php
ob_start(); 
session_start(); 
include 'connection.php';

if (isset($_SESSION['id'])) {
   $user_id = $_SESSION['id']; 
} else {
   $user_id = 0; 
}

if(!isset($user_id)){
   header("location:registration.php?message=" . urlencode("Failed to register: " . mysqli_error($connection)));
   exit();
}

if (isset($_POST['add_to_cart'])) {
   if ($user_id == 0) {
       header("location: shop.php?message=" . urlencode("Please log in to add items to the cart"));
       exit();
   }

   $product_name = mysqli_real_escape_string($connection, $_POST['product_name']);
   $product_price = mysqli_real_escape_string($connection, $_POST['product_price']);
   $product_image = mysqli_real_escape_string($connection, $_POST['product_image']);
   $product_quantity = (int)$_POST['product_quantity'];

   $check_cart = mysqli_query($connection, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'");

   if (!$check_cart) {
       header("location: shop.php?message=" . urlencode("Database error"));
       exit();
   }

   if (mysqli_num_rows($check_cart) > 0) {
       $cart_item = mysqli_fetch_assoc($check_cart);
       $new_quantity = $cart_item['quantity'] + $product_quantity;

       $update = mysqli_query($connection, "UPDATE `cart` SET quantity = '$new_quantity' WHERE id = '{$cart_item['id']}'");
       
       if ($update) {
           header("location: shop.php?update_msg=" . urlencode("{$product_quantity} item(s) added. Total quantity: {$new_quantity}"));
       } else {
           header("location: shop.php?message=" . urlencode("Update failed"));
       }
       exit();
   } else {
       $insert = mysqli_query($connection, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')");
       
       if ($insert) {
           header("location: shop.php?insert_msg=" . urlencode("{$product_quantity} item(s) added to cart!"));
       } else {
           header("location: shop.php?message=" . urlencode("Insert failed"));
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
   <title>search page</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>

@import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap');

:root{
   --purple:#8e44ad;
   --purple-light:#a55ebd;
   --orange:#f39c12;
   --black:#333;
   --dark-gray:#444;
   --white:#fff;
   --light-color:#666;
   --light-bg:#f5f5f5;
   --border:.1rem solid var(--black);
   --box-shadow:0 .5rem 1rem rgba(0,0,0,.1);
   --gradient: linear-gradient(90deg, var(--purple), #9b59b6);
}

* {
   font-family: 'Rubik', sans-serif;
   margin: 0;
   padding: 0;
   box-sizing: border-box;
   text-decoration: none;
   transition: all 0.3s ease;
}

body {
   background-color: var(--light-bg);
   color: var(--black);
}

.heading {
   display: flex;
   flex-flow: column;
   align-items: center;
   justify-content: center;
   gap: 1rem;
   background: url(images/heading-bg.webp) no-repeat;
   background-size: cover;
   background-position: center;
   text-align: center;
}


.heading h3 {
   font-size: 2.5rem;
   color: var(--white);
   text-transform: uppercase;
   letter-spacing: 1px;
   text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.heading p {
   font-size: 1.2rem;
   color: var(--black);
}

.heading p a {
   color: var(--black);
   font-weight: 500;
}

.heading p a:hover {
   text-decoration: underline;
}

.search-form {
    padding: 3rem 0;
}

.search-form form {
    max-width: 800px;
    margin: 0 auto;
    display: flex;
    gap: 1rem;
    padding: 0 1rem;
}

.search-form .box {
    flex: 1;
    padding: 1.2rem 1.5rem;
    border: 2px solid var(--purple-light);
    border-radius: 2rem;
    font-size: 1.2rem;
    background: var(--white);
    transition: all 0.3s ease;
}

.search-form .box:focus {
    border-color: var(--purple);
    box-shadow: 0 0 15px rgba(142, 68, 173, 0.1);
}

.search-form .btn {
    padding: 1rem 2.5rem;
    border-radius: 2rem;
    font-size: 1.2rem;
    background: var(--purple);
    transition: all 0.3s ease;
    color: white !important;
}

.search-form .btn:hover {
    background: var(--purple-light);
    transform: scale(1.05);
}

.products .box-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.products .box {
    background: var(--white);
    border-radius: 1rem;
    padding: 2rem;
    text-align: center;
    box-shadow: var(--box-shadow);
    transition: transform 0.3s ease;
    position: relative;
    overflow: hidden;
}

.products .box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.products .box .image {
    height: 200px;
    width: 100%;
    object-fit: contain;
    margin-bottom: 1.5rem;
}

.products .box .name {
    font-size: 1.4rem;
    color: var(--black);
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.products .box .price {
    font-size: 1.8rem;
    color: var(--purple);
    margin-bottom: 1.5rem;
    font-weight: 700;
}

.products .box .qty {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid var(--purple-light);
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
    text-align: center;
}

.products .box .btn {
    width: 100%;
    padding: 1rem;
    border-radius: 0.5rem;
    font-size: 1.2rem;
    background: var(--purple);
    transition: all 0.3s ease;
}

.products .box .btn:hover {
    background: var(--purple-light);
    transform: scale(1.02);
}

.empty {
    text-align: center;
    font-size: 1.4rem;
    color: var(--light-color);
    width: 100%;
    padding: 3rem 0;
}

@media (max-width: 768px) {
    .search-form form {
        flex-direction: column;
    }
    
    .search-form .btn {
        width: 100%;
    }
    
    .products .box-container {
        grid-template-columns: 1fr;
        padding: 2rem 1rem;
    }
}


.message-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 300px;
        }
        
        .alert-slide {
            animation: slideIn 0.5s ease-out forwards;
            opacity: 0;
            transform: translateX(100%);
        }
        
        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .alert-hide {
            animation: slideOut 0.5s ease-in forwards;
        }
        
        @keyframes slideOut {
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }
    </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="index.php">home</a> / search </p>
</div>

<section class="search-form">
    <form action="" method="post">
        <input type="text" name="search" placeholder="Search products..." class="box">
        <input type="submit" name="submit" value="Search" class="btn">
    </form>
</section>

<section class="products" style="padding-top: 0;">
    <div class="box-container">
    <?php
        if(isset($_POST['submit'])){
            $search_item = $_POST['search'];
            $select_products = mysqli_query($connection, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
            if(mysqli_num_rows($select_products) > 0){
            while($fetch_product = mysqli_fetch_assoc($select_products)){
    ?>
    <form action="" method="post" class="box">
        <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="<?php echo $fetch_product['name']; ?>" class="image">
        <div class="name"><?php echo $fetch_product['name']; ?></div>
        <div class="price">$<?php echo number_format($fetch_product['price'], 2); ?></div>
        <input type="number" class="qty" name="product_quantity" min="1" value="1">
        <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
        <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
        <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
        <input type="submit" class="btn text-light" value="Add to Cart" name="add_to_cart">
    </form>
    <?php
                }
            }else{
                echo '<p class="empty">No products found matching your search</p>';
            }
        }else{
            echo '<p class="empty">Start your search by typing product name</p>';
        }
    ?>
    </div>
</section>





<?php include 'footer.php'; ?>

<?php
$messages = [
    'message' => 'danger',
    'insert_msg' => 'success',
    'update_msg' => 'success', 
    'delete_msg' => 'success',
    'message_suc' => 'success'
];

echo '<div class="message-container">';
foreach ($messages as $param => $type) {
    if (isset($_GET[$param])) {
        $message = htmlspecialchars($_GET[$param]);
        echo '<div class="alert alert-'.$type.' alert-slide mb-2" role="alert">'.$message.'</div>';
        
        echo '<script>
            if(window.history.replaceState) {
                window.history.replaceState(null, null, window.location.pathname);
            }
        </script>';
    }
}
echo '</div>';
?>   

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('.alert-slide').each(function(index) {
            let $alert = $(this);
            
            setTimeout(() => {
                $alert.addClass('alert-hide');
                setTimeout(() => $alert.remove(), 500);
            }, 3000)
        });
    });
</script>

</body>
</html>