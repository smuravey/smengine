<?php
class ADB extends Frontdb {

	function add($data){
		
		$this->sql = "INSERT INTO `articles` (`link`, `title`, `description`, `article`, `date`, `status`, `comments`) 
									VALUES (:link, :title, :desc, :article, NOW(), :status, :comments * 1)";

		return $this->exec($data);
	
	}

	function upd($data){

		$this->sql = "UPDATE `articles` SET `link` = :link, `title` = :title, `description` = :desc, `article` = :article, `status` = :status, `comments` = :comments * 1
									WHERE `id` = :id";

		$this->exec( $data );
	
	}

	function listAdmin($p, $offset, $total){

		++$total;

		$this->sql = "SELECT `a`.`id`, `a`.`link`, `a`.`title`, DATE_FORMAT(a.date, '%d %M %y %H:%i') `date`, `a`.`status` FROM `articles` AS `a`
									WHERE `a`.`status` = '{$p}'
									ORDER BY `a`.`id` DESC
									LIMIT {$offset}, {$total}";

		return $this->selectAll();
	
	}

	function getAdmin($id){

		$this->sql = "SELECT `a`.`id`, `a`.`link`, `a`.`title`, `a`.`description`, `a`.`article` FROM `articles` AS `a`
									WHERE `a`.`id` = :id";

		return $this->select( [':id'=>$id] );
	
	}

	function getaid($aid){

		$this->sql = "SELECT `id` AS `aid` FROM `articles` WHERE `id` = :aid";

		return $this->select( [':aid'=>$aid] );

	}

// main page

	function list($offset, $total){

		++$total;

		$this->sql = "SELECT `link`, `title`, `description`, DATE_FORMAT(`date`, '%e %M %y') AS `date` FROM `articles`
									WHERE `status` = '1'
									ORDER BY `id` DESC
									LIMIT {$offset}, {$total}";

		return $this->selectAll();

	}

	function get($link){
		
		$this->sql = "SELECT `id`, `title`, `article`, `comments` FROM `articles`
									WHERE `link` = :link AND `status` != '0'";

		return $this->select( [':link'=>$link] );
	
	}

}