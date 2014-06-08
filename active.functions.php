<?php
//General-use functions


function resl($name)
{
    global $dataDirectory;
    return file_get_contents('../../' . $dataDirectory . $name);
}
//explode_escaped (not written by me)
function explode_esc($delimiter, $string)
{
    $exploded = explode($delimiter, $string);
    $fixed    = array();
    for ($k = 0, $l = count($exploded); $k < $l; ++$k) {
        if ($exploded[$k][strlen($exploded[$k]) - 1] == '\\') {
            if ($k + 1 >= $l) {
                $fixed[] = trim($exploded[$k]);
                break;
            }
            $exploded[$k][strlen($exploded[$k]) - 1] = $delimiter;
            $exploded[$k] .= $exploded[$k + 1];
            array_splice($exploded, $k + 1, 1);
            --$l;
            --$k;
        } else
            $fixed[] = trim($exploded[$k]);
    }
    return $fixed;
}


#from http://comments.gmane.org/gmane.mail.squirrelmail.plugins/9672
if (!function_exists("hex2bin")) {
    function hex2bin($data)
    {
        /* Original code by josh <at> superfork.com */
        
        $len     = strlen($data);
        $newdata = '';
        for ($i = 0; $i < $len; $i += 2) {
            $newdata .= pack("C", hexdec(substr($data, $i, 2)));
        }
        return $newdata;
    }
}
function Rq($name)
{
    if (isset($_REQUEST[$name])) {
        return $_REQUEST[$name];
    } else {
        return '';
    }
}
if (!function_exists("gzdecode")) {
    function gzdecode($gzdata, $maxlen = NULL)
    {
        #from http://include-once.org/p/upgradephp/
        #-- decode header
        $len = strlen($gzdata);
        if ($len < 20) {
            return;
        }
        $head = substr($gzdata, 0, 10);
        $head = unpack("n1id/C1cm/C1flg/V1mtime/C1xfl/C1os", $head);
        list($ID, $CM, $FLG, $MTIME, $XFL, $OS) = array_values($head);
        $FTEXT    = 1 << 0;
        $FHCRC    = 1 << 1;
        $FEXTRA   = 1 << 2;
        $FNAME    = 1 << 3;
        $FCOMMENT = 1 << 4;
        $head     = unpack("V1crc/V1isize", substr($gzdata, $len - 8, 8));
        list($CRC32, $ISIZE) = array_values($head);
        
        #-- check gzip stream identifier
        if ($ID != 0x1f8b) {
            trigger_error("gzdecode: not in gzip format", E_USER_WARNING);
            return;
        }
        #-- check for deflate algorithm
        if ($CM != 8) {
            trigger_error("gzdecode: cannot decode anything but deflated streams", E_USER_WARNING);
            return;
        }
        
        #-- start of data, skip bonus fields
        $s = 10;
        if ($FLG & $FEXTRA) {
            $s += $XFL;
        }
        if ($FLG & $FNAME) {
            $s = strpos($gzdata, "\000", $s) + 1;
        }
        if ($FLG & $FCOMMENT) {
            $s = strpos($gzdata, "\000", $s) + 1;
        }
        if ($FLG & $FHCRC) {
            $s += 2; // cannot check
        }
        
        #-- get data, uncompress
        $gzdata = substr($gzdata, $s, $len - $s);
        if ($maxlen) {
            $gzdata = gzinflate($gzdata, $maxlen);
            return ($gzdata); // no checks(?!)
        } else {
            $gzdata = gzinflate($gzdata);
        }
        
        #-- check+fin
        $chk = crc32($gzdata);
        if ($CRC32 != $chk) {
            trigger_error("gzdecode: checksum failed (real$chk != comp$CRC32)", E_USER_WARNING);
        } elseif ($ISIZE != strlen($gzdata)) {
            trigger_error("gzdecode: stream size mismatch", E_USER_WARNING);
        } else {
            return ($gzdata);
        }
    }
}
function get_url($url)
#From http://www.howtogeek.com/howto/programming/php-get-the-contents-of-a-web-page-rss-feed-or-xml-file-into-a-string-variable/ and http://stackoverflow.com/questions/5522636/get-file-content-from-a-url
    
