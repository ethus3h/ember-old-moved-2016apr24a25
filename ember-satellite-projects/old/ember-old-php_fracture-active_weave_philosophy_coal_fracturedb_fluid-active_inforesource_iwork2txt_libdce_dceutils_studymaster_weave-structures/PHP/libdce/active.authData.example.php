<?php
#authData.example.php, version 1, 20 March 2014, based on authData.php version 2, 15 December 2013 a.mn..
if ($_SERVER["HTTP_HOST"] == '127.0.0.1') {
    global $dbName;
    $dbName = 'weave';
    global $dbUsername;
    $dbUsername = 'root';
    global $dbPassword;
    $dbPassword = 'foo';
}
else {
    global $dbName;
    $dbName = 'futuqiur_weave_data_db_new';
    global $dbUsername;
    $dbUsername = 'futuqiur_weave';
    global $dbPassword;
    $dbPassword = 'foo';
}
$db_data  = array(
    'futuqiur_arcmaj3' => array(
        'futuqiur_arcmaj3',
        'foo',
        'localhost'
    ),
    'futuqiur_wordlist' => array(
        'futuqiur_words',
        'foo',
        'localhost'
    )
);
?> 