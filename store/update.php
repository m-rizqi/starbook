<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
        $address_id = intval($address_id);
        $manager_id = isset($_POST['manager_id']) ? $_POST['manager_id'] : null;
        $manager_id = intval($manager_id);
        $stmt = $pdo->prepare('UPDATE store SET name = ?, address_id = ?, manager_id = ? WHERE id = ?');
        $stmt->execute([$name, $address_id, $manager_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM store WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$store) {
        exit('Store doesn\'t exist with that id!');
    }

    $stmt = $pdo->prepare('SELECT address.* FROM address LEFT JOIN store ON address.id = store.address_id WHERE store.address_id IS NULL OR address.id = ? ORDER BY address.id');
    $stmt->execute([$store['address_id']]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT manager.* FROM manager LEFT JOIN store ON manager.id = store.manager_id WHERE store.manager_id IS NULL OR manager.id = ? ORDER BY manager.id');
    $stmt->execute([$store['manager_id']]);
    $managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Store #<?=$store['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$store['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$store['id']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Store name" class="form-control" maxlength="255"  value="<?=$store['name']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="address_id">Address</label>
            <select name="address_id" id="address_id" class="form-select" required>
                <?php
                foreach ($addresses as $address) {
                    $addressId = $address['id'];
                    $selected = '';
                    $isSelected = $addressId == $store['address_id'];
                    if ($isSelected){
                        $selected = 'selected';
                    }
                    $detailAddress = $address['detail_address'];
                    $city = $address['city'];
                    echo "<option value=\"$addressId\" $selected>$detailAddress, $city</option>";
                }
                ?>
            </select>
            <small class="form-text text-muted">An address only assigned to one store</small>
            <a href="../address/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Address</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="manager_id">Manager</label>
            <select name="manager_id" id="manager_id" class="form-select" required>
                <?php
                foreach ($managers as $manager) {
                    $manager_id = $manager['id'];
                    $manager_name = $manager['name'];
                    $selected = '';
                    $isSelected = $manager_id == $store['manager_id'];
                    if ($isSelected){
                        $selected = 'selected';
                    }
                    echo "<option value=\"$manager_id\" $isSelected>$manager_name</option>";
                }
                ?>
            </select>
            <small class="form-text text-muted">A manager only managed one store</small>
            <a href="../manager/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Manager</a>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>