# and from http://stackoverflow.com/questions/13988365/what-are-the-possible-reasons-for-curl-error-60-on-an-https-site
{
	global $l;
	$l->a('<br>Data requested from URL: '.$url.'<br>');
    $userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';
    $crl       = curl_init();
    $timeout   = 30;
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crl, CURLOPT_USERAGENT, $userAgent);
    curl_setopt($crl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    #print_r($crl);
    $ret      = curl_exec($crl);
    $error_no = curl_errno($crl);
    curl_close($crl);
    if ($error_no == 0) {
        echo '';
    } else {
        echo $error_no;
    }
    return $ret;
}
function get_url_dummy($url)
{
	echo '<br>Data requested from URL: '.$url.'<br>';
	return 'Fake data...';
}
function ia_upload($data,$identifier,$fallbackid,$filename,$accesskey,$secretkey)
{
	global $l;
	$status = 0;
	$bucketName=$identifier;
	if (!defined('awsAccessKey')) define('awsAccessKey', $accesskey);
	if (!defined('awsSecretKey')) define('awsSecretKey', $secretkey);
	if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) {
		exit("\nERROR: CURL extension not loaded\n\n");
	}
	$s3 = new S3(awsAccessKey, awsSecretKey, true, 's3.us.archive.org');
	if ($s3->putBucket($bucketName, S3::ACL_PUBLIC_READ)) {
		$l->a("Created bucket {$bucketName}".PHP_EOL.'<br>');
		
	} else {
		$l->a("information code 55: S3::putBucket(): Unable to create bucket\n<br>");
	}
	if ($s3->putObject($data, $bucketName, $filename, S3::ACL_PUBLIC_READ)) {
		$l->a("S3::putObjectFile(): File copied to {$bucketName}/".$filename.PHP_EOL.'<br>');
	} else {
		$l->a("error code 34: S3::putObjectFile(): Failed to copy file\n<br>");
		$status = 34;
	}
 	return $status;
}
function get_domain($url)
#from http://stackoverflow.com/questions/16027102/get-domain-name-from-full-url
{
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        return $regs['domain'];
    }
    return false;
}
function get_domain_simple($url)
{
    $paros   = parse_url($url);
    $host    = $paros['host'];
    $host_ex = explode('.', $host);
    $frags   = count($host_ex);
    $hostr   = $host_ex[$frags - 2] . '.' . $host_ex[$frags - 1];
    if ($host_ex[$frags - 2] == 'yahoo' | $host_ex[$frags - 3] == 'yahoo') {
        $hostr = $host_ex[$frags - 3] . '.' . $host_ex[$frags - 2] . '.' . $host_ex[$frags - 1];
    }
    echo "<br>\n<br>\nDetected potential project: $hostr<br>\n<br>\n";
    return $hostr;
}
function authorized($key,$name='authorizationKey')
{
	if($_REQUEST[$name] == $key) {
		return true;
	}
	else {
		header("HTTP/1.0 403 Forbidden");
		return false;
	}
}
function check($status,$hard=false) {
	if($status != 0) {
		global $l;
		if($hard) {
			header("HTTP/1.0 525 Request failed");
			echo '<h1>Something went wrong: status '.$status.'. Please try your request again later.</h1><br><h2>Log output:</h2><br>';
			$l->e();
		}
		return false;
	}
	return true;
}
function start_file_download($filename,$filesize)
{
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: application/octet-stream");
	header("Content-Transfer-Encoding: binary");
	header('Content-Length: ' . $filesize);
}

