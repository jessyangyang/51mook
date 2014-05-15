<?php
/**
 * DOMHtml
 *
 * @package     DuyuMvc
 * @author      Jess
 * @version     1.0
 * @license     http://wiki.duyu.com/duyuMvc
 * 
 */

namespace local\rss;

class DOMHtml extends \local\rss\Feed
{
	public static function loadHTML($url, $user = NULL, $pass = NULL)
	{
		libxml_use_internal_errors(true);
		$doc = new \DOMDocument();
		$doc->loadHTML(self::httpRequest($url, $user, $pass));
		return new \DOMXpath($doc);
	}
}