<?php
include 'connection.php';
session_start();

$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if(!isset($user_id)){
   header('location:login.php');
   exit();
}

if(isset($_POST['send'])){
   $name = mysqli_real_escape_string($connection, $_POST['name']);
   $email = mysqli_real_escape_string($connection, $_POST['email']);
   $number = $_POST['number'];
   $msg = mysqli_real_escape_string($connection, $_POST['message']);

   $select_message = mysqli_query($connection, "SELECT * FROM `message` WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');

   if(mysqli_num_rows($select_message) > 0){
       header("location: contact.php?message_warn=" . urlencode("Message sent already!"));
       exit();
   }else{
      $insert = mysqli_query($connection, "INSERT INTO `message`(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')");
      if($insert){
          header("location: contact.php?message_suc=" . urlencode("Message sent successfully!"));
          exit();

      }else{
          header("location: contact.php?message=" . urlencode("Message failed to send!"));
          exit();

      }
      exit();
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

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
    .contact form{
   background-color: var(--light-bg);
   border-radius: .5rem;
   border:var(--border);
   padding:2rem;
   max-width: 50rem;
   margin:0 50px 50px;
   text-align: center;
}

.contact form h3{
   font-size: 2.5rem;
   text-transform: uppercase;
   margin-bottom: 1rem;
   color:var(--black);
}

.contact form .box{
   margin:1rem 0;
   width: 100%;
   border:var(--border);
   background-color: var(--white);
   padding:1.2rem 1.4rem;
   font-size: 1.8rem;
   color:var(--black);
   border-radius: .5rem;
}

.contact form textarea{
   height: 20rem;
   resize: none;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
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
   <h3>contact us</h3>
   <p> <a href="index.php">Home</a> / contact </p>
</div>

<section class="contact">

<div class="container mt-5">
      <div class="row justify-content-center">
         <div class="col-md-6">
            <div class="contact-form">
               <h4 class="text-center">Say Something!</h4>
               <form action="" method="post">
                  <div class="mb-3">
                     <input type="text" name="name" required placeholder="Enter your name" class="form-control">
                  </div>
                  <div class="mb-3">
                     <input type="email" name="email" required placeholder="Enter your email" class="form-control">
                  </div>
                  <div class="mb-3">
                     <input type="number" name="number" required placeholder="Enter your number" class="form-control">
                  </div>
                  <div class="mb-3">
                     <textarea name="message" required placeholder="Enter your message" class="form-control" rows="5"></textarea>
                  </div>
                  <button type="submit" name="send" class="btn btn-warning w-50 text-light" >Send Message</button>
               </form>
            </div>
         </div>
      </div>
   </div>

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