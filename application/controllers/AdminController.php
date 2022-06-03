<?php
class AdminController extends Master {

	private $data = [], $tags = [];

	function __construct(){
		
		parent::__construct();

		if ( !$this->isAdmin() ){
			$this->err('404');
		}

		$this->data['ulink'] = $this->ulink();

	}

	function defaultAction($p){

		$this->outTemplate('main', $this->data);

	}

	function newAction(){

		$this->data += ['id'=>'','title'=>'','link'=>'','description'=>'','article' =>'','checked'=>''];
		$this->outTemplate('article_editor', $this->data);

	}

	function listAction($p){

		$p1 = isset($p[0]) ? $p[0] : 'a';
		$p2 = isset($p[1]) ? $p[1] : 0;

		$this->offset = $this->getOffset();
		$this->total = 2;

		switch ($p1){
			case 'a':
				$a = new ADB();
				$this->data['list'] = $a->listAdmin($p2, $this->offset, $this->total);
				$tpl = $partTpl = 'article_list';
				break;
			case 'c':
				$c = new CDB();
				$this->data['list'] = $c->listAdmin($p2, $this->offset, $this->total);
				$tpl = $partTpl = 'comment_list';
				break;
			case 'u':
				$u = new UDB();
				$this->data['list'] = $u->listAdmin($this->offset, $this->total);
				$tpl = $partTpl = 'user_list';
				break;
			default:
				$this->err('404');
				break;
		}

		$this->pageNavi($this->data);
		$this->data['list'] = $this->partParse($partTpl, $this->data['list']);
		
		$this->outTemplate($tpl, $this->data);

	}

	function editAction($p){

		$p1 = isset( $p[0] ) ? $p[0] : '';

		switch ($p1){
			case 'a':
				$obj = new ADB();
				$tpl = 'article_editor';
				break;
			case 'c':
				$obj = new CDB();
				$tpl = 'comment_editor';
				break;
			case 'u':
				$obj = new UDB();
				$tpl = 'user_editor';
				break;
			default:
				$this->err('404');
				break;
		}

		if ( isset($p[1]) ){

			$res = $obj->getAdmin( $p[1] );

			if ( $res ){

				$this->data += $res;
				$this->outTemplate($tpl, $this->data);

			} else { $this->err('404'); }
		} else { $this->err('404'); }

	}
	
	function sendAction(){

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ){
			
			$data = $this->getDataForm();
			$id = isset($_POST['id']) ? $_POST['id'] : 0;

			switch ( parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) ){
				case '/admin/new':
					
						// $tag = new TDB();

						// if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == '/admin/new'){

							// $tag->join($data['tags'], $aid);

						// }

						// if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH) == '/admin/edit/a/'.$data[':aid']){

							// $article->upd($data);

							// $tags = explode(',', $tag->tfa($data[':aid'])['tag']);
							// $join = array_diff($this->tags, $tags);
							// $unjoin = array_diff($tags, $this->tags);

							// if ( !empty($join) )
							// 	$tag->join($join, $data[':aid']);

							// if ( !empty($unjoin) )
							// 	$tag->unjoin($unjoin, $data[':aid']);

						// }

					$a = new ADB();
					$a->add($data);
					header('Location: /admin/list/a');
					
					break;
				case '/admin/edit/a/'.$id:

					$a = new ADB();
					$a->upd($data);
					header('Location: /admin/list/a');

					break;
				case '/admin/edit/c/'.$id:

					// print_r($_POST);

					$c = new CDB();
					$c->upd($data);
					// $c->showError();
					header('Location: /admin/list/c');

					break;
				default:
					$this->err('404');
					break;
			}
		
		}

	}

}