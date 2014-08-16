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
use \lib\models\images\ImagesBookArticle;
use \lib\models\images\ImagesCourseArticle;
use \lib\models\images\ImagesMember;
use \lib\models\images\ImagesCourse;

class ImagesManage extends \lib\dao\ImageControl 
{
	protected $images_book;
    protected $images_book_article;
    protected $images_course_article;
    protected $images_member;
    protected $images_course;

    /**
     * Instance construct
     */
    function __construct() {
    	parent::__construct();

    	$this->images_book = ImagesBook::instance();
        $this->images_book_article = ImagesBookArticle::instance();
        $this->images_course_article = ImagesCourseArticle::instance();
        $this->images_member = ImagesMember::instance();
        $this->images_course = ImagesCourse::instance();
    }


    /**
     * Get Image for BookID
     *
     * @param String ,
     * @return Array
     */
    public function getImagesBookForID($bid,$type = 1)
    {
        if(!$bid) return false; 

        $sql = "bid='$bid' AND class='$type'";
        
        return $this->images_book->where($sql)->fetchRow();
    }

    public function getImagesMemberForID($uid,$type = 1)
    {    
        if(!$uid) return false; 

        $sql = "uid='$uid' AND class='$type'";
        
        return $this->images_member->where($sql)->fetchRow();
    }

    public function getImagesBookArticleForID($bid,$type = false)
    {
        $list =  $this->images_book_article->where("bid='$bid'")->order("dateline")->fetchList();

        if (is_array($list)) {
            return $list;
        }
        return false;
    }

    public function getImagesCourseArticleForID($cid,$type = false)
    {
        $list =  $this->images_course_article->where("cid='$cid'")->order("dateline")->fetchList();

        if (is_array($list)) {
            return $list;
        }
        return false;
    }

    public function getImagesCourseForCID($cid, $type = 1)
    {
        if(!$cid) return false; 

        $sql = "cid='$cid' AND class='$type'";
        
        return $this->images_course->where($sql)->fetchRow();
    }

    /**
     * [saveImagesBook description]
     * @param  [type]  $files [description]
     * @param  [type]  $bid   [description]
     * @param  [type]  $uid   [description]
     * @param  integer $class [description]
     * @param  boolean $thumb [description]
     * @return [type]         [description]
     */
    public function saveImagesBook($files, $bid, $uid, $class= 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='book')) {
            $fields = array(
                'bid' => $bid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
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
    	if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'book', $bid)) {
    		$fields = array(
                'bid' => $bid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $filepath,
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),130,188,'small','jpeg');
               $this->makethumb(self::getRealPath($filepath),216,310,'medium','jpeg');
            }

            if ($file = $this->getImagesBookForID($bid,1)) {
            	$fields['ibid'] = $file['ibid'];
	            if ($this->images_book->where("ibid='". $file['ibid'] ."'")->update($fields)) {
	            	self::unlink(self::getRealPath($file['path']));
	            	return $file['ibid'];
	            }
            }
            else if ($this->insertId = $this->images_book->insert($fields)) {
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
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );

            if ($this->insertId = $this->images_member->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    public function saveImagesMemberFromCut($fileName, $x, $y, $width, $height, $uid, $class = 1, $thumb = false)
    {
        if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'head', $uid)) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $filepath,
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),48,48,'small','jpeg');
               $this->makethumb(self::getRealPath($filepath),130,130,'medium','jpeg');
            }

            if ($file = $this->getImagesMemberForID($uid,1)) {
                $fields['imid'] = $file['imid'];
                if ($this->images_member->where("imid='". $file['imid'] ."'")->update($fields)) {
                    self::unlink(self::getRealPath($file['path']));
                    return $file['imid'];
                }
            }
            else if ($this->insertId = $this->images_member->insert($fields)) {
                return $this->insertId;
            }
        }
    }


    public function saveImagesCourse($files, $cid, $uid, $class= 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='book')) {
            $fields = array(
                'cid' => $cid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );


            if ($this->insertId = $this->images_course->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    public function saveImagesCourseFromCut($fileName, $x, $y, $width, $height, $uid, $cid, $class= 1, $thumb = false)
    {
        if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'course', $cid)) {
            $fields = array(
                'cid' => $cid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $filepath,
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),130,188,'small','jpeg');
               $this->makethumb(self::getRealPath($filepath),216,310,'medium','jpeg');
            }

            if ($file = $this->getImagesCourseForCID($cid,1)) {
                $fields['icid'] = $file['icid'];
                if ($this->images_course->where("icid='". $file['icid'] ."'")->update($fields)) {
                    self::unlink(self::getRealPath($file['path']));
                    return $file['icid'];
                }
            }
            else if ($this->insertId = $this->images_course->insert($fields)) {
                return $this->insertId;
            }
        }
    }


    public function saveImagesBookArticle($files, $bid, $bmid, $uid, $retype = false, $class = 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, 'article', true)) {
            $fields = array(
                'bid' => $bid,
                'bmid' => $bmid,
                'uid' => $uid,
                'title' => basename($files['name']),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),100,144,'small','jpeg');
            }

            $this->insertId = $this->images_book_article->insert($fields);

            if ($retype == false) {
                return $this->insertId;
            }
            else {
                return $filepath;
            }
        }

        return false;
    }

    public function saveImagesCourseArticle($files, $cid, $ccid, $uid, $retype = false, $class = 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, 'lesson', true)) {
            $fields = array(
                'cid' => $cid,
                'ccid' => $ccid,
                'uid' => $uid,
                'title' => basename($files['name']),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),100,144,'small','jpeg');
            }

            $this->insertId = $this->images_course_article->insert($fields);

            if ($retype == false) {
                return $this->insertId;
            }
            else {
                return $filepath;
            }
        }

        return false;
    }


    public function saveImageMemberFromPath($filepath, $uid, $class= 1, $thumb = false)
    {
        if (file_exists($filepath)) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => pathinfo($filepath,PATHINFO_BASENAME),
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $filepath,
                'thumb' => 0,
                'dateline' => UPDATE_TIME
            );

            if ($this->insertId = $this->images_member->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    public function saveWebImageToLocal($url, $uid, $path = 'book', $relatived = true)
    {
        $pathinfo = pathinfo($url); 

        // print_r($pathinfo);

        $fileExt = $this->getImageType($pathinfo['extension']);
        // Allow Type
        if (!$this->hasImageType($fileExt)) {
            # code...
        }

        // Get file path
        if (!$this->_file = $this->getFilePath($pathinfo['basename'], $uid, true, $path, false)) {
            # code...
        }

        $data = file_get_contents($url);
        $img_size = file_put_contents(FILES_PATH . '/files' . $this->_file, $data);

        if (!file_exists(FILES_PATH . '/files' . $this->_file, $data)) return false;

        if ($relatived) return $this->_file;
        return FILES_PATH . '/files' . $this->_file;
    }

    /**
     * [getImageSizeForPath 获取网页等比展示的大小和真实大小]
     * @return [type] [description]
     */
    public static function getImageSizeForPath($filepath,$width = false,$height = false)
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
