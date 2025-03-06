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
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us</title>
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

    .add-to-cart-btn {
        background-color: #8e44ad !important; 
        color: white !important; 
        width: 50%; 
        transition: transform 0.3s ease-in-out;
    }
    
    .add-to-cart-btn:hover {
        transform: scale(1.1);
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

<div id="notification" class="notification"></div>


<div class="heading">
   <h3>our shop</h3>
   <p> <a href="index.php">Home</a> / shop </p>
</div>

<section class="container py-5 w-75">
    <h1 class="text-center mb-5 fs-10">LATEST PRODUCTS</h1>
    <div class="row row-cols-lg-3 row-cols-md-2 row-cols-1 g-4 ">
        <?php
        $upload_dir = 'uploaded_img';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $select_products = mysqli_query($connection, "SELECT * FROM products ") or die('Query failed: ' . mysqli_error($connection));
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
</button>                    
</form>
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

<?php 
ob_end_flush();  
?>