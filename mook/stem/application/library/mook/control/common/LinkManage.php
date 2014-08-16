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
use \mook\common;

class LinkManage { 

	/**
	 * [link description]
	 * @param  [type] $url     [description]
	 * @param  string $charset [description]
	 * @param  [type] $option  [description]
	 * @return [array]         [lead_image_url,word_count,title,content]
	 */
	public static function link($url, $charset = 'utf-8', array $option = NULL )
	{
		$common =  common::instance();

		$htmls = $common->curl_request($url);

		if ($htmls) {
			$datas = new Readability($htmls->response);
			return $datas->getContent();
		}
		return false;
	}
}