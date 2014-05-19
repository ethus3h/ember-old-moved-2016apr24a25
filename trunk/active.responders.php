<?php

//Request responders.


#Arcmaj3
// $db             = new FractureDB('futuqiur_arcmaj3');
// $pps              = $db->getColumn('am_projects', 'urlPattern');
//             $testProjects     = False;
//             $potentialProject = '';
//             print_r($pps);
function wordlist_handler()
{
    $dataValue = Rq('wordToCheck');
    $db        = new FractureDB('futuqiur_wordlist');
    $data      = $db->getRows('words', 'word', $dataValue);
    $db->close();
    #print_r($data);
    if (count($data) !== 0) {
        echo '1';
    } else {
        echo '0';
    }
}
// function array_deep_search($array,$needle){
// $step1=array_map(in_array())
// }
function arcmaj3_handler()
{
    #verd: Arcmaj3 protocol version ID
    #echo Rq('verd');
    if (Rq('verd') == '2') {
        #do things...
        if (Rq('amtask') == 'up') {
            echo 'Uploaded barrel notification received.';
            #Client has finished a barrel. Parse new URL list provided and add to URLs table. Mark barrel's original URLs as completed, excluding URLs that ran into problems. Mark barrel as finished.
            #For now, all that will be provided here is an Internet Archive identifier.
            #Check for duplicates. Add new unique URLs matching intake criteria (matching an existing project) to URLs table.
            //         $ulBarrel   = file_get_contents($_FILES['uploadedBarrelData']['tmp_name']);
            //         $ulFailed   = file_get_contents($_FILES['failedUrlData']['tmp_name']);
            $BarrelUrlListLoc = 'https://archive.org/download/' . Rq('amloc') . '/' . 'URLs.lst';
            $uBarrelData      = get_url($BarrelUrlListLoc);
            //             #REWRITING THIS TO USE A FLAT FILE
            //             $BarrelUrlDataLoc = 'https://archive.org/download/' . Rq('amloc') . '/' . 'URLs.fragment';
            //             $uBarrelAppendData      = get_url($BarrelUrlDataLoc);
            #echo $uBarrelData;
            #echo $uBarrelData;
            echo "\n\n" . 'List data URL: ' . $BarrelUrlListLoc;
            $BarrelFailedListLoc = 'https://archive.org/download/' . Rq('amloc') . '/' . 'failed.lst';
            $uBarrelFailed       = get_url($BarrelFailedListLoc);
            #echo gzdecode($uBarrelFailed);
            echo "\n\n" . 'Failed entry data URL: ' . $BarrelFailedListLoc . "\n\n";
            echo "Decoding barrel data...\n";
            $ulBarrel = preg_replace('/[\n]+/', "\n", str_replace('\nhttp://http://', 'http://', gzdecode($uBarrelData)));
            #echo 'ulBarrel:' . "\n\n";
            //print_r($ulBarrel);
            echo "\n\n";
            echo "\n\n";
            echo "Decoding failed URL data...\n";
            $ulFailed   = preg_replace('/[\n]+/', "\n", gzdecode($uBarrelFailed));
            $ulBarrel   = str_replace("\r", "\n", $ulBarrel);
            $ulFailed   = str_replace("\r", "\n", $ulFailed);
            $barrelData = explode("\n", $ulBarrel);
            echo '<pre>';
            print_r($barrelData);
            echo "\n\n";
            $failedData = explode("\n", $ulFailed);
            print_r($failedData);
            echo '</pre>';
            $barrelId       = $barrelData[0];
            $barrelUserName = $barrelData[1];
            $db             = new FractureDB('futuqiur_arcmaj3');
            //             $urlsFinished   = $db->getRows('am_urls', 'barrel', $barrelId);
            //             $urlsFinished   = $urlsFinished[0];
            $barrelSize     = Rq('barrelSize');
            #Set status to 1. Set who to $barrelUserName.
            $db->setField('am_barrels', 'ia_identifier', Rq('amloc'), $barrelId);
            $db->setField('am_barrels', 'size', $barrelSize, $barrelId);
            #$pps              = $db->getColumn('am_projects', 'urlPattern');
            $pptb = $db->getTable('am_projects');
            #echo '<H1>ENTERING GENERAL ROW PROCESSING</H1>';
            $newUrlDataFile = '';
            foreach ($barrelData as $value) {
                #Add URL to URL list.
                $testProjects     = False;
                $potentialProject = '';
                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                //print_r($pps);
                #echo "\n\n<br><br><hr><br><br>Beginning processing url: ".$value.".\n\n<br><br>";
                #echo '<pre>';
                // foreach ($pps as $ppid) {
                //                     if (stripos($value, $ppid['urlPattern']) !== false) {
                //                         $testProjects     = True;
                //                         #$potentialProject = $ppid['id'];
                //     echo '$ppid=';print_r($ppid);echo '.';
                //     #echo '$pptb=';print_r($pptb);echo '.';
                //                         foreach ($pptb as $value) {
                // if(in_array($ppid['urlPattern'],$value))
                // {
                //                         $potentialProjectA = $value;
                //
                // }
                // }
                // #                        $potentialProjectA = array_search($ppid['urlPattern'],$pptb);
                //
                //                         #$ppAstep1=array_map(in_array)
                //                         $potentialProject = $potentialProjectA['id'];
                //                     }
                //                 }
                
                foreach ($pptb as $pprow) {
                    
                    if (stripos($value, $pprow['urlPattern']) !== false) {
                        $potentialProjectA = $pprow;
                        $testProjects      = True;
                    }
                    
                }
                if (isset($potentialProjectA)) {
                    $potentialProject = $potentialProjectA['id'];
                }
                
                
                #echo '$potentialProjectA=';print_r($potentialProjectA);echo '.';
                #echo '$potentialProject=';print_r($potentialProject);echo '.';
                #echo '</pre>';
                
                #$potentialProject = get_domain_simple($value);
                #$projects  = $db->getRow('am_projects', 'id', $potentialProject);
                //                 $projects  = $potentialProjectA;
                //                 $projectId = $projects['id'];
                $projectId      = $potentialProject;
                #$projectId=1;
                if ($testProjects) {
                    #$db->SaveState();
                    
                    #$newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique, originBarrel', "'" . $db->UrlEscS($value) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($value)) . "', '" . $barrelId . "'");
                    #Generate line for the URL data file
                    #echo $db->UrlEscS($value) . "\t" . $projectId . "\t" . hash('sha512', $db->UrlEscS($value)) . "\t" . $barrelId . "\n";
                    $newUrlDataFile = $newUrlDataFile.$db->UrlEscS($value) . "\t" . $projectId . "\t" . hash('sha512', $db->UrlEscS($value)) . "\t" . $barrelId . "\n";
                    
                }
                $filenametowrite = '/.Arcmaj3ServerTemp/' . str_replace('/', '', Rq('amloc')) . '.fragment';
                file_put_contents($filenametowrite, $newUrlDataFile);
                #LOAD DATA LOCAL INFILE...
                $db->LoadFromFile($filenametowrite, 'am_urls', 'location, project, locationHashUnique, originBarrel');
                unlink($filenametowrite);
                //                 echo "<br>\n";
                //                 echo 'Added/updated row ';
                //                 echo $newUrlId;
                //                 echo ', URL ';
                //                 echo $value;
                //                 echo "<br>\n";
                
            }
            #echo '<H1>EXITING GENERAL ROW PROCESSING</H1>';
            //             foreach ($urlsFinished as $value) {
            //                 #Set completed to true.
            //                 $db->setField('am_urls', 'completed', 1, $value);
            //             }
            //             $pps              = $db->getColumn('am_projects', 'urlPattern');
            #$pptb              = $db->getTable('am_projects');
            
            #echo '<H1>ENTERING FAILED ROW PROCESSING</H1>';
            foreach ($failedData as $value) {
                
                #TODO: Increment failedAttempts, set completed to false, set barrel to 0
                #echo "\n\n<br><br><hr><br><br>Beginning working with failed row: ".$value.".\n\n<br><br>";
                $testProjects     = False;
                $potentialProject = '';
                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                //print_r($pps);
                
                foreach ($pptb as $pprow) {
                    
                    if (stripos($value, $pprow['urlPattern']) !== false) {
                        $potentialProjectA = $pprow;
                        $testProjects      = True;
                    }
                    
                }
                $potentialProject = $potentialProjectA['id'];
                /*
                foreach ($pps as $ppid) {
                if (stripos($value, $ppid['urlPattern']) !== false) {
                $testProjects     = True;
                $potentialProjectA = array_search($ppid['urlPattern'],$pptb);
                $potentialProject = $potentialProjectA['id'];
                }
                }*/
                #$potentialProject = get_domain_simple($value);
                #                $projects  = $db->getRow('am_projects', 'id', $potentialProject);
                /*                 $projects  = $potentialProjectA;
                $projectId = $projects['id'];*/
                $projectId        = $potentialProject;
                $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique, originBarrel', "'" . $db->UrlEscS($value) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($value)) . "', '" . $barrelId . "'");
                #$failedRowIdRecord = $db->getRow('am_urls', 'location', $db->UrlEscS($value));
                $failedRowIdRecordA = $db->query("SELECT id, failedAttempts FROM `am_urls` WHERE location = '" . $db->UrlEscS($value) . "'");
                $failedRowIdRecord  = $failedRowIdRecordA[0];
                $failedRowId        = $failedRowIdRecord['id'];
                #echo "\n\n<br><br><hr><br><br>Beginning working with failed row (ID $failedRowId): ".$value.".\n\n<br><br>";
                
                #echo "\n\nWorking with failed row $value, ID $failedRowId";
                //                 $currentFailed = $db->getField('am_urls', 'failedAttempts', $failedRowId);
                //                 $currentFailed++;
                $db->query('UPDATE `am_urls` SET failedAttempts = failedAttempts+1, completed=0 WHERE id=' . $failedRowId . ';');
                //                 $db->setField('am_urls', 'failedAttempts', $currentFailed, $failedRowId);
                //                 $db->setField('am_urls', 'completed', 0, $failedRowId);
                #Permanently fail URLs that have failed 100 times.
                if ($failedRowIdRecord['failedAttempts'] < 100) {
                    $db->setField('am_urls', 'barrel', 0, $failedRowId);
                }
            }
            #echo '<H1>EXITING FAILED ROW PROCESSING</H1>';
            
            $db->setField('am_barrels', 'status', 1, $barrelId);
            $db->close();
            if (count($barrelData) < 2) {
                echo 'Barrel upload failed. Expiring barrel ' . $barrelId . '…';
                #The barrel data array should always have at least two things in it, I'm saying. Could be a one-URL barrel I suppose, but even that should get listed twice (once for Wget, once for Heritrix)
                arcmaj3_barrel_expire($barrelId);
                echo 'Expired barrel ' . $barrelId . '.';
            }
            echo "\n\n<br><br>\n\nBarrel notification processing finished.";
            
        } else {
            if (Rq('amtask') == 'down') {
                #Client wants a barrel. Compile random URLs from the URL table of unfinished URLs and create a barrel. Mark the URLs as taken. Add barrel to barrels table.
                #For now, choose an uncompleted URL from the table, and send it.
                #Number of URLs per bucket:
                #Default
                $urlsPerBucket = 20;
                #Override
                if (strlen(Rq('NSConfLmDs')) !== 0) {
                    $urlsPerBucket = Rq('NSConfLmDs');
                } else {
                    $urlsPerBucket = $urlsPerBucket;
                }
                #Barrel format 0.1:
                #ID,0xURL\n            
                $db          = new FractureDB('futuqiur_arcmaj3');
                #Make a new barrel.
                $newBarrelId = $db->addRow('am_barrels', 'status, who, dateAssigned, barrelCount', "'0', '" . Rq('userName') . "', '" . date('Y') . "-" . date('m') . "-" . date('d') . "', '" . $urlsPerBucket . "'");
                echo $newBarrelId . "\n";
                $projCrawlRq = Rq('projectsToCrawl');
                $barrel      = arcmaj3_return_barrel($db, $newBarrelId, $urlsPerBucket, $projCrawlRq);
                echo $barrel;
                //             while ($urlCounter < $urlsPerBucket) {
                //                 $rowToReturn = $db->getRandomRow('am_urls', 'barrel', '0');
                //                 $db->setField('am_urls', 'barrel', $newBarrelId, $rowToReturn['id']);
                //                 #echo $rowToReturn['id'].','.$rowToReturn['location'];
                //                 #Barrel format 0.11:
                //                 #0xURL\n
                //                 echo $rowToReturn['location'] . "\n";
                //                 $urlCounter++;
                //             }
                #echo '<br><br>Page served using ' . $db->queryCount . ' queries.<br><br>';
                $db->close();
                // echo "http://drive.google.com/\n";
                // echo "http://wretch.cc/\n";
                // echo "http://example.com/\n";
                // echo "http://comments.gmane.org/gmane.mail.squirrelmail.plugins/9672\n";
                // echo "http://futuramerlin.com\n";
                // echo "http://archive.org\n";
                // echo "https://archive.org\n";
            } else {
                if (Rq('amtask') == 'expireBarrel') {
                    $barrelId = Rq('barrelId');
                    arcmaj3_barrel_expire($barrelId);
                } else {
                    if (Rq('amtask') == 'expireOldBarrels') {
                        $db         = new FractureDB('futuqiur_arcmaj3');
                        # from http://stackoverflow.com/questions/17307587/mysql-datetime-evaluation-get-all-records-whose-value-is-before-midnight-of-the
                        $barrelData = $db->query('SELECT id FROM `am_barrels` WHERE status=0 AND dateAssigned < ( DATE(NOW()) - INTERVAL 1 DAY );');
                        # print_r($barrelData);
                        foreach ($barrelData as $key => $value) {
                            $barrelId = $value['id'];
                            arcmaj3_barrel_expire($barrelId);
                        }
                        $db->close();
                    } else {
                        if (Rq('amtask') == 'addUrl') {
                            $db               = new FractureDB('futuqiur_arcmaj3');
                            $newUrl           = Rq('amNewUrl');
                            $pps              = $db->getColumn('am_projects', 'urlPattern');
                            $testProjects     = False;
                            $potentialProject = '';
                            //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                            //print_r($pps);
                            foreach ($pps as $ppid) {
                                if (stripos($newUrl, $ppid['urlPattern']) !== false) {
                                    $testProjects     = True;
                                    $potentialProject = $ppid['urlPattern'];
                                }
                            }
                            #$potentialProject = get_domain_simple($value);
                            $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
                            $projectId = $projects['id'];
                            #$projectId=1;
                            if ($testProjects) {
                                $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                            } else {
                                $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '0', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                            }
                            echo 'Inserted URL ' . $newUrlId . "<br>\n";
                            $db->close();
                        } else {
                            if (Rq('amtask') == 'addProject') {
                                $db               = new FractureDB('futuqiur_arcmaj3');
                                $newUrl           = Rq('amSeedUrl');
                                $newProject       = Rq('amFilterPattern');
                                $newProjectId     = $db->addRow('am_projects', 'urlPattern, patternHashUnique', "'" . $db->UrlEscS($newProject) . "', '" . hash('sha512', $db->UrlEscS($newProject)) . "'");
                                $pps              = $db->getColumn('am_projects', 'urlPattern');
                                $testProjects     = False;
                                $potentialProject = '';
                                //$pp=fuzzyMatchGetRow('am_projects','projectId','urlPattern','',$limit='')['projectId'];
                                //print_r($pps);
                                foreach ($pps as $ppid) {
                                    if (stripos($newUrl, $ppid['urlPattern']) !== false) {
                                        $testProjects     = True;
                                        $potentialProject = $ppid['urlPattern'];
                                    }
                                }
                                #$potentialProject = get_domain_simple($value);
                                $projects  = $db->getRow('am_projects', 'urlPattern', $potentialProject);
                                $projectId = $projects['id'];
                                #$projectId=1;
                                if ($testProjects) {
                                    $newUrlId = $db->addRowFuzzy('am_urls', 'location, project, locationHashUnique', "'" . $db->UrlEscS($newUrl) . "', '" . $projectId . "', '" . hash('sha512', $db->UrlEscS($newUrl)) . "'");
                                }
                                echo 'Inserted project ' . $newProjectId . ' (' . $newProject . ') with seed URL ' . $newUrlId . "<br>\n";
                                $db->close();
                            } else {
                                echo 'Unrecognized operation';
                            }
                        }
                    }
                    
                    
                }
            }
        }
    } else {
        echo 'Incorrect protocol auth ID';
    }
}
function DBSimpleSubmissionHandler()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    $dbName           = $_REQUEST['db'];
    $dataTargetTable  = $_REQUEST['dataTargetTable'];
    $dataTargetField  = $_REQUEST['dataTargetField'];
    $dataTargetRowId  = $_REQUEST['dataTargetRowId'];
    $dataValue        = $_REQUEST['dataValue'];
    global $generalAuthKey;
    //echo $authorizationKey;
    if($authorizationKey == $generalAuthKey) {
    	//echo 'true';
		$db               = new FractureDB($dbName);
		$db->setField($dataTargetTable, $dataTargetField, $dataValue, $dataTargetRowId);
		$db->close(); 
    }
}

