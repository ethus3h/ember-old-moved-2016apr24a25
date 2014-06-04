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
		$potentialDuplicates = $db->getColumns('chunk2', 'id', 'md5', $md5);
		$duplicateFound = false;
		foreach ($potentialDuplicates as $potential) {
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
				if($chcount < 10) {
					$l->a('Chunk insertion function status checkpoint 3a<br>');
					goto chunk;
				}
				else {
					$l->a('error 4');
					$icerror = 4;
				}
			}
			$encmd5 = amd5($ciphertext);
			$chcount = 0;
			chunk:
			global $coalVersion;
			$newChunkId = $db->addRow('coalchunks', 'length, lengthpre, parity, paritypre, md5, sha, crc, s512, compression, chunkcreatorversion', '\''.strlen($ciphertext).'\', \''.strlen($data).'\', \''.$encpar.'\', \''.$par.'\', \''.$encmd5.'\', \''.$encsha.'\', \''.$enccrc.'\', \''.$encs512.'\', \''.$compression.'\', \''.$coalVersion.'\'');
			$sccount = 0;
			$sc27try = 0;
			storechunk:
			$sc27try++;
			$sccount++;
			$identifierId = $newChunkId / 1000;
			$randomInt = rand(0,1000);
			$randomIntAlt = rand(0,1000);
			$identifier = $identifierId.$randomInt.'.COALPROJECT.RECORD33';
			$fallbackid = $identifierId.$randomIntAlt.'.COALPROJECT.RECORD33';
			$filename = $newChunkId.'.coal4';
			global $iaAuthKey;
			global $iaPrivateKey;
			$ulresult = ia_upload($ciphertext,$identifier,$fallbackid,$filename,$iaAuthKey,$iaPrivateKey,$title,$description,'texts',$keywords,true,'opensource');
			if(($ulresult == 35) && ($sc27try < 10)) {
				$l->a('information code 27');
				goto storechunk;
			}
			if(($ulresult != 0) && ($ulresult != 35) && ($sccount < 10)) {
				$l->a('information code 28');
				goto storechunk;
			}
			$db->setField('chunk2', 'address', 'ia:'.$identifier, $newChunkId);
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
	if(strlen($id) < 1) {
		$l->a('information code 32<br>');
	}
	else {
		$db               = new FractureDB('futuqiur_coalchunks');
		$rcerror = 0;
		$rccount = 0;
		$rcpcount = 0;
		retrievechunk:
		$chunkMetaRow = $db->getRow('chunk2', 'id', $id);
		$chunkAddressBlock = $chunkMetaRow['address'];
		$caExp = explode_escaped($chunkAddress,':');
		$chunkStorage = $caExp[0];
		$chunkAddress = $caExp[1];
		$chunkStoragePrefix = '';
		switch(trim($chunkStorage)) {
			case "ia":
				$chunkStoragePrefix = "http://archive.org/download/";
				break;
		}
		$chunkLocation = $chunkStoragePrefix.$chunkAddress.'/'.$id.'.coal';
		$chunkDataR = null;
		if(strlen($chunkLocation) > 0) {
 			$chunkDataR = get_url($chunkLocation);
		}
		else {
			$l->a('status 33<br>');
		}
		global $chunkMasterKey;
		$chunkDataR = mc_decrypt($chunkDataR,$chunkMasterKey);
		$chunkRmd5 = strtolower(bin2hex($chunkMetaRow['md5']));
		//help from http://stackoverflow.com/questions/4036036/php-substr-after-a-certain-char-a-substr-strpos-elegant-solution
		$chunkMeta = unserialize(base64_decode(bzdecompress(substr(strstr($chunkDataR,'@CoalFragmentMarker@', true),0,-20))));
		$chunkData = substr(strstr($chunkDataR,'@CoalFragmentMarker@'),20);
		$rmd5 = amd5($chunkDataR);
		$cklen = strlen($chunkData);
		$ckmd5 = amd5($chunkData);
		$cksha = sha($chunkData);
		$cks512 = s512($chunkData);
		$chlen = $chunkMeta->len;
		$chmd5 = $chunkMeta->md5;
		$chsha = $chunkMeta->sha;
		$chs512 = $chunkMeta->s512;
		if(($cklen != $chlen) || ($ckmd5 != $chmd5) || ($cksha != $chsha) || ($cks512 != $chs512) || ($chunkRmd5 != $rmd5)) {
			if($rccount < 10) {
				$l->a('<br>information code 29.<br>');
				$l->a('Retrieved md5 = '.$ckmd5.'; expected '.$chmd5.'.<br>');
				$l->a('Retrieved sha = '.$cksha.'; expected '.$chsha.'.<br>');
				$l->a('Retrieved len = '.$cklen.'; expected '.$chlen.'.<br>');
				$l->a('Retrieved s512 = '.$cks512.'; expected '.$chs512.'.<br>');
				$rccount++;
				goto retrievechunk;
			}
			else {
				$l->a('error 15<br>');
				$rcerror = 15;
			}
		}
		$db->close();
		return new cChunk ($chunkData,$cklen,$ckmd5,$cksha,$cks512);
	}
}
function retrieveCoal($id)
{
	global $l;
	$error = 0;
	$db = new FractureDB('futuqiur_coal');
	$rctries = 0;
	retrievec:
	$rccount = 0;
	$rcpcount = 0;
	retrievecoal:
	$coalInfo = $db->getRow('coal2', 'id', $id);
	$coalchunk = $coalMeta['chunk'];
	$coalmd5 = $coalMeta['md5'];
	$m = unserialize(bzdecompress(retrieveChunk($coalchunk)));
	$len = $m->len;
	$md5 = $m->md5;
	$sha = $m->sha;
	$s512 = $m->s512;
	$blocks = $m->blocks;
	$blockslen = $m->blockslen;
	$blocksmd5 = $m->blocksmd5;
	$blockssha = $m->blockssha;
	$blockss512 = $m->blockss512;
	$rblen = strlen($blocks);
	$rbmd5 = amd5($blocks);
	$rbsha = sha($blocks);
	$rbs512 = s512($blocks);
	if(($blockslen != $rblen) || ($blocksmd5 != $rbmd5) || ($blockssha != $rbsha) || ($blockss512 != $rbs512)) {
		$l->a('Retrieved coal failed blocklist checksum.<br>Retrieved len = '.$rblen.'; expected '.$cblen.'.<br>');
		$l->a('Retrieved par = '.$rbpar.'; expected '.$cbpar.'.<br>');
		$l->a('Retrieved sha = '.$rbsha.'; expected '.$cbsha.'.<br>');
		$l->a('Retrieved md5 = '.$rbmd5.'; expected '.$cbmd5.'.<br>');
		$l->a('Retrieved crc = '.$rbcrc.'; expected '.$cbcrc.'.<br>');
		$l->a('Retrieved s512 = '.$rbs512.'; expected '.$cbs512.'.<br>');
		if($rccount < 10) {
			$rccount++;
			$rcperror = 24;
			goto resetstatus;
		}
		else {
			$error = 17;
		}
	}
	if(strlen($coalBlockList)==0) {
		$blockListExploded = array();
	}
	else {
		$blockListExploded = explode_esc(',',$coalBlockList);
	}
	$dataToReturn = '';
	foreach($blockListExploded as $blockId) {
		requestblock:
		$blockData = retrieveChunk($blockId);
		$rbdata = $blockData->data;
		$rblen = $blockData->len;
		$rbmd5 = $blockData->md5;
		$rbsha = $blockData->sha;
		$rbs512 = $blockData->s512;
		$lblen = strlen($rbdata);
		$lbmd5 = amd5($rbdata);
		$lbsha = sha($rbdata);
		$lbs512 = s512($rbdata);
		if(($rblen != $lblen) || ($rbmd5 != $lbmd5) || ($rbsha != $lbsha) || ($rbs512 != $lbs512)) {
			if($rccount < 10) {
				$rccount++;
				//potential error
				$rcperror = 22;
				goto requestblock;
			}
			else {
				$rcerror = 18;
			}
		}
		$dcblockdata = bzdecompress($rbdata);
		$dataToReturn = $dataToReturn.$dcblockdata;
	}
	$clen = strlen($dataToReturn);
	$cmd5 = amd5($dataToReturn);
	$csha = sha($dataToReturn);
	$cs512 = s512($dataToReturn);
	if(($clen != $len) || ($cmd5 != $md5) || ($csha != $sha) || ($cs512 != $s512)) {
		if($rcpcount < 10) {
			$rcpcount++;
			$rcperror = 23;
			goto resetstatus;
		}
		else {
			$rcerror = 19;
		}
	}
	$db->close();
	return new cCoal ($dataToReturn,$clen,$cmd5,$csha,$cs512);
	resetstatus:
	$l->a('Coal retrieval function reached status checkpoint a<br>');
	$blocklist = '';
	$dataToReturn = '';
	$rctries++;
	if($rctries < 1) {
		$l->a('Coal retrieval function reached status checkpoint b<br>');
		goto retrievec;
	}
	else {
		$l->a('Coal retrieval function reached status checkpoint c<br>');
		return array(16, $rcerror, $rcperror);
	}
}

