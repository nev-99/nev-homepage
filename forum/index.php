<?php
require_once 'config.php';
require_once 'bbcode.php';
session_start();

// Style selector
$style = $_COOKIE['style'] ?? DEFAULT_STYLE;
if (isset($_GET['style']) && isset($STYLES[$_GET['style']])) {
    $style = $_GET['style'];
    setcookie('style', $style, time() + 60 * 60 * 24 * 365, '/');
}
$css = $STYLES[$style] ?? reset($STYLES);

// Get thread list
$threads = array_diff(scandir(DATA_DIR), array('.', '..'));
$threads = array_filter($threads, function ($f) {
    return preg_match('/^thread_\d+\.dat$/', $f);
});

// BUMP
usort($threads, function ($a, $b) {
    return filemtime(DATA_DIR . '/' . $b) - filemtime(DATA_DIR . '/' . $a);
});

$threads = array_slice($threads, 0, MAX_THREADS);

function get_thread_title($file)
{
    $lines = file(DATA_DIR . '/' . $file);
    if (isset($lines[0])) {
        $parts = explode("\t", $lines[0]);
        return htmlspecialchars($parts[2] ?? '');
    }
    return '';
}
function get_thread_count($file)
{
    $lines = file(DATA_DIR . '/' . $file);
    return count($lines);
}

$num1 = rand(1, 9);
$num2 = rand(1, 9);
$_SESSION['captcha_answer'] = $num1 + $num2;
?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <title><?= BOARD_TITLE ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=0.75">
    <link rel="stylesheet" type="text/css" href="<?= htmlspecialchars($css) ?>">
</head>

