<?php

class db extends core
{

	/*
		function connect
		
		Inputs: $server (string) the server IP address, $username (string) the database server username, $password (string) the database server passsword.
		Return: (boolean) true if the database has connected.
		Description: Connects to the mySQL database.
		
	*/
    public function connect($server, $username, $password)
    {
        @mysql_connect($server, $username, $password) or 
			exit(core::error(1));
        return TRUE;
    }

	/*
		function select_db
		
		Inputs: $database (string) the database name.
		Return: (boolean) true if the database server has connected to the database.
		Description: Selects the mySQL database.
		
	*/
    public function select_db($database)
    {
        @mysql_select_db($database) or 
			exit(core::error(2));
        return TRUE;
    }

	/*
		function query
		
		Inputs: $query (string) the SQL query to be ran on the database.
		Return: (resource) the query result.
		Description: Runs an SQL query on the database.
		
	*/
    public function query($query)
    {
        $query = @mysql_query($query) or 
			exit(core::error(3));
        return $query;
    }
	
	/*
		function select
		
		Inputs: $tablename (string) the table name, $where (string) the values to select, $limit (string) the limit of the result, $order (string) the order of the result.
		Return: (resource) the query result.
		Description: Runs an select SQL query on the database.
		
	*/
	public function select($tablename, $where = NULL, $limit = NULL, $order = NULL)
    {
		global $settings;
		$settings['stats']['select_queries']++;
		if($where != NULL){
			$where = "WHERE $where";
		}
		
		if($limit != NULL){
			$limit = "LIMIT $limit";
		}
		
		if($order != NULL){
			$order = "ORDER BY $order";
		}
		
		$tblprefix = $settings['db']['tblprefix'];
		$tblname = "$tblprefix$tablename";
		
		$querystring = "SELECT * FROM `$tblname` $where $order $limit";
		//echo $querystring . "<br />";
        $query = @mysql_query($querystring) or 
			exit(core::error(4));
        return $query;
    }
	
	/*
		function insert
		
		Inputs: $tablename (string) the table name, $cols (string) the column names to insert data into, $values (string) the values to insert into the database.
		Return: (resource) the query result.
		Description: Inserts a row into the database.
		
	*/
	public function insert($tablename, $cols, $values){
		global $settings;
		
		$tblprefix = $settings['db']['tblprefix'];
		$tblname = "$tblprefix$tablename";
		
		$querystring = "INSERT INTO `$tblname` ($cols)VALUES($values)";
		//echo $querystring;
        $query = @mysql_query($querystring) or 
			exit(core::error(5));
        return $query;
	}
	
	/*
		function update
		
		Inputs: $tablename (string) the table name, $values (string) the column names to update the data, $where (string) the ID in the table to update the data.
		Return: (resource) the query result.
		Description: Updates a row in the database.
		
	*/
	public function update($tablename, $values, $where){
		global $settings;
		
		$tblprefix = $settings['db']['tblprefix'];
		$tblname = "$tblprefix$tablename";
		$querystring = "UPDATE `$tblname` SET $values WHERE $where LIMIT 1";
        $query = @mysql_query($querystring) or exit(core::error(6));
        return $query;
	}
	
	/*
		function count
		
		Inputs: $querydata (resource) the completed query.
		Return: (int) the number of rows in the query.
		Description: Counts the number of returned rows from the query.
		
	*/
	public function count($querydata){
		return @mysql_num_rows($querydata);
		if(mysql_error()){
			 exit(core::error(7));
		}
	}
	
	/*
		function insert_id
		
		Inputs: none
		Return: (int) the primary key of the previous insert statement.
		Description: Gets the primary key of the last insert statement performed on the database.
		
	*/
	public function insert_id(){
		return mysql_insert_id();
	}
  
	/*
		function fetch_array
		
		Inputs: $queryinfo (resource) the completed query.
		Return: (array) an array of the rows.
		Description: Takes the completed query and splits the rows up into an array.
		
	*/
    public function fetch_array($queryinfo)
    {
        $query = @mysql_fetch_array($queryinfo);
		if(mysql_error()){
			 exit(core::error(8));
		}
        return $query;
    }
  
}

?>