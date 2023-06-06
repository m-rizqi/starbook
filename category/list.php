<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE name LIKE \'%'.$search.'%\'';
}

$stmt = $pdo->prepare('SELECT * FROM category '.$search_query.' ORDER BY id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_categories = $pdo->query('SELECT COUNT(*) FROM category')->fetchColumn();

?>


<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Categories</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Category</a>
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
                <th scope="col">Name</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <th scope="row"><?=$category['id']?></th>
                <td><?=$category['name']?></td>
                <td><?=$category['last_update']?></td>
                <td>
                    <a href="update.php?id=<?=$category['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$category['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_categories): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>