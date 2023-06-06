<?php
include 'functions.php';
?>

<?=template_header()?>

<div class="container">
	<div class="list-group mt-3">
	<a class="list-group-item bg-dark text-white">Menu</a>
	<a href="./address/list.php" class="list-group-item list-group-item-action">Addresses</a>
	<a href="./author/list.php" class="list-group-item list-group-item-action">Authors</a>
	<a href="./book/list.php" class="list-group-item list-group-item-action">Books</a>
	<a href="./catalog/list.php" class="list-group-item list-group-item-action">Catalogs</a> 
	<a href="./category/list.php" class="list-group-item list-group-item-action">Categories</a>
	<a href="./customer/list.php" class="list-group-item list-group-item-action">Customers</a>
	<a href="./language/list.php" class="list-group-item list-group-item-action">Languages</a>
	<a href="./manager/list.php" class="list-group-item list-group-item-action">Managers</a>
	<a href="./publisher/list.php" class="list-group-item list-group-item-action">Publishers</a>
	<a href="./rating/list.php" class="list-group-item list-group-item-action">Ratings</a> 
	<a href="./review/list.php" class="list-group-item list-group-item-action">Reviews</a> 
	<a href="./sale/list.php" class="list-group-item list-group-item-action">Sales</a> 
	<a href="./staff/list.php" class="list-group-item list-group-item-action">Staffs</a>
	<a href="./store/list.php" class="list-group-item list-group-item-action">Stores</a>

	<a href="./sale/create_step1.php" type="button" class="btn btn-dark mt-3 mb-3">Create New Sales</a>
	</div>
</div>

<?=template_footer()?>