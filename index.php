<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ERROR|E_PARSE);

//defining constants
define('DATABASE','sg948');
define('USERNAME','sg948');
define('PASSWORD','HfwrZHvX');
define('CONNECTION','sql1.njit.edu');

class dbConn{
    //variable to hold connection object
    protected static $db;
    
    private function __construct() {
        try {
            // assign PDO object to db variable
            self::$db = new PDO('mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            //Output error
            echo "Connection Error: " . $e->getMessage();
        }
    }
    
    public static function getConnection() {
        //creating new connection if no connection object exists
        if (!self::$db) {
            //new connection object
            new dbConn();
        }
        //return connection
        return self::$db;
    }
}
class collection {
protected $html;
    static public function create() {
      $model = new static::$modelName;
      return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet =  $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    protected static $modelName = 'account';
}
class todos extends collection {
    protected static $modelName = 'todo';
}
class model {

protected $tableName;
//saving records function
public function save()
    
    {
        if ($this->id != '') {
            $sql = $this->update($this->id);
        } else {
           $sql = $this->insert();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $array = get_object_vars($this);
        foreach (array_flip($array) as $key=>$value){
            $statement->bindParam(":$value", $this->$value);
        }
        $statement->execute();
    }
    //inserting records function
    private function insert() {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $columnString = implode(',', array_flip($array));
        $valueString = ':'.implode(',:', array_flip($array));
        print_r($columnString);
        $sql =  'INSERT INTO '.$tableName.' ('.$columnString.') VALUES ('.$valueString.')';
        return $sql;
    }
    //updating records function
    private function update($id) {
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $array = get_object_vars($this);
        $comma = " ";
        $sql = 'UPDATE '.$tableName.' SET ';
        foreach ($array as $key=>$value){
            if(! empty($value)) {
                $sql .= $comma . $key . ' = "'. $value .'"';
                $comma = ", ";
            }
        }
        $sql .= ' WHERE id='.$id;
        return $sql;
    }
    //deleting records function
    public function delete($id) {
        $db = dbConn::getConnection();
        $modelName=get_called_class();
        $tableName = $modelName::getTablename();
        $sql = 'DELETE FROM '.$tableName.' WHERE id='.$id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
}
    
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public static function getTablename(){
    $tableName='accounts';
    return $tableName;
    }
}

class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public static function getTablename(){
    $tableName='todos';
    return $tableName;
    }
}

echo "<h1><center>Accounts Table</center></h1>";
echo "<h2>Search Accounts Table</h2>";
$records = accounts::findAll();
 //display records in html table  
  $html = '<table border = 5><tbody>'; 
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }     
    $html .= '</tr>';
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br>' . '</td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
    print_r($html);
  
    echo "<h2>Search Account Table by its unique id</h2>";
   $record = accounts::findOne(11);
  
  $html = '<table border = 3><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';   
    
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';          
    }
    $html .= '</tbody></table>';
    
    print_r($html);
//inserting a record in accounts table
echo "<h2>Inserting a Record</h2>";
$record = new account();
$record->email="sangp@gmail.com";
$record->fname="sanch";
$record->lname="gupt";
$record->phone="678-908-6667";
$record->birthday="2000-01-08";
$record->gender="male";
$record->password="7890";
$record->save();
$records = accounts::findAll();
$html = '<table border = 5><tbody>';
$html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</tbody></table>';
echo "<h3>When Insertion is made</h3>";
print_r($html);
//deleting a record from todos table
echo "<h4>When Deleting A Record</h4>";
$record= new account();
$id=6;
$record->delete($id);
echo '<h5>Record of id: '.$id.' is deleted</h5>';

$record = accounts::findAll();

$html = '<table border = 5><tbody>';
$html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';      
    }
    $html .= '</tbody></table>';
echo "<h3>After Deleting</h3>";
print_r($html);
//updating a record in accounts table
echo "<h2>Updating a Record with id = 4</h2>";
$id=4;
$record = new account();
$record->id=$id;
$record->fname="Jack";
$record->lname="Shaw";
$record->gender="male";
$record->save();
$record = accounts::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 5><tbody>'; 
$html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';            
    }
    $html .= '</tbody></table>';
 
 print_r($html);

 echo"<h2><center>Todos Table</center></h2>";
 echo "<h3>Searching for all records in todos table</h3>";
 $records = todos::findAll();
 //displaying records in html table  
  $html = '<table border = 5><tbody>';
  $html .= '<tr>';
    foreach($records[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';

    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';      
    }
    $html .= '</tbody></table>';
    print_r($html);
//searching a record by its unique id
    echo "<h3>Searching by unique id</h3>";
    echo "<h4>Searching a record by its unique id 2</h4>";
  $record = todos::findOne(2);  
  $html = '<table border = 5><tbody>';
  $html .= '<tr>';
    
    foreach($record[0]as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
       
    foreach($record as $key=>$value)
    {
       $html .= '<tr>';
        
       foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
      
    }
    $html .= '</tbody></table>';
    
    print_r($html);
//inserting a record in todos table
   echo "<h3>Insert One Record</h3>";
        $record = new todo();
        $record->owneremail="sg@njit.edu";
        $record->ownerid=90;
        $record->createddate="11-09-2017";
        $record->duedate="11-13-2017";
        $record->message="New Data Inserted";
        $record->isdone=1;
        $record->save();
        $records = todos::findAll();
        echo"<h2>After Inserting</h2>";
 
     $html = '<table border = 5><tbody>';
     $html .= '<tr>';
      foreach($records[0] as $key=>$value)
         {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($records as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';           
    }
    $html .= '</tbody></table>';

print_r($html);
//deleting a record from todos table
echo "<h3>Delete One Record</h3>";
$record= new todo();
$id=1;
$record->delete($id);
echo '<h3>Record with id: '.$id.' is deleted</h3>';

$record = todos::findAll();

$html = '<table border = 5><tbody>';  
$html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';
      
    }
    
    $html .= '</tbody></table>';
echo "<h4>After Deleting</h4>";
print_r($html);

echo "<h4>Update One Record</h4>";
$id=4;
$record = new todo();
$record->id=$id;
$record->owneremail="sgupta@gmail.com";
$record->ownerid="90";
$record->createddate="2017-01-02 00:00:00";
$record->duedate="2017-09-06 00:00:00";
$record->message="HELLO";
$record->isdone="1";
$record->save();
$record = todos::findAll();
echo "<h3>Record update with id: ".$id."</h3>";
        
$html = '<table border = 5><tbody>';
  
  $html .= '<tr>';
    
    foreach($record[0] as $key=>$value)
        {
            $html .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       
    $html .= '</tr>';    
    
    foreach($record as $key=>$value)
    {
        $html .= '<tr>';
        
        foreach($value as $key2=>$value2)
        {
            $html .= '<td>' . htmlspecialchars($value2) . '<br></td>';
        }
        $html .= '</tr>';            
    }
    $html .= '</tbody></table>';
 
 print_r($html);
?>