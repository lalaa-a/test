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
        addAssets('plannedTrip','heroCard');
        addAssets('plannedTrip','navBar');
        addAssets('plannedTrip','dateChipBox');
        addAssets('plannedTrip','itineLocationItem');
        addAssets('plannedTrip','itinePlaceItem');
        addAssets('plannedTrip','dayTimeLine');
        addAssets('plannedTrip','addPlacePopCard');
        addAssets('plannedTrip','addLocationPopCard');
        printAssets();
    ?>

</head>
<body >
    
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>
    <div style="padding: 20px;"></div>

    <?php renderComponent('plannedTrip','heroCard',[]); ?>
    <?php renderComponent('plannedTrip','navBar',[]); ?>
    <?php renderComponent('plannedTrip','dateChipBox',[]); ?>
    
    <?php renderComponent('plannedTrip','dayTimeLine',[]); ?>
    <?php renderComponent('plannedTrip','addPlacePopCard',[]); ?>
    <?php renderComponent('plannedTrip','addLocationPopCard',[]); ?>
    
    <?php renderComponent('inc','footer',[]); ?>



</body>
</html>