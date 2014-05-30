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
function ia_upload($data,$identifier,$fallbackid,$filename,$accesskey,$secretkey,$title,$description,$mediatype,$keywords,$addToBucket = false,$collection = 'opensource')
{
//note that the description options etc. aren't implemented
	echo '<br>ia_upload function arguments: <br><pre>';
	print_r(func_get_args());
	echo '</pre><br>';
	$iaerror = 0;
	$bucketName=$identifier;
	//based on the example.php file from amazon-s3-php-class
	if (!defined('awsAccessKey')) define('awsAccessKey', $accesskey);
	if (!defined('awsSecretKey')) define('awsSecretKey', $secretkey);
	// Check for CURL
	if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll')) {
		exit("\nERROR: CURL extension not loaded\n\n");
	}

	// Instantiate the class
	$s3 = new S3(awsAccessKey, awsSecretKey, true, 's3.us.archive.org');

	// Create a bucket with public read access
	if ($s3->putBucket($bucketName, S3::ACL_PUBLIC_READ)) {
		echo "Created bucket {$bucketName}".PHP_EOL;

		// Put our file (also with public read access)
		if ($s3->putObject($data, $bucketName, $filename, S3::ACL_PUBLIC_READ)) {
			echo "S3::putObjectFile(): File copied to {$bucketName}/".baseName($uploadFile).PHP_EOL;
		} else {
			echo "error code 34: S3::putObjectFile(): Failed to copy file\n";
			$iaerror = 34;
		}
	} else {
		echo "error code 35: S3::putBucket(): Unable to create bucket (it may already exist and/or be owned by someone else)\n";
		$iaerror = 35;
	}
