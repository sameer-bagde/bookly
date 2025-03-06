<?php
include 'connection.php';
session_start();
if (!isset($_SESSION['email'])) {     
    header("Location: login.php");     
    exit(); 
} 
$logged_in_email = $_SESSION['email'];

if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($connection, $_GET['delete']);
    
    $check_user = mysqli_query($connection, "SELECT email FROM `user` WHERE id = '$delete_id'") or die('Query failed');
    $fetch_user = mysqli_fetch_assoc($check_user);
    
    if ($fetch_user['email'] != $logged_in_email) {
        mysqli_query($connection, "DELETE FROM `user` WHERE id = '$delete_id'") or die('Query failed');
        header('location:admin_users.php');
        exit();
    } else {
        echo "<script>alert('You cannot delete yourself!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Users</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
.title {
    margin-top: 40px;
    text-align: center;
    font-size: 2rem;
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
      
      .delete-btn {
          display: block;
          width: 100%;
          text-align: center;
          padding: 10px;
          font-size: 1rem;
          border-radius: 5px;
          background: #dc3545;
          color: white;
          text-decoration: none;
          transition: background 0.3s;
      }
      
      .delete-btn:hover {
          background: #c82333;
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

<section class="users">
   <h1 class="title">total accounts</h1>
   <div class="box-container">
      <?php
         $select_users = mysqli_query($connection, "SELECT * FROM `user` ") or die('query failed');
         while($fetch_users = mysqli_fetch_assoc($select_users)){
      ?>
      <div class="box">
         <p> User ID : <span><?php echo $fetch_users['id']; ?></span> </p>
         <p> Username : <span><?php echo $fetch_users['first_name'] . ' ' . $fetch_users['last_name']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_users['email']; ?></span> </p>
         <p> User Type : <span style="color: var(--orange);"><?php echo $fetch_users['user_type']; ?></span> </p>
         <?php if ($fetch_users['email'] != $logged_in_email) { ?>
            <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('Delete this admin user?');" class="delete-btn">Delete User</a>
         <?php } else { ?>
            <p style="color: green; font-weight: bold; text-align: center;">This is you</p>
         <?php } ?>      </div>
      <?php
         };
      ?>
   </div>
</section>


</body>
</html>