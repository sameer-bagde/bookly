<?php

ob_start(); 
session_start(); 
include('connection.php');  

if (!isset($_SESSION['email'])) {     
    header("Location: login.php");     
    exit(); 
} 

$upload_dir = 'uploaded_img';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}


if(isset($_POST['add_product'])){
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $price = mysqli_real_escape_string($connection, $_POST['price']);
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = $upload_dir . '/' . $image;
        
        $select_product_name = mysqli_query($connection, "SELECT name FROM `products` WHERE name = '$name'") or die('Query failed: ' . mysqli_error($connection));
        
        if(mysqli_num_rows($select_product_name) > 0){
            $message[] = 'Product name already added';
        } else {
            if($image_size > 2000000){
                $message[] = 'Image size is too large (max 2MB)';
            } else {
                $add_product_query = mysqli_query($connection, 
                    "INSERT INTO `products`(name, price, image) VALUES('$name', '$price', '$image')") 
                    or die('Query failed: ' . mysqli_error($connection));
                
                if($add_product_query){
                    if(move_uploaded_file($image_tmp_name, $image_folder)){
                        $message[] = 'Product added successfully!';
                    } else {
                        $message[] = 'Failed to upload image. Check directory permissions.';
                        mysqli_query($connection, "UPDATE `products` SET image = '' WHERE name = '$name'");
                    }
                } else {
                    $message[] = 'Product could not be added!';
                }
            }
        }
    } else {
        $message[] = 'Please select an image';
    }
}

if(isset($_GET['delete'])){
    $delete_id = mysqli_real_escape_string($connection, $_GET['delete']);
    $delete_image_query = mysqli_query($connection, "SELECT image FROM `products` WHERE id = '$delete_id'") 
        or die('Query failed: ' . mysqli_error($connection));
    
    if(mysqli_num_rows($delete_image_query) > 0) {
        $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
        if(!empty($fetch_delete_image['image']) && file_exists($upload_dir . '/' . $fetch_delete_image['image'])) {
            unlink($upload_dir . '/' . $fetch_delete_image['image']);
        }
        
        mysqli_query($connection, "DELETE FROM `products` WHERE id = '$delete_id'") 
            or die('Query failed: ' . mysqli_error($connection));
        
        header('location:admin_products.php');
        exit();
    }
}

if(isset($_POST['update_product'])){
    $update_p_id = mysqli_real_escape_string($connection, $_POST['update_p_id']);
    $update_name = mysqli_real_escape_string($connection, $_POST['update_name']);
    $update_price = mysqli_real_escape_string($connection, $_POST['update_price']);
    
    mysqli_query($connection, "UPDATE `products` SET name = '$update_name', price = '$update_price' WHERE id = '$update_p_id'") 
        or die('Query failed: ' . mysqli_error($connection));
    
    if(isset($_FILES['update_image']) && $_FILES['update_image']['error'] == 0) {
        $update_image = time() . '_' . $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_folder = $upload_dir . '/' . $update_image;
        $update_old_image = $_POST['update_old_image'];
        
        if($update_image_size > 2000000){
            $message[] = 'Image file size is too large (max 2MB)';
        } else {
            mysqli_query($connection, "UPDATE `products` SET image = '$update_image' WHERE id = '$update_p_id'") 
                or die('Query failed: ' . mysqli_error($connection));
            
            if(move_uploaded_file($update_image_tmp_name, $update_folder)) {
                if(!empty($update_old_image) && file_exists($upload_dir . '/' . $update_old_image)) {
                    unlink($upload_dir . '/' . $update_old_image);
                }
            } else {
                $message[] = 'Failed to upload new image. Check directory permissions.';
            }
        }
    }
    
    header('location:admin_products.php');
    exit();
}

$update_id = isset($_GET['update']) ? mysqli_real_escape_string($connection, $_GET['update']) : null;
$update_product = null;

