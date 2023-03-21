<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2015, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

// orderby helper (will be extended in time)
function order_link($link, $orderby, $text, $segment = 4)
{
    $CI =& get_instance();

    if (!$CI->uri->segment($segment) || $CI->uri->segment($segment) == 'orderdesc') {
        $order = 'orderasc';
    } else {
        $order = 'orderdesc';
    }

    if ($CI->uri->segment(($segment+1)) == $orderby) {
        $class = 'class="'.$order.'"';
    } else {
        $class = '';
    }

    echo anchor($link.'/'.$order.'/'.$orderby, $text, $class);
}

// get country codes
function get_country_codes($country = '')
{
    $countries = array(
        'AF'=>'AFGHANISTAN',
        'AX'=>'ALAND ISLANDS',
        'AL'=>'ALBANIA',
        'DZ'=>'ALGERIA',
        'AS'=>'AMERICAN SAMOA',
        'AD'=>'ANDORRA',
        'AO'=>'ANGOLA',
        'AI'=>'ANGUILLA',
        'AQ'=>'ANTARCTICA',
        'AG'=>'ANTIGUA AND BARBUDA',
        'AR'=>'ARGENTINA',
        'AM'=>'ARMENIA',
        'AW'=>'ARUBA',
        'AU'=>'AUSTRALIA',
        'AT'=>'AUSTRIA',
        'AZ'=>'AZERBAIJAN',
        'BS'=>'BAHAMAS',
        'BH'=>'BAHRAIN',
        'BD'=>'BANGLADESH',
        'BB'=>'BARBADOS',
        'BY'=>'BELARUS',
        'BE'=>'BELGIUM',
        'BZ'=>'BELIZE',
        'BJ'=>'BENIN',
        'BM'=>'BERMUDA',
        'BT'=>'BHUTAN',
        'BO'=>'BOLIVIA',
        'BA'=>'BOSNIA AND HERZEGOVINA',
        'BW'=>'BOTSWANA',
        'BV'=>'BOUVET ISLAND',
        'BR'=>'BRAZIL',
        'IO'=>'BRITISH INDIAN OCEAN TERRITORY',
        'BN'=>'BRUNEI DARUSSALAM',
        'BG'=>'BULGARIA',
        'BF'=>'BURKINA FASO',
        'BI'=>'BURUNDI',
        'KH'=>'CAMBODIA',
        'CM'=>'CAMEROON',
        'CA'=>'CANADA',
        'CV'=>'CAPE VERDE',
        'CI'=>'CâTE D\'IVOIRE',
        'KY'=>'CAYMAN ISLANDS',
        'CF'=>'CENTRAL AFRICAN REPUBLIC',
        'TD'=>'CHAD',
        'CL'=>'CHILE',
        'CN'=>'CHINA',
        'CX'=>'CHRISTMAS ISLAND',
        'CC'=>'COCOS (KEELING) ISLANDS',
        'CO'=>'COLOMBIA',
        'KM'=>'COMOROS',
        'CG'=>'CONGO',
        'CD'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
        'CK'=>'COOK ISLANDS',
        'CR'=>'COSTA RICA',
        'HR'=>'CROATIA',
        'CU'=>'CUBA',
        'CY'=>'CYPRUS',
        'CZ'=>'CZECH REPUBLIC',
        'DK'=>'DENMARK',
        'DJ'=>'DJIBOUTI',
        'DM'=>'DOMINICA',
        'DO'=>'DOMINICAN REPUBLIC',
        'EC'=>'ECUADOR',
        'EG'=>'EGYPT',
        'SV'=>'EL SALVADOR',
        'GQ'=>'EQUATORIAL GUINEA',
        'ER'=>'ERITREA',
        'EE'=>'ESTONIA',
        'ET'=>'ETHIOPIA',
        'FK'=>'FALKLAND ISLANDS (MALVINAS)',
        'FO'=>'FAROE ISLANDS',
        'FJ'=>'FIJI',
        'FI'=>'FINLAND',
        'FR'=>'FRANCE',
        'GF'=>'FRENCH GUIANA',
        'PF'=>'FRENCH POLYNESIA',
        'TF'=>'FRENCH SOUTHERN TERRITORIES',
        'GA'=>'GABON',
        'GM'=>'GAMBIA',
        'GE'=>'GEORGIA',
        'DE'=>'GERMANY',
        'GH'=>'GHANA',
        'GI'=>'GIBRALTAR',
        'GR'=>'GREECE',
        'GL'=>'GREENLAND',
        'GD'=>'GRENADA',
        'GP'=>'GUADELOUPE',
        'GU'=>'GUAM',
        'GT'=>'GUATEMALA',
        'GN'=>'GUINEA',
        'GW'=>'GUINEA-BISSAU',
        'GY'=>'GUYANA',
        'HT'=>'HAITI',
        'HM'=>'HEARD ISLAND AND MCDONALD ISLANDS',
        'VA'=>'HOLY SEE (VATICAN CITY STATE)',
        'HN'=>'HONDURAS',
        'HK'=>'HONG KONG',
        'HU'=>'HUNGARY',
        'IS'=>'ICELAND',
        'IN'=>'INDIA',
        'ID'=>'INDONESIA',
        'IR'=>'IRAN ISLAMIC REPUBLIC OF',
        'IQ'=>'IRAQ',
        'IE'=>'IRELAND',
        'IL'=>'ISRAEL',
        'IT'=>'ITALY',
        'JM'=>'JAMAICA',
        'JP'=>'JAPAN',
        'JO'=>'JORDAN',
        'KZ'=>'KAZAKHSTAN',
        'KE'=>'KENYA',
        'KI'=>'KIRIBATI',
        'KP'=>'KOREA DEMOCRATIC PEOPLE\'S REPUBLIC OF',
        'KR'=>'KOREA REPUBLIC OF',
        'KW'=>'KUWAIT',
        'KG'=>'KYRGYZSTAN',
        'LA'=>'LAO PEOPLE\'S DEMOCRATIC REPUBLIC',
        'LV'=>'LATVIA',
        'LB'=>'LEBANON',
        'LS'=>'LESOTHO',
        'LR'=>'LIBERIA',
        'LY'=>'LIBYAN ARAB JAMAHIRIYA',
        'LI'=>'LIECHTENSTEIN',
        'LT'=>'LITHUANIA',
        'LU'=>'LUXEMBOURG',
        'MO'=>'MACAO',
        'MK'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
        'MG'=>'MADAGASCAR',
        'MW'=>'MALAWI',
        'MY'=>'MALAYSIA',
        'MV'=>'MALDIVES',
        'ML'=>'MALI',
        'MT'=>'MALTA',
        'MH'=>'MARSHALL ISLANDS',
        'MQ'=>'MARTINIQUE',
        'MR'=>'MAURITANIA',
        'MU'=>'MAURITIUS',
        'YT'=>'MAYOTTE',
        'MX'=>'MEXICO',
        'FM'=>'MICRONESIA, FEDERATED STATES OF',
        'MD'=>'MOLDOVA, REPUBLIC OF',
        'MC'=>'MONACO',
        'MN'=>'MONGOLIA',
        'MS'=>'MONTSERRAT',
        'MA'=>'MOROCCO',
        'MZ'=>'MOZAMBIQUE',
        'MM'=>'MYANMAR',
        'NA'=>'NAMIBIA',
        'NR'=>'NAURU',
        'NP'=>'NEPAL',
        'NL'=>'NETHERLANDS',
        'AN'=>'NETHERLANDS ANTILLES',
        'NC'=>'NEW CALEDONIA',
        'NZ'=>'NEW ZEALAND',
        'NI'=>'NICARAGUA',
        'NE'=>'NIGER',
        'NG'=>'NIGERIA',
        'NU'=>'NIUE',
        'NF'=>'NORFOLK ISLAND',
        'MP'=>'NORTHERN MARIANA ISLANDS',
        'NO'=>'NORWAY',
        'OM'=>'OMAN',
        'PK'=>'PAKISTAN',
        'PW'=>'PALAU',
        'PS'=>'PALESTINIAN TERRITORY, OCCUPIED',
        'PA'=>'PANAMA',
        'PG'=>'PAPUA NEW GUINEA',
        'PY'=>'PARAGUAY',
        'PE'=>'PERU',
        'PH'=>'PHILIPPINES',
        'PN'=>'PITCAIRN',
        'PL'=>'POLAND',
        'PT'=>'PORTUGAL',
        'PR'=>'PUERTO RICO',
        'QA'=>'QATAR',
        'RE'=>'REUNION',
        'RO'=>'ROMANIA',
        'RU'=>'RUSSIAN FEDERATION',
        'RW'=>'RWANDA',
        'SH'=>'SAINT HELENA',
        'KN'=>'SAINT KITTS AND NEVIS',
        'LC'=>'SAINT LUCIA',
        'PM'=>'SAINT PIERRE AND MIQUELON',
        'VC'=>'SAINT VINCENT AND THE GRENADINES',
        'WS'=>'SAMOA',
        'SM'=>'SAN MARINO',
        'ST'=>'SAO TOME AND PRINCIPE',
        'SA'=>'SAUDI ARABIA',
        'SN'=>'SENEGAL',
        'CS'=>'SERBIA AND MONTENEGRO',
        'SC'=>'SEYCHELLES',
        'SL'=>'SIERRA LEONE',
        'SG'=>'SINGAPORE',
        'SK'=>'SLOVAKIA',
        'SI'=>'SLOVENIA',
        'SB'=>'SOLOMON ISLANDS',
        'SO'=>'SOMALIA',
        'ZA'=>'SOUTH AFRICA',
        'GS'=>'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
        'ES'=>'SPAIN',
        'LK'=>'SRI LANKA',
        'SD'=>'SUDAN',
        'SR'=>'SURINAME',
        'SJ'=>'SVALBARD AND JAN MAYEN',
        'SZ'=>'SWAZILAND',
        'SE'=>'SWEDEN',
        'CH'=>'SWITZERLAND',
        'SY'=>'SYRIAN ARAB REPUBLIC',
        'TW'=>'TAIWAN PROVINCE OF CHINA',
        'TJ'=>'TAJIKISTAN',
        'TZ'=>'TANZANIA UNITED REPUBLIC OF',
        'TH'=>'THAILAND',
        'TL'=>'TIMOR-LESTE',
        'TG'=>'TOGO',
        'TK'=>'TOKELAU',
        'TO'=>'TONGA',
        'TT'=>'TRINIDAD AND TOBAGO',
        'TN'=>'TUNISIA',
        'TR'=>'TURKEY',
        'TM'=>'TURKMENISTAN',
        'TC'=>'TURKS AND CAICOS ISLANDS',
        'TV'=>'TUVALU',
        'UG'=>'UGANDA',
        'UA'=>'UKRAINE',
        'AE'=>'UNITED ARAB EMIRATES',
        'GB'=>'UNITED KINGDOM',
        'US'=>'UNITED STATES',
        'UM'=>'UNITED STATES MINOR OUTLYING ISLANDS',
        'UY'=>'URUGUAY',
        'UZ'=>'UZBEKISTAN',
        'VU'=>'VANUATU',
        'VE'=>'VENEZUELA',
        'VN'=>'VIETNAM',
        'VG'=>'VIRGIN ISLANDS BRITISH',
        'VI'=>'VIRGIN ISLANDS U.S.',
        'WF'=>'WALLIS AND FUTUNA',
        'EH'=>'WESTERN SAHARA',
        'YE'=>'YEMEN',
        'ZM'=>'ZAMBIA',
        'ZW'=>'ZIMBABWE'
    );

    return $countries;
}

