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
$stmt = $pdo->prepare('SELECT * FROM author '.$search_query.' ORDER BY id LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_authors = $pdo->query('SELECT COUNT(*) FROM author')->fetchColumn();

?>


<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Authors</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Author</a>
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
                <th scope="col">About</th>
                <th scope="col">Email</th>
                <th scope="col">Photo</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authors as $author): ?>
            <tr>
                <th scope="row"><?=$author['id']?></th>
                <td><?=$author['name']?></td>
                <td><?=$author['about']?></td>
                <td><?=$author['email']?></td>
                <td>
                    <img src="<?=$author['photo_url']?>" alt="Author Photo" class="img-thumbnail"/>
                </td>
                <td><?=$author['last_update']?></td>
                <td>
                    <a href="update.php?id=<?=$author['id']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?=$author['id']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_authors): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>