<?php  
	backupDatabaseTables('localhost','root','champ123','businesswebapp');

	function backupDatabaseTables($dbHost,$dbUsername,$dbPassword,$dbName,$tables = '*') {

		$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
	    //get all of the tables
	    if($tables == '*'){
	        $tables = array();
	        $result = $db->query("SHOW TABLES");
	        while($row = $result->fetch_row()){
	            $tables[] = $row[0];
	        }
	    }else{
	        $tables = is_array($tables)?$tables:explode(',',$tables);
	    }


	    foreach($tables as $table){
	        $result = $db->query("SELECT * FROM $table");
	        $numColumns = $result->field_count;

	        // $return .= "DROP TABLE $table;";

	        $result2 = $db->query("SHOW CREATE TABLE $table");
	        $row2 = $result2->fetch_row();

	        $return .= "\n\n".$row2[1].";\n\n";

	        for($i = 0; $i < $numColumns; $i++){
	            while($row = $result->fetch_row()){
	                $return .= "INSERT INTO $table VALUES(";

	                for($j=0; $j < $numColumns; $j++){
	                    $row[$j] = addslashes($row[$j]);
	                    $row[$j] = str_replace("\n","\n",$row[$j]);

	                    if (isset($row[$j])) { $return .= '"'.$row[$j].'"' ; } else { $return .= '""'; }
	                    if ($j < ($numColumns-1)) { $return.= ','; }
	                }
	                $return .= ");\n";
	            }
	        }

	        $return .= "\n\n\n";
	    }
	    //save file
	    $dest       =   'backup/'.date('d-m-Y'); 
	    @mkdir($dest, 0777); 
		@chmod($dest,  0777); 
	    $dest2       =   $dest.'/db-backup-'.time().'.sql'; 
	    $handle = fopen($dest2,'w+');
	    fwrite($handle,$return);
	    fclose($handle);
	}

?>
