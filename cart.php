<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($connection, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   header("location:cart.php?message=" . urlencode("cart quantity updated!"));
   exit();

}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($connection, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
   header('location:cart.php?message=' . urlencode("cart deleted!"));
   exit();

}

if(isset($_GET['delete_all'])){
   mysqli_query($connection, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:cart.php?message=' . urlencode("all cart deleted!"));
   exit();

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
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

.cart-container {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: var(--box-shadow);
    padding: 2rem;
    margin: 2rem auto;
    max-width: 1200px;
}

.cart-item {
    padding: 1.5rem;
    margin: 1rem 0;
    background: var(--light-bg);
    border-radius: 0.8rem;
    transition: all 0.3s ease;
}

.cart-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.cart-item img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.cart-item h5 {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--black);
    margin-bottom: 0.5rem;
}

.cart-item .price {
    font-size: 1.3rem;
    color: var(--purple);
    font-weight: 500;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.quantity-control input[type="number"] {
    width: 70px;
    padding: 0.5rem;
    border: 2px solid var(--purple-light);
    border-radius: 0.5rem;
    text-align: center;
    font-weight: 500;
}

.btn-update {
    background: var(--purple);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.btn-update:hover {
    background: var(--purple-light);
    transform: translateY(-2px);
}

.cart-total {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--black);
    text-align: right;
    margin-top: 2rem;
    padding: 1.5rem;
    background: var(--light-bg);
    border-radius: 0.8rem;
}

.cart-total span {
    color: var(--purple);
    font-size: 2rem;
}

.btn-container {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.btn-secondary {
    background: var(--purple-light);
    color: white;
}

.btn-danger {
    background: #e74c3c;
}

.btn-danger:hover {
    background: #c0392b;
}

.disabled {
    opacity: 0.6;
    pointer-events: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cart-item {
        text-align: center;
    }
    
    .quantity-control {
        justify-content: center;
    }
    
    .btn-container {
        justify-content: center;
    }
    
    .cart-item img {
        height: 150px;
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
      <h3>Shopping Cart</h3>
      <p> <a href="index.php">Home</a> / Cart </p>
   </div>

   <div class="container mt-5">
      <div class="cart-container p-4">
         <h2 class="text-center mb-4">Products in Cart</h2>

         <?php
            $grand_total = 0;
            $select_cart = mysqli_query($connection, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            if(mysqli_num_rows($select_cart) > 0) {
               while($fetch_cart = mysqli_fetch_assoc($select_cart)) {
         ?>

         <div class="row cart-item align-items-center mb-3 border-bottom pb-3">
            <div class="col-md-2">
               <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
            </div>
            <div class="col-md-3">
               <h5><?php echo $fetch_cart['name']; ?></h5>
               <p class="text-danger">$<?php echo $fetch_cart['price']; ?>/-</p>
            </div>
            <div class="col-md-3">
               <form action="" method="post" class="d-flex align-items-center">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                  <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>" class="form-control w-50 text-center me-2">
                  <input type="submit" name="update_cart" value="Update" class="btn btn-primary btn-sm">
               </form>
            </div>
            <div class="col-md-2">
               <p class="text-muted">Subtotal: <span class="text-danger">$<?php echo $sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']); ?>/-</span></p>
            </div>
            <div class="col-md-2 text-end">
               <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this from cart?');"><i class="fas fa-trash"></i></a>
            </div>
         </div>

         <?php
            $grand_total += $sub_total;
               }
            } else {
               echo '<p class="text-center text-muted">Your cart is empty.</p>';
            }
         ?>

         <div class="cart-total">Grand Total: <span class="text-danger">$<?php echo $grand_total; ?>/-</span></div>

         <div class="btn-container mt-4">
            <a href="shop.php" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-success <?php echo ($grand_total > 1)?'':'disabled'; ?>">Proceed to Checkout</a>
            <a href="cart.php?delete_all" class="btn btn-danger <?php echo ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('Delete all from cart?');">Delete All</a>
         </div>
      </div>
   </div>





<?php include 'footer.php'; ?>

<?php
$messages = [
    'message' => 'danger',     
    'message_warn' => 'warning', 
    'message_suc' => 'success',  
    'insert_msg' => 'success',
    'update_msg' => 'success', 
    'delete_msg' => 'success'
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