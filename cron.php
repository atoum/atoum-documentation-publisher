<?php
    define('FLAG_FILE',         __DIR__ . '/flag');
    define('COMPOSER_PATH',     __DIR__ . '/composer.phar');
    define('SIA_PATH',          __DIR__ . '/sia');
    define('DOC_SOURCE',        __DIR__ . '/sources');
    define('DOC_OUTPUT',        $argv[1]);
    define('PATH',              '$PATH:/bin:/usr/bin:/usr/local/bin');

    function writeln($txt = '') {
        echo "$txt\n";
    }

    function command($command) {
        $command = 'PATH=' . PATH . '; ' . $command;
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
            command('curl -s http://getcomposer.org/installer | php');
        }
        else {
            command('php composer.phar self-update');
        }

        // get sia
        if(!file_exists(SIA_PATH)) {
            command('git clone https://github.com/marmotz/sia.git');
            chdir(SIA_PATH);
            command('php ../composer.phar install');
        }
        else {
            chdir(SIA_PATH);
            command('git pull');
            command('php ../composer.phar update');
        }

        // get documentation source
        command('rm -rf ' . DOC_SOURCE);
        command("git clone $repoUrl " . DOC_SOURCE);

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
        command('php bin/sia -i ' . DOC_SOURCE . '/fr/Contents/ -o ' . DOC_OUTPUT . '/fr -t atoum');
        command('php bin/sia -i ' . DOC_SOURCE . '/en/Contents/ -o ' . DOC_OUTPUT . '/en -t atoum');

        // reinit flag file
        file_put_contents(FLAG_FILE, '');
    }