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
 *  o pecl/cvsclient (only for --updatecvs)
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
    'verbose'   => array(
        'short' => 'v',
        'max'   => 0,
        'desc'  => 'verbose output',
    ),
    'cvsserver' => array(
        'short' => 'cs',
        'max'   => 1,
        'min'   => 1,
        'default'=>'oss.software.ibm.com',
        'desc'  => 'CVS pserver hostname',
    ),
    'cvsrepo'   => array(
        'short' => 'cr',
        'max'   => 1,
        'min'   => 1,
        'default'=>'/usr/cvs/icu',
        'desc'  => 'CVS pserver repository',
    ),
    'cvsuser'   => array(
        'short' => 'cu',
        'max'   => 1,
        'min'   => 1,
        'default'=>'anoncvs',
        'desc'  => 'CVS pserver username',
    ),
    'cvspass'   => array(
        'short' => 'cp',
        'max'   => 1,
        'min'   => 1,
        'default'=>'anoncvs',
        'desc'  => 'CVS pserver password',
    ),
    'cvsmodule' => array(
        'short' => 'cm',
        'max'   => 1,
        'min'   => 1,
        'default'=>'locale/common/main',
        'desc'  => 'CVS module',
    ),
    'checkoutdir'=>array(
        'short' => 'cd',
        'max'   => 1,
        'min'   => 1,
        'default'=>'locales',
        'desc'  => 'checkout directory to use',
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
    )
);

$opt = &Console_Getargs::factory($cnf);

if (PEAR::isError($opt)) {
    usage($opt);
}

if (    !$opt->isDefined('updatecvs')   and
        !$opt->isDefined('languages')   and
        !$opt->isDefined('countries')   and
        !$opt->isDefined('currencies')) {
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
    verbose('Updating CVS checkout');
    
    if (!PEAR::loadExtension('cvsclient')) {
        usage('ext/cvsclient not available!');
    }
    
    $host = $opt->getValue('cvsserver');
    $repo = $opt->getValue('cvsrepo');
    verbose("Connecting to CVS pserver $host:$repo");
    
    if (!$cvs = cvsclient_connect($host, $repo)) {
        usage($php_errormsg);
    }
    verbose("Connected to CVS pserver $host:$repo");
    
    $user = $opt->getValue('cvsuser');
    $pass = $opt->getValue('cvspass');
    verbose("Logging in to CVS pserver with '$user:$pass'");
    if (!cvsclient_login($cvs, $user, $pass)) {
        usage($php_errormsg);
    }
    verbose("Logged in as '$user:$pass'");
    
    $mod  = $opt->getValue('cvsmodule');
    $path = realpath($opt->getValue('checkoutdir'));
    verbose("Updating $mod in $path");
    foreach (glob($path .'/??.xml') as $file) {
        verbose("Retrieving $file");
        if (!cvsclient_retrieve($cvs, $mod, basename($file), $file)) {
            usage($php_errormsg);
        }
    }
    verbose("Updating from CVS done\n");
}

function countries($path)
{
    verbose("Updating countries");
    
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
    verbose("Updating languages");
    
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
    verbose("Updating currencies");
    
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
    if (count($sx->numbers->currencies[0]))
    foreach ($sx->numbers->currencies[0] as $c) {
        verbose('.');
        $ar[(string) $c['type']] = $c->displayName;
    }
    verbose("Loaded ". count($ar) ." codes");
    return $ar;
}

function sx_load($array, $casefunc)
{
    $ar = array();
    if (count($array))
    foreach ($array as $p) {
        if (strlen($p['type']) == 2) {
            verbose('.');
            $ar[$casefunc($p['type'])] = 
                mb_ereg_replace('\'', '\\\'', 
                mb_convert_case($p, MB_CASE_TITLE, 'UTF-8'));
        }
    }
    verbose("Loaded ". count($ar) ." codes");
    return $ar;
}

function write_lang_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('languagesdir'));
    verbose("Writing language codes of language '$lang'");
    return write_file($path ."/$lang.php", $codes);
}

function write_country_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('countriesdir'));
    verbose("Writing country codes of language '$lang'");
    return write_file($path ."/$lang.php", $codes);
}

function write_currency_file($lang, $codes)
{
    global $opt;
    $path = realpath($opt->getValue('currenciesdir'));
    verbose("Writing currency codes of language '$lang'");
    return write_file($path ."/$lang.php", $codes);
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
