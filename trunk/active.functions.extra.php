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
function insertChunk($data,$smd5,$ssha,$ss512,$compression) {
	global $l;
	$error = 0;
	$len = strlen($data);
	$md5 = amd5($data);
	$sha = sha($data);
	$s512 = s512($data);
	$newChunkId = 0;
	if(($smd5 != $md5) || ($ssha != $sha) || ($ss512 != $s512)) {
		$l->a('Chunk insertion function reached status checkpoint 1a<br>');
		$l->a('error 8');
		$error = 8;
	}
	else {
		$db = new FractureDB('futuqiur_coalchunks');
		$potentialDuplicates = $db->getColumnsUH('chunk2', 'id', 'md5', $md5);
		$duplicateFound = false;
		foreach ($potentialDuplicates as $potential) {
			$l->a('Checking potential duplicate record '.$potential['id'].'.');
			$potentialRecord = retrieveChunk($potential['id']);
			if(!is_null($potentialRecord)) {
				$potentialData = $potentialRecord->data;
				$potentiallen = $potentialRecord->len;
				$potentialmd5 = $potentialRecord->md5;
				$potentialsha = $potentialRecord->sha;
				$potentials512 = $potentialRecord->s512;
				$l->a('Provided data md5 = '.$md5.'; potential '.$potentialmd5.'.<br>');
				$l->a('Provided data len = '.$len.'; potential '.$potentiallen.'.<br>');
				$l->a('Provided data sha = '.$sha.'; potential '.$potentialsha.'.<br>');
				$l->a('Provided data s512 = '.$s512.'; potential '.$potentials512.'.<br>');
				if(($potentialData === $data) && ($potentiallen == $len) && ($potentialmd5 == $md5) && ($potentialsha == $sha) && ($potentials512 == $s512)) {
					$duplicateFound = true;
					$duplicateId = $potential['id'];
					$l->a('information code 25<br>');
					$l->a('Chunk insertion function reached status checkpoint 5a<br>');
					goto duplicatefound;
				}
			}
		}
 		duplicatefound:
 		if($duplicateFound) {
			$l->a('information code 26: duplicate found<br>');
			$newChunkId = $duplicateId;
 			goto finished;
 		}
 		else {
 			global $coalVersion;
 			$md = new cChunkMeta($len,$md5,$sha,$s512,$compression,$coalVersion);
 			$mdt = base64_encode(bzcompress(serialize($md)));
			global $chunkMasterKey;
			$ciphertext = mc_encrypt($mdt.'@CoalFragmentMarker@'.$data,$chunkMasterKey);
			if(mc_decrypt($ciphertext,$chunkMasterKey) != $mdt.'@CoalFragmentMarker@'.$data) {
				if($chcount < 1) {
					$l->a('Chunk insertion function status checkpoint 3a<br>');
					goto chunk;
				}
				else {
					$l->a('error 4<br>');
					$icerror = 4;
				}
			}
			//$encmd5 = amd5($ciphertext);
			$chcount = 0;
			chunk:
			global $coalVersion;
			$sccount = 0;
			$sc27try = 0;
			storechunk:
			$sc27try++;
			$sccount++;
			$identifierId = $newChunkId / 1000;
			$randomInt = rand(0,1000);
			$randomIntAlt = rand(0,1000);
			$identifier = $identifierId.$randomInt.'.COALPROJECT.RECORD33';
			$address = 'ia:'.$identifier;
			$fallbackid = $identifierId.$randomIntAlt.'.COALPROJECT.RECORD33';
			//print_r($db->getNextId('chunk2'));
			$nextId = $db->getNextId('chunk2');
			$nextIdFixedA = $nextId[0]['Auto_increment'];
 			//$nextIdFixed = $nextIdFixedA+1;
 			$filename = $nextIdFixedA.'.coal4';
// 			echo $nextIdFixedA;
// 			echo 'DOOOM';
// 			echo $identifier.'/'.$filename;
// 			echo 'DOOOOOM';
			global $iaAuthKey;
			global $iaPrivateKey;
			//echo 'Ciphertext added: '.md5($ciphertext);
			$ulresult = @ia_upload($ciphertext,$identifier,$fallbackid,$filename,$iaAuthKey,$iaPrivateKey,null,null,'texts',null,true,'opensource');
			// if(($ulresult == 35) && ($sc27try < 10)) {
// 				$l->a('information code 27');
// 				goto storechunk;
// 			}
// 			if(($ulresult != 0) && ($ulresult != 35) && ($sccount < 10)) {
// 				$l->a('information code 28');
// 				goto storechunk;
// 			}
			$l->a('Upload result: '.$ulresult.'.<br><br>');
			if($ulresult != 0) {
				if($sccount < 2) {
					$l->a('information code 28<br>');
					sleep(10);
					goto storechunk;
				}
				else {
					$l->a('error 44<br>');
					$error = 44;
				}
			}
			$newChunkId = $db->addRow('chunk2', 'id, address, md5', '\''.$nextIdFixedA.'\', \''.$address.'\', UNHEX(\''.$md5.'\')');
// 			//$db->setField('coalchunks', 'altstorage', 'none', $newChunkId);
// 			//$db->setField('coalchunks', 'altaddress', 'none', $newChunkId);
 			goto finished;
		}
		$db->close();
 	}
	finished:
	$l->a('Chunk insertion function reached status checkpoint a<br>');
	if($error != 0) {
		$l->a('Chunk insertion function reached status checkpoint b<br>');
		//header("HTTP/1.0 525 Request failed");
	}
	$l->a('Chunk insertion function returning new chunk ID '.$newChunkId.' and error code '.$error.'.<br>');
	return array($newChunkId, $error);
	$l->a('Chunk insertion function reached status checkpoint c<br>');
}

