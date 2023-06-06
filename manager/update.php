<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $is_image_empty = is_image_empty($_FILES["photo"]);
        $photo_query = ' ';
        if(!$is_image_empty){
            list($image_save_status, $message) = save_image("manager/",$_FILES["photo"]);
            if ($image_save_status){
                $photo_query = ', photo_url = \'' . $message . '\' ';
            };
        }
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $stmt = $pdo->prepare('UPDATE manager SET name = ?, phone = ?, email = ?' . $photo_query . 'WHERE id = ?');
        $stmt->execute([$name, $phone, $email, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM manager WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $manager = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$manager) {
        exit('Manager doesn\'t exist with that id!');
    }
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px;  margin-bottom:20px">
	<h2>Update Manager #<?=$manager['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$manager['id']?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$manager['id']?>">
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Manager name" class="form-control" rows="5" maxlength="255" value="<?=$manager['name']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" maxlength="255" value="<?=$manager['email']?>"/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="+62x-xxx-xxx-xxx" class="form-control" maxlength="255" value="<?=$manager['phone']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label>Photo</label>
            <img src="<?=$manager['photo_url']?>" alt="Manager Photo" class=".img-fluid w-25">
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