<?php
require_once 'config.php';

session_start();
$logged_in = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Style selector
$style = $_COOKIE['style'] ?? DEFAULT_STYLE;
if (isset($_GET['style']) && isset($STYLES[$_GET['style']])) {
    $style = $_GET['style'];
    setcookie('style', $style, time()+60*60*24*365, '/');
}
$css = $STYLES[$style] ?? reset($STYLES);

if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin'] = true;
        $logged_in = true;
    } else {
        $error = 'Wrong password.';
    }
}

if (!$logged_in): ?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin</title><link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($css) ?>"></head>
<body>
<div id="stylebox">
    <form method="get" style="display:inline">
        <label for="style"><?= tr('style') ?>:</label>
        <select name="style" id="style" onchange="this.form.submit()">
            <?php foreach ($STYLES as $k => $v): ?>
                <option value="<?= $k ?>"<?= $k === $style ? ' selected' : '' ?>><?= ucfirst($k) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<h1><?= tr('admin_panel') ?></h1>
<?php if (isset($error)) echo '<p style="color:red">'.$error.'</p>'; ?>
<form method="post">
    Password: <input type="password" name="password">
    <input type="submit" value="Login">
</form>
</body>
</html>
<?php exit; endif;

// Delete thread
if (isset($_GET['delthread'])) {
    $id = preg_replace('/[^\d]/', '', $_GET['delthread']);
    $file = DATA_DIR . "/thread_{$id}.dat";
    if (file_exists($file)) {
        unlink($file);
    }
    header('Location: admin.php');
    exit;
}

// List threads
$threads = array_diff(scandir(DATA_DIR), array('.', '..'));
$threads = array_filter($threads, function($f) { return preg_match('/^thread_\d+\.dat$/', $f); });
rsort($threads);
?><!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin</title><link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($css) ?>"></head>
<body>
<div id="stylebox">
    <form method="get" style="display:inline">
        <label for="style"><?= tr('style') ?>:</label>
        <select name="style" id="style" onchange="this.form.submit()">
            <?php foreach ($STYLES as $k => $v): ?>
                <option value="<?= $k ?>"<?= $k === $style ? ' selected' : '' ?>><?= ucfirst($k) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>
<h1><?= tr('admin_panel') ?></h1>
<a href="index.php">&lt; <?= tr('back_to_index') ?></a>
<h2><?= tr('threads') ?></h2>
<ul>
<?php foreach ($threads as $thread):
    $id = preg_replace('/\D/', '', $thread);
    echo '<li>'.htmlspecialchars($thread).' <a href="?delthread='.$id.'" onclick="return confirm(\'Delete this thread?\')">[Delete]</a></li>';
endforeach; ?>
</ul>
</body>
</html> 