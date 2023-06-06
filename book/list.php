<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if(!empty($search)){
    $search_query = 'WHERE title LIKE \'%'.$search.'%\' OR author.name LIKE \'%'.$search.'%\' OR publisher.name LIKE \'%'.$search.'%\'';
}

$stmt = $pdo->prepare('SELECT book.*, publisher.name AS publisher_name, author.name AS author_name, language.name AS language_name, category.name as category_name FROM book JOIN publisher ON publisher.id = book.publisher_id JOIN author ON author.id = book.author_id JOIN language ON language.code = book.language_code JOIN category ON category.id = book.category_id '.$search_query.' ORDER BY book.isbn LIMIT :record_per_page OFFSET :offset');
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->execute();

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
$num_books = $pdo->query('SELECT COUNT(*) FROM book')->fetchColumn();

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom:20px">
	<h2>Books</h2>
    <hr>
	<a href="./create.php" type="button" class="btn btn-dark" style="margin-top: 24px">Create Book</a>
	<form method="GET" action="list.php">
        <div class="input-group mt-3">
            <input type="text" name="search" class="form-control" placeholder="Search...">
            <button type="submit" class="btn btn-outline-dark">Search</button>
        </div>
    </form>
	<table class="table table-hover mt-1">
        <thead class="bg-light">
            <tr>
                <th scope="col">ISBN</th>
                <th scope="col">Title</th>
                <th scope="col">Overview</th>
                <th scope="col">Image</th>
                <th scope="col">Publisher</th>
                <th scope="col">Published Year</th>
                <th scope="col">Author</th>
                <th scope="col">Language</th>
                <th scope="col">Number of Pages</th>
                <th scope="col">Category</th>
                <th scope="col">Last Update</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $book): ?>
            <tr>
                <th scope="row"><?=$book['isbn']?></th>
                <td><?=$book['title']?></td>
                <td><?=$book['overview']?></td>
                <td>
                    <img src="<?=$book['image_url']?>" alt="Manager Photo" class="img-thumbnail">
                </td>
                <td><a href="../publisher/update.php?id=<?=$book['publisher_id']?>"><?=$book['publisher_name']?></a></td>
                <td><?=$book['published_year']?></td>
                <td><a href="../author/update.php?id=<?=$book['author_id']?>"><?=$book['author_name']?></a></td>
                <td><a href="../language/update.php?code=<?=$book['language_code']?>"><?=$book['language_name']?></a></td>
                <td><?=$book['num_pages']?></td>
                <td><a href="../category/update.php?id=<?=$book['category_id']?>"><?=$book['category_name']?></a></td>
                <td><?=$book['last_update']?></td>
                <td>
                    <a href="update.php?isbn=<?=$book['isbn']?>" class="btn btn-primary"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?isbn=<?=$book['isbn']?>" class="btn btn-danger"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div style="display: flex; justify-content:flex-end;">
		<?php if ($page > 1): ?>
		<a href="list.php?page=<?=$page-1?>" class="btn btn-secondary" style="margin-right:8px"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $num_books): ?>
		<a href="list.php?page=<?=$page+1?>" class="btn btn-secondary" style="margin-left:8px"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>
<?=template_footer()?>