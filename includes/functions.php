<?php
// includes/functions.php

function getAvailableProducts(PDO $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT product_id, product_name, category, price, stock_quantity FROM products WHERE status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}