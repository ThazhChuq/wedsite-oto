USE shop_oto_db;

CREATE TABLE IF NOT EXISTS product_vehicle_types (
    product_id INT NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    PRIMARY KEY (product_id, vehicle_type),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
