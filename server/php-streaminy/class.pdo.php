<?php


class DB
{
	private $Host;
	private $DBName;
	private $DBUser;
	private $DBPassword;
	private $DBPort;
	private $pdo;
	private $sQuery;
	private $bConnected = false;
	private $log;
	private $parameters;
	public $rowCount = 0;
	public $columnCount = 0;
	public $querycount = 0;

	public function __construct($Host, $DBName, $DBUser, $DBPassword, $DBPort = 7999)
	{
		$this->log = new Log();
		$this->Host = $Host;
		$this->DBName = $DBName;
		$this->DBUser = $DBUser;
		$this->DBPassword = $DBPassword;
		$this->DBPort = $DBPort;
		$this->Connect();
		$this->parameters = [];
	}

	public function __destruct()
	{
		if ($this->pdo) {
			$this->CloseConnection();
		}
	}

	private function Connect()
	{
		try {
			$this->pdo = new PDO('mysql:dbname=' . $this->DBName . ';host=' . $this->Host . ';port=' . $this->DBPort . ';charset=utf8', $this->DBUser, $this->DBPassword, [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true]);
			$this->bConnected = true;
		}
		catch (PDOException $e) {
			echo $this->ExceptionLog($e->getMessage());
			exit();
		}
	}

	public function CloseConnection()
	{
		$this->pdo = NULL;
	}

	private function Init($query, $parameters = '')
	{
		if (!$this->bConnected) {
			$this->Connect();
		}

		try {
			$this->parameters = $parameters;
			$this->sQuery = $this->pdo->prepare($this->BuildParams($query, $this->parameters));

			if (!empty($this->parameters)) {
				if (array_key_exists(0, $parameters)) {
					$parametersType = true;
					array_unshift($this->parameters, '');
					unset($this->parameters[0]);
				}
				else {
					$parametersType = false;
				}

				foreach ($this->parameters as $column => $value) {
					$this->sQuery->bindParam($parametersType ? intval($column) : ':' . $column, $this->parameters[$column]);
				}
			}

			$this->succes = $this->sQuery->execute();
			$this->querycount++;
		}
		catch (PDOException $e) {
			echo $this->ExceptionLog($e->getMessage(), $this->BuildParams($query));
			exit();
		}

		$this->parameters = [];
	}

	private function BuildParams($query, $params = NULL)
	{
		if (!empty($params)) {
			$rawStatement = explode(' ', $query);

			foreach ($rawStatement as $value) {
				if (strtolower($value) == 'in') {
					return str_replace('(?)', '(' . implode(',', array_fill(0, count($params), '?')) . ')', $query);
				}
			}
		}

		return $query;
	}

	public function query($query, $params = NULL, $fetchmode = PDO::FETCH_ASSOC)
	{
		$query = trim($query);
		$rawStatement = explode(' ', $query);
		$this->Init($query, $params);
		$statement = strtolower($rawStatement[0]);
		if (($statement === 'select') || ($statement === 'show')) {
			return $this->sQuery->fetchAll($fetchmode);
		}
		else if (($statement === 'insert') || ($statement === 'update') || ($statement === 'delete')) {
			return $this->sQuery->rowCount();
		}
		else {
			return NULL;
		}
	}

	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	}

	public function column($query, $params = NULL)
	{
		$this->Init($query, $params);
		$resultColumn = $this->sQuery->fetchAll(PDO::FETCH_COLUMN);
		$this->rowCount = $this->sQuery->rowCount();
		$this->columnCount = $this->sQuery->columnCount();
		$this->sQuery->closeCursor();
		return $resultColumn;
	}

	public function row($query, $params = NULL, $fetchmode = PDO::FETCH_ASSOC)
	{
		$this->Init($query, $params);
		$resultRow = $this->sQuery->fetch($fetchmode);
		$this->rowCount = $this->sQuery->rowCount();
		$this->columnCount = $this->sQuery->columnCount();
		$this->sQuery->closeCursor();
		return $resultRow;
	}

	public function single($query, $params = NULL)
	{
		$this->Init($query, $params);
		return $this->sQuery->fetchColumn();
	}

	private function ExceptionLog($message, $sql = '')
	{
		$exception = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= '<br /> You can find the error back in the log.';

		if (!empty($sql)) {
			$message .= "\r\n" . 'Raw SQL : ' . $sql;
		}

		$this->log->write($message, $this->DBName . md5($this->DBPassword));
		header('HTTP/1.1 500 Internal Server Error');
		header('Status: 500 Internal Server Error');
		return $exception;
	}
}

require dirname(__FILE__) . '/class.pdo-log.php';

?>