<?php
/**
*
* ImageManage
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\common;

use \lib\models\images\ImagesBook;
use \lib\models\images\ImagesArticle;
use \lib\models\images\ImagesMember;

class ImagesManage extends \lib\dao\ImageControl 
{
	protected $images_book;
    protected $images_article;
    protected $images_member;

    /**
     * Instance construct
     */
    function __construct() {
    	parent::__construct();

    	$this->images_book = ImagesBook::instance();
        $this->images_article = ImagesArticle::instance();
        $this->images_member = ImagesMember::instance();
    }


    /**
     * Get Image for BookID
     *
     * @param String ,
     * @return Array
     */
    public function getImagesBookForID($bid,$type = false)
    {
        if(!$bid) return false; 
        $table = $this->bookimage->table;

        $sql = "$table.bid='$bid'";
        $type and $sql.= " AND $table.type='$type'";
        
        return $this->bookimage->field("$table.pid,i.uid,i.title,i.filename,i.type,i.path,i.dateline")
        		->joinQuery("images_book as i","$table.pid=i.ibid")->where($sql)->fetchList();
    }


    public function saveImagesBook($files, $bid, $uid, $class= 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='book')) {
            $fields = array(
                'bid' => $bid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => $this->images_member->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $this->images_book->escapeString($filepath),
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );


            if ($this->insertId = $this->images_book->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    public function saveImagesBookFromCut($fileName, $x, $y, $width, $height, $uid, $bid, $class= 1, $thumb = false)
    {
    	if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'book')) {
    		$fields = array(
                'bid' => $bid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => $this->images_book->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $this->images_book->escapeString($filepath),
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );

            if ($file = $this->getImagesBookForID($bid,1)) {
            	$fields['ibid'] = $file[0]['pid'];
	            if ($this->images_book->where("ibid='". $file[0]['pid'] ."'")->update($fields)) {
	            	self::unlink(self::getRealPath($file[0]['path']));
	            	return $file[0]['pid'];
	            }
            }

            if ($this->insertId = $this->images_book->insert($fields)) {
                return $this->insertId;
            }
    	}
    }

    public function saveImagesMember($files, $uid, $class= 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='head')) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => $this->images_member->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $this->images->escapeString($filepath),
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );

            if ($this->insertId = $this->images_member->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    public function saveImagesArticle($files, $bmid, $uid, $class = 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='head')) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => $this->images_article->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $this->images_article->escapeString($filepath),
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );

            if ($this->insertId = $this->images_article->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }



    /**
     * [getImageSizeForWeb 获取网页等比展示的大小和真实大小]
     * @return [type] [description]
     */
    public static function getImageSizeForWeb($filepath,$width = false,$height = false)
    {
        if(!file_exists(self::getRealPath($filepath))) return false;

        $file = getimagesize(self::getRealPath($filepath));

        $tWidth = 0;
        $tHeight = 0;

        if ($height and $height >= $file[1]) $height = $file[1];
        if ($width and $width >= $file[0]) $width = $file[0];

        if (!$width and $height) {

            $tHeight = $height;
            $tWidth = intval($file[0]*$height/$file[1]);
        }
        else if ($width and !$height) {
            $tWidth = $width;
            $tHeight = intval($width*$file[1]/$file[0]);
        }
        else if ($width and $height) {
            $tWidth = $width;
            $tHeight = $height;
        }
        else {
            $tWidth = $file[0];
            $tHeight = $file[1];
        }

        return array(
            'scaledWidth' => $tWidth,
            'scaledHeight' => $tHeight,
            'naturalWidth' => $file[0],
            'naturalHeight' => $file[1]);
    }




}
