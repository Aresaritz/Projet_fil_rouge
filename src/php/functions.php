<?php

function userCredentialsValid($login, $password): bool
{
    if (!(isset($login) || isset($password))) {
        echo "<p class='error''>Username or password empty</p>\n";
        return false;
    }
    if (strlen($login) > 20 || strlen($password) > 40) {
        echo "<p class='error''>Username or password too long</p>\n";
        return false;
    }
    if (strlen($login) < 3 || strlen($password) < 8) {
        echo "<p class='error''>Username or password too short</p>\n";
        return false;
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        echo "<p class='error''>Username must contain only letters, numbers and underscores</p>\n";
        return false;
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', $password)) {
        echo "<p class='error'>Password must contain at least one lowercase letter, one uppercase letter, one number, and one special character</p>\n";
        return false;
    }

    return true;
}

function userExists(PDO $db, string $username): bool
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    return (bool) $stmt->fetchColumn();
}

function addUser(PDO $db, string $username, string $password): void
{
    $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
    $stmt->execute();
}