-- Loop-safe bidirectional synchronization between traveller_side_d_requests and traveller_side_t_requests.
--
-- Rules implemented:
-- 1) INSERT on traveller_side_d_requests -> INSERT matching row into traveller_side_t_requests.
-- 2) UPDATE on traveller_side_d_requests -> INSERT a NEW row into traveller_side_t_requests (history row).
-- 3) UPDATE on traveller_side_t_requests(requestStatus/respondedAt/completedAt) -> UPDATE linked row in traveller_side_d_requests.
--
-- Mapping key:
-- traveller_side_t_requests.tSideRqId = traveller_side_d_requests.requestId
--
-- Recursion guard:
-- @skip_tsd_to_tst_sync session variable prevents t->d updates from re-inserting into t.

DROP TRIGGER IF EXISTS trg_tsd_after_insert_to_tst;
DROP TRIGGER IF EXISTS trg_tsd_after_update_to_tst;
DROP TRIGGER IF EXISTS trg_tst_after_update_to_tsd;

DELIMITER $$

CREATE TRIGGER trg_tsd_after_insert_to_tst
AFTER INSERT ON traveller_side_d_requests
FOR EACH ROW
BEGIN
    IF COALESCE(@skip_tsd_to_tst_sync, 0) = 0 THEN
        INSERT INTO traveller_side_t_requests (
            tSideRqId,
            tripId,
            rqUserId,
            driverId,
            driverName,
            driverProfilePhoto,
            driverRating,
            verifyStatus,
            vehicleId,
            vehicleModel,
            vehicleYear,
            vehicleType,
            vehiclePhoto,
            vehicleCapacity,
            childSeats,
            requestStatus,
            chargeType,
            totalKm,
            totalAmount,
            requestedAt,
            respondedAt,
            completedAt
        ) VALUES (
            NEW.requestId,
            NEW.tripId,
            NEW.rqUserId,
            NEW.driverId,
            NEW.driverName,
            NEW.driverProfilePhoto,
            NEW.driverRating,
            NEW.verifyStatus,
            NEW.vehicleId,
            NEW.vehicleModel,
            NEW.vehicleYear,
            NEW.vehicleType,
            NEW.vehiclePhoto,
            NEW.vehicleCapacity,
            NEW.childSeats,
            NEW.requestStatus,
            NEW.chargeType,
            NEW.totalKm,
            NEW.totalAmount,
            NEW.requestedAt,
            NEW.respondedAt,
            NEW.completedAt
        );
    END IF;
END$$

CREATE TRIGGER trg_tsd_after_update_to_tst
AFTER UPDATE ON traveller_side_d_requests
FOR EACH ROW
BEGIN
    IF COALESCE(@skip_tsd_to_tst_sync, 0) = 0 THEN
        IF NOT (
            NEW.tripId <=> OLD.tripId
            AND NEW.rqUserId <=> OLD.rqUserId
            AND NEW.driverId <=> OLD.driverId
            AND NEW.driverName <=> OLD.driverName
            AND NEW.driverProfilePhoto <=> OLD.driverProfilePhoto
            AND NEW.driverRating <=> OLD.driverRating
            AND NEW.verifyStatus <=> OLD.verifyStatus
            AND NEW.vehicleId <=> OLD.vehicleId
            AND NEW.vehicleModel <=> OLD.vehicleModel
            AND NEW.vehicleYear <=> OLD.vehicleYear
            AND NEW.vehicleType <=> OLD.vehicleType
            AND NEW.vehiclePhoto <=> OLD.vehiclePhoto
            AND NEW.vehicleCapacity <=> OLD.vehicleCapacity
            AND NEW.childSeats <=> OLD.childSeats
            AND NEW.requestStatus <=> OLD.requestStatus
            AND NEW.chargeType <=> OLD.chargeType
            AND NEW.totalKm <=> OLD.totalKm
            AND NEW.totalAmount <=> OLD.totalAmount
            AND NEW.requestedAt <=> OLD.requestedAt
            AND NEW.respondedAt <=> OLD.respondedAt
            AND NEW.completedAt <=> OLD.completedAt
        ) THEN
            INSERT INTO traveller_side_t_requests (
                tSideRqId,
                tripId,
                rqUserId,
                driverId,
                driverName,
                driverProfilePhoto,
                driverRating,
                verifyStatus,
                vehicleId,
                vehicleModel,
                vehicleYear,
                vehicleType,
                vehiclePhoto,
                vehicleCapacity,
                childSeats,
                requestStatus,
                chargeType,
                totalKm,
                totalAmount,
                requestedAt,
                respondedAt,
                completedAt
            ) VALUES (
                NEW.requestId,
                NEW.tripId,
                NEW.rqUserId,
                NEW.driverId,
                NEW.driverName,
                NEW.driverProfilePhoto,
                NEW.driverRating,
                NEW.verifyStatus,
                NEW.vehicleId,
                NEW.vehicleModel,
                NEW.vehicleYear,
                NEW.vehicleType,
                NEW.vehiclePhoto,
                NEW.vehicleCapacity,
                NEW.childSeats,
                NEW.requestStatus,
                NEW.chargeType,
                NEW.totalKm,
                NEW.totalAmount,
                NEW.requestedAt,
                NEW.respondedAt,
                NEW.completedAt
            );
        END IF;
    END IF;
END$$

CREATE TRIGGER trg_tst_after_update_to_tsd
AFTER UPDATE ON traveller_side_t_requests
FOR EACH ROW
BEGIN
    IF (
        NOT (NEW.requestStatus <=> OLD.requestStatus)
        OR NOT (NEW.respondedAt <=> OLD.respondedAt)
        OR NOT (NEW.completedAt <=> OLD.completedAt)
    ) THEN
        SET @skip_tsd_to_tst_sync = 1;

        IF NEW.tSideRqId IS NOT NULL THEN
            UPDATE traveller_side_d_requests
            SET requestStatus = NEW.requestStatus,
                respondedAt = NEW.respondedAt,
                completedAt = NEW.completedAt,
                updatedAt = CURRENT_TIMESTAMP
            WHERE requestId = NEW.tSideRqId;
        ELSE
            UPDATE traveller_side_d_requests
            SET requestStatus = NEW.requestStatus,
                respondedAt = NEW.respondedAt,
                completedAt = NEW.completedAt,
                updatedAt = CURRENT_TIMESTAMP
            WHERE tripId = NEW.tripId
              AND rqUserId = NEW.rqUserId
              AND driverId = NEW.driverId
            ORDER BY requestId DESC
            LIMIT 1;
        END IF;

        SET @skip_tsd_to_tst_sync = 0;
    END IF;
END$$

DELIMITER ;
