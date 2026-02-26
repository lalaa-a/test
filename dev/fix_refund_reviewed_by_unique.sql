-- Drop UNIQUE constraint on reviewed_by so multiple refunds can be reviewed by same manager
-- Run this if approve/reject fails with duplicate key error

ALTER TABLE refund_requests DROP INDEX reviewed_by;
ALTER TABLE refund_requests DROP INDEX reviewed_by_2;
ALTER TABLE refund_requests ADD KEY reviewed_by (reviewed_by);
