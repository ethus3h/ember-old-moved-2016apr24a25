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
	global $l;
	$l->a('Chunk insertion function begun step 1<br>');
	$chcount = 0;
	//$l->a('NEW CHUNK DATA: '.$data);
	$icerror = 0;
	$rpar = par($data);
	$par = crc($data);
	$md5 = amd5($data);
	$crc = crc($data);
	$sha = sha($data);
	$len = strlen($data);
	$s512 = s512($data);
	$l->a('<br><br>Chunk insertion function completed step 1; calculated SHA512 hash as '.$s512.'.<br>');
	$newChunkId = 0;
	if(($spar != $rpar) || ($smd5 != $md5) || ($scrc != $crc) || ($ssha != $sha) || ($ss512 != $s512)) {
		$l->a('Chunk insertion function reached status checkpoint 1a<br>');
		$l->a('error 8');
		$icerror = 8;
	}
	else {
		$l->a('Chunk insertion function begun step 2<br>');
		$db               = new FractureDB('futuqiur_coalchunks');
		//Retrieve potential duplicates
		$potentialDuplicates = $db->getColumns('coalchunks', 'id', 'paritypre', $crc);
		//help from http://stackoverflow.com/questions/9325067/store-print-r-result-into-a-variable-as-a-string-or-text
		$l->a('<br>Potential duplicates: '.print_r($potentialDuplicates,true).'<br>');		
		$l->a('Chunk insertion function completed step 2<br>');
		//Check potentials for duplicate
		$duplicateFound = false;
		//encrypt record
		global $chunkPrivateKey;
		global $chunkPublicKey;
		//help from http://www.php.net/manual/en/function.openssl-pkey-get-public.php
		$key_private = openssl_get_privatekey($chunkPrivateKey);
		$key_public = openssl_get_privatekey($chunkPublicKey);
		//help from http://www.php.net/manual/en/function.openssl-public-encrypt.php
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key_public,$data,MCRYPT_MODE_CBC,$iv);
		$l->a('Chunk insertion function completed step 3<br>');
		//help from http://www.php.net/manual/en/function.openssl-private-decrypt.php
		$cdecrypted = '';
		openssl_private_decrypt($ciphertext,$cdecrypted,$key_private);
		$l->a('Length of encrypted data: '.strlen($ciphertext).'; length of decrypted data: '.strlen($cdecrypted).'; length of original data: '.strlen($data).'.<br>');
		if($cdecrypted != $data) {
			if($chcount < 10) {
				$l->a('Chunk insertion function status checkpoint 3a<br>');
				goto chunk;
			}
			else {
				$l->a('error 4');
				$icerror = 4;
			}
		}
		$l->a('Chunk insertion function completed step 4<br>');
		$enclen = strlen($ciphertext);
		$encpar = par($ciphertext);
		$encmd5 = amd5($ciphertext);
		$enccrc = crc($ciphertext);
		$encsha = sha($ciphertext);
		$encs512 = s512($ciphertext);
		foreach ($potentialDuplicates as $potential) {
			$l->a('<br>Chunk insertion function begun step 5; requesting chunk '.$potential['id'].'.<br>');
			//Request potential from storage
			//help from http://stackoverflow.com/questions/4207343/how-to-get-time-in-php-with-nanosecond-precision
// 			$btime = microtime(true);
// 			echo '<br>Begun chunk retrieval at '.$btime.'.<br>';
			$s=st();
			$potentialRecord = retrieveChunk($potential['id']);
// 			$etime = microtime(true);
// 			$tduration = $etime - $btime;
// 			echo '<br>Finished chunk retrieval at '.$etime.'; took '.$tduration.' seconds.<br>';
			et($s);
			$potentialData = $potentialRecord->data;
			$potentiallen = $potentialRecord->len;
			$potentialpar = $potentialRecord->par;
			$potentialmd5 = $potentialRecord->md5;
			$potentialcrc = $potentialRecord->crc;
			$potentialsha = $potentialRecord->sha;
			$potentials512 = $potentialRecord->s512;
			//$l->a('Provided data = '.$data.'; potential '.$potentialData.'.<br><br>');
			$l->a('Provided data md5 = '.$md5.'; potential '.$potentialmd5.'.<br>');
			$l->a('Provided data len = '.$len.'; potential '.$potentiallen.'.<br>');
			$l->a('Provided data par = '.$rpar.'; potential '.$potentialpar.'.<br>');
			$l->a('Provided data sha = '.$sha.'; potential '.$potentialsha.'.<br>');
			$l->a('Provided data crc = '.$crc.'; potential '.$potentialcrc.'.<br>');
			$l->a('Provided data s512 = '.$s512.'; potential '.$potentials512.'.<br>');
			if(($potentialData === $data) && ($potentiallen == $len) && ($potentialpar == $rpar) && ($potentialmd5 == $md5) && ($potentialcrc == $crc) && ($potentialsha == $sha) && ($potentials512 == $s512)) {
				$duplicateFound = true;
				$duplicateId = $potential['id'];
				$l->a('information code 25');
				$l->a('Chunk insertion function reached status checkpoint 5a<br>');
				goto duplicatefound;
			}
			$l->a('Chunk insertion function completed step 5<br>');
		}
 		duplicatefound:
 		$l->a('Chunk insertion function begun step 6<br>');
 		if($duplicateFound) {
			$l->a('information code 26: duplicate found<br>');
			$newChunkId = $duplicateId;
			$l->a('Chunk insertion function reached status checkpoint 6a<br>');
 			goto finished;
 		}
 		else {
			$l->a('Chunk insertion function begun step 7<br>');
			$chcount = 0;
			chunk:
			$l->a('Chunk insertion function reached status checkpoint 7a<br>');
			$chcount++;
			//Add record w/ ID, parity checksum
			$l->a('Chunk insertion function completed step 7<br>');
			$newChunkId = $db->addRow('coalchunks', 'length, lengthpre, parity, paritypre, md5, sha, crc, s512, compression', '\''.strlen($ciphertext).'\', \''.strlen($data).'\', \''.$encpar.'\', \''.$par.'\', \''.$encmd5.'\', \''.$encsha.'\', \''.$enccrc.'\', \''.$encs512.'\', \''.$compression.'\'');
			$l->a('Chunk insertion function completed step 8<br>');
			//store chunk
			$sccount = 0;
			$sc27try = 0;
			storechunk:
			$l->a('<br>Chunk insertion function reached status checkpoint 8a<br>');
			$sc27try++;
			$sccount++;
			$identifierId = $newChunkId / 1000;
			$randomInt = rand(0,1000);
			$randomIntAlt = rand(0,1000);
			//"RECORD33" has no meaning, but it sounds kind of cool and hopefully helps avoid collisions (not that it's needed, really... :P)
			$identifier = $identifierId.$randomInt.'.COALPROJECT.RECORD33';
			$fallbackid = $identifierId.$randomIntAlt;
// 			$identifier = $identifierId.guidv4();
			//$fallbackid = $identifierId.guidv4();
			$filename = $newChunkId.'.coal';
			global $iaAuthKey;
			global $iaPrivateKey;
			$accesskey = $iaAuthKey;
			$secretkey = $iaPrivateKey;
			$title = 'Coal chunks for '.$identifierId;
			$description = $title;
			$keywords = 'coal; data; coal chunks; ';
			$l->a('Chunk insertion function completed step 8<br>');
			$ulresult = ia_upload($ciphertext,$identifier,$fallbackid,$filename,$accesskey,$secretkey,$title,$description,'texts',$keywords,true,'opensource');
			$l->a('Chunk insertion function completed step 9; upload result code '.$ulresult.'<br>');
			if(($ulresult == 35) && ($sc27try < 10)) {
				$l->a('information code 27');
				goto storechunk;
			}
			if(($ulresult != 0) && ($ulresult != 35) && ($sccount < 10)) {
				$l->a('information code 28');
				goto storechunk;
			}
			$l->a('Chunk insertion function completed step 10<br>');
			$db->setField('coalchunks', 'storage', 'ia', $newChunkId);
			$db->setField('coalchunks', 'address', $identifier.'/'.$filename, $newChunkId);
			$l->a('Chunk insertion function completed step 11<br>');
			$l->a('Chunk insertion function completed step 6<br>');
// 			//$db->setField('coalchunks', 'altstorage', 'none', $newChunkId);
// 			//$db->setField('coalchunks', 'altaddress', 'none', $newChunkId);
 			goto finished;
		}
		$db->close();
 	}
	finished:
	$l->a('Chunk insertion function reached status checkpoint a<br>');
	if($icerror != 0) {
		$l->a('Chunk insertion function reached status checkpoint b<br>');
		//header("HTTP/1.0 525 Request failed");
	}
	$l->a('Chunk insertion function returning new chunk ID '.$newChunkId.' and error code '.$icerror.'.<br>');
	return array($newChunkId, $icerror);
	$l->a('Chunk insertion function reached status checkpoint c<br>');
}
function retrieveChunk($id)
{
	global $l;
	if(strlen($id) < 1) {
		$l->a('information code 32<br>');
	}
	else {
		$l->a('Chunk retrieval function begun; retrieving chunk '.$id.'.<br>');
		$db               = new FractureDB('futuqiur_coalchunks');
		$rcerror = 0;
		$rccount = 0;
		$rcpcount = 0;
		$l->a('Chunk retrieval function completed step 1<br>');
		retrievechunk:
		$l->a('<br>Chunk retrieval function begun step 1b<br>');
		//Get chunk address from database by ID
		$l->a('<br>Getting metadata for chunk '.$id);
		$chunkMeta = $db->getRow('coalchunks', 'id', $id);
		//print_r($chunkMeta);
		$l->a('<br>Chunk retrieval function completed step 2<br>');
		$chunkStorage = $chunkMeta['storage'];
		$chunkAddress = $chunkMeta['address'];
		$chunkStoragePrefix = '';
		switch(trim($chunkStorage)) {
			case "ia":
				$chunkStoragePrefix = "http://archive.org/download/";
				break;
		}
		$chunkLocation = $chunkStoragePrefix.$chunkAddress;
		//download chunk
		if(strlen($chunkLocation) > 0) {
 			$chunkData = get_url($chunkLocation);
		}
		else {
			$l->a('status 33<br>');
		}
 		$l->a('Chunk retrieval function completed step 3<br>');
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
				echo '<br>information code 29.<br>';
				echo '<br>Retrieved data: '.$chunkData.'<br><br>';				
				echo 'Retrieved md5 = '.$ckmd5.'; expected '.$chmd5.'.<br>';
				echo 'Retrieved par = '.$ckpar.'; expected '.$chpar.'.<br>';
				echo 'Retrieved sha = '.$cksha.'; expected '.$chsha.'.<br>';
				echo 'Retrieved len = '.$cklen.'; expected '.$chlen.'.<br>';
				echo 'Retrieved crc = '.$ckcrc.'; expected '.$chcrc.'.<br>';
				echo 'Retrieved s512 = '.$cks512.'; expected '.$chs512.'.<br>';
				$rccount++;
				goto retrievechunk;
			}
			else {
				echo 'error 15';
				$rcerror = 15;
			}
		}
		$l->a('<br>Chunk retrieval function completed step 4<br>');
		//Decrypt chunk using chunk key
		global $chunkPrivateKey;
		$key_private = openssl_get_privatekey($chunkPrivateKey);
		//help from http://www.php.net/manual/en/function.class-exists.php
