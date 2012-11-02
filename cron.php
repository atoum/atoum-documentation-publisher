<?php
    define('FLAG_FILE',         __DIR__ . '/flag');
    define('COMPOSER_PATH',     __DIR__ . '/composer.phar');
    define('EASYBOOK_PATH',     __DIR__ . '/easybook');
    define('ATOUM_DOC_PATH',    EASYBOOK_PATH . '/doc/atoum-s-documentation');

    function writeln($txt = '') {
        echo "$txt\n";
    }

    function command($command) {
        writeln("$ $command");
        passthru($command);
        writeln();
    }

    chdir(__DIR__);
    
    if(file_exists(FLAG_FILE) && ($repoUrl = file_get_contents(FLAG_FILE)) !== '') {
        // get composer
        if(!file_exists(COMPOSER_PATH)) {
            command('curl -s http://getcomposer.org/installer | ' . PHP_BINARY);
        }
        else {
            command(PHP_BINARY . ' composer.phar self-update');
        }


        // get easybook
        if(!file_exists(EASYBOOK_PATH)) {
            command(PHP_BINARY . ' composer.phar -n create-project easybook/easybook');
        }
        else {
            chdir(EASYBOOK_PATH);
            command(PHP_BINARY . ' ../composer.phar update');
        }


        // get documentation source
        command('rm -rf ' . ATOUM_DOC_PATH);
        command("git clone $repoUrl " . ATOUM_DOC_PATH);


        // generate documentation
        chdir(EASYBOOK_PATH);

        command(PHP_BINARY . ' book publish atoum-s-documentation/en print');
        command(PHP_BINARY . ' book publish atoum-s-documentation/en web');
        command(PHP_BINARY . ' book publish atoum-s-documentation/en website');

        command(PHP_BINARY . ' book publish atoum-s-documentation/fr print');
        command(PHP_BINARY . ' book publish atoum-s-documentation/fr web');
        command(PHP_BINARY . ' book publish atoum-s-documentation/fr website');


        // reinit flag file
        file_put_contents(FLAG_FILE, '');
    }