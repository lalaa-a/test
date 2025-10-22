<?php
class Vehicle {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Add a new vehicle
    public function addVehicle($vehicleData) {
        $sql = "INSERT INTO vehicles (driver_id, make, model, year, license_plate, color, vehicle_type, front_photo, back_photo, side_photo) 
                VALUES (:driver_id, :make, :model, :year, :license_plate, :color, :vehicle_type, :front_photo, :back_photo, :side_photo)";
        
        $this->db->query($sql);
        $this->db->bind(':driver_id', $vehicleData['driver_id']);
        $this->db->bind(':make', $vehicleData['make']);
        $this->db->bind(':model', $vehicleData['model']);
        $this->db->bind(':year', $vehicleData['year']);
        $this->db->bind(':license_plate', $vehicleData['license_plate']);
        $this->db->bind(':color', $vehicleData['color']);
        $this->db->bind(':vehicle_type', $vehicleData['vehicle_type']);
        $this->db->bind(':front_photo', $vehicleData['front_photo']);
        $this->db->bind(':back_photo', $vehicleData['back_photo']);
        $this->db->bind(':side_photo', $vehicleData['side_photo']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    // Get vehicles by driver ID
    public function getVehiclesByDriverId($driverId) {
        $sql = "SELECT * FROM vehicles WHERE driver_id = :driver_id ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':driver_id', $driverId);
        return $this->db->resultSet();
    }
    
    // Get vehicle by ID
    public function getVehicleById($vehicleId) {
        $sql = "SELECT v.*, u.fullname as driver_name, u.email as driver_email 
                FROM vehicles v 
                LEFT JOIN users u ON v.driver_id = u.id 
                WHERE v.id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $vehicleId);
        return $this->db->single();
    }
    
    // Get pending vehicles for verification
    public function getPendingVehicles() {
        $sql = "SELECT v.*, u.fullname as driver_name, u.email as driver_email 
                FROM vehicles v 
                LEFT JOIN users u ON v.driver_id = u.id 
                WHERE v.verification_status = 'pending' 
                ORDER BY v.created_at ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    // Get all vehicles for admin (with filters)
    public function getAllVehicles($status = null) {
        $sql = "SELECT v.*, u.fullname as driver_name, u.email as driver_email 
                FROM vehicles v 
                LEFT JOIN users u ON v.driver_id = u.id";
        
        if ($status) {
            $sql .= " WHERE v.verification_status = :status";
        }
        
        $sql .= " ORDER BY v.created_at DESC";
        
        $this->db->query($sql);
        
        if ($status) {
            $this->db->bind(':status', $status);
        }
        
        return $this->db->resultSet();
    }
    
    // Update vehicle verification status
    public function updateVerificationStatus($vehicleId, $status, $verifiedBy = null, $rejectionReason = null) {
        $sql = "UPDATE vehicles SET 
                verification_status = :status, 
                verified_by = :verified_by, 
                verified_at = :verified_at,
                rejection_reason = :rejection_reason
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $vehicleId);
        $this->db->bind(':status', $status);
        $this->db->bind(':verified_by', $verifiedBy);
        $this->db->bind(':verified_at', ($status !== 'pending') ? date('Y-m-d H:i:s') : null);
        $this->db->bind(':rejection_reason', $rejectionReason);
        
        return $this->db->execute();
    }
    
    // Update vehicle details
    public function updateVehicle($vehicleData) {
        // Build dynamic SQL based on which fields are provided
        $setParts = [
            'make = :make',
            'model = :model', 
            'year = :year',
            'license_plate = :license_plate',
            'color = :color',
            'vehicle_type = :vehicle_type',
            'verification_status = \'pending\'',
            'updated_at = NOW()'
        ];
        
        $bindings = [
            ':id' => $vehicleData['id'],
            ':make' => $vehicleData['make'],
            ':model' => $vehicleData['model'],
            ':year' => $vehicleData['year'],
            ':license_plate' => $vehicleData['license_plate'],
            ':color' => $vehicleData['color'],
            ':vehicle_type' => $vehicleData['vehicle_type']
        ];
        
        // Add photo updates only if new photos are provided
        if (isset($vehicleData['front_photo'])) {
            $setParts[] = 'front_photo = :front_photo';
            $bindings[':front_photo'] = $vehicleData['front_photo'];
        }
        if (isset($vehicleData['back_photo'])) {
            $setParts[] = 'back_photo = :back_photo';
            $bindings[':back_photo'] = $vehicleData['back_photo'];
        }
        if (isset($vehicleData['side_photo'])) {
            $setParts[] = 'side_photo = :side_photo';
            $bindings[':side_photo'] = $vehicleData['side_photo'];
        }
        
        $sql = "UPDATE vehicles SET " . implode(', ', $setParts) . " WHERE id = :id";
        
        $this->db->query($sql);
        
        foreach ($bindings as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->execute();
    }
    
    // Delete vehicle
    public function deleteVehicle($vehicleId, $driverId) {
        $sql = "DELETE FROM vehicles WHERE id = :id AND driver_id = :driver_id";
        $this->db->query($sql);
        $this->db->bind(':id', $vehicleId);
        $this->db->bind(':driver_id', $driverId);
        return $this->db->execute();
    }
    
    // Get vehicle count by driver ID
    public function getVehicleCountByDriverId($driverId) {
        $sql = "SELECT COUNT(*) as total_vehicles, 
                SUM(CASE WHEN verification_status = 'approved' THEN 1 ELSE 0 END) as approved_vehicles,
                SUM(CASE WHEN verification_status = 'pending' THEN 1 ELSE 0 END) as pending_vehicles,
                SUM(CASE WHEN verification_status = 'rejected' THEN 1 ELSE 0 END) as rejected_vehicles
                FROM vehicles WHERE driver_id = :driver_id";
        $this->db->query($sql);
        $this->db->bind(':driver_id', $driverId);
        return $this->db->single();
    }
    
    // Check if license plate exists
    public function checkLicensePlateExists($licensePlate, $excludeVehicleId = null) {
        $sql = "SELECT COUNT(*) as count FROM vehicles WHERE license_plate = :license_plate";
        
        if ($excludeVehicleId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':license_plate', $licensePlate);
        
        if ($excludeVehicleId) {
            $this->db->bind(':exclude_id', $excludeVehicleId);
        }
        
        $result = $this->db->single();
        return $result->count > 0;
    }
    
    // Check if license plate exists excluding a specific vehicle ID
    public function checkLicensePlateExistsExcluding($licensePlate, $excludeVehicleId) {
        return $this->checkLicensePlateExists($licensePlate, $excludeVehicleId);
    }
}
?>