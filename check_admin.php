<?php

try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=laravel',
        'root',
        '7533qazwsx@321'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all users
    $stmt = $pdo->query("SELECT id, name, email, is_administrator, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Users in database:\n";
    echo str_repeat("=", 80) . "\n";
    printf("%-5s %-20s %-40s %-15s %s\n", "ID", "Name", "Email", "Is Admin", "Created At");
    echo str_repeat("-", 80) . "\n";

    foreach ($users as $user) {
        printf(
            "%-5s %-20s %-40s %-15s %s\n",
            $user['id'],
            $user['name'],
            $user['email'],
            $user['is_administrator'] ? 'YES' : 'NO',
            $user['created_at']
        );
    }

    echo "\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