//from http://us1.php.net/manual/en/function.crc32.php#96262
function get_signed_int($in) {
    $int_max = pow(2, 31)-1;
    if ($in > $int_max){
        $out = $in - $int_max * 2 - 2;
    }
    else {
        $out = $in;
    }
    return $out;
} 
//from http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
function guidv4()
{
    $data = openssl_random_pseudo_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
function par($data)
{
	$split = str_split(strtolower(bin2hex(strval(get_signed_int(crc($data))))),10);
	$return = $split[0];
	return $return;
}
function crc($data)
{
	return substr(md5($data),0,10);
}
function amd5($data)
{
	return strtolower(md5($data));
}
function sha($data)
{
	return strtolower(sha1($data));
}
function s512($data)
{
	return strtolower(hash("sha512",$data));
}
function parf($file)
{
	$split = str_split(strtolower(bin2hex(strval(get_signed_int(crcf($file))))),10);
	$return = $split[0];
	return $return;
}
function crcf($file)
{
	return substr(md5_file($file),0,10);
}
function amd5f($file)
{
	return strtolower(md5_file($file));
}
function shaf($file)
{
	return strtolower(sha1_file($file));
}
function s512f($file)
{
	return strtolower(hash_file("sha512",$file));
}
//from https://github.com/NicholasRBowers/PHP-Title-Case-Function/blob/camen/title-case.php
/* GOALS & NOTES:
* BEST PRACTICE - Do away with nested ternary operators, they make everything unnecessarily complicated, and the PHP manual specifically states to avoid nesting them - "Note: It is recommended that you avoid "stacking" ternary expressions. PHP's behaviour when using more than one ternary operator within a single statement is non-obvious" (http://php.net/manual/en/language.operators.comparison.php).
* BEST PRACTICE - Break up operation into more than one statement for ease of development.
*/

// Original Title Case script © John Gruber <daringfireball.net>
// JavaScript port © David Gouch <individed.com>
// PHP port of the above by Kroc Camen <camendesign.com>

function titleCase ($title) {
  // Remove HTML, storing it for later.
    // HTML elements to ignore | tags | entities
    $regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;/';
    preg_match_all ($regx, $title, $html, PREG_OFFSET_CAPTURE);
    $title = preg_replace ($regx, '', $title);
  
  // Find each word (including punctuation attached).
    preg_match_all ('/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE);
    foreach ($m1[0] as &$m2) {
      // Shorthand these- "match" and "index".
        list ($m, $i) = $m2;
    
      // Correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
      // We fix this by recounting the text before the offset using multi-byte aware `strlen`
        $i = mb_strlen (substr ($title, 0, $i), 'UTF-8');
    
      // Find words that should always be lowercase.
        // Never on the first word, and never if preceded by a colon.
        $m = $i>0 && mb_substr ($title, max (0, $i-2), 1, 'UTF-8') !== ':' && !preg_match ('/[\x{2014}\x{2013}] ?/u', mb_substr ($title, max (0, $i-2), 2, 'UTF-8')) && preg_match ('/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m)
        ? // And convert them to lowercase.
          mb_strtolower ($m, 'UTF-8')
        : // Else: brackets and other wrappers.
          (preg_match ('/[\'"_{(\[‘“]/u', mb_substr ($title, max (0, $i-1), 3, 'UTF-8'))
            ? // Convert first letter within wrapper to uppercase.
              mb_substr ($m, 0, 1, 'UTF-8').
              mb_strtoupper (mb_substr ($m, 1, 1, 'UTF-8'), 'UTF-8').
              mb_substr ($m, 2, mb_strlen ($m, 'UTF-8')-2, 'UTF-8')
            : // Else: do not uppercase these cases.
              (preg_match ('/[\])}]/', mb_substr ($title, max (0, $i-1), 3, 'UTF-8')) || preg_match ('/[A-Z]+|&|\w+[._]\w+/u', mb_substr ($m, 1, mb_strlen ($m, 'UTF-8')-1, 'UTF-8'))
                ? // If all else fails, then no more fringe-cases; uppercase the word.
                  $m
                :
                  mb_strtoupper (mb_substr ($m, 0, 1, 'UTF-8'), 'UTF-8').mb_substr ($m, 1, mb_strlen ($m, 'UTF-8'), 'UTF-8')
              )
          );
    
      // Re-splice the title with the change (`substr_replace` is not multi-byte aware).
        $title = mb_substr ($title, 0, $i, 'UTF-8').$m.mb_substr ($title, $i+mb_strlen ($m, 'UTF-8'), mb_strlen ($title, 'UTF-8'), 'UTF-8');
    }
  
  // Restore the HTML.
    foreach ($html[0] as &$tag) $title = substr_replace ($title, $tag[0], $tag[1], 0);
    return $title;
}
global $urlroot;
global $domain;
function url_processor_callback($data) {
	global $urlroot;
	global $domain;
	//echo $urlroot;
	if(substr($data[2],0,4) != 'http') {
		if(substr($data[2],0,1) != '/') {
			$data[2] = $urlroot .'/'. $data[2];
		}
		else {
			$data[2] = $domain . $data[2];
		}
	}

	return $data[1]."=\">>@NEVER__BE__MATCHED1@<<".base64_encode(str_rot13($data[2])).">>@NEVER__BE__MATCHED2@<<\"";
}
function get_processed_url($url) {
	//echo $url;
	global $urlroot;
	global $domain;
	$data = get_url($url);
	// //based on http://www.sitepoint.com/forums/showthread.php?192267-How-to-get-folder-name-out-of-a-URL
	$parsedUrl = parse_url($url);
	$path = '';
	if(isset($parsedUrl['path'])) {
		$path = $parsedUrl['path'];
	}
	$query = '';
	if(isset($parsedUrl['query'])) {
		$query = $parsedUrl['query'];
	}
	$fragment = '';
	if(isset($parsedUrl['fragment'])) {
		$fragment = $parsedUrl['fragment'];
	}
	$root = str_replace($path.'?'.$query.'#'.$fragment,'',$url);  
	//echo $root;
	$domain = $root;
// based on http://stackoverflow.com/questions/5939412/php-string-function-to-get-substring-before-the-last-occurrence-of-a-character
$string = explode('/', $url);
array_pop($string);
$res = implode('/', $string);
	$urlroot = $res;
	//based on http://stackoverflow.com/questions/11254619/get-contents-of-body-without-doctype-html-head-and-body-tags
	$d = new DOMDocument;
	$mock = new DOMDocument;
// 	$caller = new ErrorTrap(array($xmlDoc, 'loadHTML'));
// 	// this doesn't dump out any warnings
// 	$caller->call($fetchResult);
// 	if (!$caller->ok()) {
// 	  var_dump($caller->errors());
// 	}
	//help from http://stackoverflow.com/questions/14783760/remove-dom-warning-php
	@$d->loadHTML($data);
	@$body = $d->getElementsByTagName('body')->item(0);
	foreach ($body->childNodes as $child){
		@$mock->appendChild($mock->importNode($child, true));
	}
	$data = $mock->saveHTML();
	//based on http://stackoverflow.com/questions/19190180/preg-replace-change-link-from-href; help from http://us2.php.net/manual/en/function.preg-replace-callback.php
	//$result = preg_replace_callback('/(src)="([^"]+)"/', "url_processor_callback", $data);
	$result = preg_replace_callback('/(href)="([^"]+)"/', "url_processor_callback", $data);
	$pageData = remove_script_style($result);
	// $pageData = str_replace('<style','<!--',$result);
// 	$pageData = str_replace('<script','<!-- ',$result);
// 	$pageData = str_replace('</style>','-->',$result);
// 	$pageData = str_replace('</script>','--> ',$result);
	return $pageData;
}
function get_readied_url($url) {
	$data = get_processed_url($url);
	$data = str_replace('>>@NEVER__BE__MATCHED1@<<','http://futuramerlin.com/d/r/active.php?wint=1&amp;wintNeeded=inforesource&amp;r=',$data);
	$data = str_replace('>>@NEVER__BE__MATCHED2@<<','',$data);
	return $data;
}
function get_info($topic, $type = 'unknown') {
	$data = get_readied_url("http://futuramerlin.com/pageview.php?page=render-page.php?search=".$topic);
	$data = $data . get_readied_url("http://m.bing.com/search/search.aspx?A=webresults&amp;Q=".$topic);
	//help from http://www.developphp.com/view_lesson.php?v=229
	$info = array(
				'unknown' => array (
									'overview' => "doom",
									'bing' => $data
									),
				'url' => array     (
									'overview' => "doom",
									'bing' => $data
									)
	);
	//help from http://stackoverflow.com/questions/2058635/cannot-use-string-offset-as-an-array-error (I was returning $data :P )
	return $info;
}
//based on http://www.php.net/manual/en/function.strip-tags.php#68757
function remove_script_style($data) {
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
					'@<link[^>]*?>.*?</link>@si',  // Strip out link tags
					'@<form[^>]*?>.*?</form>@si',  // Strip out form tags
				   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
				   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, '', $data);
	//based on http://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
	$output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $text);
	return $output;
}
function st($name = '') {
	global $l;
	$btime = microtime(true);
	$l->a('<br>Timed event '.$name.' begun at '.$btime.'.<br>');
	return new stt($btime,$name);
}
function et($st) {
	global $l;
	$btime = $st->btime;
	$etime = microtime(true);
	$dtime = $etime-$btime;
	$l->a('<br>Timed event '.$st->name.' finished at '.$etime.'; took '.$dtime.' seconds.<br>');
}
//from http://us1.php.net/manual/en/function.crc32.php#31832
    $GLOBALS['__crc32_table']=array();        // Lookup table array
    __crc32_init_table();

    function __crc32_init_table() {            // Builds lookup table array
        // This is the official polynomial used by
        // CRC-32 in PKZip, WinZip and Ethernet.
        $polynomial = 0x04c11db7;

        // 256 values representing ASCII character codes.
        for($i=0;$i <= 0xFF;++$i) {
            $GLOBALS['__crc32_table'][$i]=(__crc32_reflect($i,8) << 24);
            for($j=0;$j < 8;++$j) {
                $GLOBALS['__crc32_table'][$i]=(($GLOBALS['__crc32_table'][$i] << 1) ^
                    (($GLOBALS['__crc32_table'][$i] & (1 << 31))?$polynomial:0));
            }
            $GLOBALS['__crc32_table'][$i] = __crc32_reflect($GLOBALS['__crc32_table'][$i], 32);
        }
    }

    function __crc32_reflect($ref, $ch) {        // Reflects CRC bits in the lookup table
        $value=0;
       
        // Swap bit 0 for bit 7, bit 1 for bit 6, etc.
        for($i=1;$i<($ch+1);++$i) {
            if($ref & 1) $value |= (1 << ($ch-$i));
            $ref = (($ref >> 1) & 0x7fffffff);
        }
        return $value;
    }

    function __crc32_string($text) {        // Creates a CRC from a text string
        // Once the lookup table has been filled in by the two functions above,
        // this function creates all CRCs using only the lookup table.

        // You need unsigned variables because negative values
        // introduce high bits where zero bits are required.
        // PHP doesn't have unsigned integers:
        // I've solved this problem by doing a '&' after a '>>'.

        // Start out with all bits set high.
        $crc=0xffffffff;
        $len=strlen($text);

        // Perform the algorithm on each character in the string,
        // using the lookup table values.
        for($i=0;$i < $len;++$i) {
            $crc=(($crc >> 8) & 0x00ffffff) ^ $GLOBALS['__crc32_table'][($crc & 0xFF) ^ ord($text{$i})];
        }
       
        // Exclusive OR the result with the beginning value.
        return $crc ^ 0xffffffff;
    }
   
    function __crc32_file($name) {            // Creates a CRC from a file
        // Info: look at __crc32_string

        // Start out with all bits set high.
        $crc=0xffffffff;

        if(($fp=fopen($name,'rb'))===false) return false;

        // Perform the algorithm on each character in file
        for(;;) {
            $i=@fread($fp,1);
            if(strlen($i)==0) break;
            $crc=(($crc >> 8) & 0x00ffffff) ^ $GLOBALS['__crc32_table'][($crc & 0xFF) ^ ord($i)];
        }
       
        @fclose($fp);
       
        // Exclusive OR the result with the beginning value.
        return $crc ^ 0xffffffff;
    }
//from https://gist.github.com/joshhartman/5383582 / http://www.warpconduit.net/2013/04/14/highly-secure-data-encryption-decryption-made-easy-with-php-mcrypt-rijndael-256-and-cbc/
// Define a 32-byte (64 character) hexadecimal encryption key
// Note: The same encryption key used to encrypt the data must be used to decrypt the data
global $coalPrivateKey;
global $chunkPrivateKey;
global $coalMasterKey;
$coalMasterKey = hash('sha256', $coalPrivateKey);
global $chunkMasterKey;
$chunkMasterKey = hash('sha256', $chunkPrivateKey);
//define('ENCRYPTION_KEY', 'd0a7e7997b6d5fcd55f4b5c32611b87cd923e88837b63bf2941ef819dc8ca282');
 
// Encrypt Function
function mc_encrypt($encrypt, $key){
$encrypt = serialize($encrypt);
$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM);
$key = pack('H*', $key);
$mac = hash_hmac('sha256', $encrypt, substr(bin2hex($key), -32));
$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt.$mac, MCRYPT_MODE_CBC, $iv);
$encoded = base64_encode($passcrypt).'|'.base64_encode($iv);
//echo 'IV first : ' . base64_encode($iv).'<br>';
return $encoded;
}
 
