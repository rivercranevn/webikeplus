<?php
/*
 * File Name: Database.php
 * Date: November 18, 2008
 * Author: Angelo Rodrigues
 * Description: Contains database connection, result
 *              Management functions, input validation
 *
 *              All functions return true if completed
 *              successfully and false if an error
 *              occurred
 *
 */
 $config = array(
	'domain'=>'https://www.webike.net', 
	'key'=>'ca07caa8a8'
 );
 
class Database
{

    /*
     * Edit the following variables
     */
    private $db_host = 'localhost';  // Database Host
    private $db_user = 'root';       // Username
    private $db_pass = '';			 // Password 
    private $db_name = 'webikesh';   // Database 
    /*
     * End edit
     */

    private $con = false;               // Checks to see if the connection is active
    private $result = array();          // Results that are returned from the query

    /*
     * Connects to the database, only one connection
     * allowed
     */
    public function connect()
    {
        if(!$this->con)
        {	
            $myconn = @mysqli_connect($this->db_host,$this->db_user,$this->db_pass);
            if($myconn)
            {
				//mysql_query("SET NAMES 'utf8'");
                $seldb = @mysqli_select_db($myconn, $this->db_name);
                if($seldb)
                {
                    $this->con = true;
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /*
    * Changes the new database, sets all current results
    * to null
    */
    public function setDatabase($name)
    {
        if($this->con)
        {
            if(@mysql_close())
            {
                $this->con = false;
                $this->results = null;
                $this->db_name = $name;
                $this->connect();
            }
        }

    }

    /*
    * Checks to see if the table exists when performing
    * queries
    */
    private function tableExists($table)
    {
        $tablesInDb = @mysql_query('SHOW TABLES FROM '.$this->db_name.' LIKE "'.$table.'"');
        if($tablesInDb)
        {
            if(mysql_num_rows($tablesInDb)==1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    /*
    * Selects information from the database.
    * Required: table (the name of the table)
    * Optional: rows (the columns requested, separated by commas)
    *           where (column = value as a string)
    *           order (column DIRECTION as a string)
    */
    public function select($table, $rows = '*', $where = null, $order = null, $offset=0, $limit=50000)
    {
		$conditions = '';
    	if (is_array($where)) {
			foreach($where as $column => $value) {
				$conditions .= (($conditions != '')?' AND ':'WHERE ') . '`' . $column . '` = ' . '"'.$value.'"';
			}
    	}else{
			$conditions .= " WHERE ".$where; 
		}
		$conn = @mysqli_connect($this->db_host,$this->db_user,$this->db_pass,$this->db_name);
		$sql = 'SELECT '.$rows.' FROM '."`$table` "  .$conditions .' '. $order . ' LIMIT '.$offset.', '.$limit;
        $query = @mysqli_query($conn, $sql);
        if($query)
        {
            $this->numResults = mysqli_num_rows($query);
            for($i = 0; $i < $this->numResults; $i++)
            {
                $r = mysqli_fetch_array($query);
                $key = array_keys($r);
                for($x = 0; $x < count($key); $x++)
                {
                    // Sanitizes keys so only alphavalues are allowed
                    if(!is_int($key[$x]))
                    {
                        if(mysqli_num_rows($query) > 1)
                            $this->result[$i][$key[$x]] = $r[$key[$x]];
                        else if(mysqli_num_rows($query) < 1)
                            $this->result = null;
                        else
                            $this->result[$key[$x]] = $r[$key[$x]];
                    }
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }
	public function countnumrow($table, $rows = '*', $where = null, $order = null)
    {
		$conditions = '';
    	if (is_array($where)) {
			foreach($where as $column => $value) {
				$conditions .= (($conditions != '')?' AND ':'WHERE ') . '`' . $column . '` = ' . '"'.$value.'"';
			}
    	}
		$q = 'SELECT '.$rows.' FROM '."`$table` "  .$conditions;
        $query = @mysql_query($q);
      	return $this->result = mysql_num_rows($query);
    }

    /*
    * Insert values into the table
    * Required: table (the name of the table)
    *           values (the values to be inserted)
    * Optional: rows (if values don't match the number of rows)
    */
    public function insert($table,$data)
    {
        if($this->tableExists($table))
        {
			$cols =""; $vals="";
            //$insert = 'INSERT INTO '.$table;
            foreach($data as $key=>$value){
				$cols .= ", `$key`";
				$vals .= ", '$value'";
			}
			$newQuery['cols'] = substr($cols, 2);
			$newQuery['vals'] = substr($vals, 2); 
            $insert = "INSERT INTO `$table` (".$newQuery['cols'].") VALUES (".$newQuery['vals'].")";   
            $ins = @mysql_query($insert);		
           	if($ins)
            {
				$queryid = @mysql_query('select LAST_INSERT_ID()');			
				$arrId = mysql_fetch_array($queryid);
                return $arrId[0];
            }
            else
            {
                return false;
            }
        }
    }    

    /*
    * Returns the result set
    */
    public function getResult()
    {
        return $this->result;
    }
	
	public function buildTree(array $elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as $element) {
			if ($element['post_term_parent_id'] == $parentId) {
				$children = $this->buildTree($elements, $element['post_term_id']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $branch;
	}
}
?>