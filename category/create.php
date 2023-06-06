<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $stmt = $pdo->prepare('INSERT INTO category (name) VALUES (?)');
    $stmt->execute([$name]);
    $msg = 'Category Created!';
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Category</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="" class="form-control" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>