<?php
/* PREFETCH PAGE PARAMETERS */
if ($_POST["login"] == "1") {
    global $login;
    //Check that the user is properly logged in
    $userPasswdMd5 = qry('user', 'user_password_md5', 'user_name', fv('wvUserName'));
    if (md5(fv('wvUserPassword')) == $userPasswdMd5) {
        $loginverified = 1;
        $login = 1;
    } else {
        $loginverified = 0;
        err(5);
        $login = 0;
    }
    if ($loginverified !== 1) {
        err(6);
        $login = 0;
    }
} else {
    //	itf(7);
    $login = 0;
}
//page title
//Prepare data
fv('nodeId');
$titleAttr = itr(21);
/* END PAGE PARAMETERS */
?>
