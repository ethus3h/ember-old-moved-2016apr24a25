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
	$par = crc($data);
	$md5 = amd5($data);
	$crc = crc($data);
	$sha = sha($data);
	$s512 = s512($data);
	$newChunkId = 0;
	if(($spar != $par) || ($smd5 != $md5) || ($scrc != $crc) || ($ssha != $sha) || ($ss512 != $s512)) {
		$icerror = 8;
	}
	else {
		$db               = new FractureDB('futuqiur_coalchunks');
		//Retrieve potential duplicates
		$potentialDuplicates = $db->getColumns('coalchunks', 'id', 's512', $s512);
		//Check potentials for duplicate
		$duplicateFound = false;
		//encrypt record
		global $chunkPrivateKey;
		global $chunkPublicKey;
		$rsa = new Crypt_RSA();
		$rsa->loadKey($chunkPublicKey); // public key
		$plaintext = $data;
		$ciphertext = $rsa->encrypt($plaintext);
		$rsa->loadKey($chunkPrivateKey); // private key
		if($rsa->decrypt($ciphertext) != $plaintext) {
			if($chcount < 10) {
				goto chunk;
			}
			else {
				$icerror = 4;
			}
		}
		$enclen = strlen($ciphertext);
		$encpar = par($ciphertext);
		$encmd5 = amd5($ciphertext);
		$enccrc = crc($ciphertext);
		$encsha = sha($ciphertext);
		$encs512 = s512($ciphertext);
		foreach ($potentialDuplicates as $potential) {
			//Request potential from storage
			$potentialRecord = retrieveChunk($potential['id']);
			$potentialData = $potentialRecord->data;
			$potentiallen = $potentialRecord->len;
			$potentialpar = $potentialRecord->par;
			$potentialmd5 = $potentialRecord->md5;
			$potentialcrc = $potentialRecord->crc;
			$potentialsha = $potentialRecord->sha;
			$potentials512 = $potentialRecord->s512;
			if(($potentialData === $data) && ($potentiallen == $enclen) && ($potentialpar == $encpar) && ($potentialmd5 == $encmd5) && ($potentialcrc == $enccrc) && ($potentialsha == $encsha) && ($potentials512 == $encs512)) {
				$duplicateFound = true;
				$duplicateId = $potential['id'];
				goto duplicatefound;
			}
		}
		duplicatefound:
		if($duplicateFound) {
			$newChunkId = $duplicateId;
			goto finished;
		}
		else {
			$chcount = 0;
			chunk:
			$chcount++;
			//Add record w/ ID, parity checksum
			$newChunkId = $db->addRow('coalchunks', 'length, lengthpre, parity, paritypre, md5, sha, crc, s512, compression', '\''.strlen($ciphertext).'\', \''.strlen($data).'\', \''.$encpar.'\', \''.$par.'\', \''.$encmd5.'\', \''.$encsha.'\', \''.$enccrc.'\', \''.$encs512.'\', \''.$compression.'\'');
			//store chunk
			$sccount = 0;
			storechunk:
			$sccount++;
			$ulresult = ia_upload($ciphertext,$identifier,$filename,$accesskey,$secretkey,$title,$description,'texts',$keywords,true,'opensource');
			if($ulresult = 10) {
				goto storechunk;
			}
			if(($ulresult != 0) && ($ulresult != 10) && ($sccount < 10)) {
				goto storechunk;
			}
			$db->setField('coalchunks', 'storage', 'ia', $newChunkId);
			$db->setField('coalchunks', 'address', $identifier.'/'.$filename, $newChunkId);
			//$db->setField('coalchunks', 'altstorage', 'none', $newChunkId);
			//$db->setField('coalchunks', 'altaddress', 'none', $newChunkId);
			goto finished;
		}
		$db->close();
	}
	finished:
	if($icerror != 0) {
		//header("HTTP/1.0 525 Request failed");
	}
	return array($newChunkId, $icerror);
}
function retrieveChunk($id)
{
	$db               = new FractureDB('futuqiur_coalchunks');
	$rcerror = 0;
	$rccount = 0;
	$rcpcount = 0;
	retrievechunk:
	//Get chunk address from database by ID
	echo 'Getting metadata for chunk '.$id;
	$chunkMeta = $db->getRow('coalchunks', 'id', $id);
	//print_r($chunkMeta);
	$chunkStorage = $chunkMeta['storage'];
	$chunkAddress = $chunkMeta['address'];
	$chunkStoragePrefix = '';
	switch(trim($chunkStorage)) {
		case "ia":
			$chunkStoragePrefix = "http://archive.org/download/";
			break;
	}
	$chunkLocation = $chunkStoragePrefix+$chunkAddress;
	//download chunk
	$chunkData = get_url($chunkLocation);
	//check retrieved chunk checksums
	$cklen = strlen($chunkData);
	$ckpar = par($chunkData);
	$ckmd5 = amd5($chunkData);
	$ckcrc = crc($chunkData);
	$cksha = sha($chunkData);
	$cks512 = s512($chunkData);
	$chlen = $chunkMeta['length'];
	$chpar = $chunkMeta['parity'];
	$chmd5 = $chunkMeta['md5'];
	$chsha = $chunkMeta['sha'];
	$chcrc = $chunkMeta['crc'];
	$chs512 = $chunkMeta['s512'];
	if(($cklen != $chlen) || ($ckpar != $chpar) || ($ckmd5 != $chmd5) || ($cksha != $chsha) || ($ckcrc != $chcrc) || ($cks512 != $chs512)) {
		if($rccount < 10) {
			$rccount++;
			goto retrievechunk;
		}
		else {
			$rcerror = 15;
		}
	}
	//Decrypt chunk using chunk key
	global $chunkPrivateKey;
	$rsa = new Crypt_RSA();
	$rsa->loadKey($chunkPrivateKey); // private key
	$ciphertext = $chunkData;
	$plaintext = $rsa->decrypt($ciphertext);
	//Check decrypted chunk against parity checksum from database
	$ptlen = strlen($plaintext);
	$ptpar = par($plaintext);
	$ptmd5 = amd5($plaintext);
	$ptcrc = crc($plaintext);
	$ptsha = sha($plaintext);
	$pts512 = s512($plaintext);
	$plen = $chunkMeta['length'];
	$ppar = $chunkMeta['parity'];
	if(($plen != $ptlen) || ($ppar != $ptcrc)) {
		if($rcpcount < 10) {
			$rcpcount++;
			goto retrievechunk;
		}
		else {
			$rcerror = 14;
		}
	}
	$db->close();
	//return data and checksums
	return new cChunk ($plaintext,$ptlen,$ptpar,$ptmd5,$ptcrc,$ptsha,$pts512);
}
function retrieveCoal($id)
{
	$db               = new FractureDB('futuqiur_coal');
	$rctries = 0;
	retrievec:
	$rcerror = 0;
	$rccount = 0;
	$rcpcount = 0;
	retrievecoal:
	//Get chunk address from database by ID
	$coalMeta = $db->getRow('coal', 'id', $id);
	$recordpar = $coalMeta['parity'];
	$recordlen = $coalMeta['length'];
	$coalBlockList = $coalMeta['blocks'];
	$cblen = $coalMeta['blockslen'];	
	$cbpar = trim($coalMeta['blockspar']);
	$cbmd5 = $coalMeta['blocksmd5'];
	$cbsha = $coalMeta['blockssha'];
	$cbcrc = $coalMeta['blockscrc'];
	$cbs512 = $coalMeta['blocks512'];
	$rblen = strlen($coalBlockList);
	$rbpar = par($coalBlockList);
	$rbmd5 = amd5($coalBlockList);
	$rbsha = sha($coalBlockList);
	$rbcrc = crc($coalBlockList);
	$rbs512 = s512($coalBlockList);
//  echo $cblen;
// 	echo '<br>';
// 	echo $rblen;
// 	echo '<br>';
// 	echo $cbpar;
// 	echo '<br>';
// 	echo $rbpar;
// 	echo '<br>';
// 	echo $cbmd5;
// 	echo '<br>';
// 	echo $rbmd5;
// 	echo '<br>';
// 	echo $cbcrc;
// 	echo '<br>';
// 	echo $rbcrc;
// 	echo '<br>';
// 	echo $cbsha;
// 	echo '<br>';
// 	echo $rbsha;
// 	echo '<br>';
// 	echo $cbs512;
// 	echo '<br>';
// 	echo $rbs512;
// 	echo '<br>';
	if(($cblen != $rblen) || ($cbpar != $rbpar) || ($cbmd5 != $rbmd5) || ($cbsha != $rbsha) || ($cbcrc != $rbcrc) || ($cbs512 != $rbs512)) {
		if($rccount < 10) {
			$rccount++;
			$rcperror = 24;
			goto resetstatus;
		}
		else {
			$rcerror = 17;
		}
	}
	$blockListExploded = explode_esc(',',$coalBlockList);
	$dataToReturn = '';
	foreach($blockListExploded as $blockId) {
		requestblock:
		//Request block
		$blockData = retrieveChunk($blockId);
		//Check returned block data against returned block checksums
		$rbdata = $blockData->data;
		$rblen = $blockData->len;
		$rbpar = $blockData->par;
		$rbmd5 = $blockData->md5;
		$rbsha = $blockData->sha;
		$rbcrc = $blockData->crc;
		$rbs512 = $blockData->s512;
		$lblen = strlen($rbdata);
		$lbpar = par($rbdata);
		$lbmd5 = amd5($rbdata);
		$lbsha = sha($rbdata);
		$lbcrc = crc($rbdata);
		$lbs512 = s512($rbdata);
		if(($rblen != $lblen) || ($rbpar != $lbpar) || ($rbmd5 != $lbmd5) || ($rbsha != $lbsha) || ($rbcrc != $lbcrc) || ($rbs512 != $lbs512)) {
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
		//Decrypt block data using record key
		global $coalPrivateKey;
		$rsa = new Crypt_RSA();
		$rsa->loadKey($coalPrivateKey); // private key
		$ciphertext = $rbdata;
		$plaintext = $rsa->decrypt($ciphertext);
		//Decompress block data
		$dcblockdata = bzdecompress($plaintext);
		//Append block data to record data to return
		$dataToReturn = $dataToReturn.$dcblockdata;
	}
	//Check compiled record data against parity checksum
	$clen = strlen($dataToReturn);
	$cpar = par($dataToReturn);
	$cmd5 = amd5($dataToReturn);
	$csha = sha($dataToReturn);
	$ccrc = crc($dataToReturn);
	$cs512 = s512($dataToReturn);
	if(($cpar != $recordpar) || ($clen != $recordlen)) {
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
	//return data and checksums
	return new cCoal ($dataToReturn,$clen,$cpar,$cmd5,$ccrc,$csha,$cs512);
	resetstatus:
	$blocklist = '';
	$dataToReturn = '';
	$rctries++;
	if($rctries < 10) {
		goto retrievec;
	}
	else {
		//Chunk retrieval failed too many times
		//$rcerror = 16;
		//echo 'Chunk retrieval failed: 
		return array(16, $rcerror, $rcperror);
	}
}
?>