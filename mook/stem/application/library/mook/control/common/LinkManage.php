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


		if ($htmls) {
			preg_match('/=(.+\b)/',$htmls->info['content_type'], $matchs);
			$charset = isset($matchs[1]) ? $matchs[1] : 'utf-8';
			$datas = new Readability($htmls->response,$charset);
			return $datas->getContent();
		}
		return false;
	}
}