// Decrypt Function
function mc_decrypt($decrypt, $key){
//print_r(func_get_args());
$decrypt = explode('|', $decrypt);
$decoded = base64_decode($decrypt[0]);
$iv = base64_decode($decrypt[1]);
//echo 'IV now : ' . base64_encode($iv).'<br>';
$key = pack('H*', $key);
$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_CBC, $iv));
$mac = substr($decrypted, -64);
$decrypted = substr($decrypted, 0, -64);
$calcmac = hash_hmac('sha256', $decrypted, substr(bin2hex($key), -32));
if($calcmac!==$mac){ return false; }
$decrypted = unserialize($decrypted);
return $decrypted;
}
 
// echo '<h1>Rijndael 256-bit CBC Encryption Function</h1>';
//  
// $data = 'Super secret confidential string data.';
// $encrypted_data = mc_encrypt($data, ENCRYPTION_KEY);
// echo '<h2>Example #1: String Data</h2>';
// echo 'Data to be Encrypted: ' . $data . '<br/>';
// echo 'Encrypted Data: ' . $encrypted_data . '<br/>';
// echo 'Decrypted Data: ' . mc_decrypt($encrypted_data, ENCRYPTION_KEY) . '</br>';
//  
// $data = array(1, 5, 8, 9, 22, 10, 61);
// $encrypted_data = mc_encrypt($data, ENCRYPTION_KEY);
// echo '<h2>Example #2: Non-String Data</h2>';
// echo 'Data to be Encrypted: <pre>';
// print_r($data);
// echo '</pre><br/>';
// echo 'Encrypted Data: ' . $encrypted_data . '<br/>';
// echo 'Decrypted Data: <pre>';
// print_r(mc_decrypt($encrypted_data, ENCRYPTION_KEY));
// echo '</pre>';

