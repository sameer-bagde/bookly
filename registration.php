<?php 
ob_start(); 
session_start(); 
if(isset($_SESSION['email']) || ($_SESSION['id'])){
    header('location: index.php');
    exit();
}


include('header.php');
include('connection.php');
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['add_user'])) {
    $f_name = mysqli_real_escape_string($connection, trim($_POST['f_name']));
    $l_name = mysqli_real_escape_string($connection, trim($_POST['l_name']));
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = mysqli_real_escape_string($connection, trim($_POST['password']));
    $user_type = mysqli_real_escape_string($connection, trim($_POST['user_type']));
    
    if(empty($f_name) || empty($l_name) || empty($email) || empty($password) || empty($user_type)) {
        header('location:registration.php?message=All fields are required!');
        exit();
    }
    
    $query = "INSERT INTO user (first_name, last_name, email, password, user_type) 
    VALUES ('$f_name', '$l_name', '$email', '$password', '$user_type')";
    
    $result = mysqli_query($connection, $query);
    
    if(!$result) {
        header("location:registration.php?message=Failed to register: " . mysqli_error($connection));
        exit();
    } else {
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $f_name;
            $_SESSION['last_name'] = $l_name;
            $_SESSION['user_type'] = $user_type;
    
            // Send welcome email
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'bagdesameer92@gmail.com';                     //SMTP username
                $mail->Password   = 'vxfc qvew spxm girh';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465; 
                $mail->setFrom('bagdesameer92@gmail.com', 'Bookly.');
                $mail->addAddress($email, $f_name . ' ' . $l_name);
                $mail->Subject = 'Welcome to Our Website!';
                $mail->isHTML(true);
                $mail->Body    = "<h3>Dear $f_name $l_name,</h3>
                                  <p>Thank you for registering on our website. Your account has been successfully created.</p>
                                  <p><strong>Email:</strong> $email</p>
                                  <p>We hope you enjoy our services!</p>
                                  <p>Best Regards,<br>Bookly Team</p>";
    
                // Send email
                $mail->send();
            } catch (Exception $e) {
                error_log("Email could not be sent: " . $mail->ErrorInfo);
            }
    
            header("Location: login.php?insert_msg=" . urlencode("Registration successful. A confirmation email has been sent."));
            exit();
        }
    }
    ?>
    

<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 " style="width: 400px;">
        <h4 class="text-center mb-4">Register User</h4>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="f_name">First Name</label>
                <input type="text" name="f_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="l_name">Last Name</label>
                <input type="text" name="l_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="user_type">User Type</label>
                <select name="user_type" class="form-control" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100 mt-3" name="add_user">Register Now</button>
            </div>
        </form>
        <p class="text-center mt-2">Already have an account? <a href="login.php">Login now</a></p>
    </div>
</div>

<?php if (isset($_GET['message'])) {
    echo '<div class="alert alert-danger" id="messageBox">' . htmlspecialchars($_GET['message']) . '</div>';
} ?>

<?php ob_end_flush(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>  