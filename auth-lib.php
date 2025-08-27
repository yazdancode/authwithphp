<?php

function userExists(string $email, string $phone): bool
{
    global $pdo;

    $sql = "SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':phone' => $phone
    ]);

    return $stmt->fetchColumn() !== false;
}
