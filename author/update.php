<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $is_image_empty = is_image_empty($_FILES["photo"]);
        $photo_query = ' ';
        if(!$is_image_empty){
            list($image_save_status, $message) = save_image("author/",$_FILES["photo"]);
            if ($image_save_status){
                $photo_query = ', photo_url = \'' . $message . '\' ';
            };
        }
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $about = isset($_POST['about']) ? $_POST['about'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $stmt = $pdo->prepare('UPDATE author SET name = ?, about = ?, email = ?' . $photo_query . 'WHERE id = ?');
        $stmt->execute([$name, $about, $email, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM author WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$author) {
        exit('Author doesn\'t exist with that id!');
    }
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px;  margin-bottom:20px">
	<h2>Update Author #<?=$author['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$author['id']?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$author['id']?>">
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Author name" class="form-control" rows="5" maxlength="255" value="<?=$author['name']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="about">About</label>
            <textarea name="about" id="about" placeholder="Short description about the author" class="form-control" rows="3"><?=$author['about']?></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" maxlength="255" value="<?=$author['email']?>"/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label>Photo</label>
            <img src="<?=$author['photo_url']?>" alt="Author Photo" class=".img-fluid w-25">
            <input name="photo" id="photo" placeholder="photo" class="form-control mt-1" type="file">
            <?php if (isset($image_save_status) && !$image_save_status): ?>
                <div class="alert alert-danger mt-1" role="alert">
                    <?=$message?>
                </div>    
            <?php endif; ?>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>