function matches($csa,$csb) {
	if(($csa->len == $csb->len) && ($csa->md5 == $csb->md5) && ($csa->sha == $csb->sha) && ($csa->s512 == $csb->s512)) {
		return true;
	}
	return false;
}

function status_add($statusA, $statusB) {
	//Add two status codes.
	if($statusA == 0) {
		return $statusB;
	}
	return $statusA;
}

function store($data,$csum) {
	$status = 0;
	$csumn = new Csum($data);
	if(!$csum->matches($csumn)) {
		return null;
	}
	//Why I'm not doing this type of deduplication: It could lead to inaccurate metadata about the coal.
	//Ya know, screw that. Coal *shouldn't support* file uploads — that should be handled by higher level software. I'm putting this back in for now, and just remember that the Coal file-level metadata is only an ugly, non-archival-quality, incomplete hack for while Ember doesn't exist yet to take care of that.
	$db = new FractureDB('futuqiur_ember');
	$potentialDuplicates = $db->getColumnsUH('strings', 'id', 'md5', $csum->md5);
	foreach ($potentialDuplicates as $potential) {
		//echo 'duplicate testing';
		$potentialRecord = retrieveCoal($potential['id']);
		if(!is_null($potentialRecord)) {
			$potentialData = $potentialRecord['data'];
			$potentialCsum = Csum_import($potentialRecord['csum']);
			if(($potentialData === $data) && matches($csum,$potentialCsum)) {
				$duplicateId = $potential['id'];
				return array('id'=>$duplicateId,'csum'=>$potentialRecord['csum'],'status'=>$status);
			}
		}
	}
	$db->close();
	//echo 'gotten here';
	$filename = 'coal_temp/'.uniqid().'.cstf';
	file_put_contents($filename,$data);
	return insertCoal($filename,$csum);
}

