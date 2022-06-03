<?php
class InitController extends Master {

	function defaultAction(){

		$data['notice'] = '';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if ( $this->checkForm(['dbname','dbuser','dbpass','dbpass_confirm','user','email','pass','pass_confirm','sbtn']) ){
				if ( !preg_match("/\W/i", $_POST['user']) ){
					if ( $_POST['dbpass'] === $_POST['dbpass_confirm'] && $_POST['pass'] === $_POST['pass_confirm'] ){

								$this->dbinit();
								$this->addAdmin();
								$this->clear();

								header('Location: /user/login');

					} else { $data['notice'] = 'Пароли не совпадают'; }
				} else { $data['notice'] = 'Введены недопустимые символы в имени пользователя'; }
			} else { $data['notice'] = 'Заполните все поля'; }
		}

		$this->outTemplate('init', $data);
	
	}

	private function dbinit(){

		try{
			$pdo = new PDO("mysql:dbname={$_POST['dbname']};host=localhost", $_POST['dbuser'], $_POST['dbpass']);
		}catch(PDOException $e){
			exit('hello');
		}

		$sql = file_get_contents('smengine.sql');
		$pdo->exec($sql);

		$settings['dbname'] = $_POST['dbname'];
		$settings['username'] = $_POST['dbuser'];
		$settings['password'] = $_POST['dbpass'];

		$tmp = '';

		foreach ($settings as $k => $v){
			$tmp .= "{$k} = {$v}\n";
		}

		mkdir('conf');
		file_put_contents('conf/dbconf.ini', $tmp);

	}

	private function addAdmin(){

		$user = new UDB();
		
		$data[':name'] = trim( $_POST['user'] );
		$data[':role'] = 'admin';
		$data[':email'] = trim( $_POST['email'] );
		$data[':pass'] = password_hash($_POST['pass'], PASSWORD_BCRYPT, ['cost'=>12]);

		$user->add($data);

	}

	private function clear(){

		unlink(realpath(__FILE__));

	}

}