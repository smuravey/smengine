<?php
class MainController extends Master {

	public $total = 2;

	function defaultAction(){

		$article = new ADB();

		$this->offset = $this->getOffset();
		
		$data['list'] = $article->list($this->offset, $this->total);
		$this->pageNavi($data);
		$data['list'] = $this->partParse('article_list', $data['list']);
		$data['ulink'] = $this->ulink();
	
		$this->outTemplate('main', $data);

	}

}