// get country
function lookup_country($country)
{
    $countries = get_country_codes();

    return ucwords(strtolower(@$countries[$country]));
}

// helper for displaying countries (no ID)
function display_countries($name = 'country', $selected = '', $extras = '')
{
    $output = '';

    $countries = get_country_codes();

    $output .= '<select name="'.$name.'" '.$extras.'>'."\n";

    foreach ($countries as $country => $name) {
        $name = ucwords(strtolower($name));

        $output .= '<option value="'.$country.'"';
        if ($country == $selected || ($selected == '' && $country == 'US')) {
            $output .= ' selected="selected"';
        }
        $output .= '>'.$name.'</option>'."\n";
    }
    $output .= '</select>'."\n";

    return $output;
}

// get state codes
function get_state_codes($state = '')
{
    $states = array(
        ''=>'',
        'AL'=>'Alabama',
        'AK'=>'Alaska',
        'AZ'=>'Arizona',
        'AR'=>'Arkansas',
        'CA'=>'California',
        'CO'=>'Colorado',
        'CT'=>'Connecticut',
        'DE'=>'Delaware',
        'DC'=>'District Of Columbia',
        'FL'=>'Florida',
        'GA'=>'Georgia',
        'HI'=>'Hawaii',
        'ID'=>'Idaho',
        'IL'=>'Illinois',
        'IN'=>'Indiana',
        'IA'=>'Iowa',
        'KS'=>'Kansas',
        'KY'=>'Kentucky',
        'LA'=>'Louisiana',
        'ME'=>'Maine',
        'MD'=>'Maryland',
        'MA'=>'Massachusetts',
        'MI'=>'Michigan',
        'MN'=>'Minnesota',
        'MS'=>'Mississippi',
        'MO'=>'Missouri',
        'MT'=>'Montana',
        'NE'=>'Nebraska',
        'NV'=>'Nevada',
        'NH'=>'New Hampshire',
        'NJ'=>'New Jersey',
        'NM'=>'New Mexico',
        'NY'=>'New York',
        'NC'=>'North Carolina',
        'ND'=>'North Dakota',
        'OH'=>'Ohio',
        'OK'=>'Oklahoma',
        'OR'=>'Oregon',
        'PA'=>'Pennsylvania',
        'RI'=>'Rhode Island',
        'SC'=>'South Carolina',
        'SD'=>'South Dakota',
        'TN'=>'Tennessee',
        'TX'=>'Texas',
        'UT'=>'Utah',
        'VT'=>'Vermont',
        'VA'=>'Virginia',
        'WA'=>'Washington',
        'WV'=>'West Virginia',
        'WI'=>'Wisconsin',
        'WY'=>'Wyoming'
    );

    return $states;
}

