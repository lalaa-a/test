<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            color: #111;
        }
    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        addAssets('homepage','body');
        printAssets();
    ?>
</head>
<body>
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <?php renderComponent('homepage','body',[]); ?>
    
    <?php renderComponent('inc','footer',[]); ?>
</body>
</html>
