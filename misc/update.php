#! php5
<?php

/**
 * Updater for I18Nv2
 *
 * $Id$
 */

/**
 * Track Errors
 */
ini_set('track_errors', true);

/**#@+
 * Requires:
 *  o PHP5
 *  o PEAR
 *  o Console_Getargs
 *  o Archive_Tar
 *  o ext/mbstring
 *  o ext/simplexml
 */
require_once 'PEAR.php';
require_once 'Console/Getargs.php';

if (!PEAR::loadExtension('simplexml')) {
    usage('ext/simplexml is required!');
}
if (!PEAR::loadExtension('mbstring')) {
    usage('ext/mbstring is required!');
}
/**#@-*/

$cnf = array(
    'updatecvs' => array(
        'short' => 'u',
        'max'   => 0,
        'desc'  => 'update/checkout ICU XML files from CVS'
    ),
    'languages' => array(
        'short' => 'l',
        'max'   => 0,
        'desc'  => 'update I18Nv2 languages',
    ),
    'countries' => array(
        'short' => 'c',
        'max'   => 0,
        'desc'  => 'update I18Nv2 countries',
    ),
    'currencies'=> array(
        'short' => 'y',
        'max'   => 0,
        'desc'  => 'update I18Nv2 currencies',
    ),
    'locales'=> array(
        'short' => 'a',
        'max'   => 0,
        'desc'  => 'update I18Nv2 locales',
    ),
    'verbose'   => array(
        'short' => 'v',
        'max'   => 0,
        'desc'  => 'verbose output',
    ),
    'cleanup' => array(
        'short' => 'cu',
        'max'   => 0,
        'desc'  => 'cleanups after the update',
    ),
    'cvsmodule' => array(
        'short' => 'cm',
        'max'   => 1,
        'min'   => 1,
        'default'=>'cldr/common/main',
        'desc'  => 'CVS module',
    ),
    'checkoutdir'=>array(
        'short' => 'cd',
        'max'   => 1,
        'min'   => 1,
        'default'=>'locales',
        'desc'  => 'checkout directory to use',
    ),
    'cvsdir' => array(
        'short' => 'dd',
        'max'   => 1,
        'min'   => 1,
        'default'=>'cvs',
        'desc'  => 'cvs directory to use',
    ),
    'snapshothost' => array(
        'short' => 'ssh',
        'max'   => 1,
        'min'   => 1,
        'default'=>'ftp://ftp.unicode.org/Public/cldr/',
        'desc'  => 'host to fetch the snapshot from',
    ),
    'snapshotfile' => array(
        'short' => 'ssf',
        'max'   => 1,
        'min'   => 1,
        'default'=>'cldr-repository-daily.tgz',
        'desc'  => 'name of the snapshot',
    ),
    'languagesdir'=>array(
        'short' => 'dl',
        'max'   => 1,
        'min'   => 1,
        'default'=>'../Language',
        'desc'  => 'path where updated language files should be put',
    ),
    'countriesdir'=>array(
        'short' => 'dc',
        'max'   => 1,
        'min'   => 1,
        'default'=>'../Country',
        'desc'  => 'path where updated country files should be put',
    ),
    'currenciesdir'=>array(
        'short' => 'dy',
        'max'   => 1,
        'min'   => 1,
        'default'=>'../Currency',
        'desc'  => 'path where updated currency files should be put',
    ),
    'localesdir'=>array(
        'short' => 'do',
        'max'   => 1,
        'min'   => 1,
        'default'=>'../Locales',
        'desc'  => 'path where updated locales files should be put',
    )
);

$opt = &Console_Getargs::factory($cnf);

if (PEAR::isError($opt)) {
    usage($opt);
}

if (
    !$opt->isDefined('updatecvs')   &&
    !$opt->isDefined('languages')   &&
    !$opt->isDefined('countries')   &&
    !$opt->isDefined('currencies')  &&
    !$opt->isDefined('locales')
) {
    usage();
}

if ($opt->isDefined('updatecvs')) {
    updatecvs();
}
if ($opt->isDefined('languages')) {
    languages(realpath($opt->getValue('checkoutdir')));
}
if ($opt->isDefined('countries')) {
    countries(realpath($opt->getValue('checkoutdir')));
}
if ($opt->isDefined('currencies')) {
    currencies(realpath($opt->getValue('checkoutdir')));
}
if ($opt->isDefined('locales')) {
    locales(realpath($opt->getValue('checkoutdir')));
}


# --- functions

