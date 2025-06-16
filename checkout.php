<?php
include 'connection.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int)$_SESSION['id']; // Cast to integer for security

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Main order processing
if (isset($_POST['order_btn'])) {
    // Validate and sanitize inputs
    $name = sanitize_input($_POST['name']);
    $number = sanitize_input($_POST['number']);
    $email = sanitize_input($_POST['email']);
    $method = sanitize_input($_POST['method']);
    $street = sanitize_input($_POST['street']);
    $flat = sanitize_input($_POST['flat']);
    $city = sanitize_input($_POST['city']);
    $state = sanitize_input($_POST['state']);
    $country = sanitize_input($_POST['country']);
    $pin_code = sanitize_input($_POST['pin_code']);
    
    // Validate required fields
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($number) || !is_numeric($number)) $errors[] = "Valid phone number is required";
    if (empty($email) || !validate_email($email)) $errors[] = "Valid email is required";
    if (empty($method)) $errors[] = "Payment method is required";
    if (empty($street)) $errors[] = "Street address is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($country)) $errors[] = "Country is required";
    if (empty($pin_code) || !is_numeric($pin_code)) $errors[] = "Valid PIN code is required";
    
    if (!empty($errors)) {
        $error_msg = implode(', ', $errors);
        header('Location: checkout.php?message=' . urlencode($error_msg));
        exit();
    }
    
    // Create full address
    $address = "Flat no. $flat, $street, $city, $state, $country - $pin_code";
    $placed_on = date('Y-m-d H:i:s'); // Use proper datetime format
    
    $cart_total = 0;
    $cart_products = [];
    
    // Use prepared statement for cart query
    $cart_stmt = $connection->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        while ($cart_item = $cart_result->fetch_assoc()) {
            $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ')';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }
    $cart_stmt->close();
    
    $total_products = implode(', ', $cart_products);
    
    if ($cart_total == 0) {
        header('Location: checkout.php?message=' . urlencode('Your cart is empty'));
        exit();
    }
    
    // Check if orders table has status column
    $check_column = $connection->query("SHOW COLUMNS FROM `orders` LIKE 'status'");
    $has_status_column = $check_column->num_rows > 0;
    
    if ($has_status_column) {
        // Check for existing pending order
        $order_stmt = $connection->prepare("SELECT id FROM `orders` WHERE user_id = ? AND name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ? AND status = 'pending'");
        $order_stmt->bind_param("issssssd", $user_id, $name, $number, $email, $method, $address, $total_products, $cart_total);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        
        if ($order_result->num_rows > 0) {
            // Use existing pending order
            $order = $order_result->fetch_assoc();
            $_SESSION['pending_order_id'] = $order['id'];
            $order_stmt->close();
            header('Location: paygateway.php');
            exit();
        }
        $order_stmt->close();
        
        // Create new pending order
        $insert_stmt = $connection->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $insert_stmt->bind_param("issssssds", $user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on);
        
    } else {
        // Fallback for tables without status column
        $recent_time = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $order_stmt = $connection->prepare("SELECT id FROM `orders` WHERE user_id = ? AND name = ? AND number = ? AND email = ? AND method = ? AND address = ? AND total_products = ? AND total_price = ? AND placed_on >= ?");
        $order_stmt->bind_param("issssssdss", $user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $recent_time);
        $order_stmt->execute();
        $order_result = $order_stmt->get_result();
        
        if ($order_result->num_rows > 0) {
            // Use existing recent order
            $order = $order_result->fetch_assoc();
            $_SESSION['pending_order_id'] = $order['id'];
            $order_stmt->close();
            header('Location: paygateway.php');
            exit();
        }
        $order_stmt->close();
        
        // Create new order
        $insert_stmt = $connection->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("issssssds", $user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on);
    }
    
    if ($insert_stmt->execute()) {
        $_SESSION['pending_order_id'] = $connection->insert_id;
        $insert_stmt->close();
        header('Location: paygateway.php');
        exit();
    } else {
        $insert_stmt->close();
        header('Location: checkout.php?message=' . urlencode('Failed to create order. Please try again.'));
        exit();
    }
}

