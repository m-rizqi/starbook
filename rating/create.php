<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $rating = isset($_POST['rating']) ? $_POST['rating'] : '';
    $rating = floatval($rating);
    $customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '';
    $customer_id = intval($customer_id);
    $book_isbn = isset($_POST['book_isbn']) ? $_POST['book_isbn'] : '';
    $stmt = $pdo->prepare('INSERT INTO rating (rating, customer_id, book_isbn) VALUES (?, ?, ?)');
    $stmt->execute([$rating, $customer_id, $book_isbn]);
    $msg = 'Rating Created!';
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
	<h2>Create New Rating</h2>
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
            <label for="name">Rating</label>
            <input name="rating" id="rating" placeholder="10" class="form-control" type="number" step="0.1" min="0" max="10" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>