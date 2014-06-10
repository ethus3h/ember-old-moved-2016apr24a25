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

function insertChunk($data,$csum) {
	$received_data_csum = new Csum($data);
	global $l;
	$l->a("Started insertChunk<br>");
	$status = 0;
	$id = null;
	if(!matches($csum,$received_data_csum)) {
		$status = 8;
	}
	else {
		$db = new FractureDB('futuqiur_coalchunks');
		$potentialDuplicates = $db->getColumnsUH('chunk2', 'id', 'md5', $csum->md5);
		foreach ($potentialDuplicates as $potential) {
			$potentialRecord = retrieveChunk($potential['id']);
			if(!is_null($potentialRecord)) {
				$potentialData = $potentialRecord['data'];
				$potentialCsum = Csum_import($potentialRecord['csum']);
				if(($potentialData === $data) && matches($csum,$potentialCsum)) {
					$duplicateId = $potential['id'];
					return array('id'=>$duplicateId,'status'=>$status);
				}
			}
		}
		global $compression;
		global $coalVersion;
		$details = array('csum'=>$csum->export(),'compression'=>$compression,'coalVersion'=>$coalVersion);
		$prepared_details = base64_encode(serialize($details));
		global $chunkMasterKey;
		$data = bzcompress($prepared_details.'@CoalFragmentMarker@'.$data);
		$prepared_data = mc_encrypt($data,$chunkMasterKey);
		if(mc_decrypt($prepared_data,$chunkMasterKey) != $data) {
			$status = 53;
		}
		$id = $db->addRow('chunk2', 'md5', 'UNHEX(\''.$csum->md5.'\')');
		$identifierId = $id / 1000;
		$randomInt = rand(0,10000);
		$randomIntAlt = rand(0,10000);
		$identifier = $identifierId.$randomInt.'.COALPROJECT.RECORD33';
		$fallbackid = $identifierId.$randomIntAlt.'.COALPROJECT.RECORD33';
		$address = 'ia:'.$identifier;
		$filename = $id.'.coal4';
		global $iaAuthKey;
		global $iaPrivateKey;
		$upload = @ia_upload($prepared_data,$identifier,$fallbackid,$filename,$iaAuthKey,$iaPrivateKey);
		if($upload != 0) {
			$status = 54;
			$db->dropRow('chunk2',$id);
		}
		else {
			$db->setField('chunk2','address',$address,$id);
		}
		$db->close();
 	}
	$l->a("Finished insertChunk with status ".$status.'<br>');
 	return array('id'=>$id,'status'=>$status);
}

function retrieveChunk($id)
{
	global $l;
	$l->a("Started retrieveChunk<br>");
	$status = 0;
	if(strlen($id) < 1) {
		$l->a('error 50<br>');
		$status = 50;
		$data = null;
		$csume = null;
		$details = null;
	}
	else {
		$db = new FractureDB('futuqiur_coalchunks');
		$info = $db->getRow('chunk2', 'id', $id);
		//print_r($info);
		if(isset($info[0])) {
			//Row is empty
			$status = 55;
			$data = null;
			$csume = null;
			$details = null;
		}
		else {
			$compiledLocation = $info['address'];
			$locationArray = explode_esc(':',$compiledLocation);
			$storage = $locationArray[0];
			$address = $locationArray[1];
			$storagePrefix = '';
			switch(trim($storage)) {
				case "ia":
					$storagePrefix = "http://archive.org/download/";
					break;
			}
			$location = $storagePrefix.$address.'/'.$id.'.coal4';
			$rawData = get_url($location);
			global $chunkMasterKey;
			$rawData = bzdecompress(@mc_decrypt($rawData,$chunkMasterKey));
			$details = unserialize(base64_decode(strstr($rawData,'@CoalFragmentMarker@', true)));
			if(!is_array($details)) {
				$status=51;
			}
			$data = substr(strstr($rawData,'@CoalFragmentMarker@'),20);
			$retr_csum = new Csum($data);
			$csum = Csum_import($details['csum']);
			if(!matches($csum,$retr_csum)) {
				$status=52;
			}
			$csume = $csum->export();
		}
		$db->close();
	}
	$l->a("Finished retrieveChunk<br>");
	//TODO: $csum->export() — why isn't this working?!
	return array('status'=>$status,'data'=>$data,'csum'=>$csume,'details'=>$details);
}