// get state
function lookup_state($state)
{
    $states = get_state_codes();

    return ucwords(strtolower(@$states[$state]));
}

// helper for displaying countries (no ID)
function display_states($name = 'state', $selected = '', $extras = '')
{
    $output = '';

    $states = get_state_codes();

    $output .= '<select name="'.$name.'" '.$extras.'>'."\n";

    foreach ($states as $state => $name) {
        $name = ucwords(strtolower($name));

        $output .= '<option value="'.$state.'"';
        if ($state == $selected || ($selected == '' && $state == 'US')) {
            $output .= ' selected="selected"';
        }
        $output .= '>'.$name.'</option>'."\n";
    }
    $output .= '</select>'."\n";

    return $output;
}

// image loader (requires images model/lib)
function load_image($image, $thumb = false, $product = false)
{
    $CI =& get_instance();

    $imagePath = $CI->uploads->load_image($image, $thumb, $product);

    return $imagePath['src'];
}

function display_image($path, $alt, $size = '', $extras = '', $nopic = false)
{
    $image_path = '.'.$path;
    
    if (!file_exists($image_path)) {
        if ($nopic !== false) {
            $imageHTML = '<img src="'.$nopic.'" alt="No Picture" ';
        } else {
            return false;
        }
    } else {
        $imageHTML = '<img src="'.$path.'" alt="'.$alt.'" ';
        $imageSize = getimagesize($image_path);
    }

    if (!empty($size)) {
        if (is_array($size) && !empty($imageSize)) {
            $widthfactor = $imageSize[0] / $size['width'];
            $heightfactor = $imageSize[1] / $size['height'];

            if ($imageSize[0] > $size['width'] && ($widthfactor > $heightfactor || $widthfactor == $heightfactor)) {
                $factor = $imageSize[0] / $size['width'];
                $imageHTML .= 'width="'.$size['width'].'" ';
            } elseif ($imageSize[1] > $size['height'] && $heightfactor > $widthfactor) {
                $imageHTML .= 'height="'.$size['height'].'" ';
            }
        } elseif (is_numeric($size) && $size > 0 && (!empty($imageSize) && ($imageSize[0] > $size || $imageSize[1] > $size) || !empty($nopic))) {    
            if (!empty($imageSize) && ($imageSize[0] > $imageSize[1] || $imageSize[0] == $imageSize[1])) {
                $imageHTML .= 'width="'.$size.'" ';
            } elseif (!empty($imageSize) && $imageSize[1] > $imageSize[0]) {
                $imageHTML .= 'height="'.$size.'" ';
            }
        }
    }

    if (!empty($extras)) {
        $imageHTML .= $extras.' ';
    }

    $imageHTML .= '/>';

    return $imageHTML;
}

