<?php

include 'connection.php';

session_start();

if (!isset($_SESSION['email'])) {     
    header("Location: login.php");     
    exit(); 
} 

if(isset($_POST['update_order'])){

   $order_update_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   mysqli_query($connection, "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_update_id'") or die('query failed');
   $message[] = 'payment status has been updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($connection, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <style>


.title {
    text-align: center;
    padding-top: 50px;
    font-size: 2.5rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 30px;
    text-transform: uppercase;

}

.orders {
    margin-bottom: 40px;

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


<section class="orders">

   <h1 class="title">PLACED ORDERS</h1>

   <div class="box-container">
      <?php
      $select_orders = mysqli_query($connection, "SELECT * FROM `orders`") or die('query failed');
      if(mysqli_num_rows($select_orders) > 0){
         while($fetch_orders = mysqli_fetch_assoc($select_orders)){
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
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
            <select name="update_payment">
               <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
               <option value="pending">pending</option>
               <option value="completed">completed</option>
            </select>
            <input type="submit" value="update" name="update_order" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('delete this order?');" class="delete-btn">delete</a>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>
   </div>

</section>










<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>