function retrieveCoal($id)
{
	global $l;
	$l->a("Started retrieveCoal<br>");
	$db = new FractureDB('futuqiur_coal');
	$coalInfo = $db->getRow('coal2', 'id', $id);
	$detailsChunkId = $coalInfo['chunk'];
	$coalmd5 = $coalInfo['md5'];
	$detailsChunk = retrieveChunk($detailsChunkId);
	$status = $detailsChunk['status'];
	$details = unserialize($detailsChunk['data']);
	if(!is_array($details)) {
		$l->a("Status 46<br>");
		$status=46;
	}
	$csum = Csum_import($details['csum']);
	$chunks = $details['chunks'];
	$chunks_csum = Csum_import($details['chunks_csum']);
	$retr_chunks_csum = new Csum($chunks);
	$l->a("retrieveCoalcheckpointA<br>");
	if(!matches($chunks_csum,$retr_chunks_csum)) {
		$status=47;
	}
	$l->a("retrieveCoalcheckpointB<br>");
	if(strlen($chunks)==0) {
		$chunk_array = array();
	}
	else {
		$chunk_array = explode_esc(',',$chunks);
	}
	$data = '';
	foreach($chunk_array as $chunk_id) {
		$l->a('<br>Retrieving chunk '.$chunk_id.'...<br>');
		$chunk_details = retrieveChunk($chunk_id);
		$chunk = $chunk_details['data'];
		//$l->a("DATA OUT A: ".$chunk);
		$chunk_csum = Csum_import($chunk_details['csum']);
		$retr_chunk_csum = new Csum($chunk);
		if(!matches($chunk_csum,$retr_chunk_csum)) {
			$status=48;
		}
		$data = $data.$chunk;
	}
	$data_csum = new Csum($data);
	//$l->a("DATA OUT FINAL: ".$data);
// 	$l->a(print_r($data_csum,true));
// 	$l->a(print_r($csum,true));
	if(!matches($csum,$data_csum)) {
		$status=49;
	}
	$db->close();
	$filename = $details['filename'];
	if(isset($details['ulfilename'])) {
		$filename = base64_decode($details['ulfilename']);
	}
	$l->a("Finished retrieveCoal<br>");
	//TODO: $csum->export() — why isn't this working?!
	return array('data'=>$data,'csum'=>$csum->export(),'filename'=>$filename,'status'=>$status);
}

function coalFromUpload() {
    global $l;
    $l->a("Started coalFromUpload<br>");
	$status = 0;
	if(isset($_FILES['uploadedfile'])) {
		$ulfilename = base64_encode($_FILES['uploadedfile']['name']);
		$ultype = base64_encode($_FILES['uploadedfile']['type']);
		$ulsize = base64_encode($_FILES['uploadedfile']['size']);
		$tmpname = base64_encode($_FILES['uploadedfile']['tmp_name']);
		$ferror = base64_encode($_FILES['uploadedfile']['error']);
	}
	else {
		$ulfilename = null;
		$ultype = null;
		$ulsize = null;
		$tmpname = null;
		$ferror = null;
	}
	$metadata = base64_encode(var_export($_FILES,true));
	$filename = "coal_temp/"."data.".guidv4().".cot";
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
	$coal_creation = coalFromFile($filename,$csum);
	$coal_creation['details']['ulfilename'] = $ulfilename;
	$coal_creation['details']['ultype'] = $ultype;
	$coal_creation['details']['ulsize'] = $ulsize;
	$coal_creation['details']['tmpname'] = $tmpname;
	$coal_creation['details']['error'] = $ferror;
	$coal_creation['details']['metadata'] = $metadata;
	$coal_creation['status'] = status_add($status,$coal_creation['status']);
	return $coal_creation;
}

