<?php
session_start();

// Log POST data for debugging
$postData = print_r($_POST, true);
file_put_contents("debug_post_data.txt", $postData, FILE_APPEND);

if (isset($_POST['token'])) {
    $token = $_POST['token'];

    // Verify the token with Cloudflare Turnstile
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = [
        'secret' => $secretKey,
        'response' => $token,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        $curlError = curl_error($ch);
        file_put_contents("file.txt", "CURL Error: $curlError\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'CURL request failed']);
        curl_close($ch);
        exit;
    }

    file_put_contents("file.txt", $response . "\n", FILE_APPEND);

    curl_close($ch);

    $result = json_decode($response, true);
    file_put_contents("file.txt", print_r($result, true) . "\n", FILE_APPEND);

    header('Content-Type: application/json');
    if (isset($result['success']) && $result['success'] === true) {
        // CAPTCHA solved successfully
        $_SESSION['captcha_verified'] = true;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error-codes'] ?? 'Unknown error']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?> 