<?php
$db = new PDO('sqlite:db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  board TEXT,
  name TEXT,
  comment TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $board = $_POST['board'];
    $name = $_POST['name'] ?: 'Anonymous';
    $comment = $_POST['comment'];

    $stmt = $db->prepare("INSERT INTO posts (board, name, comment) VALUES (?, ?, ?)");
    $stmt->execute([$board, $name, $comment]);

    header("Location: ?board=" . $board);
    exit;
}

$currentBoard = $_GET['board'] ?? 'vipper';

$stmt = $db->prepare("SELECT * FROM posts WHERE board=? ORDER BY id DESC");
$stmt->execute([$currentBoard]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>地下鉄道＠NEV</title>

    <style>
        body {
            background: #2f3a66;
            color: #fff;
            font-family: "Courier New", monospace;
            margin: 0;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: auto;
        }

        .sidebar {
            width: 30%;
            padding: 10px;
            border-right: 1px solid #fff;
        }

        .main {
            width: 70%;
            padding: 10px;
        }

        .content {
            max-width: 700px;
            margin: 0 auto;
        }

        a {
            color: #cfd6ff;
        }

        hr {
            border: none;
            border-top: 1px solid #fff;
        }

        .reply {
            border-top: 1px solid #fff;
            padding: 8px 0;
        }

        .postername {
            color: #aaffaa;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            background: #1e274a;
            color: #fff;
            border: 1px solid #fff;
        }

        button {
            background: transparent;
            color: #fff;
            border: 1px solid #fff;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .mobile-footer {
            display: none;
        }

        @media (max-width:768px) {
            .container {
                display: block;
            }

            .sidebar {
                display: none;
            }

            .main {
                width: 100%;
                padding: 0;
            }

            .content {
                padding: 10px;
                padding-bottom: 120px;
            }

            .mobile-footer {
                display: block;
                position: fixed;
                bottom: 0;
                width: 100%;
                background: #2f3a66;
                border-top: 1px solid #fff;
                text-align: center;
                padding: 10px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="sidebar">
            <h3>Underground Line</h3>

            <ul>
                <li><a href="?board=vipper">News4vip</a></li>
                <li><a href="?board=tcc">TCC</a></li>
            </ul>

            <h3>Links</h3>
            <ul>
                <li><a href="#">Archive</a></li>
                <li><a href="#">Log</a></li>
                <li><a href="#">External</a></li>
            </ul>

            <div>
                Last Update: 2026.03.18<br>
                Admin: nev
            </div>
        </div>

        <div class="main">
            <div class="content">

                <div class="logo">Underground Line</div>

                <hr>

                <div class="board-switch">
                    [ <a href="?board=vipper">News4vip</a> |
                    <a href="?board=tcc">TCC</a> ]
                </div>

                <hr>

                <form method="POST">
                    <input type="hidden" name="board" value="<?= htmlspecialchars($currentBoard) ?>">

                    NAME<br>
                    <input type="text" name="name"><br><br>

                    COMMENT<br>
                    <textarea name="comment"></textarea><br><br>

                    <button type="submit">POST</button>
                </form>

                <hr>

                <?php foreach ($posts as $i => $post): ?>
                    <div class="reply">
                        <span class="postername"><?= htmlspecialchars($post['name']) ?></span>
                        <span><?= $post['created_at'] ?></span>
                        <span>No.<?= $post['id'] ?></span><br>
                        <blockquote><?= nl2br(htmlspecialchars($post['comment'])) ?></blockquote>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <div class="mobile-footer">
        Links |
        <a href="#">Archive</a> |
        <a href="#">Log</a> |
        <a href="#">External</a>

        <br><br>
        Last Update: 2026.03.18 / Admin: nev
    </div>

</body>

</html>