<?php
class UserController extends Master {

	private $user = false;

	function __construct(){
	
		parent::__construct();
		$this->user = new UDB();
	
	}
	
	function defaultAction($p){

		if ( isset($p[0]) ){
	
			$data = $this->user->uget( $p[0] );

			$data['ulink'] = $this->ulink();

			if ( isset($data['id']) ){
				
				if ( $this->uname == $data['name'] ){
					$data['ulink'] = '<a href="/user/logout">Выйти</a>';
				}

				$this->outTemplate('profile', $data);
			
			} else { $this->err('404'); }
		} else { $this->err('404'); }
	
	}

	function registerAction(){

		$data = $this->genCap();
		$data['notice'] = '';
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if ( $this->checkForm(['name','pass','pass_confirm','email','cap','capcheck','sbtn']) ){
				if ( !preg_match("/\W/i", $_POST['name']) ){
					if ( !isset($this->user->userExists($_POST['name'])['id']) ){
						if ( $_POST['pass'] === $_POST['pass_confirm'] ){
							if ( md5($_POST['cap']) == $_POST['capcheck'] ){

								$data2[':name'] = trim( $_POST['name'] );
								$data2[':email'] = trim( $_POST['email'] );
								$data2[':pass'] = password_hash($_POST['pass'], PASSWORD_BCRYPT, ['cost'=>12]);
								$data2[':role'] = 'user';

								$this->user->add($data2);
								header('Location: /user/login');

							} else { $data['notice'] = 'Код не совпадает'; }
						} else { $data['notice'] = 'Пароли не совпадают'; }
					} else { $data['notice'] = 'Пользователь с таким именем уже существует'; }
				} else { $data['notice'] = 'Введены недопустимые символы в имени пользователя'; }
			} else { $data['notice'] = 'Заполните все поля'; }
		}
		
		$this->outTemplate('register', $data);
	
	}

	function loginAction(){

		if ( $this->auth ){
			header('Location: /user/'.$this->uname);
		}

		$data['notice'] = '';

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			if ( $this->checkForm(['name','pass','sbtn']) ){
				
				$name = trim( $_POST['name'] );
				$pass = trim( $_POST['pass'] );
				$res = $this->user->userExists( $name );

				if ( isset($res['id']) ){
					if ( password_verify($pass, $res['password']) ){

						$this->user->visitUpd( $res['id'] );
						
						$_SESSION['id'] = $res['id'];
						$_SESSION['ip'] = md5( $_SERVER['REMOTE_ADDR'] );
						$_SESSION['name'] = $res['name'];
						$_SESSION['role'] = $res['role'];

						$url = $res['role'] == 'user' ? '/' : '/admin';

						header('Location: '.$url);
					
					} else { $data['notice'] = 'Логин или пароль введены неправильно'; }
				} else { $data['notice'] = 'Логин или пароль введены неправильно'; }
			} else { $data['notice'] = 'Заполните все поля'; }
		}

		$this->outTemplate('login', $data);

	}

	function logoutAction(){

		session_destroy();
		header('Location: /user/login');

	}

	private function genCap(){

		$width = 120; // Ширина изображения
		$height = 50; // Высота изображения
		$font_size = 16; // Размер шрифта
		$let_amount = 5; // Количество символов, которые нужно набрать
		$fon_let_amount = 30; // Количество символов на фоне
		$font = realpath('fonts/Roboto-Regular.ttf'); // Путь к шрифту

		//набор символов
		$letters = array("a","b","c","d","e","f","g","h");
		//цвета
		$colors = array("90","110","130","150","170","190","210");

		$src = imagecreatetruecolor($width, $height); // создаем изображение
		$fon = imagecolorallocate($src, 255, 255, 255); // создаем фон
		imagefill( $src, 0, 0, $fon ); // заливаем изображение фоном

		for ($i=0; $i < $fon_let_amount; $i++) { // генерирование фона
		   // случайный цвет
		  $color = imagecolorallocatealpha($src, rand(0, 255), rand(0, 255), rand(0, 255), 100);
		  // случайный символ
		  $letter = $letters[rand(0,sizeof($letters)-1)];
		  // случайный размер
		  $size = rand($font_size-2,$font_size+2);
		  imagettftext($src,$size,rand(0,45),
		  rand($width*0.1,$width-$width*0.1),
		  rand($height*0.2,$height),$color,$font,$letter);
		}

		for ($i=0; $i < $let_amount; $i++) { // генерирование кода
		   $color = imagecolorallocatealpha($src, $colors[rand(0, sizeof($colors)-1)], $colors[rand(0, sizeof($colors)-1)], $colors[rand(0, sizeof($colors)-1)], rand(20,40));
		   $letter = $letters[rand(0,sizeof($letters)-1)];
		   $size = rand($font_size*2-2,$font_size*2+2);
		   $x = (int) ($i+1)*$font_size + rand(1, 5); // даем каждому символу случайное смещение
		   $y = (int) (($height*2)/3) + rand(0, 5);
		   $cod[] = $letter; // запоминаем код
		   imagettftext($src, $size, rand(0,15), $x, $y, $color, $font, $letter);
		}

		$cod = implode("", $cod); // перевод кода в строку

		ob_start();
		imagepng($src);
		$image = ob_get_contents();
		$image = base64_encode($image);
		$md = md5($cod);
		ob_end_clean();

		return ['cap' => $image,'md' => $md];

	}

	private function passCreate($p){

		$options = ['cost' => 12];
		return password_hash($p, PASSWORD_BCRYPT, $options);

	}

}