// date formatting for mysql dates
function datefmt($date, $fmt = '', $timezone = '', $seconds = false)
{
    $CI =& get_instance();

    if ($CI->site->config['timezone'] && $timezone === '') {
        $timezone = $CI->site->config['timezone'];
    }

    if (!$fmt) {
        if (@$CI->site->config['dateOrder'] == 'MD') {
            $fmt = 'M jS Y';
        } else {
            $fmt = 'jS M Y';
        }
    }

    if ($seconds) {
        $fmt .= ', H:i';
    }

    if ($date && $date > 0) {
        $timestamp = strtotime($date);

        if ($timezone) {
            $timestamp = gmt_to_local(local_to_gmt($timestamp), $timezone, false);
        }

        return date($fmt, $timestamp);
    } else {
        return false;
    }
}
// time formatting for mysql dates
function timefmt($date, $fmt = '', $timezone = '')
{
    $CI =& get_instance();

    if ($CI->site->config['timezone'] && $timezone === '') {
        $timezone = $CI->site->config['timezone'];
    }

    if (!$fmt) {
        $fmt .= 'h:i A';
    }

    if ($date && $date > 0) {
        $timestamp = strtotime($date);

        if ($timezone) {
            $timestamp = gmt_to_local(local_to_gmt($timestamp), $timezone, false);
        }

        return date($fmt, $timestamp);
    } else {
        return false;
    }
}