function retrieve($id) {
	return retrieveCoal($id);
}

function lstore($data,$csum,$language,$fallbackLanguage = 0) {
	//Store a localizable string.
	$db = new FractureDB('futuqiur_ember');
	$csumn = new Csum($data);
	if(!$csum->matches($csumn)) {
		return null;
	}
	$store=store($data,$csum);
	$sid = $store['id'];
	//deduplicate rows here...
	$test = ltest($language,$sid);
	if(is_null($test)) {
		$id = $db->addRow('localized', 'language, data', '\''.$language.'\', \''.$sid.'\'');
		$store['id'] = $id;
		return $store;
	}
	$store['id'] = $test;
	return $store;
}

function lget($id,$language,$fallbackLanguage = 0) {
	//Retrieve a localizable string.
	$db = new FractureDB('futuqiur_ember');
	$ld = $db->getRowDF('localized','id',$id,'language',$language);
	if(is_null($ld)) {
		$ld = getRowDF('localized','id',$id,'language',$fallbackLanguage);
	}
	if(is_null($ld)) {
		$ld = getRow('localized','id',$id);
	}
	if(isset($ld[0])) {
		return null;
	}
	return retrieve($ld['data']);
}

function ltest($language,$id) {
	//Test for presence of a localizable string.
	$db = new FractureDB('futuqiur_ember');
	$res = $db->getRowDF('localized','language',$language,'data',$id);
	if(is_null($res)) {
		return null;
	}
	return $res['id'];
}

