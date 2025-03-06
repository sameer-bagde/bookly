<?php 
ob_start(); 
session_start(); 
if(isset($_SESSION['email'])){
    header('location: index.php');
    exit();
}

include('header.php');
include('connection.php');

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connection, trim($_POST['email']));
    $password = mysqli_real_escape_string($connection, trim($_POST['password']));
    
    $query = "SELECT * FROM `user` WHERE email = '$email'";
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        die('Query Failed: ' . mysqli_error($connection));
    }
    
    $row = mysqli_fetch_assoc($result);
    
    if ($row && $row['password'] === $password) {
        $_SESSION['email'] = $row['email'];
        $_SESSION['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['user_type'] = $row['user_type'];
        $_SESSION['id'] = $row['id'];

        header("Location: index.php?insert_msg=" . urlencode("Login successful"));
        exit();
    } else {
        header("Location: login.php?message=Invalid email or password");
        exit();
    }
}
?>
`
<script>
    setTimeout(function() {
        var messageBox = document.getElementById("messageBox");
        if (messageBox) {
            messageBox.style.display = "none";
        }
    }, 3000);
</script>

<div class="container d-flex justify-content-center mt-5 ">
    <div class="card p-4 " style="width: 400px;">
        <h4 class="text-center mb-4">User Login</h4>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-100 mt-3" name="login">Login Now</button>
            </div>
        </form>
        <p class="text-center mt-2">Don't have an account? <a href="registration.php">Register now</a></p>
    </div>
</div>

<?php if (isset($_GET['message'])) {
  

    echo '
      <div class=" container d-flex justify-content-center  mt-5 ">
    <div class="alert alert-danger" id="messageBox">' . htmlspecialchars($_GET['message']) . '</div>
        </div>';
    exit();

} ?>

<?php ob_end_flush(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>