function coalFromFile($filename,$csump) {
    global $l;
    $l->a("Started coalFromFile<br>");
    global $coalVersion;
	$status = 0;
	$type = null;
	$size = null;
	$stat = null;
	$smtime = null;
	$stats = null;
	$ctime = null;
	$mtime = null;
	$atime = null;
	if(file_exists($filename)) {
		$type = filetype($filename);
		$size = filesize($filename);
		$stat = stat( $filename );
		$smtime = base64_encode($stat['mtime']);
		$stats = bin2hex(var_export($stat,true));
		$ctime = base64_encode(filectime($filename));
		$mtime = base64_encode(filemtime($filename));
		$atime = base64_encode(fileatime($filename));
	}
	else {
		$status = 3;
	}
	$csum = new Csum(null,$filename);
	if(!($csump->matches($csum))) {
		$status = 57;
	}
	$chunks = '';
	//$l->a("DATA IN: ".file_get_contents($filename));
	$fhandle = fopen($filename,"r");
	while(ftell($fhandle) < $size) {
		$chunk = fread($fhandle,4194304);
		$c = new Csum($chunk);
		$chunkInfo = insertChunk($chunk,$c);
		$chunkId = $chunkInfo['id'];
		$status = status_add($status,$chunkInfo['status']);
		$cins = ',';
		if(strlen($chunks) == 0) {
			$cins = '';
		}
		$chunks = $chunks . $cins . $chunkId;
	}
	fclose($fhandle);
	$chunks_csum = new Csum($chunks);
	$details = array('csum'=>$csum->export(),'chunks'=>$chunks,'chunks_csum'=>$chunks_csum->export(),'filename'=>$filename,'type'=>$type,'size'=>$size,'smtime'=>$smtime,'stats'=>$stats,'ctime'=>$ctime,'mtime'=>$mtime,'atime'=>$atime);
	return array('filename'=>$filename,'details'=>$details,'status'=>$status);
}

function checkCoal($id) {
	global $l;
	sleep(3);
	$coal = retrieveCoal($id);
	$l->a('checkCoal returned status: '.$coal['status'].'.<br>');
	return check($coal['status']);
}

function insertCoal($file = null, $csump = null) {
	$db = new FractureDB('futuqiur_coal');
	global $l;
	$l->a("Started insertCoal<br>");
	$status = 0;
	$id = null;
	if(is_null($file)) {
		$coal = coalFromUpload();
		$details = $coal['details'];
	}
	else {
		$coal = coalFromFile($file,$csump);
		$details = $coal['details'];
	}
	$status=status_add($status,$coal['status']);
	global $coalCompressionType;
	$details['compression'] = $coalCompressionType;
	global $coalVersion;
	$details['coalVersion'] = $coalVersion;
	$detailsCsum = Csum_import($details['csum']);
	if($detailsCsum->len == 0) {
		$details['blocks'] = '';
	}
	$compressed = serialize($details);
	$c = new Csum($compressed);
	$chunkInfo = insertChunk($compressed,$c);
	$status=status_add($status,$chunkInfo['status']);
	if(check($status,true)) {
		$chunkId = $chunkInfo['id'];
		$id = $db->addRow('coal2', 'chunk, md5', '\''.$chunkId.'\', UNHEX(\''.$detailsCsum->md5.'\')');
		sleep(3);
		if(checkCoal($id)) {
			if(!is_null($file)) {
				unlink($res[0]);
			}
		}
		else {
			$status = 45;
		}
	}
	$db->close();
	return array('id'=>$id,'csum'=>$details['csum'],'status'=>$status);
}

// function coalQueue($file,$csump) {
// 	$csumr = new Csum($file);
// 	$csum = null;
// 	$id = null;
// 	$status = 0;
// 	if(!($csumr->matches($csump))) {
// 		$status = 56;
// 	}
// 	else {
// 		$csum = $csumr->export();
// 		$db = new FractureDB('futuqiur_coal');
// 		$id = $db->addRow('coal2', '', '');
// 		$db->addRow('scrub', 'file, csum', '\''.$file.'\', \''.$csum.'\'');
// 	}
// 	return array('id'=>$id,'csum'=>$csum,'status'=>$status);
// }
// 
// function coalQueuePush() {
// 	$status = 0;
// 	$row = $db->getNextRow('scrub');
// 	$res = insertCoal($row['file'],$row['csum']);
// 	$status = $status_add($status,$res['status']);
// 	return $status;
// }
?>