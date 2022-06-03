<?php
class Frontdb {

	private $pdo, $sth, $sql = '';

	function __construct(){

		$opt = [
						PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
						PDO::ATTR_PERSISTENT=>true,
						PDO::MYSQL_ATTR_INIT_COMMAND=>"SET lc_time_names='ru_RU'"
					];

		if ( file_exists('conf/dbconf.ini') ){

			$dbconf = parse_ini_file('conf/dbconf.ini', true, INI_SCANNER_RAW);

			try {
				$this->pdo = new PDO("mysql:dbname={$dbconf['dbname']};host=localhost;charset=utf8", $dbconf['username'], $dbconf['password'], $opt);
			}catch (PDOException $e){
				header('HTTP/1.1 500 Internal Server Error');
				exit($e);
			}

		} else {
			header('Location: /init');
		}

	}

	function select($data = []){

		$this->sth->execute($data);
		return $this->sth->fetch();

	}

	function selectAll($data = []){

		$this->sth->execute( $data );
		return $this->sth->fetchAll();

	}

	function exec($data = []){

		$this->sth->execute( $data );
		return $this->pdo->lastInsertId();

	}

	function __toString(){

		return $this->sth->queryString;

	}

	function __set($k, $v){

		$this->sql = $v;
		$this->sth = $this->pdo->prepare($this->sql);
	
	}

	function showError(){

		print_r( $this->sth->errorInfo() );

	}

}