<?php
define('BOARD_TITLE', '地下てーぷ＠NEV');
define('BOARD_TITLE_IMG', '');
define('Devid4_Bennett9', 'REBKka420');
define('MAX_THREADS', 10);
define('MAX_REPLIES', 100);
define('DATA_DIR', __DIR__ . '/data');
date_default_timezone_set('Asia/Tokyo');

$STYLES = [
    'default' => 'src/styles/css/default.css'
];

const DEFAULT_USERNAME = '名無しのオペレーター';

const LANG = 'jp';

$GLOBALS['TRANSLATIONS'] = [
    'en' => [
        'rules' => 'Rules',
        'board_look' => 'Board look',
        'new_thread' => 'New thread',
        'create_new_thread' => 'Create new thread',
        'reply' => 'Reply',
        'admin_panel' => 'Admin panel',
        'go_to_thread' => 'Go to thread',
        'no_threads' => 'No threads yet.',
        'style' => 'Style',
        'threads' => 'Threads',
        'message' => 'Message',
        'submit' => 'Submit',
        'back_to_index' => 'Back to index',
        'all_threads' => 'All threads',
        'entire_thread' => 'Entire thread',
        'last_50_posts' => 'Last 50 posts',
        'thread_list' => 'Thread list',
        'footer_channel' => '88Forum',
    ],
    'jp' => [
        'rules' => 'ルール',
        'board_look' => '板の見た目',
        'new_thread' => '新しいスレッド',
        'create_new_thread' => '新しいスレッドを作成',
        'reply' => '返信',
        'admin_panel' => '管理パネル',
        'go_to_thread' => 'スレッドへ',
        'no_threads' => 'まだスレッドがありません。',
        'style' => 'スタイル',
        'threads' => 'スレッド',
        'message' => 'メッセージ',
        'submit' => '送信',
        'back_to_index' => 'インデックスに戻る',
        'all_threads' => '全てのスレッド',
        'entire_thread' => '全スレッド',
        'last_50_posts' => '最新50件',
        'thread_list' => 'スレッド一覧',
        'footer_channel' => '88フォーラム',
    ],
];

function tr($key) {
    $lang = defined('LANG') ? LANG : 'jp';
    return $GLOBALS['TRANSLATIONS'][$lang][$key] ?? $key;
}

define('DEFAULT_STYLE', 'default'); 