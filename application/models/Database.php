<?php
/**
 * Manages database connections
 *
 * Author : Ajay Sreedhar
 */

/**
 * Implements database connectivity using PHP data objects
 *
 * Class Application_Model_Database
 */
class Application_Model_Database {

    /**
     * Database connection driver
     *
     * @var string
     */
    private $_dbDriver;

    /**
     * Database name
     *
     * @var string
     */
    private $_dbName;

    /**
     * Database host
     *
     * @var string
     */
    private $_dbHost;

    /**
     * Database username
     *
     * @var string
     */
    private $_dbUser;

    /**
     * Database password
     *
     * @var string
     */
    private $_dbPassword;

    /**
     * Table prefix
     *
     * @var string
     */
    private $_tablePrefix;

    /**
     * Description of the last error occurred
     *
     * @var string
     */
    private $_dbError;

    /**
     * Last query prepared
     *
     * @var string
     */
    private $_query;

    /**
     * Number of rows affected by the last query
     *
     * @var int
     */
    private $_affectedRows = 0;

    /**
     * Flag to check whether function getTableName is to be used
     *
     * The value of this flag can be changed using enableTablePrefix and
     * disableTablePrefix functions
     *
     * @var bool
     */
    private $_usePrefix = true;

    /**
     * @var PDOException
     */
    private $_dbException;

    /**
     * Object of PDO class
     *
     * @var PDO
     */
    protected $_dbHandle;

    /**
     * Loads default database parameters
     */
    private function _setDefaultDbParams()
    {
        $this->_dbDriver = "mysql";
        $this->_dbHost = "localhost";
        $this->_dbUser = "root";
        $this->_dbPassword = "";
        $this->_dbName = "web_giripuram";
    }

    /**
     * Prepares PDO data source name
     *
     * @return string
     */
    private function _dsn() {
        return $this->_dbDriver . ':dbname=' . $this->_dbName . ';host=' . $this->_dbHost;
    }

    /**
     * Prepare placeholder text from the key
     *
     * @param string $key
     * @return string
     */
    private function _placeholder( $key ) {
        return ":{$key}";
    }

    /**
     * Get strings in quotes
     *
     * $string is returned without any modifications
     * if it is a number
     *
     * @param string $string
     * @return string
     */
    private function _getInQuotes( $string ) {
        if ( is_numeric( $string ) )
            return $string;

        return "'{$string}'";
    }

    /**
     * Prepare where condition
     *
     * Parameter $where expects an array of which
     * array index is the table column name and
     * value at that index is the value to be compared
     *
     * @param array|string $where
     * @return string
     */
    private function _where( $where = null ) {
        if ( $where === null ) {
            return "1";

        } elseif ( is_string( $where ) ) {
            return $where;
        }

        $arrayWhere = array();

        foreach( $where as $key => $value ) {
            $condition = "`{$key}`=" . $this->_getInQuotes( $value );
            array_push( $arrayWhere, $condition );
        }

        if ( count( $arrayWhere ) <= 0 )
            return "1";

        return implode( " AND ", $arrayWhere );
    }

    /**
     * Prepare attribute list
     *
     * Parameter $attributes expects an array of column names
     * to be selected from a table
     *
     * @param array|string $attributes
     * @return string
     */
    private function _tableAttributes( $attributes = array() ) {
        if ( is_string( $attributes ) )
            return $attributes;

        elseif ( is_array( $attributes ) )
            return implode( ", ", $attributes );
        else
            return "*";
    }

    /**
     * Class constructor
     *
     * If $args is present, database parameters are used from $args
     * else _setDefaultDbParams is called
     *
     * @param array $args
     */
    public function __construct( $args = null ) {
        if ( $args != null && is_array( $args ) ) {
            $this->_dbDriver = $args['driver'];
            $this->_dbName   =   $args['dbname'];
            $this->_dbHost   =  $args['host'];
            $this->_dbUser   = $args['user'];
            $this->_dbPassword = $args['password'];
            if(isset($_SESSION['table_prefix']))
                self::setTablePrefix( $_SESSION['table_prefix'] );
            else
                self::setTablePrefix( "adm" );

        } else {
            $this->_setDefaultDbParams();
            if(isset($_SESSION['table_prefix']))
                self::setTablePrefix( $_SESSION['table_prefix'] );
            else
                self::setTablePrefix( "adm" );
        }

        try {
            $this->_dbHandle = new PDO( $this->_dsn(), $this->_dbUser, $this->_dbPassword );

            $this->_dbHandle->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        } catch( PDOException $ex ) {
            $this->_dbException = $ex;
            exit( $ex->getMessage() );
        }
    }

    /**
     * Enable usage of table prefix
     *
     * If table prefix usage is enabled, getTableName function need not be
     * called manually to prepare table name before passing it to any function
     * that accepts table name as one parameter
     */
    public function enableTablePrefix() {
        $this->_usePrefix = true;
    }

