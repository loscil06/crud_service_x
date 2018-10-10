<?php
const LIMIT_DEFAULT = array('min' => 0, 'max' => 100);
function cleanInputData ($data)
{
	$data = trim($data);
	$data = stripcslashes($data);
	$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
	return $data;
}
function connectDblocal ()
{
	$db = json_decode(file_get_contents( __DIR__ . '/../lib/db_credentials_local.json'));
	$pdo = new PDO("$db->dbtype:host=$db->host;dbname=$db->dbname; charset=$db->charset", $db->user, $db->passwd);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $pdo;
}
function associateKeyValueInSQLStatement ($array, $sql)
{
	foreach ($array as $key => $value) {
		$sql .= "`$key` = '$value', ";
	}
	$sql = substr($sql, 0, -2);
	return $sql;
}
function createArrayParameters ($tablename)
{
	$sql = "SELECT `COLUMN_NAME` 
		FROM `INFORMATION_SCHEMA`.`COLUMNS` 
		WHERE `TABLE_SCHEMA`='service_x_employees' 
		    AND `TABLE_NAME`='$tablename';";
	$result = queryBindExecute($this->pdo, $sql, $null);
	while ($row = $result->fetch()) {
		$parameters[] = array('$row' => ":$row");
	}
	return $parameters;
}
function queryBindExecute ($pdo, $sql, $parameters)
{
	//Theres values to be bound
	if (!empty($parameters)) {
		$query = $pdo->prepare($sql);
		foreach ($parameters as $key => $value) {
			
		}
	} else {//simple query with no values to be bound
		$query = $pdo->prepare($sql);
		$query->execute();
		return $query;
	}
}
//TODO: Implement this
class AdminLogin
{
	private $pdo;
	private $user;
	private $passwd;
	function __construct(string $user, string $passwd, PDO $pdo)
	{
		$this->user = $user;
		$this->passwd = $passwd;
		$this->pdo = $pdo;
	}
}
class GetEmployees
{
	private $pdo;
	private $limit;
	function __construct(array $limit, PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->limit = $limit;
	}
	public function getEmployees()
	{
		//if implementing limits and pagination, replace LIMIT DEFAULT when needed
		$sql = file_get_contents(__DIR__ . '/../lib/select.all.employees.sql');
		$sql .= "LIMIT $this->limit['min'], $this->limit['max']";
		return queryBindExecute($sql, $this->pdo, $parameters);
	}
}
class UpdateInformation
{
	private $id;
	private $pdo;
	private $tablename;
	function __construct(string $tablename, string $id, PDO $pdo)
	{
		$this->id = $id;
		$this->pdo = $pdo;
		$this->tablename = $tablename;
	}
	public function syncInfo ()
	{
		$sql = "UPDATE `service_x_employees`.`$tablename` SET";
		$parameters = createArrayParameters($tablename);
		$sql = associateKeyValueInSQLStatement($parameters, $sql);
		$sql .= "WHERE `id` = $this->id";
	}
}