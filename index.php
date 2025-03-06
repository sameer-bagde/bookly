<?php  
ob_start(); 
session_start(); 
include('connection.php');  
if (!isset($_SESSION['email'])) {     
    header("Location: login.php");     
    exit(); 
} 

$email = $_SESSION['email'];
$query = "SELECT * FROM user WHERE email = '$email'";
$result = mysqli_query($connection, $query);
$row = mysqli_fetch_assoc($result);
?>  

<!DOCTYPE html> 
<html lang="en"> 
<head>     
    <meta charset="UTF-8">     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <title>Document</title> 
    <style>
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

<?php if(isset($row) && $row['user_type'] === 'admin'): ?>     
    <?php include('admin_page.php'); ?>     
<?php else: ?>     
    <?php include('home.php'); ?>     
<?php endif; ?>  

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