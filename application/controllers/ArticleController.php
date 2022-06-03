<?php
class ArticleController extends Master {

	function defaultAction($p){

		if ( isset($p[0]) ){
			
			$article = new ADB();

			$res = $article->get( $p[0] );
			
			if ( isset($res['id']) ){

				$data = $res;
				$data['commentslist'] = '';
				
				if ($res['comments'] != 0){
					
					$comments = new CDB();
					$clist = $comments->cfa($data['id']);

					if ( $clist ){
						$this->lvlCount($clist);
						$data['commentslist'] = $this->partParse('comments_list', $clist);
					}

				}

				$data['ulink'] = $this->ulink();

				$this->outTemplate('article', $data);

			} else {$this->err('404'); }
		} else { $this->err('404'); }

	}

	function cmntaddAction(){

		if ( $this->auth ){
			if ( $this->checkForm(['message','aid','sbtn']) ){

				$article = new ADB();
				$data = $article->getaid( $_POST['aid'] );

				if ( isset($data['aid']) ){

					$comment = new CDB();

					$data['name'] = $this->uname;
					$data['message'] = htmlspecialchars( trim( $_POST['message'] ) );

					if ( isset($_POST['cid']) && !empty($_POST['cid']) ){
						$data['cid'] = $_POST['cid'];
						$comment->addReply($data);
					}else{
						$comment->add( $data );
					}

					header('Location: '.$_SERVER['HTTP_REFERER']);

				} else { echo 'Нет такой статьи'; }
			} else { echo 'Пустые поля'; }
		} else { echo 'Вы не авторизированны'; }

	}

	private function lvlCount(&$clist){

		foreach ($clist as $k=>$v){
			$clist[$k]['lvl'] = substr_count($v['path'], '.') * 3;
			unset($v['path']);
		}

	}

	// function cmntaddAction(){

	// 	if ( $this->auth ){
	// 		if ( $this->checkForm(['message','aid','sbtn']) ){

	// 			$article = new ADB();
	// 			$data = $article->getaid( $_POST['aid'] );

	// 			if ( isset($data['aid']) ){

	// 				$comment = new CDB();
					
	// 				$data['parent'] = 0;
	// 				$data['child'] = 0;
					
	// 				$cmntcheck['aid'] = $_POST['aid'];

	// 				if ( isset($_POST['cid']) ){
	// 					$cmntcheck['cid'] = $_POST['cid'];
	// 					$res = $comment->ccheck( $cmntcheck );

	// 					if ( isset($res['id']) ){

	// 						if ( $res['parent'] == 0 ){
	// 							$data['parent'] = $res['id'];
	// 						} else {
	// 							$data['parent'] = $res['parent'];
	// 							$data['child'] = $res['id'];
	// 						}

	// 					}

	// 				}
					
	// 				$data['name'] = $this->uname;
	// 				$data['message'] = htmlspecialchars( trim( $_POST['message'] ) );

	// 				$comment->add( $data );

	// 				header('Location: '.$_SERVER['HTTP_REFERER']);

	// 			} else { echo 'article not found'; }
	// 		} else { echo 'empty fields'; }
	// 	} else { echo 'you are not authorized'; }

	// }

	// function csort($comm){

	// 	foreach ($comm['parents'] as $k1 => $v1){

	// 		$v1['dep'] = '';
	// 		unset($v1['parent']);
	// 		$res[] = $v1;

	// 		foreach ($comm['childs'] as $k2 => $v2){

	// 			if ($v1['id'] == $v2['parent']) {
	// 				$v2['dep'] = 1;
	// 				unset($v2['parent']);
	// 				$res[] = $v2;
	// 				unset($comm['childs'][$k2]);
	// 			}else break;

	// 		}

	// 	}

	// 	return $res;

	// }

}