<?php
/* START ACTION DEFINITIONS */
if ($userpermissionverified == 0) {
    if ($nodepermerr == 1) {
        err(1);
    } else {
        err(50);
    }
} else {
    //ACTION home
    if ($wvActionId == 'home') {
        $pageBody = itr(8);
        $pageTitle = itr(16);
    } else {
    }
    //ACTION logIn
    if ($wvActionId == 'logIn') {
        session_destroy();
        if (fv('nodeId')) {
            $pageBody = str_replace('<input type="hidden" name="login" value="1">', '<input type="hidden" name="login" value="1"><input type="hidden" name="nodeId" value="' . fv('nodeId') . '"><input type="hidden" name="returnAction" value="6">', itr(9));
        } else {
            $pageBody = itr(9);
        }
        $pageTitle = itr(17);
    } else {
    }
    //ACTION logInExecute
    if ($wvActionId == 'logInExecute') {
        $_SESSION['wvUserName'] = fv('wvUserName');
        $_SESSION['wvUserPassword'] = fv('wvUserPassword');
        if(isset($_POST['returnAction'])) {
       		if ($_POST['returnAction'] == '6') {
            	$pageBody = itr(284) . buildLink('6', '&wvSession=' . session_id() . '&nodeId=' . fv('nodeId') . '&', itr(776) . fv('nodeId') . itr(777));
       		}
       		else {
            	$pageBody = itr(284) . buildLink(1, '&wvSession=' . session_id() . '&', itr(285));
        	    $pageTitle = itr(17);
     	  	}
     	}
     	else {
			$pageBody = itr(284) . buildLink(1, '&wvSession=' . session_id() . '&', itr(285));
			$pageTitle = itr(17);
     	}
   	 }
    //ACTION logOut
    if ($wvActionId == 'logOut') {
        session_destroy();
        $pageBody = itr(689) . itr(696);
        $pageTitle = itr(689);
    } else {
    }
    //ACTION newUser
    if ($wvActionId == 'newUser') {
        $pageBody = itr(10);
        $pageTitle = itr(18);
    } else {
    }
    if ($wvActionId == 'newUserExecute') {
        if (qry('user', 'user_id', 'user_name', $_POST['userName']) == '') {
            mysql_query('INSERT INTO `user` (`user_id`, `user_registration_ip`, `user_node_id`, `user_name`, `user_authorisation_type`, `user_password_md5`, `user_ip_list`, `user_node_edit_ids`) VALUES (NULL, \'' . $_SERVER['REMOTE_ADDR'] . '\', \'' . $_POST['newUserNodeId'] . '\', \'' . mysql_real_escape_string($_POST['userName']) . '\', \'' . '1' . '\', \'' . md5($_POST['password']) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\', NULL);');
            $pageBody = itr(11) . htmlspecialchars($_POST['userName'], ENT_NOQUOTES, 'utf-8', true) . itr(12) . buildLink('3', '', itr(26)) . itr(13) . qry('user', 'user_id', 'user_name', $_POST['userName']) . '.';
            $pageTitle = itr(19);
        } else {
            $pageBody = itr(15) . buildLink('4', '', itr(26)) . itr(775);
            $pageTitle = itr(20);
        }
    } else {
    }
    //ACTION newNode
    if ($wvActionId == 'newNode') {
        $pageBody = itr(65) . finishForm(itr(67));
        $pageTitle = itr(64);
    } else {
    }
    //ACTION nodeEdit
    if ($wvActionId == 'nodeEdit') {
        $keys = array(itr(934), 'name="nodeTitle"', 'name="nodeSortTitle"', 'name="nodeDisplayTitle"', 'name="nodeShortTitle"', 'name="nodeType"', 'name="nodeDescription"></textarea>', 'name="nodeDisambiguationDescription"', 'name="nodeMetadata"', 'name="nodePermissions"', 'name="nodeRelationships"', 'name="nodeSource"', 'name="nodeComment"', 'name="nodeShortDescription"', '<input type="radio" name="nodeUniverseStatus" value="1">' . itr(929) . ' <input type="radio" name="nodeUniverseStatus" value="">', '<input type="radio" name="nodeCopyrightFlag" value="1">' . itr(929) . ' <input type="radio" name="nodeCopyrightFlag" value="">', '<input type="radio" name="nodeMoralityFlag" value="1">' . itr(929) . ' <input type="radio" name="nodeMoralityFlag" value="">', '<input type="radio" name="nodeMinorFlag" value="1">' . itr(929) . ' <input type="radio" name="nodeMinorFlag" value="">', '<input type="radio" name="nodePersonalFlag" value="1">' . itr(929) . ' <input type="radio" name="nodePersonalFlag" value="">', '<input type="radio" name="nodeDataUploadFlag" value="1">' . itr(930) . '<input type="file"  name="uploadeddata" />' . itr(931) . '<input type="text" name="dataType"> <input type="radio" name="nodeDataUploadFlag" value="">', '<input type="hidden" name="a" value="8">');
        $nodeRevId = qry('node', 'node_current_revision', 'node_id', fv('nodeId'));
        if (!itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $nodeRevId))) {
            $disambigStr = '';
        } else {
            $disambigStr = itr(769) . itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $nodeRevId)) . itr(770);
        }
        if(!isset($universestatusstring)) {
        		$universestatusstring = null;
       	}
       	if(!isset($copyrightflagstring)) {
        		$copyrightflagstring = null;
       	}
       	if(!isset($moralityflagstring)) {
        		$moralityflagstring = null;
       	}
       	if(!isset($minorflagstring)) {
        		$minorflagstring = null;
       	}
       	if(!isset($personalflagstring)) {
        		$personalflagstring = null;
       	}
       	if(!isset($datastring)) {
        		$datastring = null;
       	}
        $replaces = array(itr(933), 'name="nodeTitle" value="' . itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeSortTitle" value="' . itr(qry('node_revision', 'node_revision_sort_title', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeDisplayTitle" value="' . itr(qry('node_revision', 'node_revision_display_title', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeShortTitle" value="' . itr(qry('node_revision', 'node_revision_short_title', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeType" value="' . itr(qry('node_revision', 'node_revision_type', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeDescription">' . htmlentities(itr(qry('node_revision', 'node_revision_description', 'node_revision_id', $nodeRevId))) . '</textarea><input type="hidden" name="nodeId" value="' . $nodeId . '">', 'name="nodeDisambiguationDescription" value="' . itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeMetadata" value="' . itr(qry('node_revision', 'node_revision_metadata', 'node_revision_id', $nodeRevId)) . '"', 'name="nodePermissions" value="' . itr(qry('node_revision', 'node_revision_permissions', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeRelationships" value="' . itr(qry('node_revision', 'node_revision_relationships', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeSource" value="' . itr(qry('node_revision', 'node_revision_source', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeComment" value="' . itr(qry('node_revision', 'node_revision_comment', 'node_revision_id', $nodeRevId)) . '"', 'name="nodeShortDescription" value="' . itr(qry('node_revision', 'node_revision_short_description', 'node_revision_id', $nodeRevId)) . '"', $universestatusstring, $copyrightflagstring, $moralityflagstring, $minorflagstring, $personalflagstring, $datastring, '<input type="hidden" name="a" value="26"> Edit note: <textarea rows="10" cols="80" name="editNote"></textarea><br>');
        $pageBody = str_replace($keys, $replaces, itr(65)) . finishForm(itr(67));
        $pageTitle = itr(935) . fv('nodeId') . itr(936);
    } else {
    }
    //ACTION nodeView
    if ($wvActionId == 'nodeView') {
        $nodeRevId = qry('node', 'node_current_revision', 'node_id', fv('nodeId'));
        if (!itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $nodeRevId))) {
            $disambigStr = '';
        } else {
            $disambigStr = itr(769) . itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $nodeRevId)) . itr(770);
        }
        $relquery = 'SELECT relationship_revision_first_node_id, relationship_revision_second_node_id, relationship_revision_type, MAX(relationship_revision.relationship_revision_id) FROM relationship, relationship_revision WHERE relationship.relationship_id=relationship_revision.relationship_revision_relationship_id AND (relationship_revision_first_node_id = ' . fv('nodeId') . ' OR relationship_revision_second_node_id = ' . fv('nodeId') . ') GROUP BY relationship_revision_first_node_id, relationship_revision_second_node_id, relationship_revision_type';
        //e($relquery);
        $relationships = mysql_query($relquery);
        //$relationships=mysql_query('SELECT * FROM relationship_revision WHERE relationship_revision_first_node_id = ' . fv('nodeId') . ' OR relationship_revision_second_node_id = ' . fv('nodeId'));
        while ($row = mysql_fetch_array($relationships, MYSQL_ASSOC)) {
            //$relId = qry(relationship, relationship_id,
            //TODO: Make relationship edit links
            $first_node_id = $row['relationship_revision_first_node_id'];
            $relationship_type = $row['relationship_revision_type'];
            $second_node_id = $row['relationship_revision_second_node_id'];
            $child_node_id = $first_node_id;
            $position = '1';
            $position1link = '';
            $position2link = c(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId)) . $disambigStr);
            if ($first_node_id == $nodeId) {
                $child_node_id = $second_node_id;
                $position = '2';
                $position1link = c(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId)) . $disambigStr);
            }
            $child_node_inner_qry = qry('node', 'node_current_revision', 'node_id', $child_node_id);
            $child_node_title_itr_id = qry('node_revision', 'node_revision_title', 'node_revision_id', $child_node_inner_qry);
            if (!itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', qry('node', 'node_current_revision', 'node_id', $child_node_id)))) {
                $cndisambigstr = '';
            } else {
                $cndisambigstr = itr(769) . itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', qry('node', 'node_current_revision', 'node_id', $child_node_id))) . itr(770);
            }
            $child_node_title = shorten(itr($child_node_title_itr_id));
            $child_node_link = buildLink('6', '&nodeId=' . $child_node_id . '&', $child_node_title) . $cndisambigstr;
            //echo $child_node_link . '<br>';
            if ($position == '2') {
                $position2link = $child_node_link;
            } else {
                $position1link = $child_node_link;
            }
            //print_r($row);
            $relationship_string = $relationship_string . ' ' . $position1link . ' ' . qry('relationship_type', 'relationship_type_label', 'relationship_type_id', $row['relationship_revision_type']) . ' ' . $position2link . itr(1152) . buildLink('27', '', itr(1157)) . itr(1154) . itr(891);
        }
        //print_r($relationship_string);
        $pageMenu = itr(778) . itr(785) . itr(75) . fv('nodeId') . itr(76) . fv('locale') . itr(77) . itr(784) . buildLink('17', '&nodeId=' . fv('nodeId') . '&', itr(1132)) . itr(1134) . buildLink('16', '&nodeId=' . fv('nodeId') . '&', itr(1133)) . itr(778);
        if(!isset($relationship_string)) {
        		$relationship_string = null;
       	}
        if (strlen($relationship_string) == 0) {
            $relsep = '';
        } else {
            $relsep = itr(786);
        }
        $pagetitleshortened = shorten(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId)));
        global $baggage_claim;
        $shortenedtf=$baggage_claim->claim_luggage('Shortened');
        if ($shortenedtf === 'true') {
            $shortenedtitlefull = itr(1490) . c(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $nodeRevId))) . itr(1491);
        } else {
            $shortenedtitlefull = '';
        }
        $pageBody = $shortenedtitlefull . c(itr(qry('node_revision', 'node_revision_description', 'node_revision_id', $nodeRevId)) . itr(74) . $relsep . $relationship_string);
        $pageTitle = c($pagetitleshortened . $disambigStr);
    } else {
    }
    //ACTION newNodeExecute
    if ($wvActionId == 'newNodeExecute') {
        $nodeAddedId = newNodeExecute();
        $pageBody = itr(70) . buildLink(6, '&nodeId=' . $nodeAddedId, $nodeAddedId) . itr(69);
        //Options for previous line's buildLink: '&nodeId=' . mysql_insert_id()
        $pageTitle = itr(68);
    } else {
    }
    //ACTION newNodeRevisionExecute
    if ($wvActionId == 'newNodeRevisionExecute') {
        $nodeEditedId = newNodeRevisionExecute();
        $pageBody = itr(955) . buildLink(6, '&nodeId=' . $nodeEditedId, $nodeEditedId) . itr(69);
        //Options for previous line's buildLink: '&nodeId=' . mysql_insert_id()
        $pageTitle = itr(68);
    } else {
    }
    //ACTION newRelationship
    if ($wvActionId == 'newRelationship') {
        $pageBody = str_replace('@REPLACEMEWITHDANODEID@', fv('nodeId'), itr(779)) . itr(780) . finishForm(itr(67));
        $pageTitle = str_replace('@REPLACEMEWITHDANODEID@', fv('nodeId'), itr(781));
    } else {
    }
    //ACTION newRelationshipExecute
    if ($wvActionId == 'newRelationshipExecute') {
        $pageBody = itr(889) . buildLink('6', '&nodeId=' . fv('nodeId') . '&', itr(890) . fv('nodeId') . itr(783));
        $pageTitle = str_replace('@REPLACEMEWITHDANODEID@', fv('nodeId'), itr(781));
        $target_node_id = fv('targetNode');
        $relationship_type = fv('relationshipType');
        $thisnodeid = fv('nodeId');
        $qShowStatusRel = "SHOW TABLE STATUS LIKE 'relationship'";
        $qShowStatusRes = mysql_query($qShowStatusRel) or die("Query failed: " . mysql_error() . "<br/>" . $qShowStatusRel);
        $rowdataRel = mysql_fetch_assoc($qShowStatusRes);
        $next_increment_rel = $rowdataRel['Auto_increment'];
        $sqlquery = 'INSERT INTO `relationship_revision` ( `relationship_revision_id` , `relationship_revision_first_node_id` , `relationship_revision_second_node_id` , `relationship_revision_personal_flag` , `relationship_revision_owner` , `relationship_revision_type` , `relationship_revision_source` , `relationship_revision_datetime_list` , `relationship_revision_comment` , `relationship_revision_universe_status` , `relationship_revision_dispute_flag` , `relationship_revision_morality_flag` , `relationship_revision_minor_flag` , `relationship_revision_time` , `relationship_revision_relationship_id` ) VALUES ( NULL , \'' . $thisnodeid . '\', \'' . $target_node_id . '\', \'personal flag\', \'' . fv('wvUserId') . '\', \'' . $relationship_type . '\', \'source\', \'datetime list\', \'comment\', \'universe status\', \'dispute flag\', \'morality flag\', \'minor flag\', \'revision time\', \'' . $next_increment_rel . '\' );';
        //echo $sqlquery;
        mysql_query($sqlquery);
        $sqlqueryalso = 'INSERT INTO `relationship` (`relationship_id`, `relationship_relationship_revision_id`) VALUES (NULL, \'' . mysql_insert_id() . '\');';
        //echo $sqlqueryalso;
        mysql_query($sqlqueryalso);
    } else {
    }
    //ACTION newIntf
    if ($wvActionId == 'newIntf') {
        $newInterface = new intf(0, $wvLocaleString, $HttpsWPUrl, '');
        $newInterface->new_intf();
    } else {
    }
    //ACTION newIntfExecute
    if ($wvActionId == 'newIntfExecute') {
        newintf(fv('intfContent'));
        $pageBody = itr(82) . $newIntfId;
        $pageTitle = itr(83);
    } else {
    }
    //ACTION operationIndex
    if ($wvActionId == 'operationIndex') {
        $pageBody = itr(878) . buildLink('19', '', itr(879)) . itr(88) . buildLink('5', '', itr(87)) . itr(89) . buildLink('9', '', itr(90)) . itr(910) . itr(911) . buildLink('20', '', itr(913)) . itr(910) . itr(911) . buildLink('24', '', itr(928)) . itr(910) . itr(911) . buildLink('22', '', itr(927)) . itr(912);
        $pageTitle = itr(86);
    } else {
    }
    //ACTION newCharacter
    if ($wvActionId == 'newCharacter') {
        $pageBody = itr(914) . finishForm(itr(67));
        $pageTitle = itr(913);
    } else {
    }
    //ACTION newCharacterExecute
    if ($wvActionId == 'newCharacterExecute') {
        $sqlquery = 'INSERT INTO `dce` ( `dce_id` , `dce_category` , `dce_name` , `dce_html` , `dce_comment` , `dce_decomposition` , `dce_aka` , `dce_see_also` , `dce_comments` , `dce_mojikyo` , `dce_tron` , `dce_armscii8` , `dce_unicode` ) VALUES ( NULL , \'' . fv('characterCategory') . '\', \'' . fv('characterName') . '\', \'' . fv('characterHTML') . '\', NULL, \'' . fv('characterDecomposition') . '\', \'' . fv('characterAka') . '\', \'' . fv('characterSeeAlso') . '\', \'' . fv('characterComments') . '\', \'' . fv('characterMojikyo') . '\', \'' . fv('characterTron') . '\', \'' . fv('characterArmscii') . '\', \'' . fv('characterUnicode') . '\' );';
        echo $sqlquery;
        mysql_query($sqlquery);
        $pageBody = itr(915) . mysql_insert_id() . itr(916);
        $pageTitle = itr(917);
    } else {
    }
    //ACTION newScript
    if ($wvActionId == 'newScript') {
        $pageBody = itr(914) . finishForm(itr(67));
        $pageTitle = itr(913);
    } else {
    }
    //ACTION newScriptExecute
    if ($wvActionId == 'newScriptExecute') {
        $sqlquery = 'INSERT INTO `script` ( `dce_id` , `dce_category` , `dce_name` , `dce_html` , `dce_comment` , `dce_decomposition` , `dce_aka` , `dce_see_also` , `dce_comments` , `dce_mojikyo` , `dce_tron` , `dce_armscii8` , `dce_unicode` ) VALUES ( NULL , \'' . fv('characterCategory') . '\', \'' . fv('characterName') . '\', \'' . fv('characterHtml') . '\', NULL, \'' . fv('characterDecomposition') . '\', \'' . fv('characterAka') . '\', \'' . fv('characterSeeAlso') . '\', \'' . fv('characterComments') . '\', \'' . fv('characterMojikyo') . '\', \'' . fv('characterTron') . '\', \'' . fv('characterArmscii') . '\', \'' . fv('characterUnicode') . '\' );';
        echo $sqlquery;
        mysql_query($sqlquery);
        $pageBody = itr(915) . mysql_insert_id() . itr(916);
        $pageTitle = itr(917);
    } else {
    }
    //ACTION newCharacterCategory
    if ($wvActionId == 'newCharacterCategory') {
        $pageBody = itr(914) . finishForm(itr(67));
        $pageTitle = itr(913);
    } else {
    }
    //ACTION newCharacterCategoryExecute
    if ($wvActionId == 'newCharacterCategoryExecute') {
        $sqlquery = 'INSERT INTO `character_category` ( `dce_id` , `dce_category` , `dce_name` , `dce_html` , `dce_comment` , `dce_decomposition` , `dce_aka` , `dce_see_also` , `dce_comments` , `dce_mojikyo` , `dce_tron` , `dce_armscii8` , `dce_unicode` ) VALUES ( NULL , \'' . fv('characterCategory') . '\', \'' . fv('characterName') . '\', \'' . fv('characterHtml') . '\', NULL, \'' . fv('characterDecomposition') . '\', \'' . fv('characterAka') . '\', \'' . fv('characterSeeAlso') . '\', \'' . fv('characterComments') . '\', \'' . fv('characterMojikyo') . '\', \'' . fv('characterTron') . '\', \'' . fv('characterArmscii') . '\', \'' . fv('characterUnicode') . '\' );';
        echo $sqlquery;
        mysql_query($sqlquery);
        $pageBody = itr(915) . mysql_insert_id() . itr(916);
        $pageTitle = itr(917);
    } else {
    }
    //ACTION nodeIndex (final action in action defs)
    if ($wvActionId == 'nodeIndex') {
        $nodeMin = 0;
        $nodeMaxArray = mysql_fetch_array(mysql_query('SELECT MAX(node_id) FROM `node`;'));
        $nodeMax = $nodeMaxArray['MAX(node_id)'];
        $start = fv('st');
        $start = round($start);
        $end = $start + 9;
        if ($end > $nodeMax) {
            $end = $nodeMax;
        }
        $prevStart = $start - 10;
        if ($prevStart < $nodeMin) {
            $prevStart = $nodeMin;
        }
        $nextStart = $end + 1;
        if ($nextStart > $nodeMax) {
            $nextStart = $start;
        }
        if ($nextStart == $nodeMax) {
            $nextStart = $nodeMax;
        }
        $this_node_id = $start;
        while ($this_node_id <= $end) {
            $thisNodeRevId = qry('node', 'node_current_revision', 'node_id', $this_node_id);
            if (!itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $thisNodeRevId))) {
                $disambigStr = '';
            } else {
                $disambigStr = itr(769) . itr(qry('node_revision', 'node_revision_disambiguation_description', 'node_revision_id', $thisNodeRevId)) . itr(770);
            }
            $title = c(shorten(itr(qry('node_revision', 'node_revision_title', 'node_revision_id', $thisNodeRevId))) . $disambigStr);
            $shortdesc = c(itr(qry('node_revision', 'node_revision_short_description', 'node_revision_id', $thisNodeRevId)));
            $teststringthing = str_replace('<br>', '', str_replace(': ', '', str_replace('</p>', '', str_replace('<p>', '', str_replace('</strong>', '', $shortdesc)))));
            if (strlen($teststringthing) === 0) {
                $separator = itr(858);
            } else {
                $separator = itr(859);
            }
            if(!isset($thisNodeEntry)) {
        		$thisNodeEntry = null;
       		}
            $thisNodeEntry = $thisNodeEntry . buildLink('6', '&nodeId=' . $this_node_id . '&', $this_node_id . itr(856) . itr(857) . $title . $separator . $shortdesc) . itr(74);
            $this_node_id = $this_node_id + 1;
        }
        $pageTitle = itr(850) . $start . itr(851) . $end;
        $nodeList = $thisNodeEntry;
        $idxNav = buildLink('19', '&st=' . $prevStart . '&', itr(852)) . itr(854) . buildLink('19', '&st=' . $nextStart . '&', itr(853));
        $pageBody = $nodeList . itr(855) . $idxNav;
    } else {
        if (empty($pageBody) && empty($pageTitle)) {
            $nothing = 'Oh, *damn*... I dunno what /that/ is...';
            $pageBody = itr(22);
            $pageTitle = itr(23);
        } else {
        }
    }
}
/* END ACTION DEFINITIONS */
?>