function usage($e = null)
{
    global $cnf;
    $header = "\nI18Nv2 Updater\n";
    if (isset($e)) {
        echo is_a($e, 'PEAR_Error') ? $e->getMessage() : $e, "\n";
        exit(1);
    } else {
        echo Console_Getargs::getHelp($cnf, $header);
        exit;
    }
}

function verbose($str)
{
    global $opt;
    if ($opt->isDefined('verbose')) {
        echo $str == '.' ? $str : $str ."\n";
    }
}

function updatecvs()
{
    global $opt;

    require_once 'System.php';

    $host = $opt->getValue('snapshothost');
    $file = $opt->getValue('snapshotfile');
    $wget = $host . $file;
    verbose("Fetching snapshot $wget");
    verbose(shell_exec("wget $wget"));
    if (!file_exists($file)) {
        return verbose("Failed");
    }
    verbose('Fetching complete');

    verbose('Creating directories');
    $cvs = $opt->getValue('cvsdir');
    verbose("mkdir -p $cvs");
    if (!System::mkdir(array('-p', $cvs))) {
        return verbose("Failed");
    }

    $current = dirname(__FILE__) . '/';

    $locales = $opt->getValue('checkoutdir');
    verbose("mkdir -p $current$locales");
    if (!System::mkdir(array('-p', $current.$locales))) {
        return verbose("Failed");
    }
    verbose('Done');

    verbose('Extracting snapshot');
    verbose("tar xzf $file");
    require_once 'Archive/Tar.php';
    $tar = new Archive_Tar($file);
    if (true !== $tar->extract($current.$cvs)) {
        return verbose("Failed");
    }
    verbose('Done');

    verbose('Checking out files out from local CVS');
    verbose("chdir $current$locales");
    chdir($current.$locales);
    $root = realpath($current.$cvs);
    $mod  = $opt->getValue('cvsmodule');
    $command = "cvs -d \"$root\" co -d \".\" $mod";
    verbose($command);
    verbose(shell_exec($command));
    verbose("chdir $current");
    chdir($current);
    verbose('Done');

}

function cleanup()
{
    global $opt;
    // cleanup
    require_once 'System.php';

    verbose('Performing cleanup');

    $cvs = $opt->getValue('cvsdir');
    if (!System::rm("-rf $cvs")) {
        verbose("Could not remove $cvs");
    }

    $locales = $opt->getValue('checkoutdir');
    if (!System::rm("-rf $locales")) {
        verbose("Could not remove $locales");
    }

    $file = $opt->getValue('snapshotfile');
    if (!System::rm($file)) {
        verbose("Could not remove $file");
    }

    verbose('Cleanup complete');
}

function countries($path)
{
    verbose('Updating countries');

    // load english
    $en = sx_load_ctrys($path .'/en.xml');

    $count = 0;
    foreach (glob($path .'/??.xml') as $file) {
        list($lang) = explode('.', basename($file));
        $codes = array_merge($en, sx_load_ctrys($file));
        write_country_file($lang, $codes);
        verbose("Done\n");
        ++$count;
    }

    verbose("Updated $count country files\n");
}

function languages($path)
{
    verbose('Updating languages');

    // load english
    $en = sx_load_langs($path .'/en.xml');

    $count = 0;
    foreach (glob($path .'/??.xml') as $file) {
        list($lang) = explode('.', basename($file));
        $codes = array_merge($en, sx_load_langs($file));
        write_lang_file($lang, $codes);
        verbose("Done\n");
        ++$count;
    }

    verbose("Updated $count language files\n");
}

function currencies($path)
{
    verbose('Updating currencies');

    // load english
    $en = sx_load_crrcys($path .'/en.xml');

    $count = 0;
    foreach (glob($path .'/??.xml') as $file) {
        list($lang) = explode('.', basename($file));
        $codes = array_merge($en, sx_load_crrcys($file));
        write_currency_file($lang, $codes);
        verbose("Done\n");
        ++$count;
    }

    verbose("Updated $count currency files\n");
}

function locales($path)
{
    verbose('Updating locales');

    // load english
//     $en = sx_load_????($path .'/en.xml');

    $count = 0;
    // First we fetch the global files
    foreach (glob($path .'/??.xml') as $file) {
        $locale = sx_load_locales($file);
        // Now we fetch the sub locales
        $pattern = "$path/" . substr($file, strlen($file)-6, -4) . '_??.xml';
        foreach (glob($pattern) as $sfile) {
            verbose("$sfile\n");
        }
        verbose("Done\n");
        ++$count;
    }

    verbose("Updated $count locale files\n");
}

