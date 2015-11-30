<?php
//Print the specified field returned by a query.
function qrp($table, $field, $filter, $filtervalue)
{
    e(qry($table, $field, $filter, $filtervalue));
}
?>