// Handle order cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $cancel_id = (int)$_GET['cancel'];
    
    // Check if order belongs to current user
    $check_stmt = $connection->prepare("SELECT id FROM `orders` WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $cancel_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        
        // Delete the order
        $delete_stmt = $connection->prepare("DELETE FROM `orders` WHERE id = ?");
        $delete_stmt->bind_param("i", $cancel_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        unset($_SESSION['pending_order_id']);
        header('Location: checkout.php?message_warn=' . urlencode('Order has been cancelled'));
        exit();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Secure Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap');

        :root {
            --purple: #8e44ad;
            --purple-light: #a55ebd;
            --orange: #f39c12;
            --black: #333;
            --dark-gray: #444;
            --white: #fff;
            --light-color: #666;
            --light-bg: #f5f5f5;
            --border: .1rem solid var(--black);
            --box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
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
            line-height: 1.6;
        }

        .heading {
            display: flex;
            flex-flow: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            background: var(--gradient);
            padding: 4rem 2rem;
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
            color: var(--white);
        }

        .heading p a {
            color: var(--white);
            font-weight: 500;
            opacity: 0.9;
        }

        .heading p a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .checkout {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .display-order {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1150px;
        }

        .display-order h3 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--purple);
            font-size: 2rem;
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
            border: 2px solid var(--purple-light);
        }

        .grand-total span {
            color: var(--purple);
            font-size: 1.8rem;
            font-weight: 600;
        }

        .checkout form {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            padding: 3rem;
            margin-top: 2rem;
        }

        .checkout form h3 {
            color: var(--purple);
            border-bottom: 2px solid var(--purple-light);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            font-size: 2.2rem;
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
            outline: none;
            border-color: var(--purple);
            box-shadow: 0 0 0 3px rgba(142, 68, 173, 0.1);
        }

        .inputBox input:invalid {
            border-color: #e74c3c;
        }

        .inputBox input:valid {
            border-color: #27ae60;
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
            background: var(--gradient);
            color: white;
            padding: 1.2rem 2rem;
            border-radius: 0.8rem;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            margin: 2rem auto 0;
            min-width: 200px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 1rem 2rem rgba(142, 68, 173, 0.3);
        }

        .btn[type="submit"]:active {
            transform: translateY(0);
        }

        .empty {
            text-align: center;
            color: var(--light-color);
            padding: 2rem;
            font-size: 1.2rem;
            font-style: italic;
        }

        .message-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 350px;
        }

        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            box-shadow: var(--box-shadow);
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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

        @media (max-width: 768px) {
            .checkout {
                padding: 1rem;
            }
            
            .flex {
                grid-template-columns: 1fr;
            }
            
            .grand-total {
                text-align: center;
                font-size: 1.5rem;
            }
            
            .heading h3 {
                font-size: 2rem;
            }
            
            .checkout form {
                padding: 2rem 1rem;
            }
            
            .message-container {
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }

        /* Security indicator */
        .security-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #27ae60;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Secure Checkout</h3>
    <p><a href="index.php">Home</a> / Checkout</p>
</div>

<section class="display-order">
    <h3>Your Order Summary</h3>
    <div class="security-badge">
        <i class="fas fa-shield-alt"></i>
        <span>SSL Secured Transaction</span>
    </div>
    
    <?php  
    $grand_total = 0;
    $cart_stmt = $connection->prepare("SELECT * FROM `cart` WHERE user_id = ?");
    $cart_stmt->bind_param("i", $user_id);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    
    if ($cart_result->num_rows > 0) {
        while ($fetch_cart = $cart_result->fetch_assoc()) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
    ?>
    <p> 
        <?php echo htmlspecialchars($fetch_cart['name']); ?>
        <span><?php echo $fetch_cart['quantity'] . ' x $' . number_format($fetch_cart['price'], 2); ?></span>
    </p>
    <?php
        }
        $cart_stmt->close();
    } else {
        echo '<p class="empty">Your cart is empty</p>';
    }
    ?>
    <div class="grand-total">
        Grand Total: <span>$<?php echo number_format($grand_total, 2); ?></span>
    </div>
</section>

<?php if ($grand_total > 0): ?>
<section class="checkout">
    <form action="" method="post" novalidate>
        <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
        <div class="flex">
            <div class="inputBox">
                <span><i class="fas fa-user"></i> Full Name *</span>
                <input type="text" name="name" required placeholder="John Doe" maxlength="100" pattern="[A-Za-z\s]+" title="Please enter a valid name">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-phone"></i> Phone Number *</span>
                <input type="tel" name="number" required placeholder="+1234567890" pattern="[0-9+\-\s()]+" title="Please enter a valid phone number">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-envelope"></i> Email Address *</span>
                <input type="email" name="email" required placeholder="john@example.com" maxlength="100">
            </div>
            
            <div class="inputBox payment-method">
                <span><i class="fas fa-wallet"></i> Payment Method *</span>
                <select name="method" required>
                    <option value="">Select Payment Method</option>
                    <option value="razorpay">Razorpay</option>
                </select>
            </div>

            <div class="inputBox">
                <span><i class="fas fa-home"></i> Street Address *</span>
                <input type="text" name="street" required placeholder="123 Main Street" maxlength="200">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-building"></i> Apartment/Suite</span>
                <input type="text" name="flat" placeholder="Apt 4B" maxlength="50">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-city"></i> City *</span>
                <input type="text" name="city" required placeholder="New York" maxlength="100">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-flag"></i> State/Province *</span>
                <input type="text" name="state" required placeholder="New York" maxlength="100">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-globe"></i> Country *</span>
                <input type="text" name="country" required placeholder="United States" maxlength="100">
            </div>
            <div class="inputBox">
                <span><i class="fas fa-mail-bulk"></i> ZIP/Postal Code *</span>
                <input type="text" name="pin_code" required placeholder="10001" pattern="[0-9A-Za-z\s\-]+" maxlength="20">
            </div>
        </div>

        <button type="submit" name="order_btn" class="btn">
            <i class="fas fa-lock"></i>
            <span>Place Secure Order</span>
        </button>
    </form>
</section>
<?php endif; ?>

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
    }
}
echo '</div>';
?>   

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert-slide');
    alerts.forEach((alert, index) => {
        setTimeout(() => {
            alert.classList.add('alert-hide');
            setTimeout(() => alert.remove(), 500);
        }, 3000 + (index * 500));
    });
    
    // Clean URL after showing messages
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.pathname);
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    field.style.borderColor = '#27ae60';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});
</script>

</body>
</html>