function coalFromUpload() {
    global $l;
	$error = 0;
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
	$target = "coal_temp/";
	$target = $target . "data.".guidv4().".cot";
	if(isset($_FILES['uploadedfile'])) {		
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target)) {
		} else {
			$error = 2;
			$l->a("error 2<br>");
		}
	}
	else {
		$error = 6;
		$l->a("error 6<br>");
	}
	$m = coalFromFile($target,false);
	$m->ulfilename = $ulfilename;
	$m->ultype = $ultype;
	$m->ulsize = $ulsize;
	$m->tmpname = $tmpname;
	$m->error = $ferror;
	$m->metadata = $metadata;
	return array($target,$m);
}

function coalFromFile($filename,$returnPath = true) {
    global $l;
    global $coalVersion;
	$error = 0;
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
		$error = 3;
	}
	$md5 = amd5f($filename);
	$sha = shaf($filename);
	$s512 = s512f($filename);
	$blocks = '';
	$fhandle = fopen($filename,"r");
	while(ftell($fhandle) < $size) {
		$chunk = fread($fhandle,4194304);
		$chcount = 0;
		chunk:
		$chcount++;
		$compression = "bzip2";
		$compressed = bzcompress($chunk);
		$rmd5 = amd5($compressed);
		$rsha = sha($compressed);
		$rs512 = s512($compressed);
		$ichunkcount = 0;
		ichunk:
		$ichunkcount++;
		$icRes = insertChunk($compressed,$rmd5,$rsha,$rs512,$compression);
		$newBlockId = $icRes[0];
		$l->a('<br>insertChunk returned status '.$icRes[1].'.<br>');
		if($icRes[1] != 0) {
			$l->a('<br>error 36: insertChunk returned non-zero status '.$icRes[1].'.<br>');
			$error = 36;
			if($ichunkcount < 10) {
				goto ichunk;
			}
			else {
				$error = 9;
			}
		}
		$bins = ',';
		if(strlen($blocks) == 0) {
			$bins = '';
		}
		$blocks = $blocks . $bins . $newBlockId;
	}
	fclose($fhandle);
	$blockslen = strlen($blocks);
	$blocksmd5 = amd5($blocks);
	$blockssha = sha($blocks);
	$blockss512 = s512($blocks);
	$m = new cCoalMeta($size,$md5,$sha,$s512,$blocks,$blockslen,$blocksmd5,$blockssha,$blockss512,null,$filename,$type,$size,null,null,null,null,null,$smtime,$stats,$ctime,$mtime,$atime,$compression,$coalVersion);
	if($returnPath) {
		return array($filename,$m);
	}
	else {
		return $m;
	}
}

