<?php
/*libdce: <font color="blue">Tests. Version 2.0, 31 December 2012 and accidentally 1 January 2013 (after midnight of 31 December 2012). (first independent version).
*/
/*function libdce_tests(){
//Tests:
test_add('<br><br><hr><br><h2>Begin test results</h2>');
//Input tests
test_add('<h3>Begin input translator tests</h3>');
//CDCE
test_add('<h4>Begin CDCE tests</h4>');
test_add('<h5>legacy_cdce</h5>');
test_add('Plain UTF-8 string to Dc: <font color="blue">' . dce_convert('Hello World!', 'legacy_cdce', 'dc') . '</font>. Should be: 57,86,93,93,96,18,72,96,99,93,85,19<br>');
test_add('Plain CDCE string to Dc: <font color="blue">' . dce_convert('Hello @1@World@8@!', 'legacy_cdce', 'dc') . '</font>. Should be: 57,86,93,93,96,18,1,72,96,99,93,85,8,19<br>');
test_add('Improper CDCE string to Dc: <font color="blue">' . dce_convert('Hello @1World@8@!', 'legacy_cdce', 'dc') . '</font>. Should be: 57,86,93,93,96,18,1,35,72,96,99,93,85,8,19<br>');
test_add('<h5>cdce_lstrict</h5>');
test_add('Plain UTF-8 string to Dc: <font color="blue">' . dce_convert('Hello World!', 'cdce_lstrict', 'dc') . '</font>. Should be: 57,86,93,93,96,18,72,96,99,93,85,19<br>');
test_add('Plain CDCE string to Dc: <font color="blue">' . dce_convert('Hello @1@World@8@!', 'cdce_lstrict', 'dc') . '</font>. Should be: 57,86,93,93,96,18,1,72,96,99,93,85,8,19<br>');
test_add('Improper CDCE string to Dc: <font color="blue">' . dce_convert('Hello @1World@8@!', 'cdce_lstrict', 'dc') . '</font>. Should give an error.<br>');
//DCE
test_add('<h4>Begin DCE tests</h4>');
test_add('<h5>dce</h5>');
test_add('DCE 3.0a to Dc: <font color="blue">' . dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc') . '</font>. Should be: 114,57,86,93,93,96,18,72,96,99,93,85,19,115<br>');
test_add('Simple DCE 3.01a to Dc: <font color="blue">' . dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc') . '</font>. Should be: 114,57,86,93,93,96,18,72,96,99,93,85,19,115<br>');
//3_0a
test_add('<h4>Begin DCE 3.0a tests</h4>');
test_add('<h5>3_0a</h5>');
test_add('DCE 3.0a to Dc: <font color="blue">' . dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc') . '</font>. Should be: 114,57,86,93,93,96,18,72,96,99,93,85,19,115<br>');
test_add('Simple DCE 3.01a to Dc: <font color="blue">' . dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc') . '</font>. Should give an error.<br>');
//Output tests
test_add('<br><br><h3>Begin output translator tests</h3>');
test_add('<h4>Begin Dc tests</h4>');
test_add('<h5>dc</h5>');
test_add('Dc to Dc: <font color="blue">' . dce_convert('57,86,93,93,96,18,1,72,96,99,93,85,8,19', 'dc', 'dc') . '</font>. Should be: 57,86,93,93,96,18,1,72,96,99,93,85,8,19<br>');
test_add('<br><br><br><h3>End test results</h3><br><hr><br><br>');
}*/

function libdce_tests(){
//Tests:
test_add('<br><br><hr><br><h2>Begin test results</h2>');
//Input tests
test_add('<h3>Begin input translator tests</h3>');
//CDCE
test_add('<h4>Begin CDCE tests</h4>');
test_add('<h5>legacy_cdce</h5>');
test('Plain UTF-8 string to Dc', dce_convert('Hello World!', 'legacy_cdce', 'dc'), '57,86,93,93,96,18,72,96,99,93,85,19');
test('Plain CDCE string to Dc', dce_convert('Hello @1@World@8@!', 'legacy_cdce', 'dc'), '57,86,93,93,96,18,1,72,96,99,93,85,8,19');
test('Improper CDCE string to Dc', dce_convert('Hello @1World@8@!', 'legacy_cdce', 'dc'), '57,86,93,93,96,18,1,35,72,96,99,93,85,8,19');
test_add('<h5>cdce_lstrict</h5>');
test('Plain UTF-8 string to Dc', dce_convert('Hello World!', 'cdce_lstrict', 'dc') , '57,86,93,93,96,18,72,96,99,93,85,19');
test('Plain CDCE string to Dc', dce_convert('Hello @1@World@8@!', 'cdce_lstrict', 'dc'), '57,86,93,93,96,18,1,72,96,99,93,85,8,19');
test('Improper CDCE string to Dc', dce_convert('Hello @1World@8@!', 'cdce_lstrict', 'dc'), '57,86,93,93,96,18… CDCE decoding error!');
//DCE
test_add('<h4>Begin DCE tests</h4>');
test_add('<h5>dce</h5>');
test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
//3_0a
test_add('<h4>Begin DCE 3.0a tests</h4>');
test_add('<h5>3_0a</h5>');
test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc'), 'This document is not stored using the correct version of DCE.');
//3_01a
//Needs UTF-8 encapsulation testing
test_add('<h4>Begin DCE 3.01a tests</h4>');
test_add('<h5>3_01a</h5>');
test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), 'This document is not stored using the correct version of DCE.');
test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
test('Complex DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD80C501FE48656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), '114,122,57,86,93,93,96,18,72,96,99,93,85,19,115');
//Dc
test_add('<h4>Begin Dc tests</h4>');
test_add('<h5>dc</h5>');
test('Dc to Dc', dce_convert('114,57,86,93,93,96,18,72,96,99,93,85,19,115', 'dc', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
//Output tests
test_add('<br><br><h3>Begin output translator tests</h3>');
test_add('<h4>Begin Dc tests</h4>');
test_add('<h5>dc</h5>');
test('Dc to Dc', dce_convert('57,86,93,93,96,18,1,72,96,99,93,85,8,19', 'dc', 'dc'), '57,86,93,93,96,18,1,72,96,99,93,85,8,19');

global $testfail;
if($testfail){test_add('<br><br><br><br><hr><br><br><br><br><h1><font color="red">SOME TESTS FAILED! LIBDCE DOES NOT APPEAR TO BE WORKING PROPERLY!</font></h1><br><br><br><br><hr><br><br><br><br>');}
test_add('<h3>End test results</h3><br><hr><br><br>');
}

function test($name,$command_output,$good_output) {
global $testfail;
if($command_output==$good_output) {$test_passfail = '<font color="green">PASS</font>';
}
else {$test_passfail = '<font color="red">FAIL</font>';$testfail=true;}
test_add($name . ': <font color="blue">' . $command_output . '</font>. Should be: ' . $good_output . '. → ' . $test_passfail . '<br>');
}
