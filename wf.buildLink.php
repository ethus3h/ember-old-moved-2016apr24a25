<?php
//Make a hyperlink or formlink
function buildLink($wvActionId, $options, $caption)
{
	$lttemp = false;
	if(isset($_POST["login"])) {
		if($_POST["login"] == "1") {
			$lttemp = true;
		}
	}
    if ($lttemp == true) {
        //Check that the user is properly logged in
        $wvuserpasswdmd5ln = qry('user', 'user_password_md5', 'user_name', fv('wvUserName'));
        if (md5($_SESSION['wvUserPassword']) == $wvuserpasswdmd5ln) {
            $loginverifiedln = 1;
            $loginln = 1;
        } else {
            $loginverifiedln = 0;
            err(5);
            $loginbl = 0;
        }
        if ($loginverifiedln == 1) {
        } else {
            err(289);
            $loginln = 0;
        }
    } else {
        $loginln = 0;
    }
    $localeid = fv('locale');
    if ($loginln == 0) {
        if ($options == '') {
            $separator = '';
            $options = $options . '&locale=' . $localeid;
            $options = str_replace('&&', '&', $options);
        } else {
            $separator = '&';
            $options = $options . 'locale=' . $localeid;
            $options = str_replace('&&', '&', $options);
        }
        $linkGenerated = itr(38) . $wvActionId . $separator . $options . itr(39) . $caption . itr(40);
    } else {
        if ($options == '') {
            $separator = '';
            $options = $options . '&wvSession=' . session_id() . '&' . itr(63) . $localeid;
            $options = str_replace('&&', '&', $options . '&wvSession=' . session_id());
        } else {
            $separator = itr(41);
            $options = $options . '&' . itr(62) . $localeid;
            $options = str_replace('&&', '&', $options);
        }
        $linkGenerated = str_replace('&&', '&', itr(42) . $wvActionId . str_replace(itr(54), itr(43), str_replace('=', itr(44), $options)) . itr(1083) . $caption . itr(1084));
    }
    return $linkGenerated;
}
?>
