

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookly - Your Book Destination</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>

@import url('https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600;700&display=swap');

:root {
    --primary: #8e44ad;
    --secondary: #6c3483;
    --accent: #f39c12;
    --dark: #333;
    --light: #fff;
    --gray: #f5f5f5;
    --text-color: #666;
    --border: 1px solid #ddd;
    --box-shadow: 0 .5rem 1rem rgba(0,0,0,.1);
    --transition: all 0.3s ease-in-out;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Rubik', sans-serif;
}

.share {
    display: flex;
    gap: 20px;
}

.share a,
.icons a,
.icons div {
    color: white;
    text-decoration: none;
    cursor: pointer;
    font-size: 1.5rem;
}

.user-container {
    position: relative;
    display: inline-block;
}

.account-box {
    position: absolute;
    top: 100%; 
    margin-top: 20px;
    right: 0;
    width: 300px;
    background: white;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 15px;
    display: none;
    z-index: 1000;
    border: 1px solid #ddd;
}

.account-box p {
    margin: 0 0 10px 0;
    font-size: 0.9rem;
    color: #333;
}

.account-box .btn {
    width: 100%;
    padding: 5px;
    font-size: 0.9rem;
}

.icons {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.icons i,
.icons div i,
.share i {
    font-size: 1.5rem;
}

@media (max-width: 991.98px) {
    .navbar-collapse {
        padding: 1rem 0;
    }

    .navbar-nav {
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 1rem;
        display: flex;
        flex-direction: row !important;
        justify-content: center;
        gap: 20px;
    }
    
    .share {
        display: flex;
        justify-content: center;
        margin: 1rem;
        padding: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        gap: 10px; 
    }

    .icons {
        margin: 1rem;
}
}

    </style>
</head>
<body>

<?php $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0; ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" style="font-size: 2rem;" href="index.php">Bookly.</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
    <ul class="navbar-nav m-auto p-2">
        <li class="nav-item">
            <a class="nav-link active" href="index.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_products.php">Products</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_orders.php">Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="total_users.php">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="admin_message.php">Messages</a>
        </li>
    </ul>
<?php else: ?>
    <ul class="navbar-nav m-auto p-2">
        <li class="nav-item">
            <a class="nav-link active" href="index.php">Home</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="about.php">About</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="shop.php">Shop</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="order_placed.php">My Order</a>
        </li>

    </ul>
<?php endif; ?>

                
                <div class="share m-auto">
                <a href="#"><i class="fab fa-facebook-f"></i> </a>
                <a href="#"><i class="fab fa-twitter"></i> </a>
                <a href="#"><i class="fab fa-instagram"></i> </a>
                <a href="#"><i class="fab fa-linkedin"></i> </a>
                </div>

                
                
                <?php if (isset($_SESSION['email']) && isset($_SESSION['first_name']) && isset($_SESSION['last_name'])): ?>
                    <div class="icons">
                        <a href="search.php"><i class="fas fa-search"></i></a>
                        <div class="user-container">
                            <div id="user-btn"><i class="fas fa-user"></i></div>
                            <div class="account-box">

                            <p>User: <?php echo htmlspecialchars($_SESSION['user_type']); ?></p>

                                <p>Username: <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></p>
                                <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>

                                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
                            </div>
                        </div>                        
                        <?php if ($_SESSION['user_type'] !== 'admin'): ?>
                            <?php
               $select_cart_number = mysqli_query($connection, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
               $cart_rows_number = mysqli_num_rows($select_cart_number); 
            ?>
    <a href="cart.php">
        
        <i class="fas fa-shopping-cart"></i>
    <span>(<?php echo $cart_rows_number; ?>)</span>
 
    </a>
<?php endif; ?>

                    </div>
                <?php else: ?>
                    <div class="d-flex">
                        <button class="btn btn-outline-light me-2" onclick="window.location.href='login.php';">Sign In</button>
                        <button class="btn btn-primary" onclick="window.location.href='registration.php';">Sign Up</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


    <script>
$(document).ready(function() {
    const accountBox = $('.account-box');
    let clickTimer = null;
    
    $('#user-btn').click(function(event) {
        event.stopPropagation();
        if (!clickTimer) {
            clickTimer = setTimeout(() => {
                accountBox.toggleClass('active');
                clickTimer = null;
            }, 300);
        }
    });

    $('#user-btn').on('dblclick', function(event) {
        event.stopPropagation();
        clearTimeout(clickTimer);
        clickTimer = null;
        accountBox.removeClass('active');
    });

    $(document).click(function(event) {
        if (!$(event.target).closest('.user-container').length) {
            accountBox.removeClass('active');
        }
    });

    $(window).on('resize orientationchange', function() {
        accountBox.removeClass('active');
    });

    const navbarCollapse = document.getElementById('navbarNav');
    const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
    
    $('.navbar-toggler').click(function() {
        bsCollapse.toggle();
    });

    $(document).click(function(event) {
        if (!$(event.target).closest('.navbar').length && 
            $(navbarCollapse).hasClass('show')) {
            bsCollapse.hide();
        }
    });
});
</script>

<script>
        $(document).ready(function() {
            $('#user-btn').click(function(event) {
                event.stopPropagation();  
                $('.account-box').toggle();
            });

            $(document).click(function(event) {
                if (!$(event.target).closest('.user-container').length) {
                    $('.account-box').hide();  
                }
            });
        });
    </script>

</body>
</html>
