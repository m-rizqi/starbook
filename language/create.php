<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $code = isset($_POST['code']) ? $_POST['code'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $stmt = $pdo->prepare('INSERT INTO language (code, name) VALUES (?, ?)');
    $stmt->execute([$code, $name]);
    $msg = 'Language Created!';
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Language</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group">
            <label for="code">Code</label>
            <input type="text" name="code" class="form-control" placeholder="en" id="code" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" placeholder="English" class="form-control" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>