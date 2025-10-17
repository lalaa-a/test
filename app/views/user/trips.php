<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trips</title>

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
<body>
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>

    <main>
        <h1>My Trips</h1>
        
        <!-- Create Trip Button -->
        <?php renderComponent('tripPlanInit','createTripButt',[]); ?>
        
        <!-- Display Cards -->
        <?php renderComponent('tripPlanInit','displayCard',[]); ?>
        
        <!-- Create Trip Popup -->
        <?php renderComponent('tripPlanInit','create_trip_pop',[]); ?>
        
        <!-- Completed Cards -->
        <?php renderComponent('tripPlanInit','completedCard',[]); ?>
    </main>
    
    <?php renderComponent('inc','footer',[]); ?>
</body>
</html>
