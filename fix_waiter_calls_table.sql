-- Add missing columns to waiter_calls table
ALTER TABLE waiter_calls ADD COLUMN responded_at DATETIME NULL;
ALTER TABLE waiter_calls ADD COLUMN completed_at DATETIME NULL;
ALTER TABLE waiter_calls ADD COLUMN responded_by INTEGER NULL;

-- Update status column to support new values
-- Note: SQLite doesn't support ALTER COLUMN directly, so we'll handle this in the application
