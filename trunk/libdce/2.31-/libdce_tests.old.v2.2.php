<?php
/*libdce: <font color="blue">Tests. Version 2.2, 1 January 2013. Note that Version 2.1 was misannotated; the correct annotation would have been "Version 2.1, 1 January 2013 (after midnight of 31 December 2012) (not first independent version)".
*/
function libdce_tests()
{
    //Tests:
    test_add('<br><br><hr><br><h2>Begin test results</h2>');
    //Input tests
    test_add('<h3>Begin input translator tests</h3>');
    //CDCE
    test_add('<h4>Begin CDCE tests</h4>');
    test_add('<h5>legacy_cdce</h5>');
    test('Plain UTF-8 string to Dc', dce_convert('Hello World!', 'legacy_cdce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Plain CDCE string to Dc', dce_convert('Hello @1@World@8@!', 'legacy_cdce', 'dc'), '114,57,86,93,93,96,18,1,72,96,99,93,85,8,19,115');
    test('Improper CDCE string to Dc', dce_convert('Hello @1World@13@@8@!', 'legacy_cdce', 'dc'), '114,57,86,93,93,96,18,1,35,72,96,99,93,85,1,35,37,1,8,19,115');
    test_add('<h5>cdce_lstrict</h5>');
    test('Plain UTF-8 string to Dc', dce_convert('Hello World!', 'cdce_lstrict', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Plain CDCE string to Dc', dce_convert('Hello @1@World@8@!', 'cdce_lstrict', 'dc'), '114,57,86,93,93,96,18,1,72,96,99,93,85,8,19,115');
    test('Improper CDCE string to Dc', dce_convert('Hello @1World@13@@8@!', 'cdce_lstrict', 'dc'), '114,57,86,93,93,96,18… CDCE decoding error!');
    //DCE
    test_add('<h4>Begin DCE tests</h4>');
    test_add('<h5>dce</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), 'dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Hex DCE to Dc', dce_convert('44434565020102FD8048656C6C6F20576F726C642181FD03', 'dce', 'dc'), 'This document is not stored using the specified format.');
    test_add('<h5>hex_dce</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'hex_dce', 'dc'), 'This document is not stored using the specified format.');
    test('Hex DCE 3.0a to Dc', dce_convert('44434565020101FD8048656C6C6F20576F726C642181FD03', 'hex_dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Simple Hex DCE 3.01a to Dc', dce_convert('44434565020102FD8048656C6C6F20576F726C642181FD03', 'hex_dce', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    //3_0a
    test_add('<h4>Begin DCE 3.0a tests</h4>');
    test_add('<h5>3_0a</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_0a', 'dc'), 'This document is not stored using the specified format.');
    test_add('<h5>3_0a_raw</h5>');
    test('DCE 3.0a Raw to Dc', dce_convert(hex2bin('8048656C6C6F20576F726C642181'), '3_0a_raw', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_0a_raw', 'dc'), 'This document is not stored using the specified format.');
    test_add('<h5>hex_3_0a</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'hex_3_0a', 'dc'), 'This document is not stored using the specified format.');
    test('Hex DCE 3.0a to Dc', dce_convert('44434565020101FD8048656C6C6F20576F726C642181FD03', 'hex_3_0a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Simple Hex DCE 3.01a to Dc', dce_convert('44434565020102FD8048656C6C6F20576F726C642181FD03', 'hex_3_0a', 'dc'), 'This document is not stored using the specified format.');
    test_add('<h5>hex_3_0a_raw</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), 'hex_3_0a_raw', 'dc'), 'This document is not stored using the specified format.');
    test('Hex DCE 3.0a to Dc', dce_convert('44434565020101FD8048656C6C6F20576F726C642181FD03', 'hex_3_0a_raw', 'dc'), 'This document is not stored using the specified format.');
    test('Hex DCE 3.0a Raw to Dc', dce_convert('8048656C6C6F20576F726C642181', 'hex_3_0a_raw', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    //3_01a
    //Needs UTF-8 encapsulation testing
    test_add('<h4>Begin DCE 3.01a tests</h4>');
    test_add('<h5>3_01a</h5>');
    test('DCE 3.0a to Dc', dce_convert(hex2bin('44434565020101FD8048656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), 'This document is not stored using the specified version of DCE.');
    test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Complex DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD80C501FE48656C6C6F20576F726C642181FD03'), '3_01a', 'dc'), '114,122,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test_add('<h5>3_01a_raw</h5>');
    test('Simple DCE 3.01a Raw to Dc', dce_convert(hex2bin('8048656C6C6F20576F726C642181'), '3_01a_raw', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Complex DCE 3.01a Raw to Dc', dce_convert(hex2bin('80C501FE48656C6C6F20576F726C642181'), '3_01a_raw', 'dc'), '114,122,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), '3_01a_raw', 'dc'), 'This document is not stored using the specified format.');
    test_add('<h5>hex_3_01a</h5>');
    test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), 'hex_3_01a', 'dc'), 'This document is not stored using the specified format.');
    test('Hex DCE 3.0a to Dc', dce_convert('44434565020101FD8048656C6C6F20576F726C642181FD03', 'hex_3_01a', 'dc'), 'This document is not stored using the specified version of DCE.');
    test('Simple Hex DCE 3.01a to Dc', dce_convert('44434565020102FD8048656C6C6F20576F726C642181FD03', 'hex_3_01a', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Complex Hex DCE 3.01a to Dc', dce_convert('44434565020102FD80C501FE48656C6C6F20576F726C642181FD03', 'hex_3_01a', 'dc'), '114,122,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test_add('<h5>hex_3_01a_raw</h5>');
    test('Simple DCE 3.01a to Dc', dce_convert(hex2bin('44434565020102FD8048656C6C6F20576F726C642181FD03'), 'hex_3_01a_raw', 'dc'), 'This document is not stored using the specified format.');
    test('Simple DCE 3.01a Raw to Dc', dce_convert(hex2bin('8048656C6C6F20576F726C642181'), 'hex_3_01a_raw', 'dc'), 'This document is not stored using the specified format.');
    test('Simple Hex DCE 3.01a to Dc', dce_convert('44434565020102FD8048656C6C6F20576F726C642181FD03', 'hex_3_01a_raw', 'dc'), 'This document is not stored using the specified format.');
    test('Simple Hex DCE 3.01a Raw to Dc', dce_convert('8048656C6C6F20576F726C642181', 'hex_3_01a_raw', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Complex Hex DCE 3.01a Raw to Dc', dce_convert('80C501FE48656C6C6F20576F726C642181', 'hex_3_01a_raw', 'dc'), '114,122,57,86,93,93,96,18,72,96,99,93,85,19,115');
    //Dc
    test_add('<h4>Begin Dc tests</h4>');
    test_add('<h5>dc</h5>');
    test('Dc to Dc', dce_convert('114,57,86,93,93,96,18,72,96,99,93,85,19,115', 'dc', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    //Unicode
    //These tests assume that Unicode encapsulation is not supported (it isn't, at the moment).
    test_add('<h4>Begin Unicode tests</h4>');
    test_add('<h5>utf8</h5>');
    test('UTF-8 to Dc, simple', dce_convert('Hello World!', 'utf8', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('UTF-8 to Dc, messy', dce_convert('Hello— –World!', 'utf8', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('UTF-8 to Dc, non-BMP', dce_convert('🌄 Hello World! 🌄', 'utf8', 'dc'), '114,18,57,86,93,93,96,18,72,96,99,93,85,19,18,115');
    test_add('<h5>utf32</h5>');
    test('UTF-8 to Dc', dce_convert('Hello World!', 'utf32', 'dc'), 'This document is not stored using the specified format.');
    test('UTF-32 to Dc, simple', dce_convert(iconv('UTF-8', 'UTF-32BE', 'Hello World!'), 'utf32', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('UTF-32 to Dc, messy', dce_convert(iconv('UTF-8', 'UTF-32BE', 'Hello— –World!'), 'utf32', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('UTF-32 to Dc, non-BMP', dce_convert(iconv('UTF-8', 'UTF-32BE', '🌄 Hello World! 🌄'), 'utf32', 'dc'), '114,18,57,86,93,93,96,18,72,96,99,93,85,19,18,115');
    test_add('<h5>utf8_base64</h5>');
    test('Base64 UTF-8 to Dc, simple', dce_convert(base64_encode('Hello World!'), 'utf8_base64', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Base64 UTF-8 to Dc, messy', dce_convert(base64_encode('Hello— –World!'), 'utf8_base64', 'dc'), '114,57,86,93,93,96,18,72,96,99,93,85,19,115');
    test('Base64 UTF-8 to Dc, non-BMP', dce_convert(base64_encode('🌄 Hello World! 🌄'), 'utf8_base64', 'dc'), '114,18,57,86,93,93,96,18,72,96,99,93,85,19,18,115');
    test_add('<h5>utf8_dc64</h5>');
    test('Raw Base64 Dc list encapsulated Unicode to Dc', dce_convert('156,133,148,178,156,127,195,195', 'utf8_dc64', 'dc'), '114,101,86,100,101,115');
    //Output tests
    test_add('<br><br><h3>Begin output translator tests</h3>');
    test_add('<h4>Begin Dc tests</h4>');
    test_add('<h5>dc</h5>');
    test('Dc to Dc, simple', dce_convert('114,57,86,93,93,96,18,1,72,96,99,93,85,8,19,115', 'dc', 'dc'), '114,57,86,93,93,96,18,1,72,96,99,93,85,8,19,115');
    test('Dc to Dc, missing boundedness markings', dce_convert('57,86,93,93,96,18,1,72,96,99,93,85,8,19', 'dc', 'dc'), '114,57,86,93,93,96,18,1,72,96,99,93,85,8,19,115');
    test_add('<h5>utf8_dc64_enc</h5>');
    //test('Raw Base64 Dc list encapsulated Unicode to Base64 Dc list encapsulated Unicode', dce_convert('156,133,148,178,156,127,195,195','utf8_dc64','utf8_dc64_enc'), '114,191,156,133,148,178,156,127,195,195,192,115');
    global $testfail;
    if ($testfail) {
        test_add('<br><br><br><br><hr><br><br><br><br><h1><font color="red">SOME TESTS FAILED! LIBDCE DOES NOT APPEAR TO BE WORKING PROPERLY!</font></h1><br><br><br><br><hr><br><br><br><br>');
    }
    test_add('<h3>End test results</h3><br><hr><br><br>');
}
function test($name, $command_output, $good_output)
{
    global $testfail;
    if ($command_output == $good_output) {
        $test_passfail = '<font color="green">PASS</font>';
    } else {
        $test_passfail = '<font color="red">FAIL</font>';
        $testfail = true;
    }
    test_add($name . ': <font color="blue">' . $command_output . '</font>. Should be: ' . $good_output . '. → ' . $test_passfail . '<br>');
}
