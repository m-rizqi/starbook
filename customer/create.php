<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : null;
    $store_id = intval($store_id);
    $stmt = $pdo->prepare('INSERT INTO customer (name, email, phone, store_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $phone, $store_id]);
    $msg = 'Customer Created!';
}

$stmt = $pdo->prepare('SELECT * FROM store');
$stmt->execute();
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Create New Customer</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Customer Name" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="customer@email.com" class="form-control" type="email" maxlength="255"/>
        </div>
        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <input name="phone" id="phone" placeholder="62xxxxxxxx" class="form-control" type="tel" maxlength="20"/>
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
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>