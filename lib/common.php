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
function prepareAndExecute ($sql, $pdo)
{
	$query = $pdo->prepare($sql);
	$query->execute();
	return $query;
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
		$sql = file_get_contents(__DIR__ . '/../lib/select.all.employees.sql');
		$sql .= "LIMIT $this->limit['min'], $this->limit['max']";
		return prepareAndExecute($sql, $this->pdo);
	}
}
class UpdateEmployee
{

}