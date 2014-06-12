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
                echo 'Barrel upload failed. Expiring barrel ' . $barrelId . 'â€¦';
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

function CoalIntakeHandler()
{
	global $l;
	$l = new llog;
	$l->a("Started CoalIntakeHandler<br>");
	global $generalAuthKey;
	if(authorized($generalAuthKey)) {
		global $coalVersion;
		$coalVersion = 5;
		global $coalCompressionType;
		$coalCompressionType='bz2';
    	$insertion = insertCoal();
    	//echo 'INSERTION: '. print_r($insertion,true);
    	$id = $insertion['id'];
    	$status = $insertion['status'];
    	if(check($status,true)) {
			if(isset($_REQUEST['outputwebloc'])) {
				$filename=base64_decode($insertion['filename']);
				$smallified="[InternetShortcut]\nURL=http://futuramerlin.com/d/r/active.php?coalId=".$id."&authorizationKey=".urlencode($generalAuthKey)."&handler=1&coalVerbose=1&handlerNeeded=CoalRetrieve\nIconIndex=0";
				start_file_download($filename,strlen($smallified));
				echo $smallified;
			}
			else {
				if(check($status,true)) {
					if(isset($_REQUEST['coalVerbose'])) {
						echo 'Added coal: ';
					}
					$csum = Csum_import($insertion['csum']);
					//echo 'CSUM: '.print_r($csum,true);
					echo $id.'|'.$csum->len.'|'.$csum->md5.'|'.$csum->sha.'|'.$csum->s512;
					if(isset($_REQUEST['coalVerbose'])) {
						echo '; used '.memory_get_peak_usage().' bytes of memory at peak; currently using '.memory_get_usage().' bytes of memory.<br><h1>Log output:</h1><br><small>';
						$l->e();
					}
				}
			}
		}
	}
}

function DataIntakeHandler()
{
	global $l;
	$l = new llog;
	global $generalAuthKey;
	if(authorized($generalAuthKey)) {
		$l->a("Started DataIntakeHandler<br>");
		$status = 0;
		if(isset($_FILES['uploadedfile'])) {
			$ulfilename = base64_encode($_FILES['uploadedfile']['name']);
			$ulsize = base64_encode($_FILES['uploadedfile']['size']);
		}
		else {
			$ulfilename = null;
			$ulsize = null;
		}
		$filename = "coal_temp/"."data.".guidv4().".stt";
		if(isset($_FILES['uploadedfile'])) {		
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename)) {
			} else {
				$status = 2;
				$l->a("error 2<br>");
			}
		}
		else {
			$status = 6;
			$l->a("error 6<br>");
		}
		$csum = new Csum(null,$filename);
		$insertion = store(file_get_contents($filename),$csum);
		$id = $insertion['id'];
		$status = $insertion['status'];
		if(check($status,true)) {
			echo $id.'|'.$csum->len.'|'.$csum->md5.'|'.$csum->sha.'|'.$csum->s512;
		}
	}
}

function PunkRecordIntakeHandler()
{
	global $l;
	$l = new llog;
	global $generalAuthKey;
	if(authorized($generalAuthKey)) {
		$l->a("Started DataIntakeHandler<br>");
		$status = 0;
		if(isset($_FILES['uploadedfile'])) {
			$ulfilename = base64_encode($_FILES['uploadedfile']['name']);
			$ulsize = base64_encode($_FILES['uploadedfile']['size']);
		}
		else {
			$ulfilename = null;
			$ulsize = null;
		}
		$filename = "coal_temp/"."data.".guidv4().".stt";
		if(isset($_FILES['uploadedfile'])) {		
			if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $filename)) {
			} else {
				$status = 2;
				$l->a("error 2<br>");
			}
		}
		else {
			$status = 6;
			$l->a("error 6<br>");
		}
		$csum = new Csum(null,$filename);
		$insertion = store(file_get_contents($filename),$csum);
		$id = $insertion['id'];
		$status = $insertion['status'];
		$db = new FractureDB('futuqiur_ember');
		$addedTree = $db->addRow('trees', 'user, collection, number, csum', "'" . $_REQUEST['punkUser'] . "', '" . $_REQUEST['punkCollection'] . "', '" . $_REQUEST['punkCollection'] . "', '" . $id . "', '" . $csum->len.'|'.$csum->md5.'|'.$csum->sha.'|'.$csum->s512 . "'");
		if(check($status,true)) {
			echo $id.'|'.$csum->len.'|'.$csum->md5.'|'.$csum->sha.'|'.$csum->s512;
		}
	}
}

function DataRetrieveHandler()
{
	global $l;
	$l = new llog;
	global $generalAuthKey;
	if(authorized($generalAuthKey)) {
		$l->a("Started DataRetrieveHandler<br>");
		$status = 0;
		$insertion = retrieve($_REQUEST['id']);
		//print_r($insertion);
		$status = $insertion['status'];
		$csum = Csum_import($insertion['csum']);
		if(check($status,true)) {
			echo $csum->len.'|'.$csum->md5.'|'.$csum->sha.'|'.$csum->s512.'|'.$insertion['data'];
		}
	}
}

function CoalRetrieveHandler()
{
	global $l;
	$l = new llog;
	$l->a("Started CoalRetrieveHandler<br>");
	$status = 0;
	global $generalAuthKey;
	if(authorized($generalAuthKey)) {
		$coal = retrieveCoal($_REQUEST['coalId'],true);
		if(is_object($coal) || is_int($coal)) {
			$status = 20;
		}
		else {
			if(is_null($coal)) {
					$status = 7;
			}
		}
		if(!is_array($coal)) {
			$status = 45;
		}
		if(check($status,true)) {
			$filename = $coal['filename'];
			if(isset($_REQUEST['cs'])) {
				$filename = $filename . '.coalarc';
			}
			start_file_download($filename,strlen($coal['data']));
			if(isset($_REQUEST['cs'])) {
				echo $coal['md5'].'|'.$coal['sha'].'|'.$coal['s512'].'|';
			}
			echo $coal['data'];
		}
	}
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