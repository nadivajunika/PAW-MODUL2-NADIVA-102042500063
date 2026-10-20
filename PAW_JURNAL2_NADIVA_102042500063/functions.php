
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string) ($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): ?array
    {
        if (!isset($_SESSION['flash'])) {
            return null;
        }

        $message = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $message;
    }
}

if (!function_exists('post')) {
    function post(string $key, string $default = ''): string
    {
        return trim((string) ($_POST[$key] ?? $default));
    }
}
