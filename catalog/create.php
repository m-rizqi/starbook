<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $price = intval($price);
    $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
    $stock = intval($stock);
    $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '';
    $store_id = intval($store_id);
    $book_isbn = isset($_POST['book_isbn']) ? $_POST['book_isbn'] : '';
    $stmt = $pdo->prepare('INSERT INTO catalog (price, stock, store_id, book_isbn) VALUES (?, ?, ?, ?)');
    $stmt->execute([$price, $stock, $store_id, $book_isbn]);
    $msg = 'Catalog Created!';
}

$stmt = $pdo->prepare('SELECT * FROM book');
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM store');
$stmt->execute();
$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Create New Catalog</h2>
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
            <label for="store_id">Store</label>
            <select name="store_id" id="store_id" class="form-select" required>
                <option value="">Select a store</option>
                <?php
                foreach ($stores as $store) {
                    $store_id = $store['id'];
                    $store_name = $store['name'];
                    echo "<option value=\"$store_id\">$store_name</option>";
                }
                ?>
            </select>
            <a href="../store/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Store</a>
        </div>
        <div class="form-group mt-3">
            <label for="name">Price</label>
            <input name="price" id="price" placeholder="100000" class="form-control" type="number" step="1" min="0" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Stock</label>
            <input name="stock" id="stock" placeholder="10" class="form-control" type="number" step="1" min="0" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>