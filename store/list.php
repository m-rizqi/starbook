<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE store.name LIKE \'%'.$search.'%\' OR address.detail_address LIKE \'%'.$search.'%\' OR address.city LIKE \'%'.$search.'%\' OR address.country LIKE \'%'.$search.'%\' OR manager.name LIKE \'%'.$search.'%\'';
}

$stmt = $pdo->prepare('SELECT store.*, address.detail_address, address.city, manager.name AS manager_name FROM store JOIN address ON address.id = store.address_id JOIN manager ON manager.id = store.manager_id '.$search_query.' ORDER BY store.id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_stores = $pdo->query('SELECT COUNT(*) FROM store')->fetchColumn();

?>


<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Stores</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Store</a>
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
                <th scope="col">Address</th>
                <th scope="col">Manager</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stores as $store): ?>
            <tr>
                <th scope="row"><?=$store['id']?></th>
                <td><?=$store['name']?></td>
                <td><a href="../address/update.php?id=<?=$store['address_id']?>"><?=$store['detail_address'].', '.$store['city']?></a></td>
                <td><a href="../manager/update.php?id=<?=$store['manager_id']?>"><?=$store['manager_name']?></a></td>
                <td><?=$store['last_update']?></td>
                <td>
                    <a href="update.php?id=<?=$store['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$store['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_stores): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>