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

    public function getArticleForID($bid,$type = false)
    {
        $list =  $this->images_article->where("bid=$bid")->order("dateline")->fetchList();

        if (is_array($list)) {
            return $list;
        }
        return false;
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
    	if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'book', $bid)) {
    		$fields = array(
                'bid' => $bid,
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => $this->images_book->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $this->images_book->escapeString($filepath),
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),130,188,'small');
               $this->makethumb(self::getRealPath($filepath),216,310,'medium');
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

    public function saveImagesMemberFromCut($fileName, $x, $y, $width, $height, $uid, $class = 1, $thumb = false)
    {
        if ($fileName and $filepath = $this->saveImageFromSize($fileName, $x, $y, $width, $height, $uid, 'head', $uid)) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename(pathinfo($fileName,PATHINFO_BASENAME)),
                'filename' => $this->images_member->escapeString(pathinfo($filepath,PATHINFO_BASENAME)),
                'type' => 'image/' . pathinfo($filepath,PATHINFO_EXTENSION),
                'size' => filesize(self::getRealPath($filepath)),
                'path' => $this->images_member->escapeString($filepath),
                'thumb' => $thumb ? 1 : 0,
                'dateline' => UPDATE_TIME
            );

            if ($thumb) {
               $this->makethumb(self::getRealPath($filepath),48,48,'small');
               $this->makethumb(self::getRealPath($filepath),130,130,'medium');
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

    public function saveImagesArticle($files, $bid, $bmid, $uid, $retype = false, $class = 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='article')) {
            $fields = array(
                'bid' => $bid,
                'bmid' => $bmid,
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

            if ($retype == false and $this->insertId = $this->images_article->insert($fields)) {
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

    public static function getRealCoverSize($filepath, $size = 'small')
    {
        $path = self::getRealPath($filepath);
        if (!is_file($path)) return false;

        $tmp = pathinfo($filepath);

        $filename = explode(".",$tmp['basename']);
        return self::getRelativeImage($tmp['dirname'] . "/" . $filename[0] . "_$size." . $tmp['extension']);
    }

}
