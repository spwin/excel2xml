<!DOCTYPE html>
<html lang="en">
<head>
    <title>Excel to xml converter</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body{
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
        }
        .row{
            display: block;
        }
        .row label{
            cursor: pointer;
        }
        form{
            margin-top: 100px;
        }
        input{
            display: inline-block;
        }
        .button {
            background-color: #4CAF50;
            border: none;
            color: white;
            margin-top: 20px;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button:hover {
            cursor: pointer;
            background-color: #3d8d41;
        }
        label.main{
            margin-bottom: 20px;
            display: block;
            font-size: 20px;
        }
        .mt-20px{
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <form action="" method="post" enctype="multipart/form-data">
        <label class="main">Select Excel file:</label>
        <input type="file" name="upfile" id="upfile">
        <div class="row mt-20px">
            <input id="studentai" checked type="radio" name="type" value="studentai">
            <label for="studentai">Studentai</label>
            <input id="darbuotojai" type="radio" name="type" value="darbuotojai">
            <label for="darbuotojai">Darbuotojai</label>
        </div>
        <div class="row">
            <input type="submit" class="button" value="Bake me XML" name="submit">
        </div>
    </form>

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
            $converter->makeXML($_POST['type']);
        } else {
            echo $error;
        }
    }
    ?>

</body>
</html>
