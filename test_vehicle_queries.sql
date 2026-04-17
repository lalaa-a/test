-- Test the updated queries
USE tripingoo;

-- Test getPendingVehicles query (vehicles with no verification OR status = 'pending')
SELECT 'Pending Vehicles Query:' as Test;
SELECT
    dv.vehicleId AS id,
    dv.driverName AS owner_name,
    dv.licensePlate AS registration_number,
    COALESCE(vv.status, 'NO_RECORD') AS verification_status
FROM `driver_vehicles_view` dv
LEFT JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
WHERE vv.id IS NULL OR vv.status = 'pending'
ORDER BY dv.vehicleCreatedAt DESC;

-- Test getVerifiedVehicles query
SELECT 'Verified Vehicles Query:' as Test;
SELECT
    dv.vehicleId AS id,
    dv.driverName AS owner_name,
    dv.licensePlate AS registration_number,
    vv.status AS verification_status
FROM `driver_vehicles_view` dv
INNER JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
WHERE vv.status = 'approved'
ORDER BY vv.reviewedAt DESC;

-- Test getRejectedVehicles query
SELECT 'Rejected Vehicles Query:' as Test;
SELECT
    dv.vehicleId AS id,
    dv.driverName AS owner_name,
    dv.licensePlate AS registration_number,
    vv.status AS verification_status
FROM `driver_vehicles_view` dv
INNER JOIN vehicle_verifications vv ON dv.vehicleId = vv.vehicleId
WHERE vv.status = 'rejected'
ORDER BY vv.reviewedAt DESC;
