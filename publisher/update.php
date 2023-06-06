<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $website_url = isset($_POST['website_url']) ? $_POST['website_url'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
        $address_id = intval($address_id);
        $stmt = $pdo->prepare('UPDATE publisher SET name = ?, website_url = ?, email = ?, address_id = ? WHERE id = ?');
        $stmt->execute([$name, $website_url, $email, $address_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM publisher WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $publisher = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$publisher) {
        exit('Publisher doesn\'t exist with that id!');
    }

    $stmt = $pdo->prepare('SELECT address.* FROM address LEFT JOIN publisher ON address.id = publisher.address_id WHERE publisher.address_id IS NULL OR address.id = ? ORDER BY address.id');
    $stmt->execute([$publisher['address_id']]);
    $not_reserved_address = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Publisher #<?=$publisher['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$publisher['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$publisher['id']?>"/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input name="name" id="name" placeholder="Publisher name" class="form-control" maxlength="255" value="<?=$publisher['name']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="website_url">Website</label>
            <input name="website_url" id="website_url" placeholder="publisherdomain.com" class="form-control" type="url" value="<?=$publisher['website_url']?>"/>
        </div>
        <div class="form-group mt-3">
            <label for="email">Email</label>
            <input name="email" id="email" placeholder="publisher@email.com" class="form-control" type="email" value="<?=$publisher['email']?>"/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="address_id">Address</label>
            <select name="address_id" id="address_id" class="form-select" required>
                <?php
                foreach ($not_reserved_address as $address) {
                    $addressId = $address['id'];
                    $selected = '';
                    $isSelected = $addressId == $publisher['address_id'];
                    if ($isSelected){
                        $selected = 'selected';
                    }
                    $detailAddress = $address['detail_address'];
                    $city = $address['city'];
                    echo "<option value=\"$addressId\" $selected>$detailAddress, $city</option>";
                }
                ?>
            </select>
            <small class="form-text text-muted">An address only assigned to one publisher</small>
            <a href="../address/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Address</a>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>