// 		if(!class_exists('Crypt_RSA')) {
// 			include('Crypt/RSA.php');
// 		}
		// $rsa = new Crypt_RSA();
// 		$rsa->loadKey($chunkPrivateKey); // private key
// 		$ciphertext = $chunkData;
		$plaintext = '';
		openssl_private_decrypt($chunkData,$plaintext,$key_private);
		//$plaintext = $rsa->decrypt($ciphertext);
		$l->a('Chunk retrieval function completed step 5<br>');
		//Check decrypted chunk against parity checksum from database
		$ptlen = strlen($plaintext);
		$ptpar = par($plaintext);
		$ptmd5 = amd5($plaintext);
		$ptcrc = crc($plaintext);
		$ptsha = sha($plaintext);
		$pts512 = s512($plaintext);
		$plen = $chunkMeta['lengthpre'];
		$ppar = $chunkMeta['paritypre'];
		if(($plen != $ptlen) || ($ppar != $ptcrc)) {
			if($rcpcount < 1) {
				$l->a('information code 30<br>');
				$l->a('<br>Retrieved data: '.$plaintext.'<br><br>');				
				$l->a('Retrieved len = '.$ptlen.'; expected '.$plen.'.<br>');
				$l->a('Retrieved par = '.$ptpar.'; expected '.$ppar.'.<br>');
				$rcpcount++;
				goto retrievechunk;
			}
			else {
				$rcerror = 14;
			}
		}
		$l->a('Chunk retrieval function completed step 6<br>');
		$db->close();
		$l->a('Chunk retrieval function completed step 7<br>');
		//return data and checksums
		return new cChunk ($plaintext,$ptlen,$ptpar,$ptmd5,$ptcrc,$ptsha,$pts512);
	}
}
function retrieveCoal($id)
{
	global $l;
	$l->a('Coal retrieval function begun<br>');
	$db               = new FractureDB('futuqiur_coal');
	$rctries = 0;
	retrievec:
	$rcerror = 0;
	$rccount = 0;
	$rcpcount = 0;
	$l->a('Coal retrieval function completed step 1<br>');
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
	$l->a('Coal retrieval function completed step 2<br>');
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
	$l->a('Coal retrieval function completed step 3<br>');
	$l->a('Coal block list: ' . $coalBlockList.'<br>');
	if(strlen($coalBlockList)==0) {
		$blockListExploded = array();
	}
	else {
		$blockListExploded = explode_esc(',',$coalBlockList);
	}
	$dataToReturn = '';
	$l->a('Coal retrieval function completed step 4<br>');
	foreach($blockListExploded as $blockId) {
		$l->a('Coal retrieval function running from checkpoint 5a<br>');
		requestblock:
		$l->a('Coal retrieval function requesting chunk retrieval: begun step 5<br>');
		//Request block
		$blockData = retrieveChunk($blockId);
		$l->a('Coal retrieval function completed step 5<br>');
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
		$l->a('Coal retrieval function completed step 6<br>');
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
		$l->a('Coal retrieval function completed step 7<br>');
		//Decrypt block data using record key
		// global $coalPrivateKey;
// 		$rsa = new Crypt_RSA();
// 		$rsa->loadKey($coalPrivateKey); // private key
// 		$ciphertext = $rbdata;
// 		$plaintext = $rsa->decrypt($ciphertext);
		$plaintext = $rbdata;
		$l->a('Coal retrieval function completed step 8<br>');
		//Decompress block data
		$dcblockdata = bzdecompress($plaintext);
		$l->a('Coal retrieval function completed step 9<br>');
		//Append block data to record data to return
		$dataToReturn = $dataToReturn.$dcblockdata;
		$l->a('Coal retrieval function completed step 10<br>');
	}
	//Check compiled record data against parity checksum
	$clen = strlen($dataToReturn);
	$cpar = par($dataToReturn);
	$cmd5 = amd5($dataToReturn);
	$csha = sha($dataToReturn);
	$ccrc = crc($dataToReturn);
	$cs512 = s512($dataToReturn);
	if(($ccrc != $recordpar) || ($clen != $recordlen)) {
		$l->a('Coal retrieval function reached status checkpoint 10a<br>');
		$l->a('<br>Retrieved data: '.$dataToReturn.'<br><br>');				
		$l->a('Retrieved len = '.$clen.'; expected '.$recordlen.'.<br>');
		$l->a('Retrieved crc = '.$ccrc.'; expected '.$recordpar.'.<br>');
		if($rcpcount < 10) {
			$l->a('Coal retrieval function reached status checkpoint 10b<br>');
			$rcpcount++;
			$rcperror = 23;
			goto resetstatus;
		}
		else {
			$l->a('Coal retrieval function reached status checkpoint 10c<br>');
			$rcerror = 19;
		}
	}
	$l->a('Coal retrieval function completed step 11<br>');
	$db->close();
	//return data and checksums
	return new cCoal ($dataToReturn,$clen,$cpar,$cmd5,$ccrc,$csha,$cs512);
	resetstatus:
	$l->a('Coal retrieval function reached status checkpoint a<br>');
	$blocklist = '';
	$dataToReturn = '';
	$rctries++;
	if($rctries < 10) {
		$l->a('Coal retrieval function reached status checkpoint b<br>');
		goto retrievec;
	}
	else {
		$l->a('Coal retrieval function reached status checkpoint c<br>');
		//Chunk retrieval failed too many times
		//$rcerror = 16;
		//echo 'Chunk retrieval failed: 
		return array(16, $rcerror, $rcperror);
	}
}
?>