function retrieveChunk($id)
{
	global $l;
	$status = 0;
	if(strlen($id) < 1) {
		$l->a('error 50<br>');
		$status = 50;
	}
	else {
		$db = new FractureDB('futuqiur_coalchunks');
		$info = $db->getRow('chunk2', 'id', $id);
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
		$rawData = @mc_decrypt($rawData,$chunkMasterKey);
		$details = unserialize(bzdecompress(base64_decode(strstr($rawData,'@CoalFragmentMarker@', true))));
		if(!is_array($details)) {
			$status=51;
		}
		$data = substr(strstr($rawData,'@CoalFragmentMarker@'),20);
		$retr_csum = new Csum($chunkData);
		$csum = $details['csum'];
		if(!matches($csum,$retr_csum) {
			$status=52;
		}
		$db->close();
	}
	return array('status'=>$status,'data'=>$data,'csum'=>$csum,'details'=>$details);
}

function retrieveCoal($id)
{
	global $l;
	$db = new FractureDB('futuqiur_coal');
	$coalInfo = $db->getRow('coal2', 'id', $id);
	$detailsChunkId = $coalInfo['chunk'];
	$coalmd5 = $coalInfo['md5'];
	$detailsChunk = retrieveChunk($metaChunk);
	$status = $detailsChunk['status'];
	$details = unserialize(bzdecompress($detailsChunk['data']));
	if(!is_array($details)) {
		$status=46;
	}
	$csum = $details['csum'];
	$chunks = $details['chunks'];
	$chunks_csum = Csum_import($details['chunks_csum']);
	$retr_chunks_csum = new Csum($chunks);
	if(!matches($chunks_csum,$retr_chunks_csum) {
		$status=47;
	}
	if(strlen($chunks)==0) {
		$chunk_array = array();
	}
	else {
		$chunk_array = explode_esc(',',$chunks);
	}
	$data = '';
	foreach($chunk_array as $chunk_id) {
		$chunk_details = retrieveChunk($blockId);
		$chunk = $chunk_details['data'];
		$chunk_csum = Csum_import($chunk_details['csum']);
		$retr_chunk_csum = new Csum($chunk);
		if(!matches($chunk_csum,$retr_chunk_csum) {
			$status=48;
		}
		$chunk_data = bzdecompress($chunk);
		$data = $data.$chunk_data;
	}
	$data_csum = new Csum($data);
	if(!matches($csum,$data_csum)) {
		$status=49;
	}
	$db->close();
	$filename = $details['filename'];
	if(isset($details['ulfilename'])) {
		$filename = base64_decode($details['ulfilename']);
	}
	return array('data'=>$data,'csum'=>$csum,'filename'=>$filename,'status'=>$status);
}

function coalFromUpload() {
    global $l;
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
	$coal_creation = coalFromFile($filename);
	$coal_creation['details']['ulfilename'] = $ulfilename;
	$coal_creation['details']['ultype'] = $ultype;
	$coal_creation['details']['ulsize'] = $ulsize;
	$coal_creation['details']['tmpname'] = $tmpname;
	$coal_creation['details']['error'] = $ferror;
	$coal_creation['details']['metadata'] = $metadata;
	$coal_creation['status'] = status_add($status,$coal_creation['status']);
	return $coal_creation;
}

function coalFromFile($filename) {
    global $l;
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
	$md5 = amd5f($filename);
	$sha = shaf($filename);
	$s512 = s512f($filename);
	$csum = new Csum();
	$csum->len=$size;
	$csum->md5=$md5;
	$csum->sha=$sha;
	$csum->s512=$s512;
	$chunks = '';
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
	$details = new array('csum'=>$csum->export(),'chunks'=>$chunks,'chunks_csum'=>$chunks_csum->export(),'filename'=>$filename,'type'=>$type,'size'=>$size,'smtime'=>$smtime,'stats'=>$stats,'ctime'=>$ctime,'mtime'=>$mtime,'atime'=>$atime);
	return array('filename'=>$filename,'details'=>$details,'status'=>$status);
}

function insertCoal($file = null) {
	$db = new FractureDB('futuqiur_coal');
	global $l;
	$status = 0;
	if(is_null($file)) {
		$coal = coalFromUpload();
		$details = $coal['details'];
	}
	else {
		$coal = coalFromFile($file);
		$details = $coal['details'];
	}
	$status=status_add($status,$coal['status']);
	global $coalCompressionType;
	$details['compression'] = $coalCompressionType;
	global $coalVersion;
	$details['coalVersion'] = $coalVersion;
	if($details['len'] == 0) {
		$details['blocks'] = '';
	}
	$compressed = bzcompress(serialize($details));
	$c = new Csum($compressed);
	$chunkInfo = insertChunk($compressed,$c);
	$chunkId = $chunkInfo[0];
	$id = $db->addRow('coal2', 'chunk, md5', '\''.$chunkId.'\', UNHEX(\''.$details['md5'].'\')');
	sleep(3);
	if(checkCoal($id)) {
		if(!is_null($file)) {
			unlink($res[0]);
		}
	}
	else {
		$status = 45
	}
	$db->close();
	return array('id'=>$id,'details'=>$details,'status'=>$status);
}
?>