<?php
    define('FLAG_FILE', __DIR__ . '/flag');

    function writeln($txt = '') {
        echo "$txt<br />\n";
    }

    if(!file_exists(FLAG_FILE) || !is_writable(FLAG_FILE)) {
        writeln(FLAG_FILE . 'does not exists');
        writeln('You must create this file and give it write access');
        writeln();
        writeln('touch ' . FLAG_FILE);
        writeln('chmod 0777 ' . FLAG_FILE);
    }
    else {
        file_put_contents(FLAG_FILE, 'GO !');
    }