if($update_id) {
    $update_query = mysqli_query($connection, "SELECT * FROM `products` WHERE id = '$update_id'") 
        or die('Query failed: ' . mysqli_error($connection));
    if(mysqli_num_rows($update_query) > 0) {
        $update_product = mysqli_fetch_assoc($update_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Products</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <style>
      .product-img {
         height: 200px;
         object-fit: contain;
      }
      .message {
         margin: 10px 0;
         padding: 10px;
         border-radius: 5px;
      }
      .success {
         background-color: #d4edda;
         color: #155724;
      }
      .error {
         background-color: #f8d7da;
         color: #721c24;
      }
   </style>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="container my-5">
   <h1 class="text-center fs-1 mb-4">MANAGE PRODUCTS</h1>
   

   <?php if($update_product): ?>
   <div class="card p-4 mb-4">
      <h3>Update Product</h3>
      <form action="" method="post" enctype="multipart/form-data">
         <input type="hidden" name="update_p_id" value="<?php echo $update_product['id']; ?>">
         <input type="hidden" name="update_old_image" value="<?php echo $update_product['image']; ?>">
         
         <div class="mb-3">
            <label for="update_name" class="form-label">Product Name</label>
            <input type="text" id="update_name" name="update_name" value="<?php echo htmlspecialchars($update_product['name']); ?>" class="form-control" required>
         </div>
         
         <div class="mb-3">
            <label for="update_price" class="form-label">Product Price</label>
            <input type="number" id="update_price" name="update_price" value="<?php echo htmlspecialchars($update_product['price']); ?>" class="form-control" required>
         </div>
         
         <div class="mb-3">
            <label for="update_image" class="form-label">Current Image</label>
            <div class="mb-2">
               <img src="<?php echo $upload_dir . '/' . htmlspecialchars($update_product['image']); ?>" class="img-thumbnail product-img" alt="Current product image" onerror="this.src='<?php echo $upload_dir; ?>/default.jpg';">
            </div>
            <input type="file" id="update_image" name="update_image" class="form-control" accept="image/jpg, image/jpeg, image/png">
            <small class="text-muted">Leave empty to keep current image</small>
         </div>
         
         <div class="d-flex gap-2">
            <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
            <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
         </div>
      </form>
   </div>
   <?php else: ?>
   <div class="card p-4 mb-4">
      <h3>Add Product</h3>
      <form action="" method="post" enctype="multipart/form-data">
         <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter product name" required>
         </div>
         
         <div class="mb-3">
            <label for="price" class="form-label">Product Price</label>
            <input type="number" id="price" name="price" class="form-control" placeholder="Enter product price" required>
         </div>
         
         <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpg, image/jpeg, image/png" required>
            <small class="text-muted">Max file size: 2MB</small>
         </div>
         
         <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
      </form>
   </div>
   <?php endif; ?>
   
   <h3 class="my-4">Product List</h3>
   <div class="row">
      <?php
         $select_products = mysqli_query($connection, "SELECT * FROM products") or die('Query failed: ' . mysqli_error($connection));
         if(mysqli_num_rows($select_products) > 0){
            while($product = mysqli_fetch_assoc($select_products)){
      ?>
      <div class="col-md-4 mb-3">
         <div class="card text-center p-3">
            <img src="<?php echo $upload_dir . '/' . htmlspecialchars($product['image']); ?>" class="card-img-top product-img" alt="Product Image" onerror="this.src='<?php echo $upload_dir; ?>/default.jpg';">
            <div class="card-body">
               <h4 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h4>
               <p class="card-text text-muted">$<?php echo htmlspecialchars($product['price']); ?>/-</p>
               <div class="d-flex justify-content-between mt-2">
                  <a href="admin_products.php?update=<?php echo $product['id']; ?>" class="btn btn-warning">Update</a>
                  <a href="admin_products.php?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this product?');">Delete</a>
               </div>
            </div>
         </div>
      </div>
      <?php }} else { echo '<p class="text-center text-muted">No products added yet!</p>'; } ?>
   </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>