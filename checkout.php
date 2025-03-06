<?php

include 'connection.php';

session_start();

$user_id = $_SESSION['id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['order_btn'])){

   $name = mysqli_real_escape_string($connection, $_POST['name']);
   $number = $_POST['number'];
   $email = mysqli_real_escape_string($connection, $_POST['email']);
   $method = mysqli_real_escape_string($connection, $_POST['method']);
   $address = mysqli_real_escape_string($connection, 'flat no. '. $_POST['flat'].', '. $_POST['street'].', '. $_POST['city'].', '. $_POST['country'].' - '. $_POST['pin_code']);
   $placed_on = date('d-M-Y');

   $cart_total = 0;
   $cart_products = array();

   $cart_query = mysqli_query($connection, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   if(mysqli_num_rows($cart_query) > 0){
      while($cart_item = mysqli_fetch_assoc($cart_query)){
         $cart_products[] = $cart_item['name'].' ('.$cart_item['quantity'].') ';
         $sub_total = ($cart_item['price'] * $cart_item['quantity']);
         $cart_total += $sub_total;
      }
   }

   $total_products = implode(', ',$cart_products);

   $order_query = mysqli_query($connection, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

   if($cart_total == 0){
    header('location:checkout.php?message='.urlencode('Your cart is empty'));
    exit();
}else{
    if(mysqli_num_rows($order_query) > 0){
        header('location:checkout.php?message_warn='.urlencode('Order already placed!'));
        exit();
    }else{
        mysqli_query($connection, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on) 
        VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on')") 
        or die('Query failed: ' . mysqli_error($connection));
        
        mysqli_query($connection, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        
        header('location:checkout.php?message_suc='.urlencode('Order placed successfully!'));
        exit();
    }
} 
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

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

.checkout {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
}

.display-order {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: var(--box-shadow);
    padding: 2rem;
    margin: 2rem auto;
    max-width: 1150px;
}

.display-order p {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    margin: 1rem 0;
    background: var(--light-bg);
    border-radius: 0.5rem;
    font-size: 1rem;
}

.display-order p span {
    color: var(--purple);
    font-weight: 500;
}

.grand-total {
    font-size: 2rem;
    color: var(--black);
    text-align: right;
    padding: 1.5rem;
    margin-top: 2rem;
    background: var(--light-bg);
    border-radius: 0.8rem;
}

.grand-total span {
    color: var(--purple);
    font-size: 1.5rem;
    font-weight: 600;
}

.checkout form {
    background: var(--white);
    border-radius: 1rem;
    box-shadow: var(--box-shadow);
    padding: 3rem;
}

.checkout form h3 {
    color: var(--purple);
    border-bottom: 2px solid var(--purple-light);
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.flex {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.inputBox {
    margin-bottom: 1.5rem;
}

.inputBox span {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--black);
    font-weight: 500;
    font-size: 1rem;
}

.inputBox input,
.inputBox select {
    width: 100%;
    padding: 1.2rem;
    border: 2px solid var(--purple-light);
    border-radius: 0.5rem;
    font-size: 1rem;
    background: var(--white);
    transition: all 0.3s ease;
}

.inputBox input:focus,
.inputBox select:focus {
    border-color: var(--purple);
    box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
}


input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
}

.btn[type="submit"] {
    background: var(--purple);
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 0.8rem;
    font-size: 1.2rem;
    width: auto; 
    max-width: 300px; 
    margin: 2rem auto 0;
    transition: transform 0.2s ease;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn[type="submit"]:hover {
    transform: scale(1.05);
    background: var(--purple); 
    color: white; 
}

.btn[type="submit"] i {
    color: inherit; /* Inherit parent text color */
}

.empty {
    text-align: center;
    color: var(--light-color);
    padding: 2rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .checkout {
        padding: 1rem;
    }
    
    .flex {
        grid-template-columns: 1fr;
    }
    
    .grand-total {
        text-align: center;
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
   <h3>checkout</h3>
   <p> <a href="index.php">Home</a> / checkout </p>
</div>

<section class="display-order">
    <h3 class="text-center mb-4" style="color: var(--purple);">Your Order Summary</h3>
    <?php  
    $grand_total = 0;
    $select_cart = mysqli_query($connection, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if(mysqli_num_rows($select_cart) > 0){
        while($fetch_cart = mysqli_fetch_assoc($select_cart)){
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
    ?>
    <p> 
        <?php echo $fetch_cart['name']; ?> 
        <span><?php echo $fetch_cart['quantity'] . ' x $' . number_format($fetch_cart['price'], 2); ?></span>
    </p>
    <?php
        }
    } else {
        echo '<p class="empty">Your cart is empty</p>';
    }
    ?>
    <div class="grand-total">
        Grand Total: <span>$<?php echo number_format($grand_total, 2); ?></span>
    </div>
</section>

<section class="checkout">
    <form action="" method="post">
        <h3>Shipping Information</h3>
        <div class="flex">
            <div class="inputBox">
                <span><i class="fas fa-user"></i> Full Name</span>
                <input type="text" name="name" required placeholder="John Doe">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-phone"></i> Phone Number</span>
                <input type="number" name="number" required placeholder="+1234567890">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-envelope"></i> Email Address</span>
                <input type="email" name="email" required placeholder="john@example.com">
            </div>
            
            <div class="inputBox payment-method">
                <span><i class="fas fa-wallet"></i> Payment Method</span>
                <select name="method">
                    <option value="cash on delivery">Cash on Delivery</option>
                    <option value="credit card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="paytm">Paytm</option>
                </select>
            </div>

            <div class="inputBox">
                <span><i class="fas fa-home"></i> Street Address</span>
                <input type="text" name="street" required placeholder="123 Main Street">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-building"></i> Apartment/Suite</span>
                <input type="number" min="0" name="flat" required placeholder="Apt 4B">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-city"></i> City</span>
                <input type="text" name="city" required placeholder="New York">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-flag"></i> State</span>
                <input type="text" name="state" required placeholder="New York">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-globe"></i> Country</span>
                <input type="text" name="country" required placeholder="United States">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-mail-bulk"></i> ZIP Code</span>
                <input type="number" min="0" name="pin_code" required placeholder="10001">
            </div>
        </div>

        <button type="submit" name="order_btn" class="btn">
            <i class="fas fa-shopping-bag"></i> Place Order
        </button>
    </form>
</section>




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