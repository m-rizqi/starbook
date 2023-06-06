<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    list($image_save_status, $message) = save_image("staff/",$_FILES["photo"]);
    if (!$image_save_status) return;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '';
    $store_id = intval($store_id);
    $photo_url = $message;
    $stmt = $pdo->prepare('INSERT INTO staff (name, phone, email, store_id, photo_url) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $phone, $email, $store_id, $photo_url]);
    $msg = 'Staff Created!';
}

$stmt = $pdo->prepare('SELECT * FROM store');
$stmt->execute();
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Staff</h2>
    <hr>
	<form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Staff name" class="form-control" rows="3" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="example@email.com" class="form-control" type="phone" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="+62x-xxx-xxx-xxx" type="tel" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="store_id">Store</label>
            <select name="store_id" id="store_id" class="form-select" required>
                <option value="">Select a store</option>
                <?php
                foreach ($stores as $store) {
                    $store_id = $store['id'];
                    $store_name = $store['name'];
                    echo "<option value=\"$store_id\">$store_name</option>";
                }
                ?>
            </select>
            <a href="../store/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Store</a>
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