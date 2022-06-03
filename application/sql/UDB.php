<?php
class UDB extends Frontdb {

	function userExists($name){
		
		$this->sql = "SELECT `id`, `name`, `password`, `role` FROM `users` WHERE `name` = :name";

		return $this->select( [':name'=>$name] );
	
	}

	function adminExists(){
		
		$this->sql = "SELECT `role` FROM `users` WHERE `id` = 1";
		return $this->select();

	}

	function add($data){

		$this->sql = "INSERT INTO `users` (`name`, `password`, `role`, `email`, `regdate`) VALUES (:name, :pass, :role, :email, NOW())";
		$this->exec($data);
		
	}

	function visitUpd($id){

		$this->sql = "UPDATE `users` SET `visit` = NOW()
									WHERE `id` = :id";

		return $this->exec( [':id'=>$id] );

	}

	function getAdmin($name){
		
		$this->sql = "SELECT `id`, `name`, `role`, `email`, DATE_FORMAT(`regdate`, '%d.%m.%Y') `regdate`, DATE_FORMAT(`visit`, '%d.%m.%Y (%H:%i)') `visit` FROM `users`
									WHERE `name` = :name";
	
		return $this->select( [':name'=>$name] );
	
	}

	function uget($name) {
		
		return $this->getAdmin( $name );

	}

	function listAdmin($offset, $total) {

		++$total;

		$this->sql = "SELECT `name`, `role`, `email`, `regdate`, `visit` FROM `users`
									LIMIT {$offset}, {$total}";

		return $this->selectAll();
	
	}
	
}