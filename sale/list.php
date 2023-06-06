<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE book.title LIKE \'%'.$search.'%\' OR customer.name LIKE \'%'.$search.'%\' OR staff.name LIKE \'%'.$search.'%\' OR store.name LIKE \'%'.$search.'%\'';
}
if(is_numeric($search)){
    $search_query = $search_query . ' OR sale.amount = '.intval($search).' OR sale.price_used = '.intval($search);
}

$stmt = $pdo->prepare('SELECT sale.*, book.image_url AS book_image, book.title AS book_title, book.isbn AS book_isbn, customer.name AS customer_name, staff.name AS staff_name, store.name AS store_name FROM sale JOIN catalog ON catalog.id = sale.catalog_id JOIN book ON book.isbn = catalog.book_isbn JOIN customer ON customer.id = sale.customer_id JOIN staff ON staff.id = sale.staff_id JOIN store ON store.id = staff.store_id '.$search_query.' ORDER BY sale.id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_sales = $pdo->query('SELECT COUNT(*) FROM sale')->fetchColumn();

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Sale</h2>
    <hr>
	<a href="./create_step1.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Sale</a>
	<form method="GET" action="list.php">
        <div class="input-group mt-3">
            <input type="text" name="search" class="form-control" placeholder="Search...">
            <button type="submit" class="btn btn-outline-dark">Search</button>
        </div>
    </form>
	<table class="table table-hover mt-1">
        <thead class="bg-light">
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Sale Id</th>
                <th scope="col">Book</th>
                <th scope="col">Customer</th>
                <th scope="col">Staff</th>
                <th scope="col">Store</th>
                <th scope="col">Amount</th>
                <th scope="col">Price Used</th>
                <th scope="col">Total</th>
                <th scope="col">Created At</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $sale): ?>
            <tr>
                <th scope="row"><?=$sale['id']?></th>
                <td><a href="../catalog/update.php?id=<?=$sale['catalog_id']?>"><?=$sale['catalog_id']?></a></td>
                <td>
                    <div style="display:flex; flex-direction:column">
                        <img src="<?=$sale['book_image']?>" alt="Book Cover" class="img-thumbnail">
                        <a href="../book/update.php?isbn=<?=$sale['book_isbn']?>" style="display: block; height: 100%; width:100%; text-decoration: none"><?=$sale['book_title']?></a>
                    </div>
                </td>
                <td><a href="../customer/update.php?id=<?=$sale['customer_id']?>"><?=$sale['customer_name']?></a></td>
                <td><a href="../staff/update.php?id=<?=$sale['staff_id']?>"><?=$sale['staff_name']?></a></td>
                <td><a href="../store/update.php?id=<?=$sale['store_id']?>"><?=$sale['store_name']?></a></td>
                <td><?=$sale['amount']?></td>
                <td><?=$sale['price_used']?></td>
                <td><?=$sale['amount']*$sale['price_used']?></td>
                <td><?=$sale['created_at']?></td>
                <td>
                    <!-- <a href="update.php?id=<?=$sale['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a> -->
                    <a href="delete.php?id=<?=$sale['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_sales): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>