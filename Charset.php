<?php
// +----------------------------------------------------------------------+
// | PEAR :: I18Nv2 :: Charset                                            |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is available at http://www.php.net/license/3_0.txt              |
// | If you did not receive a copy of the PHP license and are unable      |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Michael Wallner <mike@iworks.at>                  |
// +----------------------------------------------------------------------+
//
// $Id$

/**
 * I18Nv2_Charset
 *
 * @author      Michael Wallner <mike@php.net>
 * @version     $Revision$
 * @package     I18Nv2
 * 
 * @static
 * @access      public
 */
class I18Nv2_Charset
{
    /**
     * Standardize
     * 
     * @static
     * @access  public
     * @return  string
     * @param   string  $charset
     */
    function standardize($charset)
    {
        return strToUpper(preg_replace('/[^[:alnum:]-]/', '-', $charset));
    }
    
    /**
     * Is Charset
     * 
     * @static
     * @access  public
     * @return  bool
     * @param   string  $charset
     */
    function isCharset($charset)
    {
        $name = I18Nv3_Charset::standardize($charset);
        return isset($GLOBALS['_I18Nv2_Charset_Names'][$name]);
    }
    
    /**
     * Is Alias
     * 
     * @static
     * @access  public
     * @return  bool
     * @param   string  $charset
     */
    function isAlias($charset)
    {
        return (bool) I18Nv2_Charset::forAlias($charset);
    }
    
    /**
     * Exists
     * 
     * @static
     * @access  public
     * @return  bool
     * @param   string  $charset
     */
    function exists($charset)
    {
        $name = I18Nv2_Charset::standardize($charset);
        return isset($GLOBALS['_I18Nv2_Charset_Names'][$name]) or
            I18Nv2_Charset::forAlias($name) !== false;
    }
    
    /**
     * Aliases Of
     * 
     * @static
     * @access  public
     * @return  array
     * @param   string  $charset
     */
    function aliasesOf($charset)
    {
        $name = I18Nv2_Charset::standardize($charset);
        if (isset($GLOBALS['_I18Nv2_Charset_Aliases'][$name])) {
            return $GLOBALS['_I18Nv2_Charset_Aliases'][$name];
        }
        return array();
    }
    
    /**
     * For Alias
     * 
     * @static
     * @access  public
     * @return  string|false
     * @param   string  $alias
     */
    function forAlias($alias)
    {
        $name = I18Nv2_Charset::standardize($alias);
        foreach (array_keys($GLOBALS['_I18Nv2_Charset_Aliases']) as $a) {
            if (in_array($name, $GLOBALS['_I18Nv2_Charset_Aliases'][$a])) {
                return $a;
            }
        }
        return false;
    }
    
}

