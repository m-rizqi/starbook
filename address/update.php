<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $detail_address = isset($_POST['detail_address']) ? $_POST['detail_address'] : '';
        $city = isset($_POST['city']) ? $_POST['city'] : '';
        $country = isset($_POST['country']) ? $_POST['country'] : '';
        $postal_code = isset($_POST['postal_code']) ? $_POST['postal_code'] : '';
        $stmt = $pdo->prepare('UPDATE address SET detail_address = ?, city = ?, country = ?, postal_code = ? WHERE id = ?');
        $stmt->execute([$detail_address, $city, $country, $postal_code, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM address WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$address) {
        exit('Address doesn\'t exist with that id!');
    }
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Address #<?=$address['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$address['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$address['id']?>"/>
        </div>
        <div class="form-group mt-3">
            <label for="detail_address">Detail Address</label>
            <textarea name="detail_address" id="detail_address" class="form-control" rows="3"><?=$address['detail_address']?></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="city">City</label>
            <input name="city" id="city" value="<?=$address['city']?>" class="form-control" required/>
        </div>
        <div class="form-group mt-3">
            <label for="country">Country</label>
            <input name="country" id="country" value="<?=$address['country']?>" class="form-control" required/>
        </div>
        <div class="form-group mt-3">
            <label for="postal_code">Postal Code</label>
            <input name="postal_code" id="postal_code" value="<?=$address['postal_code']?>" class="form-control" maxlength="50" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>