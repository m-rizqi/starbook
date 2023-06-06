<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM address WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$address) {
        exit('Address doesn\'t exist with that id!');
    }
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $pdo->prepare('DELETE FROM address WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted the address!';
        }
        header('Location: ./list.php');
        exit;
    }
} else {
    exit('No id specified!');
}
?>

<?=template_header()?>

<div class="container" style="margin-top: 20px">
	<h2>Delete Address #<?=$address['id']?></h2>
    <hr>
    <?php if ($msg): ?>
    <p style="margin-top: 24px"><?=$msg?></p>
    <?php else: ?>
	<p style="margin-top: 24px">Are you sure you want to delete address <b><?=$address['detail_address']?></b>?</p>
    <div style="display: flex; justify-content:flex-start; mt-3">
        <a href="delete.php?id=<?=$address['id']?>&confirm=yes" class="btn btn-danger" style="margin-right:8px">Yes</a>
        <a href="delete.php?id=<?=$address['id']?>&confirm=no" class="btn btn-success" style="margin-left:8px">No</a>
    </div>
    <?php endif; ?>
</div>

<?=template_footer()?>