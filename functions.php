<?php
global $base_url, $image_dir;
$base_url = '4.193.113.8/starbook/';
$image_dir = 'images/';
function pdo_connect_postgresql() {
    $DATABASE_HOST = '4.193.113.8';
    $DATABASE_PORT = '5432';
    $DATABASE_USER = 'postgres';
    $DATABASE_PASS = 'duhlupa';
    $DATABASE_NAME = 'starbook';
    $OPTIONS = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
    try {
        return new PDO('pgsql:host=' . $DATABASE_HOST . ';port=' . $DATABASE_PORT . ';dbname=' . $DATABASE_NAME, $DATABASE_USER, $DATABASE_PASS, $OPTIONS);
    } catch (PDOException $exception) {
    	exit($exception);
    }
}
function is_image_empty($image){
    return ($image['size'] == 0 || $image['error'] != 0);
}
function save_image($folder_path, $image){
    global $image_dir;
    $image_path = $image_dir . $folder_path. basename($image["name"]);
    $image_file = '../' . $image_path;
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($image_file, PATHINFO_EXTENSION));
    
    if (is_image_empty($image)){
        return array(true, null);
    }

    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        return array(false, "File is not an image.");
    }

    $allowedExtensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($image_file_type, $allowedExtensions)) {
        return array(false, "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    if ($upload_ok == 0) {
        return array(false, "Sorry, your file was not uploaded.");
    }

    if (move_uploaded_file($image["tmp_name"], $image_file)) {
        global $base_url;
        $image_url = $base_url . $image_dir . $folder_path . basename($image["name"]); 
        return array(true, $image_url);
      } else {
        return array(false, "Error uploading file: " . $_FILES['image']['error']);
      }

}
function template_header() {
    echo <<<EOT
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Starbook</title>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    </head>
    <body>
        <nav class="navbar bg-dark" data-bs-theme="dark">
            <div class="container-fluid">
            <a class="navbar-brand" href="..">Starbook</a>
            </div>
        </nav>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    EOT;
    }
function template_footer() {
    echo <<<EOT
        </body>
    </html>
    EOT;
    }
?>