// 
// 	echo '<br>ia_upload function arguments: <br><pre>';
// 	print_r(func_get_args());
// 	echo '</pre><br>';
// 	//Keywords in $keywords should be separated by ;
// 	$iaerror = 0;
// 	//$bucketExists = false; //really, = irrelevant :P
// 	//if(!$addToBucket) {
// 		//Check for existing bucket
// 		//based on the code in the try block below and on http://stackoverflow.com/questions/5043525/php-curl-http-put; help also from http://sriram-iyengar.blogspot.com/2011/07/aws-create-s3-bucket-using-curl.html
// 		$ch = curl_init(); 
// 		$bucket_url = 'http://s3.us.archive.org/' . $identifier . '/';
// 		curl_setopt($ch, CURLOPT_VERBOSE, 1);
// 		curl_setopt($ch, CURLOPT_URL, $bucket_url);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		//help from http://stackoverflow.com/questions/3164405/show-curl-post-request-headers-is-there-a-way-to-do-this
// 		curl_setopt($ch, CURLOPT_HEADER, true);
// 		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 			'x-amz-auto-make-bucket:1',
// 			'x-archive-queue-derive:0',
// 			'x-archive-size-hint:'.strlen($data),
// 			'authorization: LOW '.$accesskey.':'.$secretkey,
// 			'x-archive-meta-mediatype:'.$mediatype,
// 			'x-archive-meta-collection:'.$collection,
// 			'x-archive-meta-title:'.$title,
// 			'x-archive-meta-description:'.$description,
// 			'x-archive-meta-subject:'.$keywords,
// 			'x-archive-meta-mediatype:'.$mediatype
// 		));
// 		
// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// 		$response = curl_exec($ch);
// 		//help from http://stackoverflow.com/questions/3164405/show-curl-post-request-headers-is-there-a-way-to-do-this
// 		$information = curl_getinfo($ch);
// 		echo '<br>CURL RESULT INFORMATION: <br><pre>';
// 		print_r($information);
// 		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 		echo '</pre><br>';
// 		echo '<br>CURL RESPONSE: <br><pre>';
// 		echo $response;
// 		echo '</pre><br>';
// 		curl_close($ch); 
// 		//help from http://stackoverflow.com/questions/4366730/how-to-check-if-a-string-contains-specific-words
// 		if(($http_status == 409) && (strpos($response,'BucketAlreadyOwnedByYou') !== false)) {
// 			$myBucket = true;
// 		}
// 		else {
// 			if($http_status != 200) {
// 				//Not my bucket.
// 				$iaerror = 10;
// 				goto finished;
// 			}
// 		}
// 	//}
// 	
// 	try {
// 	    //based on http://stackoverflow.com/questions/1915653/uploading-to-s3-using-curl, http://frankkoehl.com/2009/09/http-status-code-curl-php/, the ARCMAJ3 client script, http://stackoverflow.com/questions/3085990/post-a-file-string-using-curl-in-php, and http://stackoverflow.com/questions/8115683/php-curl-custom-headers
// 	    $ch = curl_init(); 
// 	    // form field separator
// 		$delimiter = '-------------' . uniqid();
// 		// file upload fields: name => array(type=>'mime/type',content=>'raw data')
// 		$fileFields = array(
// 			'file1' => array(
// 				'type' => 'application/octet-stream',
// 				'content' => $data
// 			), /* ... */
// 		);
// 		// all other fields (not file upload): name => value
// 		$postFields = array(
// 			/* ... */
// 		);
// 
// 		$data = '';
// 
// 		// populate normal fields first (simpler)
// 		foreach ($postFields as $name => $content) {
// 		   $data .= "--" . $delimiter . "\r\n";
// 			$data .= 'Content-Disposition: form-data; name="' . $name . '"';
// 			// note: double endline
// 			$data .= "\r\n\r\n";
// 		}
// 		// populate file fields
// 		foreach ($fileFields as $name => $file) {
// 			$data .= "--" . $delimiter . "\r\n";
// 			// "filename" attribute is not essential; server-side scripts may use it
// 			$data .= 'Content-Disposition: form-data; name="' . $name . '";' .
// 					 ' filename="' . $name . '"' . "\r\n";
// 			// this is, again, informative only; good practice to include though
// 			$data .= 'Content-Type: ' . $file['type'] . "\r\n";
// 			// this endline must be here to indicate end of headers
// 			$data .= "\r\n";
// 			// the file itself (note: there's no encoding of any kind)
// 			$data .= $file['content'] . "\r\n";
// 		}
// 		// last delimiter
// 		$data .= "--" . $delimiter . "--\r\n";
// 	    curl_setopt($ch, CURLOPT_VERBOSE, 1);
// 		curl_setopt($ch, CURLOPT_URL, $upload_url);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		//help from http://stackoverflow.com/questions/3164405/show-curl-post-request-headers-is-there-a-way-to-do-this
// 		curl_setopt($ch, CURLOPT_HEADER, true);
// 		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
// 			'x-amz-auto-make-bucket:1',
// 			'x-archive-queue-derive:0',
// 			'x-archive-size-hint:'.strlen($data),
// 			'authorization: LOW '.$accesskey.':'.$secretkey,
// 			'x-archive-meta-mediatype:'.$mediatype,
// 			'x-archive-meta-collection:'.$collection,
// 			'x-archive-meta-title:'.$title,
// 			'x-archive-meta-description:'.$description,
// 			'x-archive-meta-subject:'.$keywords,
// 			'x-archive-meta-mediatype:'.$mediatype
// 		));
// 		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// 		$response = curl_exec($ch);
// 		//help from http://stackoverflow.com/questions/3164405/show-curl-post-request-headers-is-there-a-way-to-do-this
// 		$information = curl_getinfo($ch);
// 		echo '<br>CURL RESULT INFORMATION: <br><pre>';
// 		print_r($information);
// 		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 		echo '</pre><br>';
// 		echo '<br>CURL RESPONSE: <br><pre>';
// 		echo $response;
// 		echo '</pre><br>';
// 		curl_close($ch); 
// 		if(strlen($response) > 0 || $http_status != 200) {
// 			throw new Exception('cURL request failed');
// 			$iaerror = 12;
// 		}
// 	}
// 	catch (Exception $e) {
// 		$iaerror = 11;
// 	}
// 	finished:
// 	return $iaerror;
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
	$split = str_split(strtolower(bin2hex(strval(get_signed_int(crc32($data))))),10);
	$return = $split[0];
	return $return;
}
function crc($data)
{
	return strtolower(dechex(crc32($data)));
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
function st() {
	$btime = microtime(true);
	echo '<br>Timed event begun at '.$btime.'.<br>';
	return new stt($btime);
}
function et($st) {
	$btime = $st->btime;
	$etime = microtime(true);
	$dtime = $etime-$btime;
	echo '<br>Timed event finished at '.$etime.'; took '.$dtime.' seconds.<br>';
}
?>