function insertCoal($target = null) {
	$db = new FractureDB('futuqiur_coal');
	global $l;
	$coalcount = 0;
	coal:
	$coalcount++;
	if(is_null($target)) {
		$res = coalFromUpload();
		$coalTraits = $res[1];
	}
	else {
		$res = coalFromFile($target);
		$coalTraits = $res[1];
	}
	if($coalTraits->len == 0) {
		$coalTraits->blocks = '';
	}
	$ctEnc = bzcompress(serialize($coalTraits));
	$md5 = amd5($ctEnc);
	$sha = sha($ctEnc);
	$s512 = s512($ctEnc);
	$ichunkcount = 0;
	ichunk:
	$ichunkcount++;
	$icRes = insertChunk($ctEnc,$md5,$sha,$s512,$coalTraits->compression);
	$newBlockId = $icRes[0];
	$l->a('<br>insertChunk for metadata returned status '.$icRes[1].'.<br>');
	if($icRes[1] != 0) {
		$l->a('<br>error 36: insertChunk returned non-zero status '.$icRes[1].'.<br>');
		$error = 36;
		if($ichunkcount < 10) {
			goto ichunk;
		}
		else {
			$error = 9;
		}
	}
	$newCoalId = $db->addRow('coal2', 'chunk, md5', '\''.$newBlockId.'\', UNHEX(\''.$coalTraits->md5.'\')');
	sleep(3);
	$ccoalcount = 0;
	checkcoal:
	$ccoalcount++;
	$retrievedCoal = retrieveCoal($newCoalId);
	if(is_array($retrievedCoal) || is_int($retrievedCoal)) {
		if($ccoalcount < 10) {
			$l->a( 'information code 37');
			global $chunkUploadDirty;
			if($chunkUploadDirty) {
				sleep($ccoalcount*10);
			}
			goto checkcoal;
		}
	}
	else {
		if(!is_null($retrievedCoal)) {
			if(($retrievedCoal->len != $length) ||  ($retrievedCoal->par != $par) ||  ($retrievedCoal->md5 != $md5) || ($retrievedCoal->crc != $crc) || ($retrievedCoal->sha != $sha) || ($retrievedCoal->s512 != $s512)) {
				$blockList = '';
				if($ccoalcount < 10) {
					$l->a( 'information code 37');
					global $chunkUploadDirty;
					if($chunkUploadDirty) {
						sleep($ccoalcount*10);
					}
					goto checkcoal;
				}
			}
		}
		else {
			if($ccoalcount < 10) {
				$l->a( 'information code 37');
				global $chunkUploadDirty;
				if($chunkUploadDirty) {
					sleep($ccoalcount*10);
				}
				goto checkcoal;
			}
		}
	}
	if(is_array($retrievedCoal) || is_int($retrievedCoal)) {
		$error = 20;
	}
	else {
		if(!is_null($retrievedCoal)) {
			if(($retrievedCoal->len != $length) ||  ($retrievedCoal->par != $par) ||  ($retrievedCoal->md5 != $md5) || ($retrievedCoal->crc != $crc) || ($retrievedCoal->sha != $sha) || ($retrievedCoal->s512 != $s512)) {
				$blockList = '';
				if($coalcount < 10) {
					$l->a( 'information code 31');
					goto coal;
				}
				else {
					$error = 5;
				}
			}
			else {
				$l->a('Coal test retrieval was successful');
			}
		}
		else {
			$error = 7;
		}
	}
	unlink($res[0]);
	$db->close();
	if($error != 0) {
		header("HTTP/1.0 525 Request failed");
	}
	if(isset($_REQUEST['outputwebloc'])) {
		$filenamedec=base64_decode($coalTraits->filename);
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filenamedec".'.url');
		header("Content-Type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");
		$smallified='[InternetShortcut]
URL=http://futuramerlin.com/d/r/active.php?coalId='.$newCoalId.'&authorizationKey='.urlencode($generalAuthKey).'&handler=1&coalVerbose=1&handlerNeeded=CoalRetrieve
IconIndex=0';
		header('Content-Length: ' . strlen($smallified));
		echo $smallified;
	}
	else {
		if(isset($_REQUEST['coalVerbose'])) {
			echo '<br><br>Added coal: ';
		}
		echo $newCoalId.'|'.$coalTraits->len.'|'.$coalTraits->md5.'|'.$coalTraits->sha.'|'.$coalTraits->s512;
		if(isset($_REQUEST['coalVerbose'])) {
			echo '; used '.memory_get_peak_usage().' bytes of memory at peak; currently using '.memory_get_usage().' bytes of memory.';
			echo '<br><h1>Log output:</h1><br><small>';
			$l->e();
			echo '</small><br>Coal intake handler completed step 8<br>';
		}
	}
	return $error;
}
?>