function currency_symbol($html = true, $currency = '')
{
    $CI =& get_instance();

    $currency = (!$currency) ? $CI->site->config['currency'] : $currency;

    if ($currency == 'GBP') {
        return ($html) ? '&pound;' : '£';
    } elseif ($currency == 'JPY') {
        return ($html) ? '&yen;' : '¥';
    } elseif ($currency == 'EUR') {
        return ($html) ? '&euro;' : '€';
    } elseif ($currency == 'DKK' || $currency == 'SEK' || $currency == 'NOK') {
        return 'kr ';
    } elseif ($currency == 'IDR') {
        return 'Rp ';
    } elseif ($currency == 'INR') {
        return 'Rs ';
    } elseif ($currency == 'CHF') {
        return 'CHF ';
    } elseif ($currency == 'PLN') {
        return 'zl ';
    } elseif ($currency == 'RUB') {
        return 'P.';
    } elseif ($currency == 'SGD') {
        return 'S$';
    } elseif ($currency == 'ZAR') {
        return 'R ';
    } elseif ($currency == 'MYR') {
        return 'RM ';
    } elseif ($currency == 'BRL') {
        return 'R$';
    } elseif ($currency == 'LKR') {
        return 'Rs ';
    } elseif ($currency == 'VEF') {
        return 'Bs.F ';
    } elseif ($currency == 'LVL') {
        return 'Ls ';
    } elseif ($currency == 'ILS') {
        return ($html) ? '&#8362;' : '₪';
    } elseif ($currency == 'AED') {
        return 'AED ';
    } elseif ($currency == 'CZK') {
        return ($html) ? 'K&#269; ' : 'Kč ';
    } elseif ($currency == 'KES') {
        return 'KSh ';
    } else {
        return '$';
    }
}

