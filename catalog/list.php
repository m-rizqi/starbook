<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE book.title LIKE \'%'.$search.'%\' OR store.name LIKE \'%'.$search.'%\'';
}

$stmt = $pdo->prepare('SELECT catalog.*, store.name AS store_name, book.title AS book_title, book.image_url AS book_image FROM catalog JOIN store ON store.id = catalog.store_id JOIN book ON book.isbn = catalog.book_isbn '.$search_query.' ORDER BY catalog.id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$catalogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_catalogs = $pdo->query('SELECT COUNT(*) FROM catalog')->fetchColumn();

?>


<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Catalog</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Catalog</a>
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
                <th scope="col">Book</th>
                <th scope="col">Store</th>
                <th scope="col">Price</th>
                <th scope="col">Stock</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($catalogs as $catalog): ?>
            <tr>
                <th scope="row"><?=$catalog['id']?></th>
                <td>
                    <div style="display:flex; flex-direction:column">
                        <img src="<?=$catalog['book_image']?>" alt="Book Cover" class="img-thumbnail">
                        <a href="../book/update.php?isbn=<?=$catalog['book_isbn']?>" style="display: block; height: 100%; width:100%; text-decoration: none"><?=$catalog['book_title']?></a>
                    </div>
                </td>
                <td><a href="../store/update.php?id=<?=$catalog['store_id']?>"><?=$catalog['store_name']?></a></td>
                <td><?=$catalog['price']?></td>
                <td><?=$catalog['stock']?></td>
                <td><?=$catalog['last_update']?></td>
                <td>
                    <a href="update.php?id=<?=$catalog['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$catalog['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_catalogs): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>