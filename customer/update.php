<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : null;
        $store_id = intval($store_id);
        $stmt = $pdo->prepare('UPDATE customer SET name = ?, email = ?, phone = ?, store_id = ? WHERE id = ?');
        $stmt->execute([$name, $email, $phone, $store_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM customer WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$customer) {
        exit('Customer doesn\'t exist with that id!');
    }
   
    $stmt = $pdo->prepare('SELECT * FROM store');
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Customer #<?=$customer['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$customer['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$customer['id']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Customer Name" class="form-control" maxlength="255" value="<?=$customer['name']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="customer@email.com" class="form-control" type="email" value="<?=$customer['email']?>" maxlength="255"/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="62xxxxxxxx" class="form-control" type="tel" value="<?=$customer['phone']?>" maxlength="20"/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="store_id">Store</label>
            <select name="store_id" id="store_id" class="form-select" required>
                <?php
                foreach ($stores as $store) {
                    $store_id = $store['id'];
                    $store_name = $store['name'];
                    $selected = '';
                    $is_selected = $store_id == $customer['store_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }
                    echo "<option value=\"$store_id\" $selected>$store_name</option>";
                }
                ?>
            </select>
            <a href="../store/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Store</a>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>