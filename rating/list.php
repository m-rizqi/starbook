<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE customer.name LIKE \'%'.$search.'%\' OR book.title LIKE \'%'.$search.'%\'';
}
if(is_numeric($search)){
    $search_query = $search_query . ' OR rating.rating = '.floatval($search);
}

$stmt = $pdo->prepare('SELECT rating.*, customer.name AS customer_name, book.title AS book_title, book.image_url AS book_image FROM rating JOIN customer ON customer.id = rating.customer_id JOIN book ON book.isbn = rating.book_isbn '.$search_query.' ORDER BY rating.id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_ratings = $pdo->query('SELECT COUNT(*) FROM rating')->fetchColumn();

?>


<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Rating</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Rating</a>
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
                <th scope="col">Customer</th>
                <th scope="col">Rating</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ratings as $rating): ?>
            <tr>
                <th scope="row"><?=$rating['id']?></th>
                <td>
                    <div style="display:flex; flex-direction:column">
                        <img src="<?=$rating['book_image']?>" alt="Book Cover" class="img-thumbnail">
                        <a href="../book/update.php?isbn=<?=$rating['book_isbn']?>" style="display: block; height: 100%; width:100%; text-decoration: none"><?=$rating['book_title']?></a>
                    </div>
                </td>
                <td><a href="../customer/update.php?id=<?=$rating['customer_id']?>"><?=$rating['customer_name']?></a></td>
                <td><?=$rating['rating']?>/10</td>
                <td><?=$rating['last_update']?></td>
                <td>
                    <a href="update.php?id=<?=$rating['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$rating['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_ratings): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>