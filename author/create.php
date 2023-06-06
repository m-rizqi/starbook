<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    list($image_save_status, $message) = save_image("author/",$_FILES["photo"]);
    if (!$image_save_status) return;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $about = isset($_POST['about']) ? $_POST['about'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $photo_url = $message;
    $stmt = $pdo->prepare('INSERT INTO author (name, about, email, photo_url) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $about, $email, $photo_url]);
    $msg = 'Author Created!';
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Author</h2>
    <hr>
	<form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Author name" class="form-control" rows="3" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="about">About</label>
            <textarea name="about" id="about" placeholder="Short description about the author" class="form-control"></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" maxlength="255"/>
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