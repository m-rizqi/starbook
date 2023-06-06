<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $review = isset($_POST['review']) ? $_POST['review'] : '';
        $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
        $customer_id = intval($customer_id);
        $book_isbn = isset($_POST['book_isbn']) ? $_POST['book_isbn'] : '';
        $stmt = $pdo->prepare('UPDATE review SET review = ?, book_isbn = ?, customer_id = ? WHERE id = ?');
        $stmt->execute([$review, $book_isbn, $customer_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM review WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$review) {
        exit('Review doesn\'t exist with that id!');
    }
   
    $stmt = $pdo->prepare('SELECT * FROM book');
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM customer');
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Review #<?=$review['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$review['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$review['id']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="book_isbn">Book</label>
            <select name="book_isbn" id="book_isbn" class="form-select" required>
                <?php
                foreach ($books as $book) {
                    $book_isbn = $book['isbn'];
                    $book_title = $book['title'];

                    $selected = '';
                    $is_selected = $book_isbn == $review['book_isbn'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$book_isbn\" $selected>$book_title</option>";
                }
                ?>
            </select>
            <a href="../book/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Book</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <?php
                foreach ($customers as $customer) {
                    $customer_id = $customer['id'];
                    $customer_name = $customer['name'];

                    $selected = '';
                    $is_selected = $customer_id == $review['customer_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$customer_id\" $selected>$customer_name</option>";
                }
                ?>
            </select>
            <a href="../customer/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Customer</a>
        </div>
        <div class="form-group mt-3">
            <label for="name">Review</label>
            <textarea name="review" id="review" placeholder="Good" class="form-control" rows="5" required><?=$review['review']?></textarea>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>