$GLOBALS['_I18Nv2_Charset_Names']  = array(
    'US-ASCII',
    'ISO-10646-UTF-1',
    'ISO-646-BASIC',
    'INVARIANT',
    'ISO-646-IRV',
    'BS-4730',
    'NATS-SEFI',
    'NATS-SEFI-ADD',
    'NATS-DANO',
    'NATS-DANO-ADD',
    'SEN-850200-B',
    'SEN-850200-C',
    'KS-C-5601-1987',
    'ISO-2022-KR',
    'EUC-KR',
    'ISO-2022-JP',
    'ISO-2022-JP-2',
    'ISO-2022-CN',
    'ISO-2022-CN-EXT',
    'JIS-C6220-1969-JP',
    'JIS-C6220-1969-RO',
    'IT',
    'PT',
    'ES',
    'GREEK7-OLD',
    'LATIN-GREEK',
    'DIN-66003',
    'NF-Z-62-010-',
    'LATIN-GREEK-1',
    'ISO-5427',
    'JIS-C6226-1978',
    'BS-VIEWDATA',
    'INIS',
    'INIS-8',
    'INIS-CYRILLIC',
    'ISO-5427',
    'ISO-5428',
    'GB-1988-80',
    'GB-2312-80',
    'NS-4551-1',
    'NS-4551-2',
    'NF-Z-62-010',
    'VIDEOTEX-SUPPL',
    'PT2',
    'ES2',
    'MSZ-7795-3',
    'JIS-C6226-1983',
    'GREEK7',
    'ASMO-449',
    'ISO-IR-90',
    'JIS-C6229-1984-A',
    'JIS-C6229-1984-B',
    'JIS-C6229-1984-B-ADD',
    'JIS-C6229-1984-HAND',
    'JIS-C6229-1984-HAND-ADD',
    'JIS-C6229-1984-KANA',
    'ISO-2033-1983',
    'ANSI-X3-110-1983',
    'ISO-8859-1',
    'ISO-8859-2',
    'T-61-7BIT',
    'T-61-8BIT',
    'ISO-8859-3',
    'ISO-8859-4',
    'ECMA-CYRILLIC',
    'CSA-Z243-4-1985-1',
    'CSA-Z243-4-1985-2',
    'CSA-Z243-4-1985-GR',
    'ISO-8859-6',
    'ISO-8859-6-E',
    'ISO-8859-6-I',
    'ISO-8859-7',
    'T-101-G2',
    'ISO-8859-8',
    'ISO-8859-8-E',
    'ISO-8859-8-I',
    'CSN-369103',
    'JUS-I-B1-002',
    'ISO-6937-2-ADD',
    'IEC-P27-1',
    'ISO-8859-5',
    'JUS-I-B1-003-SERB',
    'JUS-I-B1-003-MAC',
    'ISO-8859-9',
    'GREEK-CCITT',
    'NC-NC00-10',
    'ISO-6937-2-25',
    'GOST-19768-74',
    'ISO-8859-SUPP',
    'ISO-10367-BOX',
    'ISO-8859-10',
    'LATIN-LAP',
    'JIS-X0212-1990',
    'DS-2089',
    'US-DK',
    'DK-US',
    'JIS-X0201',
    'KSC5636',
    'ISO-10646-UCS-2',
    'ISO-10646-UCS-4',
    'DEC-MCS',
    'HP-ROMAN8',
    'MACINTOSH',
    'IBM037',
    'IBM038',
    'IBM273',
    'IBM274',
    'IBM275',
    'IBM277',
    'IBM278',
    'IBM280',
    'IBM281',
    'IBM284',
    'IBM285',
    'IBM290',
    'IBM297',
    'IBM420',
    'IBM423',
    'IBM424',
    'IBM437',
    'IBM500',
    'IBM775',
    'IBM850',
    'IBM851',
    'IBM852',
    'IBM855',
    'IBM857',
    'IBM860',
    'IBM861',
    'IBM862',
    'IBM863',
    'IBM864',
    'IBM865',
    'IBM866',
    'IBM868',
    'IBM869',
    'IBM870',
    'IBM871',
    'IBM880',
    'IBM891',
    'IBM903',
    'IBM904',
    'IBM905',
    'IBM918',
    'IBM1026',
    'EBCDIC-AT-DE',
    'EBCDIC-AT-DE-A',
    'EBCDIC-CA-FR',
    'EBCDIC-DK-NO',
    'EBCDIC-DK-NO-A',
    'EBCDIC-FI-SE',
    'EBCDIC-FI-SE-A',
    'EBCDIC-FR',
    'EBCDIC-IT',
    'EBCDIC-PT',
    'EBCDIC-ES',
    'EBCDIC-ES-A',
    'EBCDIC-ES-S',
    'EBCDIC-UK',
    'EBCDIC-US',
    'UNKNOWN-8BIT',
    'MNEMONIC',
    'MNEM',
    'VISCII',
    'VIQR',
    'KOI8-R',
    'KOI8-U',
    'IBM00858',
    'IBM00924',
    'IBM01140',
    'IBM01141',
    'IBM01142',
    'IBM01143',
    'IBM01144',
    'IBM01145',
    'IBM01146',
    'IBM01147',
    'IBM01148',
    'IBM01149',
    'BIG5-HKSCS',
    'IBM1047',
    'PTCP154',
    'AMIGA-1251',
    'UNICODE-1-1',
    'SCSU',
    'UTF-7',
    'UTF-16BE',
    'UTF-16LE',
    'UTF-16',
    'CESU-8',
    'UTF-32',
    'UTF-32BE',
    'UTF-32LE',
    'BOCU-1',
    'UNICODE-1-1-UTF-7',
    'UTF-8',
    'ISO-8859-13',
    'ISO-8859-14',
    'ISO-8859-15',
    'ISO-8859-16',
    'GBK',
    'GB18030',
    'OSD-EBCDIC-DF04-15',
    'OSD-EBCDIC-DF03-IRV',
    'OSD-EBCDIC-DF04-1',
    'JIS-ENCODING',
    'SHIFT-JIS',
    'EUC-JP',
    'EXTENDED-UNIX-CODE-FIXED-WIDTH-FOR-JAPANESE',
    'ISO-10646-UCS-BASIC',
    'ISO-10646-UNICODE-LATIN1',
    'ISO-10646-J-1',
    'ISO-UNICODE-IBM-1261',
    'ISO-UNICODE-IBM-1268',
    'ISO-UNICODE-IBM-1276',
    'ISO-UNICODE-IBM-1264',
    'ISO-UNICODE-IBM-1265',
    'ISO-8859-1-WINDOWS-3-0-LATIN-1',
    'ISO-8859-1-WINDOWS-3-1-LATIN-1',
    'ISO-8859-2-WINDOWS-LATIN-2',
    'ISO-8859-9-WINDOWS-LATIN-5',
    'ADOBE-STANDARD-ENCODING',
    'VENTURA-US',
    'VENTURA-INTERNATIONAL',
    'PC8-DANISH-NORWEGIAN',
    'PC8-TURKISH',
    'IBM-SYMBOLS',
    'IBM-THAI',
    'HP-LEGAL',
    'HP-PI-FONT',
    'HP-MATH8',
    'ADOBE-SYMBOL-ENCODING',
    'HP-DESKTOP',
    'VENTURA-MATH',
    'MICROSOFT-PUBLISHING',
    'WINDOWS-31J',
    'GB2312',
    'BIG5',
    'WINDOWS-1250',
    'WINDOWS-1251',
    'WINDOWS-1252',
    'WINDOWS-1253',
    'WINDOWS-1254',
    'WINDOWS-1255',
    'WINDOWS-1256',
    'WINDOWS-1257',
    'WINDOWS-1258',
    'TIS-620',
    'HZ-GB-2312',
);

