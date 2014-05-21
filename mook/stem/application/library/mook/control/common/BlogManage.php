<?php
/**
*
* BlogManage
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\common;

class BlogManage extends \local\rss\DOMHtml
{
	
	public static function loadSinaBlog($url, $year = 2014, $page = 1, $user = NULL, $pass = NULL)
	{
		$dom = self::loadHtml($url);

		if (!$dom) return false;

		$elements = $dom->query("//link[@title='RSS']")->item(0);

		$uid = basename($elements->getAttribute('href'),".xml");

		$url = "http://blog.sina.com.cn/s/articlelist_" . $uid . "_0_" . $page . ".html";

		$dom = self::loadHtml($url);

		$pages = $dom->query("//ul[@class='SG_pages']/span")->item(0);
		$pages = preg_replace('/\D/s', '', $pages->nodeValue);


		$articles = $dom->query("//div[@class='articleCell SG_j_linedot1']");
		$times = $dom->query("//span[@class='atc_tm SG_txtc']");

		$datas = array();

		foreach ($articles as $key => $value) {
			$nodeValue1 = $value->childNodes->item(1)->childNodes->item(3)->childNodes->item(1);
			$nodeValue2 = $value->childNodes->item(3)->childNodes->item(3);

			if ($year == date("Y",strtotime($nodeValue2->nodeValue))) {
				$datas[$key]['url'] = $nodeValue1->getAttribute('href');
				$datas[$key]['title'] = $nodeValue1->nodeValue;
				$datas[$key]['dateline'] = strtotime($nodeValue2->nodeValue);
			}
		}

		if ($datas) {
			foreach ($datas as $key => $value) {
				sleep(1);
				$datas[$key]['body'] = self::loadSinaContent($value['url']);
			}
		}

		return $datas;
	}

	public static function loadSinaContent($url)
	{
		$doc = new \DOMDocument();
		$body = self::httpRequest($url,NULL,NULL);
		$doc->loadHTML($body);
		$dom = new \DOMXpath($doc);

		$preg_title = '/<div id=\"sina_keyword_ad_area2\" class=\"articalContent ([\s\S]*?)<\/div>/';   
		preg_match_all($preg_title, $body, $arr);

		isset($arr[0][0]) and $string=self::clearHtml($arr[0][0]);

		// $article = $dom->query("//div[@id='sina_keyword_ad_area2']");

		// $content = $article->item(0)->childNodes->item(2);

		// $data = '';
		// if (get_class($content) == 'DOMElement') {
		// 	foreach ($content->childNodes as $key => $value) {
		// 		if (get_class($value) == 'DOMElement' and $value->nodeName == 'div') {
		// 			$data.= "<p>" . $value->nodeValue . "</p>";
		// 		}
		// 	}
		// }

		return $string;
	}


	public function saveImagesForHtml($response,$path){ 
	    $pattern_src = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/'; 
	    $num = preg_match_all($pattern_src, $cont, $match_src); 
	    $pic_arr = $match_src[1];
	    foreach ($pic_arr as $pic_item) {
	        // save_pic($pic_item,$path);
	        echo "[OK]..!"; 
	    } 
	} 

	public static function clean($text) {   
  
	    $text = implode("\r",$text);   
	  
	    // normalize white space   
	    $text = eregi_replace("[[:space:]]+", " ", $text);   
	    $text = str_replace("> <",">\r\r<",$text);   
	    $text = str_replace("<br>","<br>\r",$text);   
	  
	    // remove everything before <body>   
	    $text = strstr($text,"<body");   
	  
	    // keep tags, strip attributes   
	    $text = ereg_replace("<p [^>]*BodyTextIndent[^>]*>([^\n|\n\015|\015\n]*)</p>","<p>\\1</p>",$text);   
	    $text = eregi_replace("<p [^>]*margin-left[^>]*>([^\n|\n\015|\015\n]*)</p>","<blockquote>\\1</blockquote>",$text);   
	    $text = str_replace(" ","",$text);   
	  
	    //clean up whatever is left inside <p> and <li>   
	    $text = eregi_replace("<p [^>]*>","<p>",$text);   
	    $text = eregi_replace("<li [^>]*>","<li>",$text);   
	  
	    // kill unwanted tags   
	    $text = eregi_replace("</?span[^>]*>","",$text);   
	    $text = eregi_replace("</?body[^>]*>","",$text);   
	    $text = eregi_replace("</?div[^>]*>","",$text);   
	    $text = eregi_replace("<\![^>]*>","",$text);   
	    $text = eregi_replace("</?[a-z]\:[^>]*>","",$text);   
	  
	    // kill style and on mouse* tags   
	    $text = eregi_replace("([ \f\r\t\n\'\"])style=[^>]+", "\\1", $text);   
	    $text = eregi_replace("([ \f\r\t\n\'\"])on[a-z]+=[^>]+", "\\1", $text);   
	  
	    //remove empty paragraphs   
	    $text = str_replace("<p></p>","",$text);   
	  
	    //remove closing </html>   
	    $text = str_replace("</html>","",$text);   
	  
	    //clean up white space again   
	    $text = eregi_replace("[[:space:]]+", " ", $text);   
	    $text = str_replace("> <",">\r\r<",$text);   
	    $text = str_replace("<br>","<br>\r",$text);

	    return $text;
	}

	public static function clearHtml($content) {  
	   	// $content = preg_replace("/<a[^>]*>/i", "", $content);
	   	// $content = preg_replace("/<\/a>/i", "", $content);
	   	$content = preg_replace("/<div[^>]*>/i", "", $content);
	   	$content = preg_replace("/<\/div>/i", "", $content);
	   	$content = preg_replace("/<!--[^>]*-->/i", "", $content);//注释内容
	   	$content = preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式
	   	$content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式  
	   	$content = preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式     
	   	$content = preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式      
	   	$content = preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式   
	   	$content = preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式   
	   	$content = preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式   
	   	$content = preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式   
	   	$content = preg_replace("/face=.+?['|\"]/",'',$content);//去除样式只允许
	   	$content = preg_replace("/name=.+?['|\"]/i",'',$content);
	 		//小写正则匹配没有带 i 参数
	   	return $content;
	}
}