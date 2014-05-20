<?php

//Miscellaneous additional functions

//dHandler is a function that has something to do with FractureDB.
function dHandler($db, $query, $e, $qf = 'normal', $failed = False)
{
    global $displayDebugMessages;
    #based on http://stackoverflow.com/questions/12368561/deadlock-exception-code-for-php-mysql-pdoexception
    if ($e->errorInfo[0] == 40001 /*(ISO/ANSI) Serialization failure, e.g. timeout or deadlock*/ && $pdoDBHandle->getAttribute(PDO::ATTR_DRIVER_NAME == "mysql") && $exc->errorInfo[1] == 1213 /*(MySQL SQLSTATE) ER_LOCK_DEADLOCK*/ ) {
        if ($failed = False) {
            if ($qf = 'num') {
                $db->query_num($query, True);
            } else {
                $db->query($query, True);
            }
        } else {
            if ($displayDebugMessages) {
                print("Error: " . $e->getMessage());
            }
            throw $e;
        }
    } else {
        if ($displayDebugMessages) {
            print("Error: " . $e->getMessage());
        }
        throw $e;
    }
}

function arcmaj3_return_barrel($db, $newBarrelId, $urlsPerBucket = 1, $projectsToCrawl = '')
{
    #echo 'returning barrel';
    $projects = $db->getColumn_num('am_projects', 'id', 'status', '0');
    $prArrayv = array();
    foreach ($projects as $key => $value) {
        $prArrayv = array_merge($prArrayv, $value);
    }
    #print_r($prArrayv);
    
    #$projects              = $db->getRandomRow('am_projects', 'status', '0', 'id', $urlsPerBucket);
    $pArray = explode(',', $projectsToCrawl);
    #print_r($pArray);
    #$pInt is a list of project IDs to crawl.
    $pInt   = array();
    #If the user has specified which buckets to call, populate $pInt with those choices that are present in the Projects table. Otherwise, populate it with all available projects with status 0.
    if (strlen($projectsToCrawl) !== 0) {
        #echo 'Using this.';
        #echo 'Intersecting:';
        #print_r($prArrayv);
        
        #echo 'with';
        #print_r($pArray);
        #echo 'yields:';
        $pInt = array_intersect($prArrayv, $pArray);
        #print_r($pInt);
        // if(count($pInt)==0){
        // #No projects suggested matched; return all projects
        //  $pInt = $projects;
        //  }
    }
    
    else {
        $pInt = $projects;
        #print_r($pInt);
        #echo 'Using that.';
    }
    // $returnBarrel='';
    //     foreach ($pInt as $key => $value)
    // {
    // #foreach ($value as $key => $value){
    // $rowToAppend = $db->getRandomRow('am_urls', 'barrel', $value);
    // $returnBarrel = $returnBarrel + $rowToAppend['location'] + '\n';
    // #}
    // }
    //     foreach ($pInt as $key => $value) {
    //         $rowToAppend = $db->getRandomRow('am_urls', 'project', $value);
    //         $db->setField('am_urls', 'barrel', $newBarrelId, $rowToAppend['id']);
    //         $pInt[$key] = $rowToAppend['location'];
    //     }
    #echo '$pInt=';print_r($pInt);echo '.';
    $barrelRet  = '';
    #echo "\n\nENTERING DEBUG SECTION\n\n";
    #print_r($pInt);
    $urlCounter = 0;
    //     echo 'Urls per bucket: '. $urlsPerBucket . ".\n";
    while ($urlCounter < $urlsPerBucket) {
        //         echo 'Entered URL choice loop';
        //         echo $urlCounter . "\n";
        $randomChoice = array_rand($pInt);
        #echo $randomChoice . "\n";
        #echo '$randomChoice=';print_r($randomChoice);echo '.';
        #$randChInd    = $randomChoice[0];
        #echo $randChInd . "\n";
        $randomPA     = $pInt[$randomChoice];
        if (gettype($randomPA) == 'array') {
            $randomP = $randomPA[0];
        } else {
            $randomP = $randomPA;
        }
        #print_r($randomPA);
        #echo '$randomPA=';print_r($randomPA);echo '.';
        #echo 'RandomP='.$randomP . ".\n";
        #$rowToAppend = $db->getNextRow('am_urls', 'project', $randomP);
        $rowToAppend = $db->getNextRowEF('am_urls', 'project', $randomP, 'barrel = 0');
        #echo '$rowToAppend=';print_r($rowToAppend);echo '.';
        //         if ($rowToAppend['barrel'] !== '0') {
        //             #do nothing, the URL is already taken
        //             echo '';
        //         } else {
        echo '';
        if ($rowToAppend['project'] == '0') {
            #This is a stopgap measure until the # of barrel 0 rows goes down. Remove this block of code then.
            #What this block of code does is check each row chosen to get added to the barrel that has its Project listed as 0, check it against the Projects table, and if it matches an available project, update the URLs table to reflect that.
            #All URL patterns are in $pps.
            $pps              = $db->getColumn('am_projects', 'urlPattern');
            $testProjects     = False;
            $potentialProject = '';
            //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
            //print_r($pps);
            foreach ($pps as $ppid) {
                #Operate on each URL pattern from the Projects table. If the selected row contains a URL pattern, set $testProjects to True and $potentialProject to the URL pattern it matched.
                if (stripos($rowToAppend['location'], $ppid['urlPattern']) !== false) {
                    $testProjects     = True;
                    $potentialProject = $ppid['urlPattern'];
                }
            }
            #$potentialProject = get_domain_simple($value);
            #$projects contains the row from Projects corresponding to the URL pattern that matched the selected URL.
            $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
            #$projectId contains the ID of the project that the current URL matched. If the matched ID is other than 0, update the URLs table to reflect this.
            $projectId = $projects['id'];
            if ($projectId !== 0) {
                $db->setField('am_urls', 'project', $projectId, $rowToAppend['id']);
            }
        }
        #print_r($rowToAppend);
        #The chosen URL has now been updated if necessary. Update the barrel column to reflect its addition to the barrel.
        
        $UrlNotAppended = False;
        #Next line can be commented out for debugging this function. UNCOMMENT WHEN DONE.
        if ($rowToAppend['id'] > 1) {
            $db->setField('am_urls', 'barrel', $newBarrelId, $rowToAppend['id']);
        } else {
            $UrlNotAppended = True;
        }
        
        
        #$pInt[$randChInd] = $rowToAppend['location'];
        #If the URL is already in the barrel, do nothing. Otherwise, add it to the barrel.
        if ((stripos($barrelRet, $rowToAppend['id']) !== false) | $UrlNotAppended === True) {
            #if (stripos($barrelRet, $rowToAppend['id']) !== false) {
            #do nothing, the URL is already in here
            echo '';
        } else {
            $barrelRet = $barrelRet . $rowToAppend['location'] . "\n";
            $urlCounter++;
        }
        #}
    }
    #print_r($pInt);
    #echo "\n\nLEAVING DEBUG SECTION\n\n";
    
    #print_r($pInt);
    #$returnBarrel = implode("\n", $pInt);
    #print_r($returnBarrel);
    $returnBarrel = $barrelRet;
    #$returnBarrel = 'Test barrel result.';
    return $returnBarrel;
}
function arcmaj3_barrel_expire($barrelId)
{
    echo 'Expiring barrel ' . $barrelId . '...<br>' . "\n";
    $db = new FractureDB('futuqiur_arcmaj3');
    $db->updateColumn('am_urls', 'barrel', '0', 'barrel', $barrelId);
    $db->setField('am_barrels', 'status', '2', $barrelId);
    echo 'Expired barrel ' . $barrelId . '.<br>' . "\n";
    $db->close();
}
function insertChunk($data,$spar,$smd5,$scrc,$ssha,$ss512,$compression) {
	$icerror = 0;
	$par = par($data);
	$md5 = strtolower(md5($data));
	$crc = strtolower(dechex(crc32($data+md5($data))));
	$sha = strtolower(sha1($data));
	$s512 = strtolower(hash("sha512",$data));
	if(($spar != $par) || ($smd5 != $md5) || ($scrc != $crc) || ($ssha != $sha) || ($ss512 != $s512)) {
		$icerror = 8;
	}
	else {
		
	}
	if($icerror != 0) {
		header("HTTP/1.0 525 Request failed");
	}
	return array($newChunkId, $icerror);
}

?>