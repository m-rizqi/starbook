<?php
include '../functions.php';
$pdo = pdo_connect_postgresql();
$msg = '';
$store_id = isset($_GET['store_id']) && is_numeric($_GET['store_id']) ? (int)$_GET['store_id'] : -1;
$staff_id = isset($_GET['staff_id']) && is_numeric($_GET['staff_id']) ? (int)$_GET['staff_id'] : -1;
$customer_id = isset($_GET['customer_id']) && is_numeric($_GET['customer_id']) ? (int)$_GET['customer_id'] : -1;
$catalogs_id = isset($_GET['catalogs_id']) ? $_GET['catalogs_id'] : '-1';

$is_valid_param = true;
$stmt = $pdo->prepare('SELECT * FROM store WHERE id = ?');
$stmt->execute([$store_id]);
$store = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT catalog.*, book.image_url AS book_image, book.title AS book_title FROM catalog JOIN book ON book.isbn = catalog.book_isbn WHERE id = ANY (STRING_TO_ARRAY(?, \'.\')::integer[])');
$stmt->execute([$catalogs_id]);
$catalogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    $pdo->beginTransaction();
    try {
            foreach($catalogs as $catalog){
                $amount = $_POST['amount_'.$catalog['id']];
                if($amount > $catalog['stock']){
                    throw new PDOException('The number of purchases of catalog '.$catalog['id'].' exceeds the stock');
                }

                $stmt1 = $pdo->prepare("INSERT INTO sale (catalog_id, customer_id, staff_id, store_id, amount, price_used) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt1->execute([$catalog['id'], $customer_id, $staff_id, $store_id, $amount, $catalog['price']]);

                $stock = $catalog['stock'] - $amount;
                $stmt2 = $pdo->prepare("UPDATE catalog SET stock = ? WHERE id = ?");
                $stmt2->execute([$stock, $catalog['id']]);

            }
        $pdo->commit();
        echo "Transaction committed successfully.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $msg = "Transaction rolled back. Error: " . $e->getMessage();
    }
}

?>

<?=template_header()?>
<div class="container" style="margin-top: 20px">
	<h2>Create New Sale - Step 3 #Lasr</h2>
    <hr>
    <?php if (empty($catalogs)): ?>
        <div class="alert alert-danger mt-1" role="alert">
            Please, select at least one catalog in previous step!
        </div>
    <?php else: ?> 
        <?php if (!empty($msg)): ?>
            <div class="alert alert-danger mt-1" role="alert">
                <?=$msg?>
            </div>    
        <?php endif; ?>
        <form action="create_step3.php?store_id=<?=$store_id?>&staff_id=<?=$staff_id?>&customer_id=<?=$customer_id?>&catalogs_id=<?=$catalogs_id?>" method="post">
            <table class="table table-hover" style="margin-top:16px">
                <thead class="bg-light">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Book</th>
                        <th scope="col">Store</th>
                        <th scope="col">Price</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Amount</th>
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
                            <div class="form-group">
                                <input name="amount_<?=$catalog['id']?>" id="amount" placeholder="1" class="form-control" type="number" step="1" min="1" max="<?=$catalog
                                ['stock']?>" required/>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <input type="submit" class="btn btn-dark mt-3 mb-3" value="Submit">
    </form>
    <?php endif; ?>
</div>
<?=template_footer()?>