<body class="mainpage">

    <div id="titlebox" class="outerbox">
        <div class="innerbox">
            <?php
            if (preg_match('/\.(jpg|jpeg|png|gif|bmp|webp)$/i', BOARD_TITLE_IMG)) {
                echo '<img src="' . htmlspecialchars(BOARD_TITLE_IMG) . '" alt="Board Title" style="max-width:100%;height:auto;">';
            } else {
                echo '<h1>' . htmlspecialchars(BOARD_TITLE_IMG) . '</h1>';
            }
            ?>
            <div class="threadnavigation">
                <a href="#menu" title="Jump to thread list">■</a>
                <a href="#1" title="Jump to next thread">▼</a>
            </div>
            <div id="rules">
                <p>
                    我らは世間一般より拒まれし者どものためにここに在り, 検閲を退け言論の自由を断固として擁護する.<br>
                    されど, 犯罪を誘発する一切の行為 ――児童ポルノ, 犯行予告 等―― これを厳禁とする.<br>
                    秩序を乱す者は断乎として排除されねばならぬ, ここは自由の名において享受される場である.
                </p>
            </div>
        </div>
    </div>

    <div id="stylebox" class="outerbox">
        <div class="innerbox">
            <strong><?= tr('board_look') ?>:</strong>
            <?php foreach ($STYLES as $k => $v): ?>
                <a href="?style=<?= $k ?>"><?= ucfirst($k) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <a name="menu"></a>
    <div id="threadbox" class="outerbox">
        <div class="innerbox">
            <div id="threadlist">
                <?php $n = 1;
                foreach ($threads as $thread):
                    $id = basename($thread, '.dat');
                    $title = get_thread_title($thread);
                    $count = get_thread_count($thread);
                ?>
                    <span class="threadlink">
                        <a href="thread.php?id=<?= urlencode($id) ?>" rel="nofollow"><?= $n ?>:</a>
                        <a href="#<?= $n ?>"> <?= $title ?> (<?= $count ?>)</a>
                    </span>
                <?php $n++;
                endforeach; ?>
            </div>
            <div id="threadlinks">
                <a href="#newthread"><?= tr('new_thread') ?></a>
                <a href="allthreads.php"><?= tr('all_threads') ?></a>
            </div>
        </div>
    </div>

    <div id="posts">
        <?php $n = 1;
        foreach ($threads as $thread):
            $id = basename($thread, '.dat');
            $lines = file(DATA_DIR . '/' . $thread, FILE_IGNORE_NEW_LINES);
            $title = get_thread_title($thread);
            $count = count($lines);


            $max_preview = 5;
            $total = count($lines);
            $start = max(0, $total - $max_preview);
        ?>
            <a name="<?= $n ?>"></a>
            <div class="thread">
                <h2><a href="thread.php?id=<?= urlencode($id) ?>" rel="nofollow"><?= $title ?> <small>(<?= $count ?>)</small></a></h2>
                <div class="threadnavigation">
                    <a href="#menu" title="Jump to thread list">■</a>
                    <a href="#<?= $n - 1 ?>" title="Jump to previous thread">▲</a>
                    <a href="#<?= $n + 1 ?>" title="Jump to next thread">▼</a>
                </div>

                <div class="replies">
                    <div class="allreplies">

                        <?php if ($total > $max_preview): ?>
                            <div class="replyabbrev">
                                Posts omitted: <?= $total - $max_preview ?>
                            </div>
                        <?php endif; ?>

                        <?php foreach (array_slice($lines, $start) as $num => $line):
                            $parts = explode("\t", $line);
                            $name = htmlspecialchars($parts[1] ?? DEFAULT_USERNAME);
                            $message = bbcode_to_html($parts[3] ?? '');
                            $date = htmlspecialchars($parts[0] ?? '');
                        ?>
                            <div class="reply">
                                <h3>
                                    <span class="replynum">
                                        <a title="Quote post number in reply" href="javascript:void(0);">
                                            <?= $start + $num + 1 ?>
                                        </a>
                                    </span>
                                    Name: <span class="postername"><?= $name ?></span> : <?= $date ?>
                                </h3>
                                <div class="replytext"><?= $message ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <form action="post.php" method="post">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <table>
                        <tbody>
                            <tr>
                                <td>Name:</td>
                                <td><input type="text" name="name" size="19" maxlength="100"></td>
                                <td><input type="submit" value="<?= tr('reply') ?>"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="2"><textarea name="message" cols="64" rows="5"></textarea></td>
                            </tr>
                            <tr>
                                <td>Captcha:</td>
                                <td colspan="2">
                                    How much is <?= $num1 ?> + <?= $num2 ?>?
                                    <input type="text" name="captcha" required>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>

                <div class="threadlinks">
                    <a href="thread.php?id=<?= urlencode($id) ?>"><?= tr('entire_thread') ?></a>
                    <a href="thread.php?id=<?= urlencode($id) ?>"><?= tr('last_50_posts') ?></a>
                    <a href="#menu"><?= tr('thread_list') ?></a>
                </div>
            </div>
        <?php $n++;
        endforeach; ?>
    </div>

    <a name="newthread"></a>
    <div id="createbox" class="outerbox">
        <div class="innerbox">
            <h2><?= tr('new_thread') ?></h2>
            <form action="post.php" method="post">
                <input type="hidden" name="action" value="newthread">
                <table>
                    <tbody>
                        <tr>
                            <td>Title:</td>
                            <td><input type="text" name="title" size="46" maxlength="100"></td>
                            <td><input type="submit" value="<?= tr('create_new_thread') ?>"></td>
                        </tr>
                        <tr>
                            <td>Name:</td>
                            <td colspan="2"><input type="text" name="name" size="19" maxlength="100"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2"><textarea name="message" cols="64" rows="5"></textarea></td>
                        </tr>
                        <tr>
                            <td>Captcha:</td>
                            <td colspan="2">
                                How much is <?= $num1 ?> + <?= $num2 ?>?
                                <input type="text" name="captcha" required>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <div id="footer">
        <a href="/"></a>
        &nbsp;-&nbsp;
        &nbsp;-&nbsp; <a href="https://github.com/Strangeman2222/Licchannel">Licchannel 1.2</a>
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