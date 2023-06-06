<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';

if (!empty($_POST)) {
    $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '';
    $store_id = intval($store_id);
    header('Location: ./create_step2.php?store_id='.$store_id);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM store ORDER BY id');
$stmt->execute();
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Sale - Step 1</h2>
    <hr>
	<form action="create_step1.php" method="post">
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
        <input type="submit" class="btn btn-dark mt-3" value="Next">
    </form>
</div>
<?=template_footer()?>