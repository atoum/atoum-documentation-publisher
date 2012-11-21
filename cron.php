<?php
    define('FLAG_FILE',         __DIR__ . '/flag');
    define('COMPOSER_PATH',     __DIR__ . '/composer.phar');
    define('EASYBOOK_PATH',     __DIR__ . '/easybook');
    define('ATOUM_DOC_PATH',    EASYBOOK_PATH . '/doc/atoum-s-documentation');

    if(!defined('PHP_BINARY')) {
        define('PHP_BINARY', $_SERVER['_']);
    }

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
        (
            isset($argv[1]) &&
            !preg_match('/^\-/', $argv[1]) &&
            ($repoUrl = $argv[1]) !== ''
        ) ||
        (
            file_exists(FLAG_FILE) &&
            ($repoUrl = trim(file_get_contents(FLAG_FILE))) !== ''
        )
    ) {
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

        if(isset($argv[2]) && !preg_match('/^\-/', $argv[2])) {
            chdir(ATOUM_DOC_PATH);
            command('git checkout ' . escapeshellarg($argv[2]));
        }

        // generate documentation
        chdir(EASYBOOK_PATH);

        $langs = array('--fr', '--en');
        $lang['fr'] = count(array_intersect($langs, $argv)) ? in_array('--fr', $argv) : true;
        $lang['en'] = count(array_intersect($langs, $argv)) ? in_array('--en', $argv) : true;

        $pubs = array('--print', '--web', '--website');
        $pub['print']   = count(array_intersect($pubs, $argv)) ? in_array('--print', $argv) : true;
        $pub['web']     = count(array_intersect($pubs, $argv)) ? in_array('--web', $argv) : true;
        $pub['website'] = count(array_intersect($pubs, $argv)) ? in_array('--website', $argv) : true;

        foreach($lang as $langKey => $langSwitch) {
            foreach($pub as $pubKey => $pubSwitch) {
                if($langSwitch && $pubSwitch) {
                    command(PHP_BINARY . ' book publish atoum-s-documentation/' . $langKey . ' ' . $pubKey);
                }
            }
        }

        // reinit flag file
        file_put_contents(FLAG_FILE, '');
    }
