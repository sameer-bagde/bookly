<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   
   <style>


body {
   background-color: #f8f9fa;

}
      .dashboard .card {
         box-shadow: 0 4px 10px rgba(0,0,0,0.1);
         border: none;
         transition: 0.3s;
      }
      .dashboard .card:hover {
         transform: scale(1.03);
      }
      .dashboard h1 {
         text-align: center;
         margin-bottom: 1rem;
         text-transform: uppercase;
         color: #333;
         font-size: 1.8rem;
         font-weight: 600;
      }
   </style>
</head>
<body>

<div class="container my-5">
   <section class="dashboard">
   <h1 class="text-center fs-1 mb-4">Dashboard</h1>

      <div class="row g-3">
         <div class="col-md-3 col-sm-6">
            <div class="card text-center p-3">
               <?php
                  $total_pendings = 0;
                  $select_pending = mysqli_query($connection, "SELECT total_price FROM `orders` WHERE payment_status = 'pending'") or die('Query failed');
                  while($fetch_pendings = mysqli_fetch_assoc($select_pending)){
                     $total_pendings += $fetch_pendings['total_price'];
                  }
               ?>
               <h3>$<?php echo $total_pendings; ?>/-</h3>
               <p class="text-muted">Total Pendings</p>
            </div>
         </div>

         <div class="col-md-3 col-sm-6">
            <div class="card text-center p-3">
               <?php
                  $total_completed = 0;
                  $select_completed = mysqli_query($connection, "SELECT total_price FROM `orders` WHERE payment_status = 'completed'") or die('Query failed');
                  while($fetch_completed = mysqli_fetch_assoc($select_completed)){
                     $total_completed += $fetch_completed['total_price'];
                  }
               ?>
               <h3>$<?php echo $total_completed; ?>/-</h3>
               <p class="text-muted">Completed Payments</p>
            </div>
         </div>

         <div class="col-md-3 col-sm-6">
         <a href="admin_orders.php" class="text-decoration-none">

            <div class="card text-center p-3">
               <?php 
                  $select_orders = mysqli_query($connection, "SELECT * FROM `orders`") or die('Query failed');
                  $number_of_orders = mysqli_num_rows($select_orders);
               ?>
               <h3><?php echo $number_of_orders; ?></h3>
               <p class="text-muted">Orders Placed</p>
            </div>
         </a>
         </div>

         <div class="col-md-3 col-sm-6">
   <a href="admin_products.php" class="text-decoration-none">
      <div class="card text-center p-3">
         <?php 
            $select_products = mysqli_query($connection, "SELECT * FROM `products`") or die('Query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <h3><?php echo $number_of_products; ?></h3>
         <p class="text-muted">Products Added</p>
      </div>
   </a>
</div>


         <div class="col-md-3 col-sm-6">
         <a href="normal_users.php" class="text-decoration-none">

            <div class="card text-center p-3">
               <?php 
                  $select_users = mysqli_query($connection, "SELECT * FROM `user` WHERE user_type = 'user'") or die('Query failed');
                  $number_of_users = mysqli_num_rows($select_users);
               ?>
               <h3><?php echo $number_of_users; ?></h3>
               <p class="text-muted">Normal Users</p>
            </div>
               </a>
         </div>

         <div class="col-md-3 col-sm-6">
         <a href="admin_users.php" class="text-decoration-none">

            <div class="card text-center p-3">
               <?php 
                  $select_admins = mysqli_query($connection, "SELECT * FROM `user` WHERE user_type = 'admin'") or die('Query failed');
                  $number_of_admins = mysqli_num_rows($select_admins);
               ?>
               <h3><?php echo $number_of_admins; ?></h3>
               <p class="text-muted">Admin Users</p>
            </div>
            </a>
         </div>

         <div class="col-md-3 col-sm-6">
         <a href="total_users.php" class="text-decoration-none">

            <div class="card text-center p-3">
               <?php 
                  $select_account = mysqli_query($connection, "SELECT * FROM `user`") or die('Query failed');
                  $number_of_account = mysqli_num_rows($select_account);
               ?>
               <h3><?php echo $number_of_account; ?></h3>
               <p class="text-muted">Total Accounts</p>
            </div>
               </a>
         </div>

         <div class="col-md-3 col-sm-6">
         <a href="admin_message.php" class="text-decoration-none">
            <div class="card text-center p-3">
               <?php 
                  $select_messages = mysqli_query($connection, "SELECT * FROM `message`") or die('Query failed');
                  $number_of_messages = mysqli_num_rows($select_messages);
               ?>
               <h3><?php echo $number_of_messages; ?></h3>
               <p class="text-muted">New Messages</p>
            </div>
               </a>
         </div>
      </div>

   </section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