function currencies()
{
    $values = array(
        'USD' => 'US Dollars (USD)',
        'GBP' => 'UK Pounds (GBP)',
        'EUR' => 'Euro (EUR)',
        'AED' => 'UAE Dirham (AED)',
        'ARS' => 'Argentina Pesos (ARS)',
        'AUD' => 'Australia Dollars (AUD)',
        'BRL' => 'Brazil Real (BRL)',
        'CAD' => 'Canada Dollars (CAD)',
        'CHF' => 'Switzerland Francs (CHF)',
        'CZK' => 'Czech Republic Koruny (CZK)',
        'DKK' => 'Denmark Kroner (DKK)',
        'DOP' => 'Dominican Republic Peso (DOP)',
        'HKD' => 'Hong Kong Dollar (HKD)',
        'IDR' => 'Indonesia Rupiah (IDR)',
        'ILS' => 'Israel New Shekels (ILS)',
        'INR' => 'India Rupees (INR)',
        'JPY' => 'Japan Yen (JPY)',
        'KES' => 'Kenya Shilling (KES)',
        'LKR' => 'Sri Lanka Rupees (LKR)',
        'LVL' => 'Latvia Lat (LVL)',
        'MXN' => 'Mexico Peso (MXN)',
        'MYR' => 'Malaysia Ringgit (MYR)',
        'NOK' => 'Norway Kroner (NOK)',
        'NZD' => 'New Zealand Dollars (NZD)',
        'PLN' => 'Poland Zloty (PLN)',
        'RUB' => 'Russian Federation Ruble (RUB)',
        'SEK' => 'Sweden Kronor (SEK)',
        'SGD' => 'Singapore Dollars (SGD)',
        'ZAR' => 'South Africa Rand (ZAR)',
        'VEF' => 'Venezuela Bolivar Fuerte (VEF)'
    );
    return $values;
}

function languages()
{
    $values = array(
        'english' => 'English',
        'danish' => 'Danish',
        'dutch' => 'Dutch',
        'finnish' => 'Finnish',
        'french' => 'French',
        'german' => 'German',
        'norweigan' => 'Norweigan',
        'portugese' => 'Portugese',
        'russian' => 'Russian',
        'spanish' => 'Spanish'
    );
    return $values;
}

function fraction($int)
{
    $fraction = ($int - floor($int));
    if ($fraction == '0.25') {
        return floor($int).' &frac14;';
    } elseif ($fraction == '0.5') {
        return floor($int).' &frac12;';
    } elseif ($fraction == '0.75') {
        return floor($int).' &frac34;';
    } else {
        return $int;
    }
}

function order($by, $title, $desc = false, $class = '', $extras = '')
{
    $CI =& get_instance();

    $segments = $CI->uri->segment_array();

    if ($key = @array_search('orderby', $segments)) {
        if ($segments[$key + 1] == $by) {
            if ($segments[$key + 2] == 'desc') {
                $segments[$key + 2] = 'asc';
                $class = 'orderdesc '.$class;
            } else {
                $segments[$key + 2] = 'desc';
                $class = 'orderasc '.$class;
            }
        } else {
            $segments[$key + 2] = ($desc) ? 'desc' : 'asc';
        }

        $segments[$key + 1] = $by;
    } else {
        array_push($segments, 'orderby');
        array_push($segments, $by);
        array_push($segments, (($desc) ? 'desc' : 'asc'));
    }

    $href = '/'.implode('/', str_replace('_ajax', '', $segments));

    return anchor($href, $title, 'class="'.trim($class).'" '.$extras);
}

function expiry_months_dropdown($name, $selected = '', $html = '')
{
    return form_dropdown($name, array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ), $selected, $html);
}

function expiry_years_dropdown($name, $selected = '', $html = '')
{
    $options = array();

    for ($i=0; $i < 20; $i++) {
        $options[date('Y', time()+(60*60*24*365)*$i+1)] = date('Y', time()+(60*60*24*365)*$i+1);
    }

    return form_dropdown($name, $options, $selected, $html);
}

// Markdown + Extra
function mkdn($text)
{
    $CI =& get_instance();

    $CI->load->library('Mkdn');

    return $CI->mkdn->defaultTransform($text);
}
