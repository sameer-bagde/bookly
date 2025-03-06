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

.title {
    text-align: center;
    padding-top: 50px;
    font-size: 2.5rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 30px;
    text-transform: uppercase;
    background-color: #f8f9fa;

}

.orders {
    background-color: #f8f9fa;
    padding-bottom: 40px;
}

.box-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    justify-content: center;
    max-width: 1100px;
    margin: auto;
    padding: 0 20px;
}

.box {
    background: #ffffff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    width: 100%;
    max-width: 350px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    margin: auto;
}

.box:hover {
    transform: scale(1.03);
}

.box p {
    font-size: 1rem;
    margin-bottom: 10px;
    color: #555;
}

.box p span {
    font-weight: bold;
    color: #6f42c1;
}

.box form {
    text-align: center;
}

.box select {
    border-radius: 5px;
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    background: #f8f9fa;
    font-size: 1rem;
    color: #333;
    margin-bottom: 10px;
}

.button-group {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.option-btn,
.delete-btn {
    flex: 1;
    padding: 10px;
    font-size: 1rem;
    border-radius: 5px;
    text-align: center;
    text-decoration: none;
    transition: background 0.3s;
}

.option-btn {
    background: #28a745;
    color: white;
    border: none;
}

.option-btn:hover {
    background: #218838;
}

.delete-btn {
    background: #dc3545;
    color: white;
}

.delete-btn:hover {
    background: #c82333;
}

.empty {
    text-align: center;
    font-size: 1.2rem;
    color: #777;
}

@media (max-width: 1024px) {
    .box-container {
        grid-template-columns: repeat(2, 1fr);
        max-width: 750px;
    }
}

@media (max-width: 768px) {
    .box-container {
        grid-template-columns: repeat(1, 1fr);
        max-width: 400px;
    }
}


   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div id="notification" class="notification"></div>


<div class="heading">
   <h3>our shop</h3>
   <p> <a href="index.php">Home</a> / order </p>
</div>

<section class="orders">

   <h1 class="title">PLACED ORDERS</h1>

   <?php
$stmt = $connection->prepare("SELECT * FROM `orders` WHERE `user_id` = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$select_orders = $stmt->get_result();
?>

<div class="box-container">
    <?php
    if ($select_orders->num_rows > 0) {
        while ($fetch_orders = $select_orders->fetch_assoc()) {
    ?>
            <div class="box">
                <p> user id : <span><?php echo $fetch_orders['user_id']; ?></span> </p>
                <p> placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                <p> name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                <p> number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                <p> email : <span><?php echo $fetch_orders['email']; ?></span> </p>
                <p> address : <span><?php echo $fetch_orders['address']; ?></span> </p>
                <p> total products : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                <p> total price : <span>$<?php echo $fetch_orders['total_price']; ?>/-</span> </p>
                <p> payment method : <span><?php echo $fetch_orders['method']; ?></span> </p>
                <p> payment status : <span><?php echo $fetch_orders['payment_status']; ?></span> </p>
            </div>
    <?php
        }
    } else {
        echo '<p class="empty">No orders placed yet!</p>';
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