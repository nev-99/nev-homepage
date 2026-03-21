<?php
function bbcode_to_html($text) {
    // Convertir el marcador especial de salto de línea a salto de línea real
    $text = str_replace('\\n', "\n", $text);
    
    // Detectar ASCII art y aplicar clase .aa
    $lines = explode("\n", $text);
    $is_ascii_art = false;
    
    // Verificar si parece ASCII art (múltiples líneas con caracteres especiales)
    if (count($lines) > 2) {
        $ascii_chars = 0;
        $total_chars = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 0) {
                $total_chars += strlen($line);
                // Contar caracteres típicos de ASCII art
                $ascii_chars += preg_match_all('/[∧＿｀･ωつ旦と＿\(\)\[\]{}<>\/\\|@#$%^&*+=~`]/u', $line);
            }
        }
        // Si más del 20% de los caracteres son de ASCII art, considerarlo como tal
        if ($total_chars > 0 && ($ascii_chars / $total_chars) > 0.2) {
            $is_ascii_art = true;
        }
    }
    
    $bbcode = [
        '/\[b\](.*?)\[\/b\]/is' => '<strong>$1</strong>',
        '/\[i\](.*?)\[\/i\]/is' => '<em>$1</em>',
        '/\[u\](.*?)\[\/u\]/is' => '<u>$1</u>',
        '/\[s\](.*?)\[\/s\]/is' => '<s>$1</s>',
        '/\[quote\](.*?)\[\/quote\]/is' => '<blockquote>$1</blockquote>',
        '/\[url=(.*?)\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener">$2</a>',
        '/\[url\](.*?)\[\/url\]/is' => '<a href="$1" target="_blank" rel="noopener">$1</a>',
        '/\[code\](.*?)\[\/code\]/is' => '<pre class="aa">$1</pre>',
        '/\[aa\](.*?)\[\/aa\]/is' => '<div class="aa">$1</div>',
    ];
    foreach ($bbcode as $pattern => $replace) {
        $text = preg_replace($pattern, $replace, $text);
    }
    
    // Convertir saltos de línea a <br>
    $text = nl2br($text);
    
    // Si se detectó ASCII art y no está dentro de tags especiales, envolver en div.aa
    if ($is_ascii_art && !preg_match('/<pre|<div class="aa"/', $text)) {
        $text = '<div class="aa">' . $text . '</div>';
    }
    
    return $text;
} 