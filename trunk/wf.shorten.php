<?php
// Returns a trunctated version of $str up to $max chars, excluding $trunc.
//Not written by me.
// $strict = FALSE will allow longer strings to fit the last word.
function str_trunc($str, $max, $strict = FALSE, $trunc = '')
{
    if (strlen($str) <= $max) {
        return $str;
    } else {
        if ($strict) {
            return substr($str, 0, strrposlimit($str, ' ', 0, $max + 1)) . $trunc;
        } else {
            $strloc = strpos($str, ' ', $max);
            if (strlen($strloc) != 0) {
                return substr($str, 0, $strloc) . $trunc;
            } else {
                return $str;
            }
        }
    }
}
// Works like strrpos, but allows a limit
//Not written by me.
function strrposlimit($haystack, $needle, $offset = 0, $limit = NULL)
{
    if ($limit === NULL) {
        return strrpos($haystack, $needle, $offset);
    } else {
        $search = substr($haystack, $offset, $limit);
        return strrpos($search, $needle, 0);
    }
}
//Shorten a string
function shorten($content)
{
    if (strlen($content) > 32) {
        //trim to 64 but round by words
        $shortenedstring = str_trunc($content, 32) . itr(1493);
        global $baggage_claim;
        $baggage_claim->check_luggage('Shortened', 'true');
        if (strlen($shortenedstring) > 40) {
            //trim to 64
            $shortenedstring = substr($content, 0, 32) . itr(1493);
        }
    } else {
        $shortenedstring = $content;
        global $baggage_claim;
        $baggage_claim->check_luggage('Shortened', 'false');
    }
    return $shortenedstring;
}
?>
