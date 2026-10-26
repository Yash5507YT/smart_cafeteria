-- Smart Cafeteria: Admin Order Management Migration
-- Run this once in the smart_cafeteria database.

ALTER TABLE orders
    ADD COLUMN order_status VARCHAR(30) NOT NULL DEFAULT 'pending' AFTER payment_mode,
    ADD COLUMN admin_note VARCHAR(255) NULL AFTER order_status,
    ADD COLUMN status_updated_at DATETIME NULL AFTER admin_note;

CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_date_status ON orders(order_date, order_status);

-- Existing orders remain Pending after this migration.
-- Supported values used by the admin panel:
-- pending, confirmed, preparing, ready, completed, cancelled,
-- refund_requested, refunded
