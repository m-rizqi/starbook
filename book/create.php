<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (!empty($_POST)) {
    list($image_save_status, $message) = save_image("book/",$_FILES["image"]);
    if (!$image_save_status) return;

    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $overview = isset($_POST['overview']) ? $_POST['overview'] : '';
    $publisher_id = isset($_POST['publisher_id']) ? $_POST['publisher_id'] : '';
    $publisher_id = intval($publisher_id);
    $published_year = isset($_POST['published_year']) ? $_POST['published_year'] : '';
    $published_year = intval($published_year);
    $author_id = isset($_POST['author_id']) ? $_POST['author_id'] : '';
    $author_id = intval($author_id);
    $language_code = isset($_POST['language_code']) ? $_POST['language_code'] : '';
    $num_pages = isset($_POST['num_pages']) ? $_POST['num_pages'] : '';
    $num_pages = intval($num_pages);
    $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
    $category_id = intval($category_id);
    $image_url = $message;

    $stmt = $pdo->prepare('INSERT INTO book (isbn, title, overview, image_url, publisher_id, published_year, author_id, language_code, num_pages, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$isbn, $title, $overview, $image_url, $publisher_id, $published_year, $author_id, $language_code, $num_pages, $category_id]);
    $msg = 'Book Created!';
}

$stmt = $pdo->prepare('SELECT * FROM publisher ORDER BY id');
$stmt->execute();
$publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM author ORDER BY id');
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM language ORDER BY code');
$stmt->execute();
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT * FROM category ORDER BY id');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px; margin-bottom: 20px">
	<h2>Create New Book</h2>
    <hr>
	<form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="isbn">ISBN</label>
            <input name="isbn" id="isbn" placeholder="9876543210" class="form-control" maxlength="20" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Title</label>
            <input name="title" id="title" placeholder="Book Title" class="form-control" maxlength="255" required/>
        </div>
        <div class="form-group mt-3">
            <label for="overview">Overview</label>
            <textarea name="overview" id="overview" class="form-control" rows="5" required></textarea>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="publisher_id">Publisher</label>
            <select name="publisher_id" id="publisher_id" class="form-select" required>
                <option value="">Select an publisher</option>
                <?php
                foreach ($publishers as $publisher) {
                    $publisher_id = $publisher['id'];
                    $publisher_name = $publisher['name'];
                    echo "<option value=\"$publisher_id\">$publisher_name</option>";
                }
                ?>
            </select>
            <a href="../publisher/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Publisher</a>
        </div>
        <div class="form-group mt-3">
            <label for="published_year">Published Year</label>
            <input name="published_year" id="published_year" class="form-control" type="number" min="1000" max="9999" step="1" maxlength="4" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="author_id">Author</label>
            <select name="author_id" id="author_id" class="form-select" required>
                <option value="">Select an author</option>
                <?php
                foreach ($authors as $author) {
                    $author_id = $author['id'];
                    $author_name = $author['name'];
                    echo "<option value=\"$author_id\">$author_name</option>";
                }
                ?>
            </select>
            <a href="../author/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Author</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="language_code">Language</label>
            <select name="language_code" id="language_code" class="form-select" required>
                <option value="">Select an language</option>
                <?php
                foreach ($languages as $language) {
                    $language_code = $language['code'];
                    $language_name = $language['name'];
                    echo "<option value=\"$language_code\">$language_name</option>";
                }
                ?>
            </select>
            <a href="../language/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Language</a>
        </div>
        <div class="form-group mt-3">
            <label for="num_pages">Number of Pages</label>
            <input name="num_pages" id="num_pages" class="form-control" type="number" step="1" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Select an category</option>
                <?php
                foreach ($categories as $category) {
                    $category_id = $category['id'];
                    $category_name = $category['name'];
                    echo "<option value=\"$category_id\">$category_name</option>";
                }
                ?>
            </select>
            <a href="../category/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Category</a>
        </div>
        <div class="form-group mt-3">
            <label for="image">Image</label>
            <input name="image" id="image" placeholder="image" class="form-control" type="file">
            <?php if (isset($image_save_status) && !$image_save_status): ?>
                <div class="alert alert-danger mt-1" role="alert">
                    <?=$message?>
                </div>    
            <?php endif; ?>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Create">
    </form>
</div>
<?=template_footer()?>