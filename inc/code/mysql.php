<?php
	/**
	 *  PHP class for interacting with the MySQL Database
	 *
	 *  Copyright 2015 PromInc Productions. All Rights Reserved.
	 *  
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 *
	 */

	class MySQL
	{
	
		const DB_TABLE_SEARCH_ANALYTICS = 'search_analytics';
		
		
		/**
		 *  Query database.  Retrun all values from a table
		 *
		 *  @param $table     String   Table name
		 *
		 *  @returns   Object   Database records.  MySQL object
		 */
		public function qryDBall($table) {
			return $GLOBALS['db']->query("SELECT * FROM $table");
		}


		/**
		 *  Insert into Database
		 *
		 *  @param $table     String   Table name
		 *  @param $string     String   A value string to insert into the database. "field1"="value1", "field2"="value2"
		 *
		 *  @returns   Mixed   true(bool) upon success,
		 *             else returns error messsage upon failure
		 */
		public function qryDBinsert($table,$valueString) {
			$GLOBALS['db']->query("INSERT INTO $table VALUES ($valueString)");
		
			if(mysqli_error($GLOBALS['db'])) { printf("Errormessage: %s\n", mysqli_error($GLOBALS['db'])); } else { return true; }
		}
		
		
		/**
		 *  Get Number of Rows
		 *
		 *  @param $table     String   Table name
		 *  @param $searchParams     Array   Keys = field, Values = value
		 *
		 *  @returns   Int   Number of rows
		 */	
		public function qryDBupdate( $table, $matchParams, $updateParams ) {
			$matchstring = self::formQueryString( $matchParams );
			$updatestring = self::formQueryString( $updateParams );

			$query = "UPDATE ".$table." SET ".$updatestring." WHERE ".$matchstring;
/* 			$query = "SELECT id FROM ".$table." WHERE ".$querystring; */
			return self::query( $query );
		}


		/**
		 *  Format query string
		 *
		 *  @param $searchParams     Array   Keys = field, Values = value
		 *
		 *  @returns   Bool   true/false
		 */	
		public function formQueryString( $params ) {
			$c = 0; $querystring = "";
			foreach( $params as $field => $value ) {
				if( $c != 0 ) { $querystring .= " AND "; }
				$querystring .= $field . "='" . $value . "'";
				$c += 1;
			}
			return $querystring;
		}


		/**
		 *  Query Database
		 *
		 *  @param $query     String   SQL formated query
		 *
		 *  @returns   Object   MySQL response
		 */		
		public function query( $query ) {
			return $GLOBALS['db']->query( $query );
		}


		/**
		 *  Get Number of Rows
		 *
		 *  @param $table     String   Table name
		 *  @param $searchParams     Array   Keys = field, Values = value
		 *
		 *  @returns   Int   Number of rows
		 */	
		public function numRows( $table, $searchParams ) {
			$c = 0; $querystring = "";
			foreach( $searchParams as $field => $value ) {
				if( $c != 0 ) { $querystring .= " AND "; }
				$querystring .= $field . "='" . $value . "'";
				$c += 1;
			}

			$query = "SELECT id FROM ".$table." WHERE ".$querystring;
			return self::query( $query )->num_rows;
		}
		
		
		/**
		 *  Get array of settings of a particular type
		 *
		 *  @param $settingType     String   Setting type being requested
		 *
		 *  @returns   Array   Values for that setting type
		 *                     key = value for that setting record
		 *                     value = data set for that setting record
		 */	
		public function getSettings( $settingType, $valueMatch = NULL ) {
			/* Set query */
			if( $valueMatch ) {
				$query = "SELECT id,value,data FROM settings WHERE type='".$settingType."' AND data='".$valueMatch."'";
			} else {
				$query = "SELECT id,value,data FROM settings WHERE type='".$settingType."'";
			}
			/* Query Database */
			$result = self::query( $query );
			/* Prepare array to return */
			$return = array();
			if( is_object( $result ) ) {
				foreach( $result as $row ) {
					$return[ $row['value'] ] = $row['data'];
				}
				/* Send response */
				return $return;
			} else {
				return false;
			}			
		}


	}
?>