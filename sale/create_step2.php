<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
$store_id = isset($_GET['store_id']) && is_numeric($_GET['store_id']) ? (int)$_GET['store_id'] : -1;

if (!empty($_POST)) {
    if(isset($_POST['catalogs'])) {
        $selected_catalogs = $_POST['catalogs'];
        if(empty($selected_catalogs)){
            $msg = 'Select at least one catalog!';
        }else{
            $catalogs = '&catalogs_id=';
            $catalogs = $catalogs . implode('.', $selected_catalogs);
            $staff_id = $_POST['staff_id'];
            $staff_id = intval($staff_id);
            $customer_id = $_POST['customer_id'];
            $customer_id = intval($customer_id);
            header('Location: create_step3.php?store_id='.$store_id.'&staff_id='.$staff_id.'&customer_id='.$customer_id.$catalogs);
            exit;
        }
    } else {
        $msg = 'Select at least one catalog!';
    }
}

$is_valid_store_id = true;
$stmt = $pdo->prepare('SELECT * FROM store WHERE id = ?');
$stmt->execute([$store_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$store) {
    $is_valid_store_id = false;
}

$stmt = $pdo->prepare('SELECT catalog.*, book.image_url AS book_image, book.title AS book_title FROM catalog JOIN book ON book.isbn = catalog.book_isbn WHERE catalog.store_id = ?');
$stmt->execute([$store_id]);
$catalogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT staff.* FROM staff WHERE staff.store_id = ?');
$stmt->execute([$store_id]);
$staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT customer.* FROM customer WHERE customer.store_id = ?');
$stmt->execute([$store_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Sale - Step 2</h2>
    <hr>
    <?php if (!$is_valid_store_id): ?>
        <div class="alert alert-danger mt-1" role="alert">
            Invalid store id!
        </div>
    <?php else: ?> 
        <?php if (!empty($msg) || empty($selected_catalogs)): ?>
            <div class="alert alert-danger mt-1" role="alert">
                <?php if (empty($selected_catalogs)): ?>
                    Select at least one catalog!
                <?php else: ?>
                    <?=$msg?>
                <?php endif; ?>
            </div>    
        <?php endif; ?>
        <form action="create_step2.php?store_id=<?=$store_id?>" method="post">
            <table class="table table-hover" style="margin-top:16px">
                <thead class="bg-light">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Book</th>
                        <th scope="col">Store</th>
                        <th scope="col">Price</th>
                        <th scope="col">Stock</th>
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
                        <td><a href="../store/update.php?id=<?=$store_id?>"><?=$store['name']?></a></td>
                        <td><?=$catalog['price']?></td>
                        <td><?=$catalog['stock']?></td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="catalogs[]" value="<?=$catalog['id']?>" id="<?=$catalog['id']?>">
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="staff_id">Staff</label>
            <select name="staff_id" id="staff_id" class="form-select" required>
                <option value="">Select a staff</option>
                <?php
                foreach ($staffs as $staff) {
                    $staff_id = $staff['id'];
                    $staff_name = $staff['name'];
                    echo "<option value=\"$staff_id\">$staff_name</option>";
                }
                ?>
            </select>
            <a href="../staff/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Staff</a>
        </div>
        <div class="form-group mt-3" style="display: flex; flex-direction: column;">
            <label for="customer_id">Customer</label>
            <select name="customer_id" id="customer_id" class="form-select" required>
                <option value="">Select a customer</option>
                <?php
                foreach ($customers as $customer) {
                    $customer_id = $customer['id'];
                    $customer_name = $customer['name'];
                    echo "<option value=\"$customer_id\">$customer_name</option>";
                }
                ?>
            </select>
            <a href="../customer/create.php" type="button" class="btn btn-outline-dark mt-2 mb-3">Create Customer</a>
        </div>
        <input type="submit" class="btn btn-dark mt-3 mb-3" value="Next">
    </form>
    <?php endif; ?>
</div>
<?=template_footer()?>