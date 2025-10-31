-- Создание базы данных
CREATE DATABASE IF NOT EXISTS tender_service 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE tender_service;

-- Таблица тендеров
CREATE TABLE IF NOT EXISTS tenders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    purchase_number VARCHAR(255) NOT NULL UNIQUE,
    object_info TEXT,
    max_price DECIMAL(15,2),
    currency_code VARCHAR(10),
    published_at DATETIME,
    collecting_finished_at DATETIME,
    purchase_type VARCHAR(100),
    region INT,
    stage INT,
    customers JSON,
    ikzs JSON,
    created_at DATETIME NOT NULL,
    
    INDEX idx_purchase_number (purchase_number),
    INDEX idx_published_at (published_at),
    INDEX idx_region (region),
    INDEX idx_created_at (created_at)
);

-- Таблица документов тендеров
CREATE TABLE IF NOT EXISTS tender_docs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tender_id INT NOT NULL,
    doc_type VARCHAR(255) NOT NULL,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_tender_doc (tender_id, doc_type),
    FOREIGN KEY (tender_id) REFERENCES tenders(id) ON DELETE CASCADE,
    INDEX idx_tender_id (tender_id),
    INDEX idx_doc_type (doc_type)
);
