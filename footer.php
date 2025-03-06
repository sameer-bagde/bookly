<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Footer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <style>
.footer {
    background: #2c3e50;
    color: white;
    padding: 50px 0;
}
.footer h3 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    font-weight: 600;
    color: #f39c12;
}
.footer a {
    color: #bdc3c7;
    text-decoration: none;
    transition: 0.3s;
    display: block;
    font-size: 1.1rem;
    padding: 5px 0;
}
.footer a:hover {
    color: #f39c12;
    text-decoration: none;
}
.footer p {
    margin: 5px 0;
    font-size: 1.1rem;
}
.footer i {
    margin-right: 10px;
    color: #f39c12;
}
.footer .credit {
    text-align: center;
    margin-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    padding-top: 20px;
    font-size: 1.2rem;
    color: #bdc3c7;
}
.footer .credit span {
    color: #f39c12;
}

.footer .row {
            justify-content: center;
        }

        .footer .col-md-3 {
            margin-bottom: 30px;
            padding: 0 25px;
        }

    </style>
</head>
<body>

<footer class="footer bg-dark ">
<div class="container">
<div class="row text-center text-md-start">
    <div class="d-flex justify-content-center">
    <div class="col-md-3 col-12">
                <h3>Quick Links</h3>
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="shop.php">Shop</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="col-md-3 col-12">
                <h3>Extra Links</h3>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
            </div>
            <div class="col-md-3 col-12">
                <h3>Contact Info</h3>
                <p><i class="fas fa-phone"></i> +987-654-3210</p>
                <p><i class="fas fa-phone"></i> +444-555-6666</p>
                <p><i class="fas fa-envelope"></i> myemail@example.com</p>
                <p><i class="fas fa-map-marker-alt"></i> New York, USA - 10001</p>
            </div>
            <div class="col-md-3 col-12">
                <h3>Follow Us</h3>
                <a href="#"><i class="fab fa-facebook-f"></i> Facebook</a>
                <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="#"><i class="fab fa-linkedin"></i> LinkedIn</a>
            </div>
    </div>

        </div>
        <p class="credit"> &copy; <?php echo date('Y'); ?> by <span>Task 8</span></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
