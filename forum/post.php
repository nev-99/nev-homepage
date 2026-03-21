<?php
require_once 'config.php';
session_start(); 

date_default_timezone_set('UTC');

$action = $_POST['action'] ?? '';
$name = trim($_POST['name'] ?? '');
if ($name === '') $name = DEFAULT_USERNAME;

$message = trim($_POST['message'] ?? '');
$message = str_replace("\n", "\\n", $message);
$date = date('Y-m-d H:i:s');

// === Anti-flood:
if (isset($_SESSION['last_post_time'])) {
    $diff = time() - $_SESSION['last_post_time'];
    if ($diff < 10) {
        die('You are posting too fast. Please wait ' . (10 - $diff) . ' seconds.');
    }
}

// === Captcha check ===
$captcha_input = trim($_POST['captcha'] ?? '');
if (!isset($_SESSION['captcha_answer']) || $captcha_input == '' || (int)$captcha_input !== $_SESSION['captcha_answer']) {
    die('Captcha incorrect.');
}


unset($_SESSION['captcha_answer']);

if ($action === 'newthread') {
    $title = trim($_POST['title'] ?? '');
    if ($title === '' || $message === '') {
        die('Title and message are required.');
    }
    $id = time();
    $file = DATA_DIR . "/thread_{$id}.dat";
    $line = "$date\t$name\t$title\t$message\n";
    file_put_contents($file, $line);

    $_SESSION['last_post_time'] = time(); 
    header("Location: thread.php?id=$id");
    exit;

} elseif ($action === 'reply') {
    $id = preg_replace('/[^\d]/', '', $_POST['id'] ?? '');
    $file = DATA_DIR . "/thread_{$id}.dat";
    if (!file_exists($file)) {
        die('Thread not found.');
    }
    if ($message === '') {
        die('Message is required.');
    }
    $line = "$date\t$name\t\t$message\n";
    file_put_contents($file, $line, FILE_APPEND);

    $_SESSION['last_post_time'] = time(); 
    header("Location: thread.php?id=$id");
    exit;

} else {
    die('Invalid action.');
}