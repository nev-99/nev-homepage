<?php
require_once 'config.php';

// Get thread list
$threads = array_diff(scandir(DATA_DIR), array('.', '..'));
$threads = array_filter($threads, function($f) { return preg_match('/^thread_\d+\.dat$/', $f); });
rsort($threads);
$threads = array_slice($threads, 0, MAX_THREADS);

function get_thread_title($file) {
    $lines = file(DATA_DIR . '/' . $file);
    if (isset($lines[0])) {
        $parts = explode("\t", $lines[0]);
        return htmlspecialchars($parts[2] ?? '');
    }
    return '';
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Threads - <?= BOARD_TITLE ?></title>
    <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($STYLES[DEFAULT_STYLE]) ?>">
</head>
<body>
<h1>All Threads</h1>
<ul>
<?php foreach ($threads as $thread):
    $id = basename($thread, '.dat');
    $title = get_thread_title($thread);
?>
    <li><a href="thread.php?id=<?= urlencode($id) ?>"> <?= $title ?> </a></li>
<?php endforeach; ?>
</ul>
<a href="index.php">Back to index</a>
</body>
</html> 