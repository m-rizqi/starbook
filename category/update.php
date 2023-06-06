<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $stmt = $pdo->prepare('UPDATE category SET name = ? WHERE id = ?');
        $stmt->execute([$name, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM category WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$category) {
        exit('Category doesn\'t exist with that id!');
    }
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Category #<?=$category['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$category['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="" id="id" value="<?=$category['id']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="" class="form-control" value="<?=$category['name']?>" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>