<?php

class RssPost{    var $date;    var $ts;    var $link;    var $title;    var $text; var $image;}
class RssFeed{
    var $posts = array();
	var $feed_uri ;
	var $cache_life = 500 ; //seconds
	function GetCacheFile(){
		return P_BASE.'rss_cache'.DS . md5($this->feed_uri) .'.txt' ;
	}	
	function WriteCache(){
		if (!is_dir(P_BASE.'rss_cache'.DS)){	mkdir(P_BASE.'rss_cache'.DS);	}
		$cache_file = $this->GetCacheFile() ;
		$cont = json_encode($this->posts)  ;
		if($f = @fopen($cache_file,"w")){
            if(@fwrite($f, $cont)){   @fclose($f);   }
        }
	}
	function ReadCache(){
		$cache_file = $this->GetCacheFile() ;
		$filemtime = @filemtime($cache_file);
		if ($filemtime && (time() - $filemtime <= $this->cache_life)){
			$cont = file_get_contents($cache_file) ;
			$this->posts = 	json_decode($cont,false); 
			return true;
		}
		return false ;
	}	
	
    function RssFeed($feed_uri) { //__constructor
		$this->feed_uri = $feed_uri ;		
		if ($this->ReadCache()){
			return ;
		}		
        $xml_source = file_get_contents($feed_uri);
        $x = simplexml_load_string($xml_source);
        if(count($x) == 0)    return;
        foreach($x->channel->item as $item) {
            $post = new RssPost();
            $post->date = (string) $item->pubDate;
            $post->ts = strtotime($item->pubDate);
            $post->link = (string) $item->link;
            $post->title = (string) $item->title;
            $post->text = (string) $item->description;
			$imgs = array() ;
			preg_match('/<img.*?>/',$post->text ,$imgs) ;
			if (count($imgs)){
				$post->image = $imgs[0];
			}else{
				$post->image = '';	
			}
            $summary = strip_tags($post->text ,'<p><img><b><br>') ;
            $post->summary = $summary;            
			$this->posts[] = $post;
        }
		if (count($this->posts) ){
			$this->WriteCache();	
		}
    }
}

?>