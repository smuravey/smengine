<?php
class Master {

	public $auth = false, $id, $uname = '';
	private $partPath = '';
	
	function __construct(){

		session_start([
									'cookie_httponly'=>true,
									'cookie_secure'=>false,
									'use_strict_mode'=>true
									]);

		$this->setAuth();

	}

	// user section //

	private function setAuth(){

		if ( isset($_SESSION['id']) && isset($_SESSION['name']) ){
			
			$this->auth = true;
			$this->id = $_SESSION['id'];
			$this->uname = $_SESSION['name'];

		}

	}

	function isAdmin(){

		if ( $this->auth ){
			
			if ( isset($_SESSION['role']) && $_SESSION['role'] == 'admin' && isset($_SESSION['ip']) && $_SESSION['ip'] == md5($_SERVER['REMOTE_ADDR']) ){
				return true;
			}
		
		}

		return false;

	}

	function ulink(){
		
		if ($this->auth){
			return '<a href="/user/'.$this->uname.'">'.$this->uname.'</a>';
		}else{
			return '<a href="/user/login">Войти</a>';
		}
	
	}

	// start template out section //

	private function getMarks( $tpl ){

		preg_match_all('/\%([a-z]+)\%/uis', $tpl, $search);
		
		if ( !empty($search[0]) ) {
		
			$search[0] = array_unique($search[0]);
			$search[1] = array_unique($search[1]);

			return $search;
		
		}

		return false;
	
	}

	private function getTpl( $tpl, $part = false ){

		$tplType = get_called_class() == 'AdminController' ? 'admin' : 'main';
		
		$path = 'templates'.DIRECTORY_SEPARATOR.$tplType.DIRECTORY_SEPARATOR;
		// $path = 'templates'.DIRECTORY_SEPARATOR.$this->partPath;

		if ( $part ){
			$path = $path.'parts'.DIRECTORY_SEPARATOR;
		}

		$tpl = $path.$tpl.'.html';

		if ( file_exists($tpl) ){
			return file_get_contents($tpl);
		}

		return false;
	
	}

	private function filter( $marks, $data ){
	
		$res = [];
		
		foreach ($marks as $k => $v){
			if( array_key_exists($v, $data) ) {
				$res[$k] = $data[$v];
			}
		}

		return $res;
	
	}

	function partParse( $tpl, $data ){
	
		$tpl = $this->getTpl($tpl, true);
		
		if ($tpl){
			
			$partTpl = '';
			$marks = $this->getMarks($tpl);

			foreach ($data as $v){

				$v = $this->filter($marks[1], $v);
				$partTpl .= str_replace( $marks[0], $v, $tpl );
			
			}
			
			return $partTpl;
		
		}
	
	}

	function outTemplate( $mainTpl, $data = false ){

		$mainTpl = $this->getTpl($mainTpl);
		// $mainMarks = $this->getMarks($mainTpl);

		if ( $mainTpl ){

			$mainMarks = $this->getMarks($mainTpl);
			
			if($mainMarks){
				$data = $this->filter( $mainMarks[1], $data );
				$mainTpl = str_replace( $mainMarks[0], $data, $mainTpl );
			}
			
			exit($mainTpl);

		
		} else { $this->err('404'); }
		
		// if( $mainTpl ){
		// 	exit($mainTpl);
		// 	// echo $mainTpl;
		// 	// global $start;
		// 	// $time = microtime(true) - $start;
		// 	// printf('Скрипт выполнялся %.5F сек.', $time);
		// }else{
		// 	$this->err('404');
		// }

	}

	// end template out section //

	// other methods section //

	function getOffset(){
		
		if ( isset($_GET['offset']) && is_numeric($_GET['offset']) && $_GET['offset'] > 0 ){
			return (int) $_GET['offset'];
		} else {
			return 0;
		}
	
	}

	function pageNavi(&$data){

		$data['prev'] = $this->offset == 0 ? '' : $this->offset - $this->total;
		$data['next'] = '';
		
		if( isset( $data['list'] ) && count( $data['list'] ) > $this->total ){
			$data['next'] = $this->offset + $this->total;
			array_pop( $data['list'] );
		}
	
	}

  function err($msg = ''){

		header('HTTP/1.1 404 Not Found');
		$this->outTemplate('error', ['msg'=>$msg]);

	}

	private function clear_separators($str, $sep = ''){
		
		$pattern = "/[^\pL0-9]/ui";
		$str = mb_strtolower($str);
		$str = preg_replace($pattern, $sep, $str);
		
		if($sep != ''){
			$str = trim(preg_replace("/[--]+/", '-', $str), '-');
		}
		
		return $str;
	
	}

	function rus2latin($string){
		$string = mb_strtolower($string);
		$converter = array(
											'а' => 'a',   'б' => 'b',   'в' => 'v',
											'г' => 'g',   'д' => 'd',   'е' => 'e',
											'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
											'и' => 'i',   'й' => 'y',   'к' => 'k',
											'л' => 'l',   'м' => 'm',   'н' => 'n',
											'о' => 'o',   'п' => 'p',   'р' => 'r',
											'с' => 's',   'т' => 't',   'у' => 'u',
											'ф' => 'f',   'х' => 'h',   'ц' => 'c',
											'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
											'ь' => '',  	'ы' => 'y',   'ъ' => '',
											'э' => 'e',   'ю' => 'yu',  'я' => 'ya'
										);

		return $this->clear_separators(strtr($string, $converter), '-');
	
	}

	function checkForm($fields){
			
		$check = false;

		foreach ($fields as $k => $v){
			
			if( isset($v, $_POST[$v]) && !empty($_POST[$v]) ){
				$check = true;
			}else{
				$check = false;
				break;
			}
		
		}

		return $check;
	
	}

	function getDataForm(){

		if ( empty($_POST['id']) ){
			unset($_POST['id']);
		}
			
		if ( isset($_POST['title']) && empty($_POST['title']) ){
			$_POST['title'] = 'Черновик от ' . date('d.m.y H:i:s');
		}

		if ( isset($_POST['link']) && empty($_POST['link']) ){
			$_POST['link'] = $this->rus2latin($_POST['title']);
		}

		// $data[':status'] = isset($_POST['status']) ? $_POST['status'] : 0;

		foreach ($_POST as $k => $v){
			$data[':'.$k] = $v;
		}

		return $data;

	}

}