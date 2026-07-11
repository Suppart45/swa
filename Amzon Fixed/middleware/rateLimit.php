<?php
function rateLimiter($maxRequests = 8, $windowMinutes = 4, $banMinutes = 60) {
    session_start();

    $ip = $_SERVER['REMOTE_ADDR']; 
    $currentTime = time();
    $windowSeconds = $windowMinutes * 60;
    $banSeconds = $banMinutes * 60;

    if (!isset($_SESSION['rate_limit'][$ip])) {
        $_SESSION['rate_limit'][$ip] = [
            'count' => 1,
            'start_time' => $currentTime,
            'banned_until' => null
        ];
    } else {
        $data = &$_SESSION['rate_limit'][$ip];

        
        if ($data['banned_until'] && $currentTime < $data['banned_until']) {
            http_response_code(429); // 429 Too Many Requests
            //header('Content-Type: application/json');
            //echo json_encode(["message" => "Too many requests. Try again later."]);
            header("Location: " . BASE_URL);
            exit;
        }

        // Reset counter if window time has passed
        if ($currentTime - $data['start_time'] > $windowSeconds) {
            $data['count'] = 1;
            $data['start_time'] = $currentTime;
        } else {
            $data['count']++;
        }
        
        if ($data['count'] > $maxRequests) {
            $data['banned_until'] = $currentTime + $banSeconds;
            header("Location: " . BASE_URL);
            exit;
        }
    }
}
?>