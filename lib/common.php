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
	$result = queryBindExecute($this->pdo, $sql, $null, $null);
	while ($row = $result->fetch()) {
		$parameters[] = array('$row' => ":$row");
	}
	return $parameters;
}
function queryBindExecute ($pdo, $sql, $parameters, $values)
{
	//_POST[] indexes MUST have the same names as the database's columns, else this function  wont work!

	//If theres values to be bound
	if (!empty($parameters)) {
		$query = $pdo->prepare($sql);
		foreach ($parameters as $key => $subst) {
			$query->bindValue($key, $values[$key]);
		}
	} else {//simple query with no values to be bound
		$query = $pdo->prepare($sql);
		$query->execute();
		return $query;
	}
}
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
	public function check ()
	{
		$stmt = queryBindExecute($this->pdo, "SELECT `username`, `passwd` FROM `administrators` WHERE `username` = '$this->username'", $null, $null);
		$result = $stmt->fetch();
		if (password_verify($this->passwd, $result['passwd'])) {
			return true;
		} else {
			return false;
		}
	}
}
class GetEmployees //Read
{
	private $pdo;
	private $limit;
	function __construct(array $limit, PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->limit = $limit;
	}
	public function all ($option) //Option: determines if there'll be limit
	{
		//if implementing limits and pagination, replace LIMIT DEFAULT when needed
		$sql = file_get_contents(__DIR__ . '/../lib/select.all.employees.sql');
		if ($option) {
			$sql .= "LIMIT $this->limit['min'], $this->limit['max']";
		}
		return queryBindExecute($sql, $this->pdo, $null, $null);
	}
}
class UpdateInformation //Update
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
	public function syncInfo ($values)
	{
		$parameters = createArrayParameters($tablename);
		$sql = associateKeyValueInSQLStatement($parameters, "UPDATE `service_x_employees`.`$this->tablename` SET");
		$sql .= "WHERE `id` = $this->id";
		queryBindExecute($this->pdo, $sql, $parameters, $values);
	}
}
class DeleteInfo extends UpdateInformation //Delete
{
	public function syncInfo($values)
	{
		$sql = "DELETE FROM `service_x_employees`.`employees` WHERE `id` = '$this->id';";
		queryBindExecute($this->pdo, $sql, $null, $null);
	}
}
class NewInfo //Create
{
	private $pdo;
	private $tablename;
	public function __construct (array $values, PDO $pdo, string $tablename)
	{
		$this->pdo = $pdo;
		$this->tablename = $tablename;
	}
	public function insertNew ($values)
	{
		$parameters = createArrayParameters($tablename);
		$sql = (associateKeyValueInSQLStatement($parameters, "INSERT INTO `service_x_employees`.`$this->tablename` SET "));
		queryBindExecute($this->pdo, $sql, $parameters, $values);
	}
}