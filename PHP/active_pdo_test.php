<?php
#PDO test. 5 November 2013.
try {
$query='SELECT * FROM am_urls;';
$password='Kuzmenkotaxservices5.';
echo '<br><br><font color="red">EXECUTING QUERY: '.$query.'</font><br><br>';
$db   = new PDO('mysql:host=localhost;dbname='.'futuqiur_arcmaj3;charset=utf8', "futuqiur_arcmaj3", $password);
$result=$db->query($query);
print '<br><br>This query returned: <br>';
$res_array=$result->fetchAll();
print_r($res_array);
print '<br><br>';
} catch(PDOException $e) {
        print("Error: " . $e->getMessage());
}
return $res_array;
?> 