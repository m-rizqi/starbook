<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['isbn'])) {
    $stmt = $pdo->prepare('SELECT * FROM book WHERE isbn = ?');
    $stmt->execute([$_GET['isbn']]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book) {
        exit('Book doesn\'t exist with that isbn!');
    }
    if (isset($_GET['confirm'])) {
        if ($_GET['confirm'] == 'yes') {
            $stmt = $pdo->prepare('DELETE FROM book WHERE isbn = ?');
            $stmt->execute([$_GET['isbn']]);
            $msg = 'You have deleted the book!';
        }
        header('Location: ./list.php');
        exit;
    }
} else {
    exit('No isbn specified!');
}
?>

<?=template_header()?>

<div class="container" style="margin-top: 20px">
	<h2>Delete Book #<?=$book['isbn']?></h2>
    <hr>
    <?php if ($msg): ?>
    <p style="margin-top: 24px"><?=$msg?></p>
    <?php else: ?>
	<p style="margin-top: 24px">Are you sure you want to delete book <b><?=$book['title']?></b>?</p>
    <div style="display: flex; justify-content:flex-start; mt-3">
        <a href="delete.php?isbn=<?=$book['isbn']?>&confirm=yes" class="btn btn-danger" style="margin-right:8px">Yes</a>
        <a href="delete.php?isbn=<?=$book['isbn']?>&confirm=no" class="btn btn-success" style="margin-left:8px">No</a>
    </div>
    <?php endif; ?>
</div>

<?=template_footer()?>