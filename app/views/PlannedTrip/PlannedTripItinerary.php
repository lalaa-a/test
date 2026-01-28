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
            font-family: 'Geologica', Arial, sans-serif;
            background: #fff;
            color: #111;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(10px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
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
        addAssets('plannedTrip','addGuidePopCard');
        addAssets('plannedTrip','driverSelection');
        addAssets('plannedTrip','bookingPayment');
        printAssets();
    ?>

    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;500;600;700;800&display=swap" rel="stylesheet">

</head>
<body >
    
    <!--navigation bar-->
    <?php renderComponent('inc','navigation',[]); ?>
    <div style="padding: 20px;"></div>

    <?php renderComponent('plannedTrip','heroCard',[]); ?>
    <?php renderComponent('plannedTrip','navBar',[]); ?>
    <?php renderComponent('plannedTrip','dateChipBox',[]); ?>
    
    <?php renderComponent('plannedTrip','dayTimeLine',[]); ?>
    
    <!-- Trip Action Buttons -->
    <div style="padding: 40px 0; text-align: center;">
        <div style="background: #f8fffe; padding: 30px; border-radius: 16px; margin: 20px 0; border: 1px solid #e0f2f2;">
            <h3 style="margin: 0 0 16px 0; color: #006A71; font-size: 20px; font-weight: 600;">Complete Your Trip</h3>
            <p style="margin: 0 0 24px 0; color: #666; font-size: 14px;">Add a driver and proceed with payment to confirm your booking</p>
            
            <div style="display: flex; gap: 20px; justify-content: center; align-items: center; flex-wrap: wrap;">
                <button id="openDriverSelection" type="button" style="
                    background: teal; 
                    color: white; 
                    border: none; 
                    padding: 12px 28px; 
                    border-radius: 50px; 
                    font-size: 16px; 
                    font-weight: 600; 
                    cursor: pointer; 
                    font-family: 'Geologica', Arial, sans-serif;
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                " onmouseover="this.style.background='#006666'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='teal'; this.style.transform='translateY(0)'">
                    Add Driver
                </button>
                
                <button id="openBookingPayment" type="button" style="
                    background: #28a745; 
                    color: white; 
                    border: none; 
                    padding: 12px 28px; 
                    border-radius: 50px; 
                    font-size: 16px; 
                    font-weight: 600; 
                    cursor: pointer; 
                    font-family: 'Geologica', Arial, sans-serif;
                    transition: all 0.3s ease;
                    display: none;
                    align-items: center;
                    gap: 8px;
                    opacity: 0;
                " onmouseover="this.style.background='#218838'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#28a745'; this.style.transform='translateY(0)'">
                    Pay & Book
                </button>
            </div>
        </div>
    </div>
    
    <?php renderComponent('plannedTrip','addPlacePopCard',[]); ?>
    <?php renderComponent('plannedTrip','addLocationPopCard',[]); ?>
    <?php renderComponent('plannedTrip','addGuidePopCard',[]); ?>
    <?php renderComponent('plannedTrip','driverSelection',[]); ?>
    <?php renderComponent('plannedTrip','bookingPayment',[]); ?>
    
    <?php renderComponent('inc','footer',[]); ?>
</body>
</html>