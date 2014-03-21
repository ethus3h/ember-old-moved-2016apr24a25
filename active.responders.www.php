<?php


#Arcmaj3
function arcmaj3_wint()
{
    $main_console = new FluidActive('Arcmaj3 statistics web console');
    $db           = new FractureDB('futuqiur_arcmaj3');
    #$main_console->DBTextEntry($db, 'am_urls', 'location', 0);
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->DBTableEntry($db, 'am_urls');
    #$main_console->DBRowEntry($db, 'am_urls', 1);
    #$main_console->getQueryCount($db);
    $allProjects  = $db->getTable('am_projects');
    $main_console->append('<small>');
    $main_console->append('<big><b>Overall</b></big><br>');
    $total     = $db->countTable('am_urls');
    $total     = $total[0];
    $total     = $total['COUNT(*)'];
    #print_r($total);
    $crawled   = $db->countRows('am_urls', ' WHERE barrel != \'0\'');
    $crawled   = $crawled[0];
    $crawled   = $crawled['COUNT(*)'];
    #print_r($crawled);
    $remaining = $total - $crawled;
    if ($total == 0) {
        $pcCr = 0;
        $pcRm = 0;
    } else {
        $pcCr = round(($crawled / $total) * 100, 2);
        $pcRm = round(($remaining / $total) * 100, 2);
    }
    $main_console->append('Total URLs: ' . $total . '<br>');
    $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
    $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    foreach ($allProjects as $index => $data) {
        $main_console->append('<br><br><big><b>Project: ' . $data['urlPattern'] . ' (ID ' . $data['id'] . ')</b></big><br>');
        $total     = $db->countTable('am_urls', 'project', $data['id']);
        $total     = $total[0];
        $total     = $total['COUNT(*)'];
        #print_r($total);
        $crawled   = $db->countTable('am_urls', 'project', $data['id'], ' AND barrel != \'0\'');
        $crawled   = $crawled[0];
        $crawled   = $crawled['COUNT(*)'];
        #print_r($crawled);
        $remaining = $total - $crawled;
        if ($total == 0) {
            $pcCr = 0;
            $pcRm = 0;
        } else {
            $pcCr = round(($crawled / $total) * 100, 2);
            $pcRm = round(($remaining / $total) * 100, 2);
        }
        $main_console->append('Total URLs: ' . $total . '<br>');
        $main_console->append('Remaining URLs: ' . $remaining . ' (' . $pcRm . '%)<br>');
        $main_console->append('Crawled URLs: ' . $crawled . ' <b>(' . $pcCr . '%)</b><br>');
    }
    $main_console->append('</small>');
    $main_console->close();
    $db->close();
}
function arcmaj3_adm()
{
    $main_console = new FluidActive('Arcmaj3 management web console');
    #handler=1&handlerNeeded=arcmaj3&amtask=expireBarrel&verd=2&barrelId=117
    $main_console->append('Expire old barrels (>1wk out): <form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireOldBarrels"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1"><input type="submit"></form><br><br>
Expire a barrel:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="expireBarrel"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Barrel ID:<input type="text" name="barrelId"><input type="submit"></form><br><br>
Add a URL:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addUrl"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">New URL:<input type="text" name="amNewUrl"><input type="submit"></form><br><br>
Add a new project:<br><form action="active.php"><input type="hidden" name="verd" value="2"><input type="hidden" name="amtask" value="addProject"><input type="hidden" name="handlerNeeded" value="arcmaj3"><input type="hidden" name="handler" value="1">Filter pattern:<input type="text" name="amFilterPattern">Seed URL:<input type="text" name="amSeedUrl"><input type="submit"></form>');
    $main_console->close();
}
function fluid_demo()
{
    $main = new FluidActive('Fluid//Active Demo Page. Background image: 4278145217_f6f7e5f871_o.jpg: by Casey Yee. CC Attribution Share-Alike 2.0.');
    $main->close();
}
function ember()
{

    $main = new FluidActive('Ember');
    
    $main->close();
}
?>