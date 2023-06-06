<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
if (isset($_GET['isbn'])) {
    if (!empty($_POST)) {
        $is_image_empty = is_image_empty($_FILES["image"]);
        $image_query = ' ';
        if(!$is_image_empty){
            list($image_save_status, $message) = save_image("book/",$_FILES["image"]);
            if ($image_save_status){
                $image_query = ', image_url = \'' . $message . '\' ';
            };
        }

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

        $stmt = $pdo->prepare('UPDATE book SET title = ?, overview = ?, publisher_id = ?, published_year = ?, author_id = ?, language_code = ?, num_pages = ?, category_id = ?' . $image_query . 'WHERE isbn = ?');
        $stmt->execute([$title, $overview, $publisher_id, $published_year, $author_id, $language_code, $num_pages, $category_id, $_GET['isbn']]);
        $msg = 'Updated Successfully!';
    }
    $stmt = $pdo->prepare('SELECT * FROM book WHERE isbn = ?');
    $stmt->execute([$_GET['isbn']]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        exit('Book doesn\'t exist with that isbn!');
    }

    $stmt = $pdo->prepare('SELECT publisher.* FROM publisher LEFT JOIN book ON publisher.id = book.publisher_id WHERE book.publisher_id IS NULL OR publisher.id = ? ORDER BY publisher.id');
    $stmt->execute([$book['publisher_id']]);
    $publishers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT author.* FROM author LEFT JOIN book ON author.id = book.author_id WHERE book.author_id IS NULL OR author.id = ? ORDER BY author.id');
    $stmt->execute([$book['author_id']]);
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT language.* FROM language LEFT JOIN book ON language.code = book.language_code WHERE book.language_code IS NULL OR language.code = ? ORDER BY language.code');
    $stmt->execute([$book['language_code']]);
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare('SELECT category.* FROM category LEFT JOIN book ON category.id = book.category_id WHERE book.category_id IS NULL OR category.id = ? ORDER BY category.id');
    $stmt->execute([$book['category_id']]);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    exit('No isbn specified!');
}
?>

<?=template_header()?>
<div class="container" style="margin-top: 20px;  margin-bottom:20px">
	<h2>Update Book #<?=$book['isbn']?></h2>
    <hr>
	<form action="update.php?isbn=<?=$book['isbn']?>" method="post" enctype="multipart/form-data">
    <form action="create.php" method="post" enctype="multipart/form-data">
        <div class="form-group mt-3">
            <label for="isbn">ISBN</label>
            <input name="isbn" id="isbn" placeholder="9876543210" class="form-control-plaintext" maxlength="20" value="<?=$book['isbn']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="name">Title</label>
            <input name="title" id="title" placeholder="Book Title" class="form-control" maxlength="255" value="<?=$book['title']?>" required/>
        </div>
        <div class="form-group mt-3">
            <label for="overview">Overview</label>
            <textarea name="overview" id="overview" class="form-control" rows="5" required><?=$book['overview']?></textarea>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="publisher_id">Publisher</label>
            <select name="publisher_id" id="publisher_id" class="form-select" required>
                <?php
                foreach ($publishers as $publisher) {
                    $publisher_id = $publisher['id'];
                    $publisher_name = $publisher['name'];

                    $selected = '';
                    $is_selected = $publisher_id == $book['publisher_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$publisher_id\" $selected>$publisher_name</option>";
                }
                ?>
            </select>
            <a href="../publisher/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Publisher</a>
        </div>
        <div class="form-group mt-3">
            <label for="published_year">Published Year</label>
            <input name="published_year" id="published_year" class="form-control" type="number" min="1000" max="9999" step="1" maxlength="4" value="<?=$book['published_year']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="author_id">Author</label>
            <select name="author_id" id="author_id" class="form-select" required>
                <?php
                foreach ($authors as $author) {
                    $author_id = $author['id'];
                    $author_name = $author['name'];

                    $selected = '';
                    $is_selected = $author_id == $book['author_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$author_id\" $selected>$author_name</option>";
                }
                ?>
            </select>
            <a href="../author/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Author</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="language_code">Language</label>
            <select name="language_code" id="language_code" class="form-select" required>
                <?php
                foreach ($languages as $language) {
                    $language_code = $language['code'];
                    $language_name = $language['name'];

                    $selected = '';
                    $is_selected = $language_code == $book['language_code'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$language_code\" $selected>$language_name</option>";
                }
                ?>
            </select>
            <a href="../language/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Language</a>
        </div>
        <div class="form-group mt-3">
            <label for="num_pages">Number of Pages</label>
            <input name="num_pages" id="num_pages" class="form-control" type="number" step="1" value="<?=$book['num_pages']?>" required/>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <?php
                foreach ($categories as $category) {
                    $category_id = $category['id'];
                    $category_name = $category['name'];

                    $selected = '';
                    $is_selected = $category_id == $book['category_id'];
                    if ($is_selected){
                        $selected = 'selected';
                    }

                    echo "<option value=\"$category_id\" $is_selected>$category_name</option>";
                }
                ?>
            </select>
            <a href="../category/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Category</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="image">Image</label>
            <img src="<?=$book['image_url']?>" alt="Manager Photo" class=".img-fluid w-25">
            <input name="image" id="image" placeholder="image" class="form-control mt-1" type="file">
            <?php if (isset($image_save_status) && !$image_save_status): ?>
                <div class="alert alert-danger mt-1" role="alert">
                    <?=$message?>
                </div>    
            <?php endif; ?>
        </div>
        <input type="submit" class="btn btn-dark mt-3" value="Update">
    </form>
</div>
<?=template_footer()?>