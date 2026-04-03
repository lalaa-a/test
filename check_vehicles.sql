-- Check vehicles in database
USE tripingoo;

-- Check all vehicles
SELECT 'Total vehicles:' as Info, COUNT(*) as Count FROM vehicles;
SELECT 'Approved vehicles:' as Info, COUNT(*) as Count FROM vehicles WHERE isApproved = 1;
SELECT 'Pending vehicles:' as Info, COUNT(*) as Count FROM vehicles WHERE isApproved = 0;

-- Sample vehicle data
SELECT vehicleId, driverId, licensePlate, isApproved 
FROM vehicles 
LIMIT 5;

-- Check vehicle_verifications table
SELECT 'Vehicle verifications:' as Info, COUNT(*) as Count FROM vehicle_verifications;

-- Check driver_vehicles_view
SELECT 'Driver vehicles view:' as Info, COUNT(*) as Count FROM driver_vehicles_view;

-- Sample from driver_vehicles_view
SELECT vehicleId, driverName, licensePlate, vehicleApproved 
FROM driver_vehicles_view 
LIMIT 5;
