<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
    $address_id = intval($address_id);
    $manager_id = isset($_POST['manager_id']) ? $_POST['manager_id'] : null;
    $manager_id = intval($manager_id);
    $stmt = $pdo->prepare('INSERT INTO store (name, address_id, manager_id) VALUES (?, ?, ?)');
    $stmt->execute([$name, $address_id, $manager_id]);
    $msg = 'Store Created!';
}

$stmt = $pdo->prepare('SELECT address.* FROM address LEFT JOIN publisher ON address.id = publisher.address_id WHERE publisher.address_id IS NULL ORDER BY address.id');
$stmt->execute();
$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT manager.* FROM manager LEFT JOIN store ON manager.id = store.manager_id WHERE store.manager_id IS NULL ORDER BY manager.id');
$stmt->execute();
$managers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Create New Store</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Store name" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="address_id">Address</label>
            <select name="address_id" id="address_id" class="form-select" required>
                <option value="">Select an address</option>
                <?php
                foreach ($addresses as $address) {
                    $addressId = $address['id'];
                    $detailAddress = $address['detail_address'];
                    $city = $address['city'];
                    echo "<option value=\"$addressId\">$detailAddress, $city</option>";
                }
                ?>
            </select>
            <small class="form-text text-muted">An address only assigned to one store</small>
            <a href="../address/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Address</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="manager_id">Manager</label>
            <select name="manager_id" id="manager_id" class="form-select" required>
                <option value="">Select a manager</option>
                <?php
                foreach ($managers as $manager) {
                    $manager_id = $manager['id'];
                    $manager_name = $manager['name'];
                    echo "<option value=\"$manager_id\">$manager_name</option>";
                }
                ?>
            </select>
            <small class="form-text text-muted">A manager only managed one store</small>
            <a href="../manager/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Manager</a>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>