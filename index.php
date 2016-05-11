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
        .filialai label{
            display: block;
            margin-top: 15px;
        }
        .filialai .row{
            margin-top: 10px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

    <form action="" method="post" enctype="multipart/form-data">
        <label class="main">Select Excel file:</label>
        <input type="file" name="upfile" id="upfile">
        <div class="filialai row mt-20px">
            <label for="bendras">Administracinė bibliotekėlė:</label>
            <div class="row">
                <input id="bendras" type="text" value="KK550" name="bendras" required>
            </div>
            <label for="filialas[]">Bibliotekos filialai:</label>
            <div class="row">
                <div class="row">
                    <input id="filialas_1" type="text" value="KK5CB" name="filialas[]" required>
                </div>
            </div>
            <div class="items" id="items"></div>
            <div class="row">
                <button id="add_filialas">Pridėti filialą</button>
            </div>
        </div>
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
        $validate = $converter->validateInput($_POST);
        $error = $converter->checkFile($_FILES['upfile']);
        if (!$error && $validate['pass']) {
            $converter->makeXML($_POST['type']);
        } else {
            echo $validate['error'];
            echo $error;
        }
    }
    ?>

    <div id="new_filialas" class="hidden">
        <div class="row">
            <input type="text" value="" name="filialas[]">
            <button id="remove" onclick="this.parentNode.parentNode.removeChild(this.parentNode);">Pašalinti</button>
        </div>
    </div>

    <script type="text/javascript">
        (function() {
            document.getElementById("add_filialas").onclick = function(event){
                event.preventDefault();
                var theDiv = document.getElementById("items");
                theDiv.appendChild(document.getElementById("new_filialas").firstElementChild.cloneNode(true));
            };
        })();
    </script>
</body>
</html>
