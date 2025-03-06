<?php

include 'connection.php';

session_start();

if (!isset($_SESSION['email'])) {     
    header("Location: login.php");     
    exit(); 
} 



if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($connection, "DELETE FROM `message` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_message.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <style>

      .messages {
         max-width: 100%;
         margin: 20px auto;
         background: #fff;
         padding: 20px;
      }
      .title {
    text-align: center;
    padding-top: 25px;
    font-size: 2.5rem;
    font-weight: bold;
    color: #343a40;
    margin-bottom: 30px;
    text-transform: uppercase;

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
         background: #f9f9f9;
         padding: 15px;
         border-radius: 5px;
         box-shadow: 0 2px 5px rgba(0,0,0,0.1);
         transition: 0.3s;
      }
      .box:hover {
         transform: scale(1.02);
      }
      .box p {
         margin: 5px 0;
         font-size: 14px;
         color: #555;
      }
      .box p span {
         font-weight: bold;
         color: #333;
      }
      .delete-btn {
         display: inline-block;
         padding: 8px 12px;
         background: red;
         color: white;
         text-decoration: none;
         border-radius: 3px;
         margin-top: 10px;
         transition: 0.3s;
         text-align: center;
      }
      .delete-btn:hover {
         background: darkred;
      }
      .empty {
         text-align: center;
         color: #999;
         font-size: 16px;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<section class="messages">
   <h1 class="title"> messages </h1>
   <div class="box-container">
   <?php
      $select_message = mysqli_query($connection, "SELECT * FROM `message`") or die('query failed');
      if(mysqli_num_rows($select_message) > 0){
         while($fetch_message = mysqli_fetch_assoc($select_message)){
   ?>
   <div class="box">
      <p> user id : <span><?php echo $fetch_message['user_id']; ?></span> </p>
      <p> name : <span><?php echo $fetch_message['name']; ?></span> </p>
      <p> number : <span><?php echo $fetch_message['number']; ?></span> </p>
      <p> email : <span><?php echo $fetch_message['email']; ?></span> </p>
      <p> message : <span><?php echo $fetch_message['message']; ?></span> </p>
      <a href="admin_message.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">Delete</a>
   </div>
   <?php
      };
   }else{
      echo '<p class="empty">you have no messages!</p>';
   }
   ?>
   </div>
</section>

<script src="js/admin_script.js"></script>

</body>
</html>