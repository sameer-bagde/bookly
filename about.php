<?php
ob_start(); 
session_start(); 
include 'connection.php';

if (isset($_SESSION['id'])) {
   $user_id = $_SESSION['id']; 
} else {
   $user_id = 0; 
}

if(!isset($user_id)){
   header("location:registration.php?message=" . urlencode("Failed to register: " . mysqli_error($connection)));
   exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us</title>
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

.about {
   padding: 1rem;
}

.about .flex {
   margin: 0 auto;
   display: flex;
   align-items: center;
   flex-wrap: wrap;
   gap: 1.5rem;
   max-width: 1200px;
}

.about .flex .image {
   flex: 1 1 25rem;
   overflow: hidden;
   border-radius: 0.5rem;
   box-shadow: var(--box-shadow);
}

.about .flex .image img {
   width: 100%;
   transition: transform 0.5s ease;
}

.about .flex .image:hover img {
   transform: scale(1.05);
}

.about .flex .content {
   flex: 1 1 25rem;
   padding: 1.8rem;
   background-color: var(--white);
   border-radius: 0.5rem;
   box-shadow: var(--box-shadow);
}

.about .flex .content h3 {
   font-size: 1.8rem;
   color: var(--black);
   text-transform: uppercase;
   margin-bottom: 0.5rem;
   position: relative;
   padding-bottom: 0.5rem;
}

.about .flex .content h3:after {
   content: '';
   position: absolute;
   left: 0;
   bottom: 0;
   height: 3px;
   width: 50px;
   background: var(--purple);
}

.about .flex .content p {
   padding: 0.5rem 0;
   line-height: 1.6;
   font-size: 1rem;
   color: var(--light-color);
}

.title {
   text-align: center;
   margin-bottom: 2rem;
   text-transform: uppercase;
   color: var(--black);
   font-size: 2rem;
   position: relative;
   padding-bottom: 0.8rem;
}

.title:after {
   content: '';
   position: absolute;
   left: 50%;
   bottom: 0;
   height: 3px;
   width: 80px;
   background: var(--purple);
   transform: translateX(-50%);
}

.btn1 {
   display: inline-block;
   margin-top: 1rem;
   padding: 0.7rem 2.2rem;
   cursor: pointer;
   color: var(--white);
   font-size: 1rem;
   font-weight: 500;
   border-radius: .5rem;
   text-transform: capitalize;
   background: var(--gradient);
   box-shadow: 0 2px 10px rgba(142, 68, 173, 0.3);
}

.btn1:hover {
   transform: scale(1.05);
}

/* Reviews Section */
.reviews {
   padding: 2rem 1.5rem;
   background-color: var(--white);
   margin: 2rem 0;
}

.reviews .box-container {
   margin: 0 auto;
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr));
   gap: 1.5rem;
   max-width: 1200px;
}

.reviews .box-container .box {
   background-color: var(--light-bg);
   box-shadow: var(--box-shadow);
   border-radius: .5rem;
   text-align: center;
   padding: 1.5rem;
   transition: transform 0.3s ease;
}

.reviews .box-container .box:hover {
   transform: translateY(-8px);
}

.reviews .box-container .box img {
   border-radius: 50%;
   width: 80px;
   height: 80px;
   object-fit: cover;
   margin: 0 auto;
   border: 3px solid var(--purple);
   padding: 3px;
}

.reviews .box-container .box p {
   padding: 0.8rem 0;
   line-height: 1.7;
   color: var(--light-color);
   font-size: 0.9rem;
   font-style: italic;
}

.reviews .box-container .box .stars {
   background-color: rgba(142, 68, 173, 0.1);
   display: inline-block;
   margin: .3rem 0;
   border-radius: .5rem;
   padding: .3rem 1rem;
}

.reviews .box-container .box .stars i {
   font-size: 1rem;
   color: var(--orange);
   margin: 0 .1rem;
}

.reviews .box-container .box h3 {
   font-size: 1.2rem;
   color: var(--black);
   margin-top: 0.8rem;
}

/* Authors Section */
.authors {
   padding: 2rem 1.5rem;
   background-color: var(--light-bg);
}

.authors .box-container {
   margin: 0 auto;
   display: grid;
   grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr));
   gap: 1.5rem;
   max-width: 1200px;
}