function sx_load_langs($file)
{
    verbose("Loading languages of '$file'");
    $sx = simplexml_load_file($file);
    return sx_load($sx->localeDisplayNames->languages[0], 'strtolower');
}

function sx_load_ctrys($file)
{
    verbose("Loading countries of '$file'");
    $sx = simplexml_load_file($file);
    return sx_load($sx->localeDisplayNames->territories[0], 'strtoupper');
}

function sx_load_crrcys($file)
{
    verbose("Loading currencies of '$file'");
    $sx = simplexml_load_file($file);
    $ar = array();
    if (count($sx->numbers->currencies[0])) {
        foreach ($sx->numbers->currencies[0] as $c) {
            verbose('.');
            $ar[(string) $c['type']] = $c->displayName;
        }
    }
    verbose('Loaded ' . count($ar) . ' codes');
    return $ar;
}

function sx_load_locales($file)
{
    verbose("Loading locales of '$file'");
    $sx = simplexml_load_file($file);
    $ar = array();
    if (count($sx->dates->calendars->calendar->dateFormats[0])) {
        foreach ($sx->dates->calendars->calendar->dateFormats[0] as $l) {
            $ar['date'][(string)$l['type']] = (string)$l->dateFormat->pattern;
        }
    } else {
        $ar['date'] = array();
    }

    if (count($sx->dates->calendars->calendar->timeFormats[0])) {
        foreach ($sx->dates->calendars->calendar->timeFormats[0] as $t) {
            $ar['time'][(string)$t['type']] = (string)$t->timeFormat->pattern;
        }
    } else {
        $ar['time'] = array();
    }

    if (count($sx->numbers->currencyFormats[0])) {
        foreach ($sx->numbers->currencyFormats[0] as $c) {
            $ar['currency'][] = (string)$c->currencyFormatLength->pattern;
        }
    } else {
        $ar['currency'] = array();
    }

var_dump($ar);
    exit;
}

function sx_load($array, $casefunc)
{
    mb_regex_encoding('UTF-8');
    $ar = array();
    if (count($array)) {
        foreach ($array as $p) {
            if (strlen($p['type']) == 2) {
                verbose('.');
                $ar[$casefunc($p['type'])] =
                    mb_ereg_replace('\'', '\\\'',
                    mb_strtoupper(mb_substr($p, 0, 1, 'UTF-8'), 'UTF-8') .
                    mb_substr($p, 1, mb_strlen($p, 'UTF-8'), 'UTF-8'));
            }
        }
    }
    verbose('Loaded ' . count($ar) . ' codes');
    return $ar;
}

function write_lang_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('languagesdir'));
    verbose("Writing language codes of language '$lang'");
    return write_file("$path/$lang.php", $codes);
}

function write_country_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('countriesdir'));
    verbose("Writing country codes of language '$lang'");
    return write_file("$path/$lang.php", $codes);
}

function write_currency_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('currenciesdir'));
    verbose("Writing currency codes of language '$lang'");
    return write_file("$path/$lang.php", $codes);
}

function write_locale_file($locale, $data)
{
    global $opt;
    $path = realpath($opt->getValue('localesdir'));
    verbose("Writing data of locale '$locale'");
    $path = "$path/$locale.php";

    if (!is_dir($dir = dirname($path))) {
        require_once 'System.php';
        verbose("Createding directory '$dir'");
        System::mkdir(array('-p', $dir));
    }

    $content = "<?php\n/**\n * \$Id\$\n */\n";

    // process dates
    $content .= '$this->dateFormats = array(' . "\n";
    foreach ($data['date'] as $code => $string) {
        verbose('.');
        $content .= "    '$code'   => '$string',\n";
    }
    $content .= ");\n";

    // process times
    $content .= '$this->timeFormats = array(' . "\n";
    foreach ($data['time'] as $code => $string) {
        verbose('.');
        $content .= "    '$code'   => '$string',\n";
    }
    $content .= ");\n";

    $content .= "?>\n";
    return file_put_contents($path, $content);
}

function write_file($path, $codes)
{
    verbose("Writing ". count($codes) ." codes to '$path'");

    if (!is_dir($dir = dirname($path))) {
        require_once 'System.php';
        verbose("Createding directory '$dir'");
        System::mkdir(array('-p', $dir));
    }
    $content = "<?php\n/**\n * \$Id\$\n */\n\$this->codes = array(\n";
    foreach ($codes as $code => $string) {
        verbose('.');
        $content .= "    '$code' => '$string',\n";
    }
    $content .= ");\n?>\n";
    return file_put_contents($path, $content);
}
?>
