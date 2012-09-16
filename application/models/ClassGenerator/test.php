<?php

/**
 * This is an example Class
 * 
 * ---- SQL CREATE TABLE STATEMENT: ----
 * CREATE TABLE tbl_example_class(
 * 	example_attribute FLOAT,
 * );
 * -------------------------------------
 * 
 * 
 * 
 * @Table tbl_example_class
 */
class ExampleClass
{

    /**
     * This is an example Attribute.
     * 
     * fasdfasdfasdf
     * 
     * @var float
     * @Column example_attribute
     * @Pkey
     */
    private $exampleAttribute = array();

    /**
     * The SQL table to persist all properties to.
     * 
     * @var string
     */
    const DB_TABLE_TBL_EXAMPLE_CLASS = 'tbl_example_class';

    /**
     * The SQL table colum to persist the attribute $exampleAttribute to.
     * 
     * @var string
     */
    const DB_COL_TBL_EXAMPLE_CLASS_EXAMPLEATTRIBUTE = 'example_attribute';

    /**
     * Singleton: The objects indexed by their primary keys.
     * 
     * @var ExampleClass
     */
    public $instances = null;

    /**
     * Returns a new instance of ExampleClass.
     * 
     * @return ExampleClass
     */
    public static function createInstance(Zend_Db_Adapter_Abstract $parZendDbObj)
    {
        return new self($parZendDbObj);
    }

    /**
     * Loads the entry from the database.
     */
    public static function load(Zend_Db_Adapter_Abstract $zendDbObj, float $parExampleAttribute)
    {
        if(!isset(self::$instance[$parExampleAttribute])){
        	$table = self::DB_TABLE_TBL_EXAMPLE_CLASS;
        	$select = $this->zendDbObj->select($table, '*');
        	$select->where(self::DB_TABLE_TBL_EXAMPLE_CLASS.'.'.self::DB_COL_TBL_EXAMPLE_CLASS_EXAMPLEATTRIBUTE.'=?', $this->exampleAttribute);
        
        	self::$instance = new self($parZendDbObj);
        }
        
        return self::$instance;
    }

    private function __construct(Zend_Db_Adapter_Abstract $parZendDbObj)
    {
        $this->zendDbObj = $parZendDbObj;
    }

    /**
     * Deletes the entry from the database and clears the automatically set values.
     */
    public function delete()
    {
        $table = self::DB_TABLE_TBL_EXAMPLE_CLASS;
        $where = $this->zendDbObj->quoteInto(self::DB_TABLE_TBL_EXAMPLE_CLASS.'.'.self::DB_COL_TBL_EXAMPLE_CLASS_EXAMPLEATTRIBUTE.'=?', $this->exampleAttribute);
        $rowsAffected = $this->zendDbObj->delete($table, $where);
        if($rowsAffected<>1){
        	throw new Exception('Delete not successful. "'.$rowsAffected.'" rows were deleted.');
        }
        unset(self::$instance[$parExampleAttribute]);
    }

    /**
     * Persists the object to the database, creates a new entry if not persistet yet,
     * else updates the existing entry.
     */
    public function save()
    {
        if($this->isSaved()) {
        	$this->saveUpdate();
        } else {
        	$this->saveNew();
        }
    }

    /**
     * Creates a new database entry representing this objects persistence.
     */
    public function saveNew()
    {
        $table = self::DB_TABLE_TBL_EXAMPLE_CLASS;
        $data = $this->toArray();
        $rowsAffected = $this->zendDbObj->insert($table, $data);
        if($rowsAffected<>1){
        	throw new Exception('Save new not successful. "'.$rowsAffected.'" rows were inserted.');
        }
        //Save to instances cache.
        self::$instance[$parExampleAttribute] = $this;
    }

    /**
     * Updates the database entry containing the persistence data of this object.
     */
    public function saveUpdate()
    {
        $table = self::DB_TABLE_TBL_EXAMPLE_CLASS;
        $data = $this->toArray();
        $where = $this->zendDbObj->quoteInto(self::DB_TABLE_TBL_EXAMPLE_CLASS.'.'.self::DB_COL_TBL_EXAMPLE_CLASS_EXAMPLEATTRIBUTE.'=?', $this->exampleAttribute);
        $rowsAffected = $this->zendDbObj->update($table, $data, $where);
        if($rowsAffected<>1){
        	throw new Exception('Save new not successful. "'.$rowsAffected.'" rows were inserted.');
        }
    }

    /**
     * Returns the array representation of the object using the table column names as
     * keys.
     */
    public function toArray()
    {
        return array(
        	self::DB_COL_TBL_EXAMPLE_CLASS_EXAMPLEATTRIBUTE' => $this->exampleAttribute,
        );
    }


}