.authors .box-container .box {
   position: relative;
   text-align: center;
   border: none;
   box-shadow: var(--box-shadow);
   overflow: hidden;
   border-radius: .5rem;
   background: var(--white);
}

.authors .box-container .box img {
   width: 100%;
   height: 200px;
   object-fit: cover;
   transition: transform 0.5s ease;
}

.authors .box-container .box:hover img {
   transform: scale(1.1);
}

.authors .box-container .box .share {
   position: absolute;
   top: 1rem;
   left: -3.5rem;
   display: flex;
   flex-direction: column;
   z-index: 10;
   transition: all 0.4s ease;
}

.authors .box-container .box:hover .share {
   left: 1rem;
}

.authors .box-container .box .share a {
   height: 2.5rem;
   width: 2.5rem;
   line-height: 2.5rem;
   font-size: 1.2rem;
   background-color: var(--white);
   border-radius: 50%;
   margin: 0.3rem 0;
   color: var(--black);
   box-shadow: 0 0 5px rgba(0,0,0,0.2);
}

.authors .box-container .box .share a:hover {
   background-color: var(--purple);
   color: var(--white);
   transform: rotate(360deg);
}

.authors .box-container .box h3 {
   font-size: 1.2rem;
   color: var(--black);
   padding: 1rem;
   background-color: var(--white);
   border-top: 1px solid rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
   .about .flex .content h3 {
      font-size: 1.5rem;
   }
   
   .about .flex .content p {
      font-size: 0.9rem;
   }
}

@media (max-width: 450px) {
   .heading h3 {
      font-size: 2rem;
   }
   
   .about .flex .content {
      padding: 1.2rem;
   }
}
   </style>
</head>
<body>

<?php include 'header.php'; ?>

   
   <div class="heading">
      <h3>about us</h3>
      <p><a href="index.php">Home</a> / about</p>
   </div>

   <section class="about">
      <div class="flex">
         <div class="image">
            <img src="images/about-img.jpg" alt="About Us Image">
         </div>

         <div class="content">
            <h3>why choose us?</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eveniet voluptatibus aut hic molestias, reiciendis natus fuga, cumque excepturi veniam ratione.</p>
            <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Impedit quos enim minima ipsa dicta officia corporis ratione.</p>
            <a href="contact.php" class="btn1 p-2 fs-5 text-light">contact us</a>
         </div>
      </div>
   </section>

   <section class="reviews">
      <h1 class="title">client's reviews</h1>

      <div class="box-container">
         <div class="box">
            <img src="images/pic-1.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>john deo</h3>
         </div>

         <div class="box">
            <img src="images/pic-2.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>john deo</h3>
         </div>

         <div class="box">
            <img src="images/pic-3.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt ad, quo labore fugiat nam accusamus quia.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>john deo</h3>
         </div>
      </div>
   </section>

   <section class="authors">
      <h1 class="title">great authors</h1>

      <div class="box-container">
         <div class="box">
            <img src="images/author-1.jpg" alt="">
            <div class="share">
               <a href="#" class="fab fa-facebook-f"></a>
               <a href="#" class="fab fa-twitter"></a>
               <a href="#" class="fab fa-instagram"></a>
               <a href="#" class="fab fa-linkedin"></a>
            </div>
            <h3>john deo</h3>
         </div>

         <div class="box">
            <img src="images/author-2.jpg" alt="">
            <div class="share">
               <a href="#" class="fab fa-facebook-f"></a>
               <a href="#" class="fab fa-twitter"></a>
               <a href="#" class="fab fa-instagram"></a>
               <a href="#" class="fab fa-linkedin"></a>
            </div>
            <h3>john deo</h3>
         </div>

         <div class="box">
            <img src="images/author-3.jpg" alt="">
            <div class="share">
               <a href="#" class="fab fa-facebook-f"></a>
               <a href="#" class="fab fa-twitter"></a>
               <a href="#" class="fab fa-instagram"></a>
               <a href="#" class="fab fa-linkedin"></a>
            </div>
            <h3>john deo</h3>
         </div>
      </div>
   </section>


   <?php include 'footer.php'; ?>

</body>
</html>