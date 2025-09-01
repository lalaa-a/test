<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>

        body {
            padding: 0 150px; /* adds 40px left and right margin */
            border: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            color: #111;
        }

    </style>

    <?php
        include APP_ROOT.'/libraries/Functions.php';
        addAssets('inc','navigation');
        addAssets('inc','footer');
        addAssets('tripPlanInit','displayCard');
        addAssets('tripPlanInit','createTripButt');
        addAssets('tripPlanInit','create_trip_pop');
        addAssets('tripPlanInit','completedCard');
        printAssets();
    ?>

</head>
<body >
    
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>
    <h1 class="tpc-section-title">My Trips</h1>
    <?php renderComponent('tripPlanInit','createTripButt'); ?>
    <h2 class="tpc-section-title">Ongoing</h2>
    <?php renderComponent('tripPlanInit','displayCard',[]); ?>
    <?php renderComponent('tripPlanInit','displayCard',[]); ?>
    <?php renderComponent('tripPlanInit','displayCard',[]); ?>
    <?php renderComponent('tripPlanInit','create_trip_pop',[]); ?>

    <div style="display:flex;justify-content:space-between;margin:10px" ;>
        <?php renderComponent('tripPlanInit','completedCard',[]); ?>
        <?php renderComponent('tripPlanInit','completedCard',[]); ?>
    </div>
    
    <?php renderComponent('inc','footer',[]); ?>



</body>
</html>