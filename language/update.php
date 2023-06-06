<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['code'])) {
    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $stmt = $pdo->prepare('UPDATE language SET name = ? WHERE code = ?');
        $stmt->execute([$name, $_GET['code']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM language WHERE code = ?');
    $stmt->execute([$_GET['code']]);
    $language = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$language) {
        exit('Language doesn\'t exist with that code!');
    }
} else {
    exit('No code specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Language #<?=$language['code']?></h2>
    <hr>
	<form action="update.php?code=<?=$language['code']?>" method="post">
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" class="form-control-plaintext" placeholder="en" id="code" value="<?=$language['code']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="English" class="form-control" value="<?=$language['name']?>" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>