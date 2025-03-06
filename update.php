<?php include('header.php'); ?>
<?php include('connection.php'); ?>

<?php 
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$query = "SELECT * FROM `employees` WHERE `id` = '$id'";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
} else {
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['update_employees'])) {
    $f_name = mysqli_real_escape_string($connection, $_POST['f_name']);
    $l_name = mysqli_real_escape_string($connection, $_POST['l_name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    $query = "UPDATE `employees` SET `first_name`='$f_name', `last_name`='$l_name', `email`='$email', `password`='$password' WHERE `id` = $id";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($connection));
    } else {
        header("location:index.php?update_msg=You have successfully updated the employee data");
        exit();
    }
}
?>

<form action="update.php?id=<?= $row['id'] ?>" method="post">
    <div class="modal-body">
        <div class="form-group">
            <label for="f_name">First Name</label>
            <input type="text" name="f_name" class="form-control" value="<?= htmlspecialchars($row['first_name']) ?>">
        </div>
        <div class="form-group">
            <label for="l_name">Last Name</label>
            <input type="text" name="l_name" class="form-control" value="<?= htmlspecialchars($row['last_name']) ?>">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($row['password']) ?>">
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="update_employees" value="UPDATE">Update</button>
    </div>
</form>

<?php include('footer.php'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
