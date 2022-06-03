<?php
class TDB extends Frontdb {

	function tfa( $id ){
		
		$this->sql = "SELECT GROUP_CONCAT(t.tag) AS tag FROM tags AS t
									JOIN (tags_articles AS ta, articles AS a) ON ta.id_tag = t.id AND ta.id_article = a.id
									WHERE a.id = :id";
		
		return $this->select( [':id'=>$id] );
	
	}

	function tagsArticles( $tag ){

		$res = $this->get( $tag );

		if( !isset($res['id']) ){
			return false;
		}
		
		$data['tag'] = $res['tag'];

		$this->sql = "SELECT a.code, a.title, description, DATE_FORMAT(`date`, '%d.%m.%y %H:%i:%S') AS `date` FROM articles AS a
									JOIN (tags_articles AS ta) ON ta.id_tag = :tid AND ta.id_article = a.id
									WHERE a.status = '1'
									LIMIT 10";
		
		$data['list'] = $this->selectAll([':tid'=>$res['id']]);

		return $data;
	
	}

	private function get( $tag ){

		$this->sql = "SELECT id, tag FROM tags WHERE tag = :tag";
		
		return $this->select( [':tag'=>$tag] );
	
	}

	private function add( $tag ){

		$this->sql = "INSERT INTO tags ( tag ) VALUES ( :tag )";
		
		return $this->exec();

	}

	function join( $tag, $aid ){

			foreach ($tag as $v) {

				$tid = $this->get( $v );
				
				if( !isset($tid['id']) ) { $tid['id'] = $this->add( $v ); }

				$this->sql = "INSERT INTO tags_articles (id_tag, id_article) VALUES (:tid, :aid)";
				
				$this->exec( ['tid'=>$tid['id'],':aid'=>$aid] );

			}

	}

	function unjoin( $tag, $aid ){

		if( !empty($tag[0]) )
			foreach ($tag as $v){
				
				$tid = $this->get( $v )['id'];

				$this->sql = "DELETE FROM tags_articles WHERE id_tag = :tid AND id_article = :aid";
			
				$this->exec( [':tid'=>$tid,':aid'=>$aid] );
			
			}
	
	}

}