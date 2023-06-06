<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $review = isset($_POST['review']) ? $_POST['review'] : '';
    $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
    $customer_id = intval($customer_id);
    $book_isbn = isset($_POST['book_isbn']) ? $_POST['book_isbn'] : '';
    $stmt = $pdo->prepare('INSERT INTO review (review, customer_id, book_isbn) VALUES (?, ?, ?)');
    $stmt->execute([$review, $customer_id, $book_isbn]);
    $msg = 'Review Created!';
}

$stmt = $pdo->prepare('SELECT * FROM book');
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM customer');
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Create New Review</h2>
    <hr>
	<form action="create.php" method="post">
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="book_isbn">Book</label>
            <select name="book_isbn" id="book_isbn" class="form-select" required>
                <option value="">Select a book</option>
                <?php
                foreach ($books as $book) {
                    $book_isbn = $book['isbn'];
                    $book_title = $book['title'];
                    echo "<option value=\"$book_isbn\">$book_title</option>";
                }
                ?>
            </select>
            <a href="../book/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Book</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <option value="">Select a customer</option>
                <?php
                foreach ($customers as $customer) {
                    $customer_id = $customer['id'];
                    $customer_name = $customer['name'];
                    echo "<option value=\"$customer_id\">$customer_name</option>";
                }
                ?>
            </select>
            <a href="../customer/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Customer</a>
        </div>
        <div class="form-group mt-3">
            <label for="name">Review</label>
            <textarea name="review" id="review" placeholder="Good" class="form-control" rows="5" required></textarea>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>