function test($value,$expected,$description = '',$f=false) {
	//help from http://stackoverflow.com/questions/139474/how-can-i-capture-the-result-of-var-dump-to-a-string
	ob_start();
	var_dump($value);
	$valdbg = ob_get_clean();
	ob_start();
	var_dump($expected);
	$expdbg = ob_get_clean();
	if($f) {
		//test is intended to fail
		if($value==$expected) {
			echo '<font color="red">Test failed: '.$valdbg.' is not the same as the expected value '.$expdbg.'. '.$description.'</font><br>';
		}
		else {
			echo '<font color="green">Test passed: '.$value.' is correct. '.$description.'</font><br>';
		}
	}
	else {
		if($value==$expected) {
			if($value===$expected) {
				echo '<font color="green">Test passed: '.$value.' is correct. '.$description.'</font><br>';
			}
			else {
				echo '<font color="red">Test failed: '.$valdbg.' is not the same type as the expected value '.$expdbg.'. '.$description.'</font><br>';
			}
		}
		else {
			echo '<font color="red">Test failed: '.$valdbg.' is not the same as the expected value '.$expdbg.'. '.$description.'</font><br>';
		}
	}
}

function phash($password) {
	//using PHPass (http://www.openwall.com/articles/PHP-Users-Passwords)
	$hasher = new PasswordHash(8, FALSE);
	$hash = $hasher->HashPassword($password);
	if (strlen($hash) < 20)
		fail('Failed to hash new password');
	unset($hasher);
	return $hash;
}

