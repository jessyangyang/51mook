<?php
/**
 * Test Controllers
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 */

namespace mook\control\common;

use \local\common\Readability;

class LinkManage { 

	/**
	 * [link description]
	 * @param  [type] $url     [description]
	 * @param  string $charset [description]
	 * @param  [type] $option  [description]
	 * @return [array]         [lead_image_url,word_count,title,content]
	 */
	public static function link($url, $charset = false, array $option = NULL )
	{
		$common =  \Yaf\Registry::get('common');

		$htmls = $common->curl_request($url);

		$charset = 'utf-8';

		if ($htmls) {
			preg_match('/=(.+\b)/',$htmls->info['content_type'], $matchs_one);
			if (isset($matchs_one[1]) and $matchs_one[1]) {
				$charset = $matchs_one[1];
			}
			else if (preg_match('/=(.+\b)/',self::getMetaEncoding($htmls->response), $matchs_two) and isset($matchs_two[1])){
				$charset = $matchs_two[1];
			}
			
			$datas = new Readability($htmls->response,$charset);
			return $datas->getContent();
		}
		return false;
	}

	public static function getMetaEncoding($response)
	{
		$doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($response);
        $metas = $doc->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++)
        {
            $meta = $metas->item($i);
            if ($meta->getAttribute('http-equiv') == 'Content-Type') {
                return $meta->getAttribute('content');
            }
        }
	}
}