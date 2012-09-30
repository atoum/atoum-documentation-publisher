<?php
    define('FLAG_FILE',         __DIR__ . '/flag');
    define('EASYBOOK_PATH',     __DIR__ . '/easybook');
    define('EASYBOOK_DOC_PATH', EASYBOOK_PATH . '/doc');

    function writeln($txt = '') {
        echo "$txt\n";
    }

    function command($command) {
        writeln("$ $command");
        passthru($command);
        writeln();
    }

    chdir(__DIR__);
    
    if(file_exists(FLAG_FILE) && file_get_contents(FLAG_FILE) === 'GO !') {
        if(!file_exists(EASYBOOK_PATH)) {
            command('git clone http://github.com/javiereguiluz/easybook');

            chdir(EASYBOOK_PATH);
            command('curl -s http://getcomposer.org/installer | ' . PHP_BINARY);
            command(PHP_BINARY . ' composer.phar install');

            chdir(EASYBOOK_DOC_PATH);
            command('git clone http://github.com/marmotz/atoum-s-documentation');
        }

        chdir(EASYBOOK_PATH);
        command(PHP_BINARY . ' book publish atoum-s-documentation/fr print');
        command(PHP_BINARY . ' book publish atoum-s-documentation/fr web');
        command(PHP_BINARY . ' book publish atoum-s-documentation/fr website');

        file_put_contents(FLAG_FILE, '');
    }