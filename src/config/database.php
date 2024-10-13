<?php

if (!function_exists('load_env')) {
    function load_env(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new Exception("No file .env : $filePath");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            }

            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;

            if (!getenv($name)) {
                putenv("$name=$value");
            }
        }
    }
}

if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASSWORD']) || !isset($_ENV['API_KEY'])) {
    load_env(__DIR__ . '/.env');
}

return [
    'host' => $_ENV['DB_HOST'],
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'passkey' => $_ENV['passkey'],
];