$GLOBALS['_I18Nv2_Charset_Aliases'] = array(
    'US-ASCII' => array(
        'ISO-IR-6',
        'ANSI-X3-4-1986',
        'ISO-646-IRV',
        'ASCII',
        'ISO646-US',
        'ANSI-X3-4-1968',
        'US',
        'IBM367',
        'CP367',
        'CSASCII',
    ),
    'ISO-10646-UTF-1' => array(
        'CSISO10646UTF1',
    ),
    'ISO-646-BASIC' => array(
        'REF',
        'CSISO646BASIC1983',
    ),
    'INVARIANT' => array(
        'CSINVARIANT',
    ),
    'ISO-646-IRV' => array(
        'ISO-IR-2',
        'IRV',
        'CSISO2INTLREFVERSION',
    ),
    'BS-4730' => array(
        'ISO-IR-4',
        'ISO646-GB',
        'GB',
        'UK',
        'CSISO4UNITEDKINGDOM',
    ),
    'NATS-SEFI' => array(
        'ISO-IR-8-1',
        'CSNATSSEFI',
    ),
    'NATS-SEFI-ADD' => array(
        'ISO-IR-8-2',
        'CSNATSSEFIADD',
    ),
    'NATS-DANO' => array(
        'ISO-IR-9-1',
        'CSNATSDANO',
    ),
    'NATS-DANO-ADD' => array(
        'ISO-IR-9-2',
        'CSNATSDANOADD',
    ),
    'SEN-850200-B' => array(
        'ISO-IR-10',
        'FI',
        'ISO646-FI',
        'ISO646-SE',
        'SE',
        'CSISO10SWEDISH',
    ),
    'SEN-850200-C' => array(
        'ISO-IR-11',
        'ISO646-SE2',
        'SE2',
        'CSISO11SWEDISHFORNAMES',
    ),
    'KS-C-5601-1987' => array(
        'ISO-IR-149',
        'KS-C-5601-1989',
        'KSC-5601',
        'KOREAN',
        'CSKSC56011987',
    ),
    'ISO-2022-KR' => array(
        'CSISO2022KR',
    ),
    'EUC-KR' => array(
        'CSEUCKR',
    ),
    'ISO-2022-JP' => array(
        'CSISO2022JP',
    ),
    'ISO-2022-JP-2' => array(
        'CSISO2022JP2',
    ),
    'JIS-C6220-1969-JP' => array(
        'JIS-C6220-1969',
        'ISO-IR-13',
        'KATAKANA',
        'X0201-7',
        'CSISO13JISC6220JP',
    ),
    'JIS-C6220-1969-RO' => array(
        'ISO-IR-14',
        'JP',
        'ISO646-JP',
        'CSISO14JISC6220RO',
    ),
    'IT' => array(
        'ISO-IR-15',
        'ISO646-IT',
        'CSISO15ITALIAN',
    ),
    'PT' => array(
        'ISO-IR-16',
        'ISO646-PT',
        'CSISO16PORTUGUESE',
    ),
    'ES' => array(
        'ISO-IR-17',
        'ISO646-ES',
        'CSISO17SPANISH',
    ),
    'GREEK7-OLD' => array(
        'ISO-IR-18',
        'CSISO18GREEK7OLD',
    ),
    'LATIN-GREEK' => array(
        'ISO-IR-19',
        'CSISO19LATINGREEK',
    ),
    'DIN-66003' => array(
        'ISO-IR-21',
        'DE',
        'ISO646-DE',
        'CSISO21GERMAN',
    ),
    'NF-Z-62-010-' => array(
        'ISO-IR-25',
        'ISO646-FR1',
        'CSISO25FRENCH',
    ),
    'LATIN-GREEK-1' => array(
        'ISO-IR-27',
        'CSISO27LATINGREEK1',
    ),
    'ISO-5427' => array(
        'ISO-IR-37',
        'CSISO5427CYRILLIC',
        'ISO-IR-54',
        'ISO5427CYRILLIC1981',
    ),
    'JIS-C6226-1978' => array(
        'ISO-IR-42',
        'CSISO42JISC62261978',
    ),
    'BS-VIEWDATA' => array(
        'ISO-IR-47',
        'CSISO47BSVIEWDATA',
    ),
    'INIS' => array(
        'ISO-IR-49',
        'CSISO49INIS',
    ),
    'INIS-8' => array(
        'ISO-IR-50',
        'CSISO50INIS8',
    ),
    'INIS-CYRILLIC' => array(
        'ISO-IR-51',
        'CSISO51INISCYRILLIC',
    ),
    'ISO-5428' => array(
        'ISO-IR-55',
        'CSISO5428GREEK',
    ),
    'GB-1988-80' => array(
        'ISO-IR-57',
        'CN',
        'ISO646-CN',
        'CSISO57GB1988',
    ),
    'GB-2312-80' => array(
        'ISO-IR-58',
        'CHINESE',
        'CSISO58GB231280',
    ),
    'NS-4551-1' => array(
        'ISO-IR-60',
        'ISO646-NO',
        'NO',
        'CSISO60DANISHNORWEGIAN',
        'CSISO60NORWEGIAN1',
    ),
    'NS-4551-2' => array(
        'ISO646-NO2',
        'ISO-IR-61',
        'NO2',
        'CSISO61NORWEGIAN2',
    ),
    'NF-Z-62-010' => array(
        'ISO-IR-69',
        'ISO646-FR',
        'FR',
        'CSISO69FRENCH',
    ),
    'VIDEOTEX-SUPPL' => array(
        'ISO-IR-70',
        'CSISO70VIDEOTEXSUPP1',
    ),
    'PT2' => array(
        'ISO-IR-84',
        'ISO646-PT2',
        'CSISO84PORTUGUESE2',
    ),
    'ES2' => array(
        'ISO-IR-85',
        'ISO646-ES2',
        'CSISO85SPANISH2',
    ),
    'MSZ-7795-3' => array(
        'ISO-IR-86',
        'ISO646-HU',
        'HU',
        'CSISO86HUNGARIAN',
    ),
    'JIS-C6226-1983' => array(
        'ISO-IR-87',
        'X0208',
        'JIS-X0208-1983',
        'CSISO87JISX0208',
    ),
    'GREEK7' => array(
        'ISO-IR-88',
        'CSISO88GREEK7',
    ),
    'ASMO-449' => array(
        'ISO-9036',
        'ARABIC7',
        'ISO-IR-89',
        'CSISO89ASMO449',
    ),
    'ISO-IR-90' => array(
        'CSISO90',
    ),
    'JIS-C6229-1984-A' => array(
        'ISO-IR-91',
        'JP-OCR-A',
        'CSISO91JISC62291984A',
    ),
    'JIS-C6229-1984-B' => array(
        'ISO-IR-92',
        'ISO646-JP-OCR-B',
        'JP-OCR-B',
        'CSISO92JISC62991984B',
    ),
    'JIS-C6229-1984-B-ADD' => array(
        'ISO-IR-93',
        'JP-OCR-B-ADD',
        'CSISO93JIS62291984BADD',
    ),
    'JIS-C6229-1984-HAND' => array(
        'ISO-IR-94',
        'JP-OCR-HAND',
        'CSISO94JIS62291984HAND',
    ),
    'JIS-C6229-1984-HAND-ADD' => array(
        'ISO-IR-95',
        'JP-OCR-HAND-ADD',
        'CSISO95JIS62291984HANDADD',
    ),
    'JIS-C6229-1984-KANA' => array(
        'ISO-IR-96',
        'CSISO96JISC62291984KANA',
    ),
    'ISO-2033-1983' => array(
        'ISO-IR-98',
        'E13B',
        'CSISO2033',
    ),
    'ANSI-X3-110-1983' => array(
        'ISO-IR-99',
        'CSA-T500-1983',
        'NAPLPS',
        'CSISO99NAPLPS',
    ),
    'ISO-8859-1' => array(
        'ISO-IR-100',
        'ISO-8859-1',
        'LATIN1',
        'L1',
        'IBM819',
        'CP819',
        'CSISOLATIN1',
    ),
    'ISO-8859-2' => array(
        'ISO-IR-101',
        'ISO-8859-2',
        'LATIN2',
        'L2',
        'CSISOLATIN2',
    ),
    'T-61-7BIT' => array(
        'ISO-IR-102',
        'CSISO102T617BIT',
    ),
    'T-61-8BIT' => array(
        'T-61',
        'ISO-IR-103',
        'CSISO103T618BIT',
    ),
    'ISO-8859-3' => array(
        'ISO-IR-109',
        'ISO-8859-3',
        'LATIN3',
        'L3',
        'CSISOLATIN3',
    ),
    'ISO-8859-4' => array(
        'ISO-IR-110',
        'ISO-8859-4',
        'LATIN4',
        'L4',
        'CSISOLATIN4',
    ),
    'ECMA-CYRILLIC' => array(
        'ISO-IR-111',
        'KOI8-E',
        'CSISO111ECMACYRILLIC',
    ),
    'CSA-Z243-4-1985-1' => array(
        'ISO-IR-121',
        'ISO646-CA',
        'CSA7-1',
        'CA',
        'CSISO121CANADIAN1',
    ),
    'CSA-Z243-4-1985-2' => array(
        'ISO-IR-122',
        'ISO646-CA2',
        'CSA7-2',
        'CSISO122CANADIAN2',
    ),
    'CSA-Z243-4-1985-GR' => array(
        'ISO-IR-123',
        'CSISO123CSAZ24341985GR',
    ),
    'ISO-8859-6' => array(
        'ISO-IR-127',
        'ISO-8859-6',
        'ECMA-114',
        'ASMO-708',
        'ARABIC',
        'CSISOLATINARABIC',
    ),
    'ISO-8859-6-E' => array(
        'CSISO88596E',
    ),
    'ISO-8859-6-I' => array(
        'CSISO88596I',
    ),
    'ISO-8859-7' => array(
        'ISO-IR-126',
        'ISO-8859-7',
        'ELOT-928',
        'ECMA-118',
        'GREEK',
        'GREEK8',
        'CSISOLATINGREEK',
    ),
    'T-101-G2' => array(
        'ISO-IR-128',
        'CSISO128T101G2',
    ),
    'ISO-8859-8' => array(
        'ISO-IR-138',
        'ISO-8859-8',
        'HEBREW',
        'CSISOLATINHEBREW',
    ),
    'ISO-8859-8-E' => array(
        'CSISO88598E',
    ),
    'ISO-8859-8-I' => array(
        'CSISO88598I',
    ),
    'CSN-369103' => array(
        'ISO-IR-139',
        'CSISO139CSN369103',
    ),
    'JUS-I-B1-002' => array(
        'ISO-IR-141',
        'ISO646-YU',
        'JS',
        'YU',
        'CSISO141JUSIB1002',
    ),
    'ISO-6937-2-ADD' => array(
        'ISO-IR-142',
        'CSISOTEXTCOMM',
    ),
    'IEC-P27-1' => array(
        'ISO-IR-143',
        'CSISO143IECP271',
    ),
    'ISO-8859-5' => array(
        'ISO-IR-144',
        'ISO-8859-5',
        'CYRILLIC',
        'CSISOLATINCYRILLIC',
    ),
    'JUS-I-B1-003-SERB' => array(
        'ISO-IR-146',
        'SERBIAN',
        'CSISO146SERBIAN',
    ),
    'JUS-I-B1-003-MAC' => array(
        'MACEDONIAN',
        'ISO-IR-147',
        'CSISO147MACEDONIAN',
    ),
    'ISO-8859-9' => array(
        'ISO-IR-148',
        'ISO-8859-9',
        'LATIN5',
        'L5',
        'CSISOLATIN5',
    ),
    'GREEK-CCITT' => array(
        'ISO-IR-150',
        'CSISO150',
        'CSISO150GREEKCCITT',
    ),
    'NC-NC00-10' => array(
        'CUBA',
        'ISO-IR-151',
        'ISO646-CU',
        'CSISO151CUBA',
    ),
    'ISO-6937-2-25' => array(
        'ISO-IR-152',
        'CSISO6937ADD',
    ),
    'GOST-19768-74' => array(
        'ST-SEV-358-88',
        'ISO-IR-153',
        'CSISO153GOST1976874',
    ),
    'ISO-8859-SUPP' => array(
        'ISO-IR-154',
        'LATIN1-2-5',
        'CSISO8859SUPP',
    ),
    'ISO-10367-BOX' => array(
        'ISO-IR-155',
        'CSISO10367BOX',
    ),
    'ISO-8859-10' => array(
        'ISO-IR-157',
        'L6',
        'ISO-8859-10',
        'CSISOLATIN6',
        'LATIN6',
    ),
    'LATIN-LAP' => array(
        'LAP',
        'ISO-IR-158',
        'CSISO158LAP',
    ),
    'JIS-X0212-1990' => array(
        'X0212',
        'ISO-IR-159',
        'CSISO159JISX02121990',
    ),
    'DS-2089' => array(
        'DS2089',
        'ISO646-DK',
        'DK',
        'CSISO646DANISH',
    ),
    'US-DK' => array(
        'CSUSDK',
    ),
    'DK-US' => array(
        'CSDKUS',
    ),
    'JIS-X0201' => array(
        'X0201',
        'CSHALFWIDTHKATAKANA',
    ),
    'KSC5636' => array(
        'ISO646-KR',
        'CSKSC5636',
    ),
    'ISO-10646-UCS-2' => array(
        'CSUNICODE',
    ),
    'ISO-10646-UCS-4' => array(
        'CSUCS4',
    ),
    'DEC-MCS' => array(
        'DEC',
        'CSDECMCS',
    ),
    'HP-ROMAN8' => array(
        'ROMAN8',
        'R8',
        'CSHPROMAN8',
    ),
    'MACINTOSH' => array(
        'MAC',
        'CSMACINTOSH',
    ),
    'IBM037' => array(
        'CP037',
        'EBCDIC-CP-US',
        'EBCDIC-CP-CA',
        'EBCDIC-CP-WT',
        'EBCDIC-CP-NL',
        'CSIBM037',
    ),
    'IBM038' => array(
        'EBCDIC-INT',
        'CP038',
        'CSIBM038',
    ),
    'IBM273' => array(
        'CP273',
        'CSIBM273',
    ),
    'IBM274' => array(
        'EBCDIC-BE',
        'CP274',
        'CSIBM274',
    ),
    'IBM275' => array(
        'EBCDIC-BR',
        'CP275',
        'CSIBM275',
    ),
    'IBM277' => array(
        'EBCDIC-CP-DK',
        'EBCDIC-CP-NO',
        'CSIBM277',
    ),
    'IBM278' => array(
        'CP278',
        'EBCDIC-CP-FI',
        'EBCDIC-CP-SE',
        'CSIBM278',
    ),
    'IBM280' => array(
        'CP280',
        'EBCDIC-CP-IT',
        'CSIBM280',
    ),
    'IBM281' => array(
        'EBCDIC-JP-E',
        'CP281',
        'CSIBM281',
    ),
    'IBM284' => array(
        'CP284',
        'EBCDIC-CP-ES',
        'CSIBM284',
    ),
    'IBM285' => array(
        'CP285',
        'EBCDIC-CP-GB',
        'CSIBM285',
    ),
    'IBM290' => array(
        'CP290',
        'EBCDIC-JP-KANA',
        'CSIBM290',
    ),
    'IBM297' => array(
        'CP297',
        'EBCDIC-CP-FR',
        'CSIBM297',
    ),
    'IBM420' => array(
        'CP420',
        'EBCDIC-CP-AR1',
        'CSIBM420',
    ),
    'IBM423' => array(
        'CP423',
        'EBCDIC-CP-GR',
        'CSIBM423',
    ),
    'IBM424' => array(
        'CP424',
        'EBCDIC-CP-HE',
        'CSIBM424',
    ),
    'IBM437' => array(
        'CP437',
        '437',
        'CSPC8CODEPAGE437',
    ),
    'IBM500' => array(
        'CP500',
        'EBCDIC-CP-BE',
        'EBCDIC-CP-CH',
        'CSIBM500',
    ),
    'IBM775' => array(
        'CP775',
        'CSPC775BALTIC',
    ),
    'IBM850' => array(
        'CP850',
        '850',
        'CSPC850MULTILINGUAL',
    ),
    'IBM851' => array(
        'CP851',
        '851',
        'CSIBM851',
    ),
    'IBM852' => array(
        'CP852',
        '852',
        'CSPCP852',
    ),
    'IBM855' => array(
        'CP855',
        '855',
        'CSIBM855',
    ),
    'IBM857' => array(
        'CP857',
        '857',
        'CSIBM857',
    ),
    'IBM860' => array(
        'CP860',
        '860',
        'CSIBM860',
    ),
    'IBM861' => array(
        'CP861',
        '861',
        'CP-IS',
        'CSIBM861',
    ),
    'IBM862' => array(
        'CP862',
        '862',
        'CSPC862LATINHEBREW',
    ),
    'IBM863' => array(
        'CP863',
        '863',
        'CSIBM863',
    ),
    'IBM864' => array(
        'CP864',
        'CSIBM864',
    ),
    'IBM865' => array(
        'CP865',
        '865',
        'CSIBM865',
    ),
    'IBM866' => array(
        'CP866',
        '866',
        'CSIBM866',
    ),
    'IBM868' => array(
        'CP868',
        'CP-AR',
        'CSIBM868',
    ),
    'IBM869' => array(
        'CP869',
        '869',
        'CP-GR',
        'CSIBM869',
    ),
    'IBM870' => array(
        'CP870',
        'EBCDIC-CP-ROECE',
        'EBCDIC-CP-YU',
        'CSIBM870',
    ),
    'IBM871' => array(
        'CP871',
        'EBCDIC-CP-IS',
        'CSIBM871',
    ),
    'IBM880' => array(
        'CP880',
        'EBCDIC-CYRILLIC',
        'CSIBM880',
    ),
    'IBM891' => array(
        'CP891',
        'CSIBM891',
    ),
    'IBM903' => array(
        'CP903',
        'CSIBM903',
    ),
    'IBM904' => array(
        'CP904',
        '904',
        'CSIBBM904',
    ),
    'IBM905' => array(
        'CP905',
        'EBCDIC-CP-TR',
        'CSIBM905',
    ),
    'IBM918' => array(
        'CP918',
        'EBCDIC-CP-AR2',
        'CSIBM918',
    ),
    'IBM1026' => array(
        'CP1026',
        'CSIBM1026',
    ),
    'EBCDIC-AT-DE' => array(
        'CSIBMEBCDICATDE',
    ),
    'EBCDIC-AT-DE-A' => array(
        'CSEBCDICATDEA',
    ),
    'EBCDIC-CA-FR' => array(
        'CSEBCDICCAFR',
    ),
    'EBCDIC-DK-NO' => array(
        'CSEBCDICDKNO',
    ),
    'EBCDIC-DK-NO-A' => array(
        'CSEBCDICDKNOA',
    ),
    'EBCDIC-FI-SE' => array(
        'CSEBCDICFISE',
    ),
    'EBCDIC-FI-SE-A' => array(
        'CSEBCDICFISEA',
    ),
    'EBCDIC-FR' => array(
        'CSEBCDICFR',
    ),
    'EBCDIC-IT' => array(
        'CSEBCDICIT',
    ),
    'EBCDIC-PT' => array(
        'CSEBCDICPT',
    ),
    'EBCDIC-ES' => array(
        'CSEBCDICES',
    ),
    'EBCDIC-ES-A' => array(
        'CSEBCDICESA',
    ),
    'EBCDIC-ES-S' => array(
        'CSEBCDICESS',
    ),
    'EBCDIC-UK' => array(
        'CSEBCDICUK',
    ),
    'EBCDIC-US' => array(
        'CSEBCDICUS',
    ),
    'UNKNOWN-8BIT' => array(
        'CSUNKNOWN8BIT',
    ),
    'MNEMONIC' => array(
        'CSMNEMONIC',
    ),
    'MNEM' => array(
        'CSMNEM',
    ),
    'VISCII' => array(
        'CSVISCII',
    ),
    'VIQR' => array(
        'CSVIQR',
    ),
    'KOI8-R' => array(
        'CSKOI8R',
    ),
    'IBM00858' => array(
        'CCSID00858',
        'CP00858',
        'PC-MULTILINGUAL-850-EURO',
    ),
    'IBM00924' => array(
        'CCSID00924',
        'CP00924',
        'EBCDIC-LATIN9--EURO',
    ),
    'IBM01140' => array(
        'CCSID01140',
        'CP01140',
        'EBCDIC-US-37-EURO',
    ),
    'IBM01141' => array(
        'CCSID01141',
        'CP01141',
        'EBCDIC-DE-273-EURO',
    ),
    'IBM01142' => array(
        'CCSID01142',
        'CP01142',
        'EBCDIC-DK-277-EURO',
        'EBCDIC-NO-277-EURO',
    ),
    'IBM01143' => array(
        'CCSID01143',
        'CP01143',
        'EBCDIC-FI-278-EURO',
        'EBCDIC-SE-278-EURO',
    ),
    'IBM01144' => array(
        'CCSID01144',
        'CP01144',
        'EBCDIC-IT-280-EURO',
    ),
    'IBM01145' => array(
        'CCSID01145',
        'CP01145',
        'EBCDIC-ES-284-EURO',
    ),
    'IBM01146' => array(
        'CCSID01146',
        'CP01146',
        'EBCDIC-GB-285-EURO',
    ),
    'IBM01147' => array(
        'CCSID01147',
        'CP01147',
        'EBCDIC-FR-297-EURO',
    ),
    'IBM01148' => array(
        'CCSID01148',
        'CP01148',
        'EBCDIC-INTERNATIONAL-500-EURO',
    ),
    'IBM01149' => array(
        'CCSID01149',
        'CP01149',
        'EBCDIC-IS-871-EURO',
    ),
    'IBM1047' => array(
        'IBM-1047',
    ),
    'PTCP154' => array(
        'CSPTCP154',
        'PT154',
        'CP154',
        'CYRILLIC-ASIAN',
    ),
    'AMIGA-1251' => array(
        'AMI1251',
        'AMIGA1251',
        'AMI-1251',
    ),
    'UNICODE-1-1' => array(
        'CSUNICODE11',
    ),
    'CESU-8' => array(
        'CSCESU-8',
    ),
    'BOCU-1' => array(
        'CSBOCU-1',
    ),
    'UNICODE-1-1-UTF-7' => array(
        'CSUNICODE11UTF7',
    ),
    'ISO-8859-14' => array(
        'ISO-IR-199',
        'ISO-8859-14',
        'ISO-8859-14',
        'LATIN8',
        'ISO-CELTIC',
        'L8',
    ),
    'ISO-8859-15' => array(
        'ISO-8859-15',
        'LATIN-9',
    ),
    'ISO-8859-16' => array(
        'ISO-IR-226',
        'ISO-8859-16',
        'ISO-8859-16',
        'LATIN10',
        'L10',
    ),
    'GBK' => array(
        'CP936',
        'MS936',
        'WINDOWS-936',
    ),
    'JIS-ENCODING' => array(
        'CSJISENCODING',
    ),
    'SHIFT-JIS' => array(
        'MS-KANJI',
        'CSSHIFTJIS',
    ),
    'EUC-JP' => array(
        'CSEUCPKDFMTJAPANESE',
    ),
    'EXTENDED-UNIX-CODE-FIXED-WIDTH-FOR-JAPANESE' => array(
        'CSEUCFIXWIDJAPANESE',
    ),
    'ISO-10646-UCS-BASIC' => array(
        'CSUNICODEASCII',
    ),
    'ISO-10646-UNICODE-LATIN1' => array(
        'CSUNICODELATIN1',
        'ISO-10646',
    ),
    'ISO-UNICODE-IBM-1261' => array(
        'CSUNICODEIBM1261',
    ),
    'ISO-UNICODE-IBM-1268' => array(
        'CSUNICODEIBM1268',
    ),
    'ISO-UNICODE-IBM-1276' => array(
        'CSUNICODEIBM1276',
    ),
    'ISO-UNICODE-IBM-1264' => array(
        'CSUNICODEIBM1264',
    ),
    'ISO-UNICODE-IBM-1265' => array(
        'CSUNICODEIBM1265',
    ),
    'ISO-8859-1-WINDOWS-3-0-LATIN-1' => array(
        'CSWINDOWS30LATIN1',
    ),
    'ISO-8859-1-WINDOWS-3-1-LATIN-1' => array(
        'CSWINDOWS31LATIN1',
    ),
    'ISO-8859-2-WINDOWS-LATIN-2' => array(
        'CSWINDOWS31LATIN2',
    ),
    'ISO-8859-9-WINDOWS-LATIN-5' => array(
        'CSWINDOWS31LATIN5',
    ),
    'ADOBE-STANDARD-ENCODING' => array(
        'CSADOBESTANDARDENCODING',
    ),
    'VENTURA-US' => array(
        'CSVENTURAUS',
    ),
    'VENTURA-INTERNATIONAL' => array(
        'CSVENTURAINTERNATIONAL',
    ),
    'PC8-DANISH-NORWEGIAN' => array(
        'CSPC8DANISHNORWEGIAN',
    ),
    'PC8-TURKISH' => array(
        'CSPC8TURKISH',
    ),
    'IBM-SYMBOLS' => array(
        'CSIBMSYMBOLS',
    ),
    'IBM-THAI' => array(
        'CSIBMTHAI',
    ),
    'HP-LEGAL' => array(
        'CSHPLEGAL',
    ),
    'HP-PI-FONT' => array(
        'CSHPPIFONT',
    ),
    'HP-MATH8' => array(
        'CSHPMATH8',
    ),
    'ADOBE-SYMBOL-ENCODING' => array(
        'CSHPPSMATH',
    ),
    'HP-DESKTOP' => array(
        'CSHPDESKTOP',
    ),
    'VENTURA-MATH' => array(
        'CSVENTURAMATH',
    ),
    'MICROSOFT-PUBLISHING' => array(
        'CSMICROSOFTPUBLISHING',
    ),
    'WINDOWS-31J' => array(
        'CSWINDOWS31J',
    ),
    'GB2312' => array(
        'CSGB2312',
    ),
    'BIG5' => array(
        'CSBIG5',
    ),
);

?>
