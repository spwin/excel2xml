<!DOCTYPE html>
<html lang="en">
<head>
    <title>Excel to xml converter</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<?php
if(isset($_FILES['upfile'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once dirname(__FILE__) . '/private/converter.php';
    require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

    $converter = new Converter();
    $error = $converter->checkFile($_FILES['upfile']);
    if (!$error) {
        $converter->makeXML();
    } else {
        echo $error;
    }
}
?>

<form action="" method="post" enctype="multipart/form-data">
    Select Excel file:
    <input type="file" name="upfile" id="upfile">
    <input type="submit" value="Bake me XML" name="submit">
</form>

</body>
</html>
