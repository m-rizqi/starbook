<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $price = intval($price);
        $stock = isset($_POST['stock']) ? $_POST['stock'] : '';
        $stock = intval($stock);
        $store_id = isset($_POST['store_id']) ? $_POST['store_id'] : '';
        $store_id = intval($store_id);
        $book_isbn = isset($_POST['book_isbn']) ? $_POST['book_isbn'] : '';
        $stmt = $pdo->prepare('UPDATE catalog SET price = ?, stock = ?, book_isbn = ?, store_id = ? WHERE id = ?');
        $stmt->execute([$price, $stock, $book_isbn, $store_id, $_GET['id']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM catalog WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $catalog = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$catalog) {
        exit('Catalog doesn\'t exist with that id!');
    }
   
    $stmt = $pdo->prepare('SELECT * FROM book');
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT * FROM store');
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No id specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Update Catalog #<?=$catalog['id']?></h2>
    <hr>
	<form action="update.php?id=<?=$catalog['id']?>" method="post">
        <div class="form-group">
            <label for="id">Id</label>
            <input type="text" name="id" class="form-control-plaintext" placeholder="en" id="id" value="<?=$catalog['id']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="book_isbn">Book</label>
            <select name="book_isbn" id="book_isbn" class="form-select" required>
                <?php
                foreach ($books as $book) {
                    $book_isbn = $book['isbn'];
                    $book_title = $book['title'];

                    $selected = '';
                    $is_selected = $book_isbn == $catalog['book_isbn'];
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
            <label for="store_id">Store</label>
            <select name="store_id" id="store_id" class="form-select" required>
                <?php
                foreach ($stores as $store) {
                    $store_id = $store['id'];
                    $store_name = $store['name'];

                    $selected = '';
                    $is_selected = $store_id == $catalog['store_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$store_id\" $selected>$store_name</option>";
                }
                ?>
            </select>
            <a href="../store/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Store</a>
        </div>
        <div class="form-group mt-3">
            <label for="name">Price</label>
            <input name="price" id="price" placeholder="100000" class="form-control" type="number" step="1" min="0" value="<?=$catalog['price']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Stock</label>
            <input name="stock" id="stock" placeholder="10" class="form-control" type="number" step="1" min="0" value="<?=$catalog['stock']?>" required/>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>