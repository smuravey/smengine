<?php
class CDB extends Frontdb {

	function add($data) {

		$this->sql = "INSERT INTO `comments` (`aid`, `path`, `name`, `message`, `date`) VALUES (:aid, 0, :name, :message, NOW());
									UPDATE `comments` SET `path` = LAST_INSERT_ID() WHERE `id`= LAST_INSERT_ID();";

		$this->exec( [':aid'=>$data['aid'],':name'=>$data['name'],':message'=>$data['message']] );

	}

	function addReply($data){

		$this->sql = "INSERT INTO `comments` (`aid`, `path`, `name`, `message`, `date`) VALUES (:aid, 0, :name, :message, NOW());
									UPDATE `comments`, (SELECT `path` AS `path2` FROM `comments` WHERE `id` = :cid) AS `X` SET `path` = CONCAT(`path2`,'.',LAST_INSERT_ID()) WHERE `id`= LAST_INSERT_ID();";

		$this->exec( [':aid'=>$data['aid'],':cid'=>$data['cid'],':name'=>$data['name'],':message'=>$data['message']] );

	}

	function upd($data){

		$this->sql = "UPDATE `comments` SET `message` = :message, `status` = :status
									WHERE `id` = :id";

		$this->exec($data);

	}

	function listAdmin($p, $offset, $total){

		++$total;

		$this->sql = "SELECT c.id, c.name, c.message, DATE_FORMAT(c.date, '%d %M %y %H:%i') AS date, a.id AS aid, a.link, a.title FROM comments AS c
									JOIN (articles AS a) ON c.aid = a.id
									WHERE c.status = '{$p}'
									ORDER BY c.id DESC
									LIMIT {$offset}, {$total}";

		return $this->selectAll();

	}

	function getAdmin($cid) { // get comment for admin

		$this->sql = "SELECT `id`, `name`, `message`, DATE_FORMAT(`date`, '%d %M %y (%H:%i)') AS `date` FROM `comments`
									WHERE `id` = :cid";

		return $this->select( [':cid'=>$cid] );

	}

	function getcid($data) {

		$sql = "SELECT `id` FROM `comments`
						WHERE `id` = :cid AND `aid` = :aid";

		$sth = $this->pdo->prepare($sql);
		$sth->execute( [ ':cid'=>$data['cid'],':cid'=>$data['cid'] ] );
		
		return $sth->fetch();

	}

	function ccheck($data){ // comment check

		$this->sql = "SELECT id, parent, child, name FROM comments
									WHERE id = :cid AND aid = :aid";

		return $this->select( [':cid'=>$data['cid'],':aid'=>$data['aid']] );
	
	}

	function get($data){
		
		$this->sql = "SELECT id AS cid, aid, name, message FROM comments
									WHERE id = :cid AND aid = :aid AND status = '1'";

		return $this->select( [':cid'=>$data['cid'],':aid'=>$data['aid']] );
	
	}

// main

	function cfa($aid){ // comments for article

		// $this->sql = "SELECT `id`, `name`, `message`, IF(`pid` = 0, 1, 2) AS `lvl`, DATE_FORMAT(`date`, '%d %M %y | %H:%i') AS `date` FROM `comments`
		// 							WHERE `aid` = :aid AND `status` = '1'
		// 							ORDER BY IF(`pid` != 0, `pid`, `id`), `id`";
		
		$this->sql = "SELECT `id`, `path`, `name`, IF(`status` = '2', 'Комментарий будет опубликован после проверки.', `message`) AS `message`, DATE_FORMAT(`date`, '%d %M %y (%H:%i)') AS `date` FROM `comments`
									WHERE `aid` = :aid AND `status` = '1'
									ORDER BY `path`";

		return $this->selectAll( [':aid'=>$aid] );

	}

}