    /**
     * Disable usage of table prefix
     *
     * If table prefix usage is disabled, getTableName function must be
     * called manually to prepare table name before passing it any function
     * that accepts table name as one parameter
     */
    public function disableTablePrefix() {
        $this->_usePrefix = false;
    }

    /**
     * Get table name with prefix
     *
     * The table prefix is prepended only if _usePrefix flag is set to true
     *
     * @param string $table
     * @return string
     */
    public function getTableName( $table, $type=true ) {
        return   ( $type === true ) ? $this->_tablePrefix . '_' . $table : $table  ;
    }

    /**
     * Equivalent to getTableName method
     *
     * @param string $table
     * @return string
     */
    public function table( $table, $type=true ) {
        return $this->getTableName( $table, $type );
    }

    /**
     * Execute a hardcoded query
     *
     * After execution, _affectedRows variable is updated
     *
     * @param string $query
     * @return bool
     */
    public function execute( $query ) {
        $this->_query = $query;

        try {
            $this->_affectedRows = $this->_dbHandle->exec( $this->_query );
        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Fetch data from a database
     *
     * Return type depends on $returnType parameter
     *
     * @param string $query
     * @param array $bind
     *
     * @return PDOStatement
     */
    public function query( $query, $bind = null ) {
        $this->_query = $query;

        try {
            $stHandle = $this->_dbHandle->prepare( $this->_query );

            $stHandle->execute( $bind );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            return null;
        }

        return $stHandle;
    }

    /**
     * Fetch next row from a PDOStatement object
     *
     * @param PDOStatement $statement
     * @return array
     */
    public function fetch( PDOStatement $statement ) {
        try {
            return $statement->fetch( PDO::FETCH_BOTH );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            return null;
        }
    }

    /**
     * Get the number of rows in a PDOStatement object
     *
     * @param PDOStatement $statement
     * @return int
     */
    public function getRowCount( PDOStatement $statement ) {
        try {
            return $statement->rowCount();
        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            return -1;
        }
    }

    /**
     * Fetch one row from a table in the database
     *
     * Result is an array indexed by the table column names
     *
     * @param string $table
     * @param array|string $attributes
     * @param array|string $where
     *
     * @return mixed|null
     */
    public function fetchOneRow( $table, $attributes = "*", $where = null,$orderBy="" ) {
        $this->_query = 'SELECT ' . $this->_tableAttributes( $attributes ) .
            ' FROM ' . $this->getTableName( $table ) .
            ' WHERE ' . $this->_where( $where );

        if(!empty($orderBy))
            $this->_query.=" ORDER BY ".$orderBy;

        $stHandle = $this->query( $this->_query );

        return $this->fetch( $stHandle );
    }

    /**
     * Get the maximum value of a table attribute
     *
     * @param string $table
     * @param string $attribute
     * @return int|null
     */
    public function getMaxId( $table, $attribute , $table_prefix_flag=true)
    {
        $this->_query = "SELECT MAX(`{$attribute}`) AS 'max' FROM " . $this->getTableName( $table ,$table_prefix_flag);

        $stHandle    = $this->query( $this->_query );
        $result = $this->fetch( $stHandle );

        if ( isset( $result['max'] ) && is_numeric( $result['max'] ) )
            return $result['max'];
        else
            return 0;
    }

    /**
     * Get the next value of a table attribute
     *
     * @param string $table
     * @param string $attribute
     *
     * @return int
     */

    public function getNextId( $table, $attribute , $table_prefix_flag=true) {

        $currentId = $this->getMaxId( $table, $attribute,$table_prefix_flag);

        if ( is_numeric( $currentId ) )
            return $currentId + 1;

        return $currentId;
    }

    /**
     * Insert a new row into a table in the database
     *
     * Parameter $bind expects an array of values indexed by column names
     *
     * @param string $table
     * @param array $bind
     *
     * @return bool
     */
    public function insert( $table, $bind, $table_prefix_flag=true ) {
        if ( !is_array( $bind ) || $bind === null ) {
            $this->_dbError = "Arguments for the function dbInsert is not an array";
            return false;
        }

        $strAttr 	= "("; /* attributes enclosed in brackets */
        $strValues	= "("; /* attribute values enclosed in brackets */

        $totalCount = count ( $bind );
        $indexCount = 1;

        foreach ( $bind as $attrName => $attrValue) {
            if ( $totalCount == $indexCount) {
                $strAttr 	.= "`{$attrName}`)";
                $strValues	.= $this->_placeholder( $attrName ) . ")";
            } else {
                $strAttr 	.= "`{$attrName}`, ";
                $strValues	.= $this->_placeholder( $attrName ) . ", ";
            }
            $indexCount++;
        }

        if($table_prefix_flag)
            $tableName = $this->getTableName( $table );
        else
            $tableName =  $table ;

        $this->_query = "INSERT INTO {$tableName} {$strAttr} VALUES {$strValues}";

        try {
            $stHandle = $this->_dbHandle->prepare( $this->_query );
            $stat = $stHandle->execute( $bind );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            echo $this->_dbError = $ex->getMessage();die();
            error_log("\n".date("Y-m-d H:m:s").$ex->getMessage()." ".$this->_query, 3, "error_log/my-errors.log");
            return false;
        }

        return $stat;
    }

    /**
     * Update an existing row in a table in the database
     *
     * Parameter $bind expects an array of values indexed by column names
     *
     * @param string $table
     * @param array $bind
     * @param array|string $where
     *
     * @return bool
     */
    public function update( $table, $bind, $where, $table_prefix_flag=true ) {
        if ( !is_array( $bind ) ) {
            $this->_dbError = "Arguments for the function dbUpdate is not an array";
            return false;
        }

        $setList = "";

        $totalCount = count ( $bind );
        $indexCount = 1;

        foreach ( $bind as $attrName => $attrValue) {
            if ( $totalCount == $indexCount) {
                $strAttr 	= "`{$attrName}`";
                $strValues	= $this->_placeholder( $attrName );

            } else {
                $strAttr 	= "`{$attrName}`";
                $strValues	= $this->_placeholder( $attrName ) . ", ";
            }

            $setList .= "{$strAttr}={$strValues}";
            $indexCount++;
        }

        if($table_prefix_flag)
            $tableName = $this->getTableName( $table );
        else
            $tableName =  $table ;
        $this->_query = "UPDATE {$tableName} SET {$setList} WHERE " . $this->_where( $where );

        try {
            $stHandle = $this->_dbHandle->prepare( $this->_query );
            $stat = $stHandle->execute( $bind );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            error_log("\n".date("Y-m-d H:m:s").$ex->getMessage()." ".$this->_query, 3, "error_log/my-errors.log");
            return false;
        }

        return $stat;
    }

    public function updateDirect( $table, $bind, $where=null, $table_prefix_flag=true ) {
        if ( !is_array( $bind ) ) {
            $this->_dbError = "Arguments for the function dbUpdate is not an array";
            return false;
        }

        $setList = "";

        $totalCount = count ( $bind );
        $indexCount = 1;

        foreach ( $bind as $attrName => $attrValue) {
            if ( $totalCount == $indexCount) {
                $strAttr 	= "`{$attrName}`";
                $strValues	= $this->_placeholder( $attrName );

            } else {
                $strAttr 	= "`{$attrName}`";
                $strValues	= $this->_placeholder( $attrName ) . ", ";
            }

            $setList .= "{$strAttr}={$strValues}";
            $indexCount++;
        }

        if($table_prefix_flag)
            $tableName = $this->getTableName( $table );
        else
            $tableName =  $table ;
        if(empty($where))
            $this->_query = "UPDATE {$tableName} SET {$setList} ";
        else
            $this->_query = "UPDATE {$tableName} SET {$setList} WHERE " . $where ;

        try {
            $stHandle = $this->_dbHandle->prepare( $this->_query );
            $stat = $stHandle->execute( $bind );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
            $this->_dbError = $ex->getMessage();
            error_log("\n".date("Y-m-d H:m:s").$ex->getMessage()." ".$this->_query, 3, "error_log/my-errors.log");
            return false;
        }

        return $stat;
    }

    /**
     * Delete a row from a table in the database
     *
     * @param string $table
     * @param array|string $where
     *
     * @return bool
     */
    public function delete( $table, $where = null , $table_prefix_flag=true) {
        if($table_prefix_flag)
            $tableName = $this->getTableName( $table );
        else
            $tableName =  $table ;

         $query = 'DELETE FROM ' . $tableName .' WHERE ' . $this->_where( $where );
        try {
            $this->_affectedRows = $this->_dbHandle->exec( $query );

        } catch ( PDOException $ex ) {
            $this->_dbException = $ex;
           $this->_dbError = $ex->getMessage();
            error_log("\n".date("Y-m-d H:m:s").$ex->getMessage()." ".$this->_query, 3, "error_log/my-errors.log");
            return false;
        }

        return true;
    }

    /**
     * Get last error message
     *
     * @return string
     */
    public function getDbError() {
        return $this->_dbError;
    }

    /**
     * Get last query prepared
     *
     * @return string
     */
    public function getQuery() {
        return $this->_query;
    }

    /**
     * Get the number of affected rows by the last query
     *
     * @return int
     */
    public function getAffectedRows() {
        return $this->_affectedRows;
    }

    /**
     * Set the table prefix
     *
     * @param string $tablePrefix
     */
    public function setTablePrefix( $tablePrefix ) {

        $this->_tablePrefix = $tablePrefix;
    }

    /**
     * @return PDOException
     */
    public function getDbException() {
        return $this->_dbException;
    }
}
