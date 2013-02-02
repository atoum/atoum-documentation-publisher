<?php
    define('FLAG_FILE',         __DIR__ . '/flag');
    define('COMPOSER_PATH',     __DIR__ . '/composer.phar');
    define('SIA_PATH',          __DIR__ . '/sia');
    define('DOC_SOURCE',        __DIR__ . '/sources');
    define('DOC_OUTPUT',        $argv[1]);

    if(!defined('PHP_BINARY')) {
        define('PHP_BINARY', '/usr/local/bin/php');
    }

    define('GIT_BINARY',  '/usr/local/bin/git');
    define('CURL_BINARY', '/usr/local/bin/curl');

    function writeln($txt = '') {
        echo "$txt\n";
    }

    function command($command) {
        writeln("$ $command");
        passthru($command);
        writeln();
    }

    chdir(__DIR__);
    if(
        file_exists(FLAG_FILE) &&
        ($repoUrl = trim(file_get_contents(FLAG_FILE))) !== ''
    ) {
        // get composer
        if(!file_exists(COMPOSER_PATH)) {
            command(CURL_BINARY . ' -s http://getcomposer.org/installer | ' . PHP_BINARY);
        }
        else {
            command(PHP_BINARY . ' composer.phar self-update');
        }

        // get sia
        if(!file_exists(SIA_PATH)) {
            command(GIT_BINARY . ' clone https://github.com/marmotz/sia.git');
            chdir(SIA_PATH);
            command(PHP_BINARY . ' ../composer.phar install');
        }
        else {
            chdir(SIA_PATH);
            command(GIT_BINARY . ' pull');
            command(PHP_BINARY . ' ../composer.phar update');
        }

        // get documentation source
        command('rm -rf ' . DOC_SOURCE);
        command(GIT_BINARY . " clone $repoUrl " . DOC_SOURCE);

        // create ouput directory
        if(!file_exists(DOC_OUTPUT)) {
            mkdir(DOC_OUTPUT);
        }

        if(!file_exists(DOC_OUTPUT . '/fr')) {
            mkdir(DOC_OUTPUT . '/fr');
        }

        if(!file_exists(DOC_OUTPUT . '/en')) {
            mkdir(DOC_OUTPUT . '/en');
        }

        // generate documentation
        chdir(SIA_PATH);
        command(PHP_BINARY . ' bin/sia -i ' . DOC_SOURCE . '/fr/Contents/ -o ' . DOC_OUTPUT . '/fr -t atoum');
        command(PHP_BINARY . ' bin/sia -i ' . DOC_SOURCE . '/en/Contents/ -o ' . DOC_OUTPUT . '/en -t atoum');

        // reinit flag file
        file_put_contents(FLAG_FILE, '');
    }