<?php
error_reporting(E_ALL);
ini_set("display_errors", true);
$displayDebugMessages = True;
//FractureDB is the class that implements the FractureDB data management system.
class FractureDB
{
    function __construct($name)
    {
        $this->name = $name;
        
        $this->queryCount = 0;
        global $db_data;
        #print_r($db_data);
        //echo '<br><br><font color="red">EXECUTING QUERY: ' . $query . '</font><br><br>';
        $username = $db_data[$this->name][0];
        $password = $db_data[$this->name][1];
        $host     = $db_data[$this->name][2];
        try {
	        $this->db = new Database('mysql:host=' . $host . ';dbname=' . $this->name . ';charset=utf8', $username, $password);
	    }
	    catch(PDOException $e)
		{
			throw new Exception($e->getMessage());
		}
		$dbh      = $this->db;
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();
    }
    function UrlEscS($data)
    {
        //I think this is something for ARCMAJ3. Not sure what it's doing in FractureDB, but whatever.
        $data = str_replace(' ', '%20', $data);
        $data = str_replace('\'', '%27', $data);
        $data = str_replace('<', '%3C', $data);
        $data = str_replace('>', '%3E', $data);
        $data = str_replace('[', '%5B', $data);
        $data = str_replace(']', '%5D', $data);
        $data = str_replace('(', '%28', $data);
        $data = str_replace(')', '%29', $data);
        return $data;
    }
    function close()
    {
        $dbh = $this->db;
        $dbh->commit();
    }
    function SaveState()
    {
        $dbh    = $this->db;
        $result = $dbh->prepare('SAVEPOINT databaseState');
        $result->execute();
    }
    function restoreSave()
    {
        $dbh    = $this->db;
        $result = $dbh->prepare('ROLLBACK TO SAVEPOINT databaseState');
        $result->execute();
    }
    function query($query, $failed = False)
    {
        echo '<br><br><font color="red">EXECUTING QUERY: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            $result->execute();
        }
        catch (PDOException $e) {
            dHandler($dbh, $query, $e, 'normal', True);
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        if (stripos($query, 'INSERT') === 0) {
            #query begins with INSERT
            #$query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
            #return $this->query($query);
            return 'Inserted';
        }
        if (stripos($query, 'UPDATE') === 0) {
            #query begins with INSERT
            #$query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
            #return $this->query($query);
            return 'Updated';
        }
        return $result->fetchAll(Database::FETCH_ASSOC);
    }
    function query_num($query, $failed = False)
    {
        #echo '<br><br><font color="red">EXECUTING QUERY_NUM: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            $result->execute();
        }
        catch (PDOException $e) {
            dHandler($dbh, $query, $e, 'num', True);
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        return $result->fetchAll(Database::FETCH_NUM);
    }
    function queryInsert($query)
    {
        global $displayDebugMessages;
        #echo '<br><br><font color="red">EXECUTING QUERYINSERT: ' . $query . '</font><br><br>';
        $dbh = $this->db;
        $this->queryCount++;
        #http://pastebin.com/bbCRpA2m        
        try {
            $result = $dbh->prepare($query);
            #print_r($result);
            $result->execute();
            #print $query;
        }
        catch (PDOException $e) {
            if ($displayDebugMessages) {
                print("Error: " . $e->getMessage());
            }
        }
        #print '<br><br>This query returned: <br>';
        #print_r($result->fetchAll(PDO::FETCH_ASSOC));
        #print '<br><br>';
        return $dbh->lastInsertId();
    }
    function getRowList($table)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        return $this->query('SELECT COLUMN_NAME,DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\'');
    }
    function getSimpleRowList($table)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        return $this->query('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\'');
    }
    function getRowType($table, $key)
    {
        #explain extended select * from am_urls; show warnings;
        #return $this->query('SHOW columns FROM '.$table);
        #return $this->query('SELECT GROUP_CONCAT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\''.$table.'\'');
        $resultAll      = $this->query('SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=\'' . $table . '\' AND column_name=\'' . $key . '\'');
        $resultFiltered = str_replace('longtext', 'text', str_replace('bigint', 'int', $resultAll[0]['DATA_TYPE']));
        return $resultFiltered;
    }
    function getNextId($table)
    {
        $query = 'SHOW TABLE STATUS LIKE \'' . $table . '\';';
        return $this->query($query);
    }
    function getTable($table)
    {
        $query = 'SELECT * FROM ' . $table . ' ORDER BY id;';
        return $this->query($query);
    }
    function dropRow($table,$id)
    {
    	//Check length of $id. I'm not sure what this would do if $id was empty, and I don't want to find out.
    	if(strlen($id)>0) {
			//help from http://www.w3schools.com/sql/sql_delete.asp
			$query = 'DELETE FROM ' . $table . ' WHERE id='.$id.';';
			$this->query($query);
		}
    }
    function countTable($table, $filterField = '', $filterValue = '', $additions = '')
    {
        if ($filterField !== '') {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT COUNT(*) FROM ' . $table . $queryInsert . $additions . ';';
        #echo "\n".$query."\n";
        return $this->query($query);
    }
    function countRows($table, $additions = '')
    {
        $query = 'SELECT COUNT(*) FROM `' . $table . '`' . $additions . ';';
        #echo "\n".$query."\n";
        return $this->query($query);
    }
    function listIds($table)
    {
        $query      = 'SELECT GROUP_CONCAT(id) FROM ' . $table . ';';
        //echo $query;
        $qResLevel1 = $this->query($query);
        $qResLevel2 = $qResLevel1[0];
        #print_r($qResLevel2);
        return $qResLevel2['GROUP_CONCAT(id)'];
    }
    function getRow($table, $field, $value)
    {
        $query    = 'SELECT * FROM ' . $table . ' WHERE ' . $field . ' = \'' . $value . '\';';
        $rowData  = $this->query($query);
        //print_r($rowData);
        if(isset($rowData[0])) {
        	$rowDataP = $rowData[0];
        }
        else {
        	//row is empty
        	$rowDataP = $this->getSimpleRowList($table);
        }
       //  echo "\n\n~~~~\n\n";
//         print_r($rowDataP);
//         echo "\n\n~~~~\n\n";
        return $rowDataP;
    }
    function getRows($table, $field, $value, $order = '')
    {
    	if ($order !== '') {
            $queryInsert = ' ORDER BY `'.$order.'`';
        } else {
            $queryInsert = '';
        }
        $query   = 'SELECT * FROM ' . $table . ' WHERE `' . $field . '` = \'' . $value . '\'' . $queryInsert . ';';
        $rowData = $this->query($query);
        return $rowData;
    }
    function getOrderedRows($table, $order)
    {
    	if ($order !== '') {
            $queryInsert = ' ORDER BY '.$order;
        } else {
            $queryInsert = '';
        }
        $query   = 'SELECT * FROM ' . $table . $queryInsert . ';';
        $rowData = $this->query($query);
        return $rowData;
    }
    function lockTable($table)
    {
		//help from http://stackoverflow.com/questions/443909/locking-a-mysql-database-so-only-one-person-at-once-can-run-a-query
        $query   = 'LOCK TABLES ' . $table . ';';
        $this->query($query);
    }
    function unlockTable($table)
    {
		//help from http://stackoverflow.com/questions/443909/locking-a-mysql-database-so-only-one-person-at-once-can-run-a-query
        $query   = 'UNLOCK TABLES ' . $table . ';';
        $this->query($query);
    } 
     function LoadFromFile($filename, $table, $columnList)
    {

        if ($columnList !== '') {
            $queryInsert = ' ('.$columnList.')';
        } else {
            $queryInsert = '';
        }
        global $db_data;
        $username = $db_data[$this->name][0];
        $password = $db_data[$this->name][1];
        # from http://stackoverflow.com/questions/7638090/load-data-local-infile-forbidden-in-php
        exec("mysql -u " . $username . " -p" . $password . " --local-infile -e \"USE " . $this->name . ";LOAD DATA LOCAL INFILE '" . $filename . "' IGNORE INTO TABLE " . $table . $queryInsert.";\"; ");
    }
    function getRandomRow($table, $filterField = '', $filterValue = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . 'ORDER BY RAND() LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getNextRow($table, $filterField = '', $filterValue = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . ' LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getNextRowEF($table, $filterField = '', $filterValue = '', $custom = '', $idFieldName = 'id', $limit = 1)
    {
        if ($filterField !== '') {
            $queryInsert = $filterField . ' = \'' . $filterValue . '\' ';
        } else {
            $queryInsert = '';
        }
        //         if ($custom !== '') {
        //             $queryInsert2 = $filterField2 . ' = \'' . $filterValue2 . '\' ';
        //         } else {
        //             $queryInsert2 = '';
        //         }
        $query    = 'SELECT * FROM `' . $table . '` WHERE ' . $queryInsert . ' AND ' . $custom . ' LIMIT ' . $limit . ';';
        #echo "\n".$query."\n";
        $rowData  = $this->query($query);
        $rowDataP = $rowData[0];
        return $rowDataP;
    }
    function getField($table, $field, $id)
    {
        $query    = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE id = ' . $id . ';';
        $rowData  = $this->query($query);
        if(isset($rowData[0])) {
        	$rowDataP = $rowData[0];
			#print_r($rowDataP);
			#echo $rowDataP[$field];
			return $rowDataP[$field];
		}
		else {
			return "";
		}
    }
    function getColumn($table, $field, $filterField = '', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $queryInsert . ';';
        //echo '<br>Requesting column from database with query '.$query.'.<br>';
        return $this->query($query);
    }
    function getColumnsUH($table, $field, $filterField = '', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = UNHEX(\'' . $filterValue . '\')';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $queryInsert . ';';
        //echo '<br>Requesting column from database with query '.$query.'.<br>';
        return $this->query($query);
    }
    function getColumns($table, $fields, $filterField = '', $filterValue = '')
    {
        return $this->getColumn($table, $fields, $filterField, $filterValue);
    }
    function getColumn_num($table, $field, $filterField = '', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $queryInsert . ';';
        return $this->query_num($query);
    }
    function setField($table, $field, $value, $id = '')
    {
        $query = 'INSERT INTO ' . $table . ' (id, ' . $field . ') VALUES (' . $id . ',\'' . $value . '\') ON DUPLICATE KEY UPDATE ' . $field . ' = \'' . $value . '\';';
        //echo $query;
        $this->query($query);
    }
    function getPrimaryKey($table) {
		$query = "SHOW KEYS FROM " . $table . " WHERE key_name = 'PRIMARY'";
		//echo $query;
		$data = $this->query($query);
		//print_r($data);
		return $data[0]['Column_name'];
    }
    function addRow($table, $fields, $values)
    {
        $query    = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');';
        $newRowId = $this->queryInsert($query);
        return $newRowId;
    }
    function addRowFuzzy($table, $fields, $values)
    {
        $query = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');';
        #$query    = 'INSERT INTO ' . $table . ' (' . $fields . ') VALUES (' . $values . ');';
        #echo '<br><br><font color="red">EXECUTING addRowfuzzy QUERY: ' . $query . '</font><br><br>';
        try {
            $newRowId = $this->queryInsert($query);
        }
        catch (PDOException $e) {
            #condition from http://stackoverflow.com/questions/4366730/how-to-check-if-a-string-contains-specific-words
            if (strpos($e->getMessage, 'Duplicate entry') !== False) {
                #Do nothing; this is a normal condition
            }
            
            else {
                #Something went wrong
                # from http://www.php.net/manual/en/class.pdoexception.php#95812
                throw new Exception($e->getMessage(), $e->getCode());
                #from http://stackoverflow.com/questions/15887070/php-trigger-fatal-error
                trigger_error("Fatal error inserting URL", E_USER_ERROR);
            }
        }
        return $newRowId;
    }
    function updateColumn($table, $field, $value, $filterField = 'id', $filterValue = '')
    {
        if (strlen($filterValue) !== 0) {
            $queryInsert = ' WHERE ' . $filterField . ' = \'' . $filterValue . '\'';
        } else {
            $queryInsert = '';
        }
        $query = 'UPDATE `' . $table . '` SET ' . $field . ' = \'' . $value . '\'' . $queryInsert . ';';
        $this->query($query);
        return $query;
    }
    function fuzzyMatchGetRow($table, $field, $filterField, $filterValue, $limit = '')
    {
        if (strlen($limit) !== 0) {
            $limitIns = ' LIMIT ' . $limit;
        } else {
            $limitIns = '';
        }
        # from http://stackoverflow.com/questions/6447899/select-where-row-value-contains-string-mysql
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $filterField . ' LIKE \'%' . $filterValue . '%\'' . $limitIns . ';';
        return $this->query($query);
    }
}
?>