function CoalIntake()
{
    $authorizationKey = $_REQUEST['authorizationKey'];
    $dbName           = $_REQUEST['db'];
    global $generalAuthKey;
    global $error;
    if($authorizationKey == $generalAuthKey) {
    	//Request accepted
    	//help on times from http://stackoverflow.com/questions/5849702/php-file-upload-time-created
    	$metadata = bin2hex(var_export($_FILES,true));
    	$filename = $_FILES['uploadedfile']['name'];
    	$type = $_FILES['uploadedfile']['type'];
    	$size = $_FILES['uploadedfile']['size'];
    	$tmp_name = $_FILES['uploadedfile']['tmp_name'];
    	$error = $_FILES['uploadedfile']['error'];
    	//based on http://www.tizag.com/phpT/fileupload.php?MAX_FILE_SIZE=100000&uploadedfile=
    	$target_path = "coal_temp/";
    	//cot file extension — Coal temporary data file; can be any binary data
		$target_path = $target_path . "data.cot"; 
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
			echo "The file ".basename( $_FILES['uploadedfile']['name'])." has been uploaded";
		} else {
  			echo "There was an error uploading the file, please try again!";
  			$error = 2;
		}
		if(file_exists($target_path)) {
    		$data = file_get_contents($target_path);
    		$stat = stat( $target_path );
    		$smtime = $stat['mtime'];
    		$stats = bin2hex(var_export($stat,true));
    		$ctime = filectime($target_path);
    		$mtime = filemtime($target_path);
    		$atime = fileatime($target_path);
    	}
  		else {
    		//Failed
    		$error = 3;
  		}
		$md5 = md5($data);
		$crc = bin2hex(get_signed_int(crc32($data)));
		$sha = sha1($data);
		
		$db               = new FractureDB($dbName);
		$db->setField($dataTargetTable, $dataTargetField, $dataValue, $dataTargetRowId);
		$db->close(); 
    }
    else {
    	$error = 1;
    }
}
function CoalRetrieve()
{
   
}
function CoalChunkIntake()
{
   
}
function CoalChunkRetrieve()
{
   
}

function emberBackend() {
	session_start();
	if (isset($_REQUEST['emSession'])) {
		session_id($_REQUEST['emSession']);
	} else {
	}
	global $emUserName;
	$emUserName = $_SESSION['emUserName'];
	global $emUserPassword;
	$emUserPassword = $_SESSION['emUserPassword'];
	if ($emberActionId == 'emLogInExecute') {
		$_SESSION['emUserName'] = $_POST['emUserName'];
		$_SESSION['emUserPassword'] = $_POST['emUserPassword'];
	} else {
	}
}

?>