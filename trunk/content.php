<?php
//CONTENT
if ($pageClass == 'w') {
    include ('d/r/weave.php');
} else {
    if ($pageClass == 'iwork2txt') {
        include ('d/r/iwork2txt.php');
    } else {
        if ($pageClass == 'e') {
            include ('d/r/ember.php');
        } else {
            include ('d/r/render-page.php');
        }
    }
}
?>
