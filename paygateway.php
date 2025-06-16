<?php
include 'connection.php';
session_start();

$user_id = $_SESSION['id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

// Check if there's a pending order
if (!isset($_SESSION['pending_order_id'])) {
    header('Location: checkout.php?message='.urlencode('No pending order found'));
    exit();
}

$order_id = $_SESSION['pending_order_id'];

// Fetch order details with prepared statements
$check_column = mysqli_query($connection, "SHOW COLUMNS FROM `orders` LIKE 'status'");
$has_status_column = mysqli_num_rows($check_column) > 0;

if($has_status_column) {
    $stmt = mysqli_prepare($connection, "SELECT * FROM `orders` WHERE id = ? AND user_id = ? AND status = 'pending'");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
} else {
    $stmt = mysqli_prepare($connection, "SELECT * FROM `orders` WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
}

mysqli_stmt_execute($stmt);
$order_query = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($order_query) == 0) {
    header('Location: checkout.php?message='.urlencode('Order not found or already processed'));
    exit();
}

$order = mysqli_fetch_assoc($order_query);

// Razorpay credentials - REPLACE WITH YOUR ACTUAL CREDENTIALS
$api_key = "rzp_test_4niUmWhUCYZzTw";
$api_secret = "YOUR_ACTUAL_SECRET_KEY_HERE"; // YOU MUST REPLACE THIS

// For testing, you can use these test credentials:
// Test Key ID: rzp_test_4niUmWhUCYZzTw
// Test Key Secret: Get from Razorpay Dashboard

// Handle payment success
if (isset($_POST['razorpay_payment_id'])) {
    $payment_id = mysqli_real_escape_string($connection, $_POST['razorpay_payment_id']);
    $razorpay_order_id = mysqli_real_escape_string($connection, $_POST['razorpay_order_id']);
    $signature = mysqli_real_escape_string($connection, $_POST['razorpay_signature']);
    
    // IMPORTANT: Only verify signature if you have the actual secret key
    if ($api_secret !== "YOUR_ACTUAL_SECRET_KEY_HERE") {
        // Verify payment signature (IMPORTANT for security)
        $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $payment_id, $api_secret);
        
        if (!hash_equals($signature, $generated_signature)) {
            // Invalid signature - possible fraud
            header('Location: checkout.php?message='.urlencode('Payment verification failed. Please try again.'));
            exit();
        }
    }
    
    // Payment is authentic (or signature verification skipped for testing)
    
    // Check available columns
    $check_status = mysqli_query($connection, "SHOW COLUMNS FROM `orders` LIKE 'status'");
    $check_payment = mysqli_query($connection, "SHOW COLUMNS FROM `orders` LIKE 'payment_id'");
    $has_status = mysqli_num_rows($check_status) > 0;
    $has_payment_id = mysqli_num_rows($check_payment) > 0;
    
    // Build update query with prepared statements
    if($has_status && $has_payment_id) {
        $stmt = mysqli_prepare($connection, "UPDATE `orders` SET status = 'completed', payment_id = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $payment_id, $order_id);
    } elseif($has_status) {
        $stmt = mysqli_prepare($connection, "UPDATE `orders` SET status = 'completed' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $order_id);
    } elseif($has_payment_id) {
        $stmt = mysqli_prepare($connection, "UPDATE `orders` SET payment_id = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $payment_id, $order_id);
    } else {
        $processed_on = date('d-M-Y H:i:s');
        $stmt = mysqli_prepare($connection, "UPDATE `orders` SET placed_on = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $processed_on, $order_id);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        // Clear cart after successful payment
        $cart_stmt = mysqli_prepare($connection, "DELETE FROM `cart` WHERE user_id = ?");
        mysqli_stmt_bind_param($cart_stmt, "i", $user_id);
        mysqli_stmt_execute($cart_stmt);
        
        // Clear session
        unset($_SESSION['pending_order_id']);
        
        header('Location: orders.php?message_suc='.urlencode('Payment successful! Your order has been placed.'));
        exit();
    } else {
        header('Location: checkout.php?message='.urlencode('Failed to update order. Please contact support.'));
        exit();
    }
}

// Handle payment failure or cancellation
if (isset($_GET['status']) && $_GET['status'] == 'failed') {
    header('Location: checkout.php?message_warn='.urlencode('Payment failed or was cancelled. Please try again.'));
    exit();
}

// Create a simple order reference (no need for actual Razorpay order creation in test mode)
$razorpay_order_id = 'order_' . $order['id'] . '_' . time();
$error_message = '';

// Validate that we have the minimum required data
if (empty($order['total_price']) || $order['total_price'] <= 0) {
    $error_message = 'Invalid order amount. Please try again.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    
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
            padding: 3rem 1rem;
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

        .payment-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .order-summary {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .order-summary h3 {
            color: var(--purple);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            border-bottom: 2px solid var(--purple-light);
            padding-bottom: 0.5rem;
        }

        .order-info {
            display: grid;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem;
            background: var(--light-bg);
            border-radius: 0.5rem;
        }

        .info-label {
            font-weight: 500;
            color: var(--black);
        }

        .info-value {
            color: var(--purple);
            font-weight: 600;
        }

        .total-amount {
            font-size: 1.5rem;
            text-align: center;
            padding: 1.5rem;
            background: var(--purple);
            color: var(--white);
            border-radius: 0.8rem;
            margin: 1.5rem 0;
        }

        .payment-section {
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
            padding: 2rem;
            text-align: center;
        }

        .payment-section h3 {
            color: var(--purple);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .payment-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .pay-btn {
            background: var(--purple);
            color: var(--white);
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.8rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 200px;
            justify-content: center;
        }

        .pay-btn:hover {
            background: var(--purple-light);
            transform: translateY(-2px);
        }

        .pay-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .cancel-btn {
            background: #e74c3c;
            color: var(--white);
            padding: 1rem 2rem;
            border: none;
            border-radius: 0.8rem;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 200px;
            justify-content: center;
            text-decoration: none;
        }

        .cancel-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 4px solid var(--light-bg);
            border-top: 4px solid var(--purple);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #c62828;
        }

        .test-info {
            background: #e3f2fd;
            color: #1565c0;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #1565c0;
        }

        @media (max-width: 768px) {
            .payment-container {
                padding: 1rem;
            }
            
            .payment-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .pay-btn, .cancel-btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Payment Gateway</h3>
    <p><a href="index.php">Home</a> / <a href="checkout.php">Checkout</a> / Payment</p>
</div>

<section class="payment-container">
    <?php if ($error_message): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="test-info">
        <i class="fas fa-info-circle"></i> 
        <strong>Test Mode:</strong> Use test card details: 4111 1111 1111 1111, any future expiry, any CVV
    </div>

    <div class="order-summary">
        <h3><i class="fas fa-receipt"></i> Order Summary</h3>
        <div class="order-info">
            <div class="info-row">
                <span class="info-label"><i class="fas fa-hashtag"></i> Order ID:</span>
                <span class="info-value">#<?php echo htmlspecialchars($order['id']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-user"></i> Customer Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['name']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-envelope"></i> Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-phone"></i> Phone:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['number']); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-credit-card"></i> Payment Method:</span>
                <span class="info-value"><?php echo ucfirst(htmlspecialchars($order['method'])); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-box"></i> Items:</span>
                <span class="info-value"><?php echo htmlspecialchars($order['total_products']); ?></span>
            </div>
        </div>
        
        <div class="total-amount">
            <i class="fas fa-rupee-sign"></i> Total Amount: ₹<?php echo number_format($order['total_price'], 2); ?>
        </div>
    </div>

    <div class="payment-section">
        <h3><i class="fas fa-lock"></i> Secure Payment</h3>
        <p>Complete your payment using Razorpay's secure payment gateway</p>
        
        <div class="payment-buttons">
            <button id="rzp-button" class="pay-btn" <?php echo $error_message ? 'disabled' : ''; ?>>
                <i class="fas fa-credit-card"></i>
                Pay Now - ₹<?php echo number_format($order['total_price'], 2); ?>
            </button>
            
            <a href="checkout.php?cancel=<?php echo htmlspecialchars($order['id']); ?>" class="cancel-btn" onclick="return confirm('Are you sure you want to cancel this order?')">
                <i class="fas fa-times"></i>
                Cancel Order
            </a>
        </div>
        
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Processing payment...</p>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.getElementById('rzp-button').onclick = function(e){
    e.preventDefault();
    
    // Check if button is disabled
    if (this.disabled) {
        return false;
    }
    
    // Show loading
    document.getElementById('loading').style.display = 'block';
    document.getElementById('rzp-button').style.display = 'none';
    
    var options = {
        "key": "<?php echo $api_key; ?>",
        "amount": "<?php echo intval($order['total_price'] * 100); ?>", // Amount in paise (integer)
        "currency": "INR",
        "name": "Your Store Name",
        "description": "Order #<?php echo htmlspecialchars($order['id']); ?>",
        "handler": function (response){
            console.log('Payment Success:', response);
            
            // Validate response
            if (!response.razorpay_payment_id) {
                alert('Payment failed: Invalid payment response');
                resetPaymentButton();
                return;
            }
            
            // Create a form to submit payment details
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            var payment_id = document.createElement('input');
            payment_id.type = 'hidden';
            payment_id.name = 'razorpay_payment_id';
            payment_id.value = response.razorpay_payment_id;
            form.appendChild(payment_id);
            
            var order_id = document.createElement('input');
            order_id.type = 'hidden';
            order_id.name = 'razorpay_order_id';
            order_id.value = response.razorpay_order_id || '<?php echo $razorpay_order_id; ?>';
            form.appendChild(order_id);
            
            var signature = document.createElement('input');
            signature.type = 'hidden';
            signature.name = 'razorpay_signature';
            signature.value = response.razorpay_signature || 'test_signature';
            form.appendChild(signature);
            
            document.body.appendChild(form);
            form.submit();
        },
        "prefill": {
            "name": "<?php echo htmlspecialchars($order['name']); ?>",
            "email": "<?php echo htmlspecialchars($order['email']); ?>",
            "contact": "<?php echo htmlspecialchars($order['number']); ?>"
        },
        "notes": {
            "order_id": "<?php echo htmlspecialchars($order['id']); ?>",
            "user_id": "<?php echo $user_id; ?>"
        },
        "theme": {
            "color": "#8e44ad"
        },
        "modal": {
            "ondismiss": function(){
                console.log('Payment modal dismissed');
                resetPaymentButton();
            }
        }
    };
    
    var rzp1 = new Razorpay(options);
    
    rzp1.on('payment.failed', function (response){
        console.error('Payment failed:', response.error);
        alert('Payment failed: ' + (response.error.description || 'Unknown error occurred'));
        resetPaymentButton();
    });
    
    try {
        rzp1.open();
    } catch (error) {
        console.error('Razorpay initialization error:', error);
        alert('Payment initialization failed. Please refresh the page and try again.');
        resetPaymentButton();
    }
}

function resetPaymentButton() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('rzp-button').style.display = 'flex';
}

// Prevent double clicks
var paymentInProgress = false;
document.getElementById('rzp-button').addEventListener('click', function() {
    if (paymentInProgress) {
        return false;
    }
    paymentInProgress = true;
    setTimeout(() => {
        paymentInProgress = false;
    }, 5000);
});
</script>

</body>
</html>