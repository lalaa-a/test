<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <title>Document</title>

    <style>

        body {
            font-family: 'Geologica';
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
<body >
    
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <?php renderComponent('homepage','body',[]); ?>
    
    <?php renderComponent('inc','footer',[]); ?>

    
    
    



</body>
</html>