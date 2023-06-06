<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $detail_address = isset($_POST['detail_address']) ? $_POST['detail_address'] : '';
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $country = isset($_POST['country']) ? $_POST['country'] : '';
    $postal_code = isset($_POST['postal_code']) ? $_POST['postal_code'] : '';
    $stmt = $pdo->prepare('INSERT INTO address (detail_address, city, country, postal_code) VALUES (?, ?, ?, ?)');
    $stmt->execute([$detail_address, $city, $country, $postal_code]);
    $msg = 'Address Created!';
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Address</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group mt-3">
            <label for="detail_address">Detail Address</label>
            <textarea name="detail_address" id="detail_address" placeholder="Abc street..." class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group mt-3">
            <label for="city">City</label>
            <input name="city" id="city" placeholder="Def" class="form-control" required/>
        </div>
        <div class="form-group mt-3">
            <label for="country">Country</label>
            <input name="country" id="country" placeholder="Ghj" class="form-control" required/>
        </div>
        <div class="form-group mt-3">
            <label for="postal_code">Postal Code</label>
            <input name="postal_code" id="postal_code" placeholder="123" class="form-control" maxlength="50" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>