-- Loop-safe bidirectional synchronization between traveller_side_g_requests and guide_side_g_requests.
--
-- Rules implemented:
-- 1) INSERT on traveller_side_g_requests -> INSERT matching row into guide_side_g_requests.
-- 2) UPDATE on traveller_side_g_requests -> INSERT a NEW row into guide_side_g_requests (history row).
-- 3) UPDATE on guide_side_g_requests(status/respondedAt/completedAt) -> UPDATE linked row in traveller_side_g_requests.
--
-- Mapping key:
-- guide_side_g_requests.tSideRqId = traveller_side_g_requests.id
--
-- Recursion guard:
-- @skip_tsg_to_gsg_sync session variable prevents guide->traveller mirrored updates
-- from re-inserting into guide_side_g_requests.

DROP TRIGGER IF EXISTS trg_tsg_after_insert_to_gsg;
DROP TRIGGER IF EXISTS trg_tsg_after_update_to_gsg;
DROP TRIGGER IF EXISTS trg_gsg_after_update_to_tsg;

DELIMITER $$

CREATE TRIGGER trg_tsg_after_insert_to_gsg
AFTER INSERT ON traveller_side_g_requests
FOR EACH ROW
BEGIN
    IF COALESCE(@skip_tsg_to_gsg_sync, 0) = 0 THEN
        INSERT INTO guide_side_g_requests (
            tSideRqId,
            userId,
            tripId,
            eventId,
            travelSpotId,
            guideId,
            status,
            guideFullName,
            guideProfilePhoto,
            guideAverageRating,
            guideBio,
            chargeType,
            numberOfPeople,
            totalCharge,
            requestedAt,
            respondedAt,
            completedAt
        ) VALUES (
            NEW.id,
            NEW.userId,
            NEW.tripId,
            NEW.eventId,
            NEW.travelSpotId,
            NEW.guideId,
            NEW.status,
            NEW.guideFullName,
            NEW.guideProfilePhoto,
            NEW.guideAverageRating,
            NEW.guideBio,
            NEW.chargeType,
            NEW.numberOfPeople,
            NEW.totalCharge,
            NEW.requestedAt,
            NEW.respondedAt,
            NEW.completedAt
        );
    END IF;
END$$

CREATE TRIGGER trg_tsg_after_update_to_gsg
AFTER UPDATE ON traveller_side_g_requests
FOR EACH ROW
BEGIN
    IF COALESCE(@skip_tsg_to_gsg_sync, 0) = 0 THEN
        IF NOT (
            NEW.userId <=> OLD.userId
            AND NEW.tripId <=> OLD.tripId
            AND NEW.eventId <=> OLD.eventId
            AND NEW.travelSpotId <=> OLD.travelSpotId
            AND NEW.guideId <=> OLD.guideId
            AND NEW.status <=> OLD.status
            AND NEW.guideFullName <=> OLD.guideFullName
            AND NEW.guideProfilePhoto <=> OLD.guideProfilePhoto
            AND NEW.guideAverageRating <=> OLD.guideAverageRating
            AND NEW.guideBio <=> OLD.guideBio
            AND NEW.chargeType <=> OLD.chargeType
            AND NEW.numberOfPeople <=> OLD.numberOfPeople
            AND NEW.totalCharge <=> OLD.totalCharge
            AND NEW.requestedAt <=> OLD.requestedAt
            AND NEW.respondedAt <=> OLD.respondedAt
            AND NEW.completedAt <=> OLD.completedAt
        ) THEN
            INSERT INTO guide_side_g_requests (
                tSideRqId,
                userId,
                tripId,
                eventId,
                travelSpotId,
                guideId,
                status,
                guideFullName,
                guideProfilePhoto,
                guideAverageRating,
                guideBio,
                chargeType,
                numberOfPeople,
                totalCharge,
                requestedAt,
                respondedAt,
                completedAt
            ) VALUES (
                NEW.id,
                NEW.userId,
                NEW.tripId,
                NEW.eventId,
                NEW.travelSpotId,
                NEW.guideId,
                NEW.status,
                NEW.guideFullName,
                NEW.guideProfilePhoto,
                NEW.guideAverageRating,
                NEW.guideBio,
                NEW.chargeType,
                NEW.numberOfPeople,
                NEW.totalCharge,
                NEW.requestedAt,
                NEW.respondedAt,
                NEW.completedAt
            );
        END IF;
    END IF;
END$$

CREATE TRIGGER trg_gsg_after_update_to_tsg
AFTER UPDATE ON guide_side_g_requests
FOR EACH ROW
BEGIN
    IF (
        NOT (NEW.status <=> OLD.status)
        OR NOT (NEW.respondedAt <=> OLD.respondedAt)
        OR NOT (NEW.completedAt <=> OLD.completedAt)
    ) THEN
        SET @skip_tsg_to_gsg_sync = 1;

        IF NEW.tSideRqId IS NOT NULL THEN
            UPDATE traveller_side_g_requests
            SET status = NEW.status,
                respondedAt = NEW.respondedAt,
                completedAt = NEW.completedAt,
                updatedAt = CURRENT_TIMESTAMP
            WHERE id = NEW.tSideRqId;
        ELSE
            UPDATE traveller_side_g_requests
            SET status = NEW.status,
                respondedAt = NEW.respondedAt,
                completedAt = NEW.completedAt,
                updatedAt = CURRENT_TIMESTAMP
            WHERE tripId = NEW.tripId
              AND userId = NEW.userId
              AND eventId = NEW.eventId
              AND (guideId <=> NEW.guideId)
            ORDER BY id DESC
            LIMIT 1;
        END IF;

        SET @skip_tsg_to_gsg_sync = 0;
    END IF;
END$$

DELIMITER ;
