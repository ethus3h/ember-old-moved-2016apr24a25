<?php
/* VERIFYING ACTION APPROVAL */
$axnAuthPriv = qry('operation', 'operation_permission_required', 'operation_id', fv('a'));
if ($login == 0) {
    $userAuth = 0;
} else {
    $userAuth = qry('user', 'user_authorisation_type', 'user_name', fv('wvUserName'));
}
if ($userAuth >= $axnAuthPriv) {
    $userpermissionverified = 1;
    if (strpos('node', $wvActionId) === true || strpos('Node', $wvActionId) === true) {
        //TODO
        checkPermissions($nodeId);
        if (($userPermissionRead == 1 || $userPermissionWrite == 2 && $wvActionIdCheck == 'viewNode') || ($userPermissionWrite == 2 && $wvActionIdCheck == 'editNode')) {
            $nodepermerr == 1;
        } else {
            $nodepermerr == 0;
            echo SELECT;
        }
    } else {
        //This is not a node action
        $nodepermerr = 0;
    }
} else {
    $userpermissionverified = 0;
}
/* END ACTION APPROVAL */
?>
