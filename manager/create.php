<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    list($image_save_status, $message) = save_image("manager/",$_FILES["photo"]);
    if (!$image_save_status) return;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $photo_url = $message;
    $stmt = $pdo->prepare('INSERT INTO manager (name, phone, email, photo_url) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $phone, $email, $photo_url]);
    $msg = 'Manager Created!';
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Manager</h2>
    <hr>
	<form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Manager name" class="form-control" rows="3" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="+62x-xxx-xxx-xxx" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="photo">Photo</label>
            <input name="photo" id="photo" placeholder="photo" class="form-control" type="file">
            <?php if (isset($image_save_status) && !$image_save_status): ?>
                <div class="alert alert-danger mt-1" role="alert">
                    <?=$message?>
                </div>    
            <?php endif; ?>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>