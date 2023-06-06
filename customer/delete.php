<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM customer WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$customer) {
        exit('Customer doesn\'t exist with that id!');
    }
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $pdo->prepare('DELETE FROM customer WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $msg = 'You have deleted the customer!';
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
	<h2>Delete Customer #<?=$customer['id']?></h2>
    <hr>
    <?php if ($msg): ?>
    <p style="margin-top: 24px"><?=$msg?></p>
    <?php else: ?>
	<p style="margin-top: 24px">Are you sure you want to delete customer <b><?=$customer['name']?></b>?</p>
    <div style="display: flex; justify-content:flex-start; mt-3">
        <a href="delete.php?id=<?=$customer['id']?>&confirm=yes" class="btn btn-danger" style="margin-right:8px">Yes</a>
        <a href="delete.php?id=<?=$customer['id']?>&confirm=no" class="btn btn-success" style="margin-left:8px">No</a>
    </div>
    <?php endif; ?>
</div>

<?=template_footer()?>