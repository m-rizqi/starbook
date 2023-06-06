<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $is_image_empty = is_image_empty($_FILES["photo"]);
        $photo_query = ' ';
        if(!$is_image_empty){
            list($image_save_status, $message) = save_image("staff/",$_FILES["photo"]);
            if ($image_save_status){
                $photo_query = ', photo_url = \'' . $message . '\' ';
            };
        }
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '';
        $store_id = intval($store_id);
        $stmt = $pdo->prepare('UPDATE staff SET name = ?, phone = ?, email = ?, store_id = ? ' . $photo_query . 'WHERE id = ?');
        $stmt->execute([$name, $phone, $email, $store_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM staff WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$staff) {
        exit('Staff doesn\'t exist with that id!');
    }

    $stmt = $pdo->prepare('SELECT * FROM store');
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px;  margin-bottom:20px">
	<h2>Update Staff #<?=$staff['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$staff['id']?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$staff['id']?>">
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Staff name" class="form-control" rows="3" maxlength="255" value="<?=$staff['name']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" maxlength="255" value="<?=$staff['email']?>" type="email" required/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="+62x-xxx-xxx-xxx" type="tel" class="form-control" maxlength="255" value="<?=$staff['phone']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="store_id">Store</label>
            <select name="store_id" id="store_id" class="form-select" required>
                <?php
                foreach ($stores as $store) {
                    $store_id = $store['id'];
                    $store_name = $store['name'];

                    $selected = '';
                    $is_selected = $store_id == $staff['store_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$store_id\" $selected>$store_name</option>";
                }
                ?>
            </select>
            <a href="../store/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Store</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction:column">
            <label for="photo">Photo</label>
            <img src="<?=$staff['photo_url']?>" alt="Manager Photo" class=".img-fluid w-25">
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