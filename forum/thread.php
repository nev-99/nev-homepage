<?php
require_once 'config.php';
require_once 'bbcode.php';
session_start(); 

// Style selector
$style = $_COOKIE['style'] ?? DEFAULT_STYLE;
if (isset($_GET['style']) && isset($STYLES[$_GET['style']])) {
    $style = $_GET['style'];
    setcookie('style', $style, time()+60*60*24*365, '/');
}
$css = $STYLES[$style] ?? reset($STYLES);

$id = isset($_GET['id']) ? preg_replace('/[^\d]/', '', $_GET['id']) : '';
$thread_file = DATA_DIR . "/thread_{$id}.dat";
if (!file_exists($thread_file)) {
    die('Thread not found.');
}
$lines = file($thread_file, FILE_IGNORE_NEW_LINES);


$captcha_a = rand(1, 10);
$captcha_b = rand(1, 10);
$_SESSION['captcha_answer'] = $captcha_a + $captcha_b;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= BOARD_TITLE ?> - Thread <?= htmlspecialchars($id) ?></title>
    <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($css) ?>">
</head>
<body class="threadpage">
<div id="titlebox">
    <h1><?= BOARD_TITLE ?> - Thread <?= htmlspecialchars($id) ?></h1>
    <div class="threadnavigation">
        <form method="get" style="display:inline">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <label for="style"><?= tr('style') ?>:</label>
            <select name="style" id="style" onchange="this.form.submit()">
                <?php foreach ($STYLES as $k => $v): ?>
                    <option value="<?= $k ?>"<?= $k === $style ? ' selected' : '' ?>><?= ucfirst($k) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>
<div id="posts">
    <?php foreach ($lines as $num => $line):
        $parts = explode("\t", $line);
        $name = htmlspecialchars($parts[1] ?? DEFAULT_USERNAME);
        $title = htmlspecialchars($parts[2] ?? '');
        $message = bbcode_to_html($parts[3] ?? '');
        $date = htmlspecialchars($parts[0] ?? '');
    ?>
    <div class="reply">
        <h3>
            <span class="replynum"><?= $num+1 ?></span>
            <span class="postername"><?= $name ?></span>
            <?php if ($num === 0): ?>
                <strong><?= $title ?></strong>
            <?php endif; ?>
            <small><?= $date ?></small>
        </h3>
        <div class="replytext">
            <?= $message ?>
        </div>
    </div>
    <?php endforeach; ?>

    <form action="post.php" method="post">
        <input type="hidden" name="action" value="reply">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <label>Name: <input type="text" name="name" maxlength="30"></label><br>
        <label><?= tr('message') ?>:<br>
        <textarea name="message" rows="4" cols="50" required></textarea></label><br>
        <!-- Captcha -->
        <label>Solve: <?= $captcha_a ?> + <?= $captcha_b ?> = <input type="text" name="captcha" required></label><br>
        <input type="submit" value="<?= tr('reply') ?>">
    </form>
</div>

<div id="footer">
    <a href="index.php">&lt; <?= tr('back_to_index') ?></a>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea[name="message"]');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const form = this.closest('form');
                if (form) form.submit();
            }
        });
    });
});
</script>
</body>
</html>