function getmusic($id){
    global $dataDirectory;
$musicdata= @file_get_contents($dataDirectory . '/s/music/r/'.$id.'.html');

#reencode to utf8 if necessary

if(strpos($musicdata,'ISO-8859-1')){
$musicdata=str_replace("ISO-8859-1","UTF-8",iconv("ISO-8859-1","UTF-8",$musicdata));
}

#clean up html mess
$musicdata=str_replace('<html>','',$musicdata);
$musicdata=str_replace('</html>','',$musicdata);
$musicdata=str_replace('<head>','',$musicdata);
$musicdata=str_replace('</head>','',$musicdata);
$musicdata=str_replace('<body>','',$musicdata);
$musicdata=str_replace('</body>','',$musicdata);
$musicdata=str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">','',$musicdata);
$musicdata=str_replace('<meta content="text/html; charset=UTF-8" http-equiv="content-type">','',$musicdata);
$musicdata = preg_replace("/<title(.*)<\/title>/iUs", "", $musicdata);
//$musicdata = preg_replace('/<a target="_blank" href="http:\/\/archive.org\/download\/(.*)"flac">&#128266;<\/a>/', '<a target="_blank" href="http://archive.org/download/' . '$1' . 'mp3">&#128266;</a>', $musicdata);

$musicdata = preg_replace('/<a href="http:\/\/archive.org\/download\/(.*)\/(.*).flac">/', '<audio src="http://archive.org/download/$1/$2.mp3" preload="none" class="audioplayer">Could not start audio player. Does your browser support it?</audio><a href="http://archive.org/download/$1/$2.flac">', $musicdata);
$musicdata=str_replace('&#128266;</a>','Lossless…</a>',$musicdata);



$musicdata=str_replace('cellpadding="2" cellspacing="2"','',$musicdata);

$musicdata= str_replace('AnoeyFuturamerlincom','anoeyfuturamerlincommedium,AnoeyFuturamerlincom',str_replace('<img',' <img',str_replace('src="a/','class="mlefti" src="d/s/music/r/a/',str_replace('href="a/','class="mlefti" href="d/s/music/r/a/',str_replace('href="../','href="d/s/music/',str_replace('href=','target="_blank" href=',str_replace('</h1>','</h2>',str_replace('<h1>','<h2>',$musicdata))))))));
return $musicdata;
}
function e($content,$pageVersionDirect = "")
{
    global $websiteName;
    global $cssFile;
    global $serverUrl;
    global $pageClass;
    if ($pageClass == 'w') {
        $tbl = '';
    } else {
        $tbl = '<table border="0" cellpadding="24" width="100%"><tbody><tr><td><br><h1>';
    }
    $univNeedles = array('href="' . $serverUrl, "\t", "\n", "\r", "  ", "  ", "  ", "> <", '@p1@', '@p2@', '@p3@', '@p4@', '@cssFile@', '@websiteName@', '@tbl@', '@l@', '@n@', 'https://futuramerlincom.fatcow.com','@greenhead@','@sylfan2@','@bonnou@','@bonnousuper@','@fullmoon@','@yy@','@sumquiaestis@','@128@','@flautrock@','@taito@','@itfaen@','.css";</style></title>');



    $univHaystacks = array('href="', " ", " ", " ", " ", " ", " ", '><', res('1.d' . $pageVersionDirect), res('2.d' . $pageVersionDirect), res('3.d' . $pageVersionDirect), res('4.d' . $pageVersionDirect), $cssFile, $websiteName, $tbl, '<a href="r.php?', '">', 'http://localhost','<div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div>',getmusic('sylfan2'),getmusic('bonnou'),getmusic('bonnousuper'),getmusic('fullmoon'),getmusic('yy'),getmusic('sumquiaestis'),getmusic('128'),getmusic('flautrock'),getmusic('taito'),getmusic('itfaen'),'.css";</style>');


    echo trim(str_replace("\n",' ',str_replace($univNeedles, $univHaystacks, $content)));
}

function fv($var)
{
    //global $$var;
    if(isset($_SESSION[$var])) {
		if ($_SESSION[$var]) {
			$$var = $_SESSION[$var];
		} else {
			$$var = $_REQUEST[$var];
		}
	}
	else {
	    if(isset($_REQUEST[$var])) {
			$$var = $_REQUEST[$var];
		}
		else {
			$$var = null;
		}
	}
    return $$var;
}
?>