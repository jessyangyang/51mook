<?php
/**
*
* ImageControl
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\dao;

use \lib\models\images\Images;
use \lib\models\BookImage;

class ImageControl extends \local\image\Images 
{

    // Images Instance
    protected $images;
    // BookImage Instance
    protected $bookimage;
    // File Name
    protected $fileName;

    // lastest insertId for database server
    public $insertId;

    // Files Size : KB
    protected $allowFileSize = 2000;

    // Files Directory
    protected $path = array();

    // Server Files
    protected $siteUrl;

    /**
     * Class construct
     * @return void
     */
    function __construct()
    {

        // file_exists($file) and $this->_file = $file;
        $this->images = images::instance();
        $this->bookimage = BookImage::instance();

        $sitePath = \Yaf\Application::app()->getConfig()->toArray();

        $this->siteUrl = $sitePath['server']['imagesBook'];
        $this->path = $sitePath['path'];
    }

    /**
     * Save image to folder and Update Images table.
     *
     * @param 
     */
    public function save($FILE, $uid, $path = "image", $fixed = false)
    {
        if (!$uid)  return false;

        $FILE['size'] = $FILE['size'] ? $FILE['size'] : "";

        // Checkout File
        if ($FILE['size'] || $tmpName || !empty($FILE['error'])) {
            # code...
        }

        $fileExt = $this->getImageType($FILE['name']);
        // Allow Type
        if (!$this->hasImageType($fileExt)) {
            # code...
        }

        // Get file path
        if (!$this->_file = $this->getFilePath($FILE['name'],$uid, true, $path, $fixed)) {
            # code...
        }
        
        // Save to server 
        if ($this->upload($FILE['tmp_name'])) {
            return $this->_file;
        }

        // // Make a Thumb
        // if ($thumb) {
        //    $this->makethumb(self::getRealPath($this->_file));
        // }

        return false;
    }

    /**
     * [saveImages description]
     * @param  [type]  $files [description]
     * @param  [type]  $uid   [description]
     * @param  integer $class [description]
     * @param  boolean $thumb [description]
     * @return [type]         [description]
     */
    public function saveImages($files, $uid, $class = 1, $thumb = false)
    {
        if ($filepath = $this->save($files, $uid, $path='image')) {
            $fields = array(
                'uid' => $uid,
                'class' => $class,
                'title' => basename($files['name']),
                'filename' => $this->fileName,
                'type' => $files['type'],
                'size' => $files['size'],
                'path' => $this->images->escapeString($filepath),
                'thumb' => 0,
                'published' => time()
            );

            if ($this->insertId = $this->images->insert($fields)) {
                return $this->insertId;
            }
        }

        return false;
    }

    /**
     *  Remove Image Files
     *
     *  @param String ,$path
     *  @return Boolean
     */
    public static function unlink($path)
    {
        $filepath = FILES_PATH . '/files' . $path;
        if(file_exists($filepath)) return unlink($filepath);
        return false;
    }

    /**
     * [getRealPath description]
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public static function getRealPath($file)
    {
        $filepath = FILES_PATH . '/files' . $file;
        if(file_exists($filepath)) return $filepath;
        return false;
    }

    /**
     * [saveImageFromSize description]
     * @param  [type]  $fileName [description]
     * @param  [type]  $x        [description]
     * @param  [type]  $y        [description]
     * @param  [type]  $width    [description]
     * @param  [type]  $height   [description]
     * @param  [type]  $uid      [description]
     * @param  string  $path     [description]
     * @return [type]            [description]
     */
    public function saveImageFromSize($fileName, $x, $y, $width, $height, $uid ,$path = 'image')
    {
        if(!file_exists(self::getRealPath($fileName))) return false;

        $im = '';
        if($data = getimagesize(self::getRealPath($fileName))) {
            if($data[2] == 1) {
                if(function_exists("imagecreatefromgif")) {
                    $im = imagecreatefromgif(self::getRealPath($fileName));
                }
            } elseif($data[2] == 2) {
                if(function_exists("imagecreatefromjpeg")) {
                    $im = imagecreatefromjpeg(self::getRealPath($fileName));
                }
            } elseif($data[2] == 3) {
                if(function_exists("imagecreatefrompng")) {
                    $im = imagecreatefrompng(self::getRealPath($fileName));
                }
            }
        }


        $fileName = pathinfo($fileName);
        $filePath =$this->getFilePath($fileName['basename'],$uid,true, $path);

        if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && $ni = imagecreatetruecolor($width, $height)) {
                imagecopyresampled($ni, $im, 0, 0, $x, $y, $width, $height, $width, $height);

                if(function_exists('imagejpeg')) {
                    imagejpeg($ni, FILES_PATH . '/files' . $filePath);
                } elseif(function_exists('imagepng')) {
                    imagepng($ni, FILES_PATH . '/files' . $filePath);
                }
                imagedestroy($ni);
                imagedestroy($im);

                return $filePath;
        }
        return false;
    }

    /**
     * Save Image from url
     *
     * @param 
     */
    public function saveImageFromUrl($url, $uid, $class = 1, $path = "image", $thumb = false, $fixed = false)
    {
        $pathinfo = pathinfo($url); 

        // print_r($pathinfo);

        $fileExt = $this->getImageType($pathinfo['extension']);
        // Allow Type
        if (!$this->hasImageType($fileExt)) {
            # code...
        }

        // Get file path
        if (!$this->_file = $this->getFilePath($pathinfo['basename'], $uid, true, $path, $fixed)) {
            # code...
        }

        $data = file_get_contents($url);
        $img_size = file_put_contents(FILES_PATH . '/files' . $this->_file, $data);

        if ($img_size) {
            $imageParam = array(
            'uid' => $uid,
            'class' => $class,
            'title' => $pathinfo['basename'],
            'filename' => $this->fileName,
            'type' => "image/".$pathinfo['extension'],
            'size' => strlen($data),
            'path' => $this->images->escapeString($this->_file),
            'thumb' => 0,
            'published' => time()
            );

            $this->images->insert($imageParam);
            $this->insertId = $this->images->insertId();
            return $this->insertId ? $this->insertId : 0;
        }
        return false;
    }

    /**
     * [Get file path ]
     * @param  [type]  $fileType [description]
     * @param  boolean $mkdir    [description]
     * @param  string  $type     [$type = {'head','image','book'}
     * @param  String  $fixed    the path is fixed dir.
     * @return [type]            [description]
     */
    public function getFilePath($file, $id, $mkdir = false, $path = "image" ,$fixed = false)
    {
        $pathOne = gmdate("Ym");
        $pathTwo = gmdate('j');

        $common =  \Yaf\Registry::get('common');

        if (!$id) return false;
        $fileInfo = pathinfo($file);
        $this->fileName = $id ."_".$common->random(4) . "_" . date("Ymd_hi") .'.'.$fileInfo['extension'];

        if ($mkdir) {
            if($fixed)
            {
                $newFilePath = FILES_PATH . '/files' . $this->path[$path] . $fixed;
                if (!is_dir($newFilePath)) {
                    if (!mkdir($newFilePath,0755)) {
                        return $this->fileName;
                    }
                }
            }
            else
            {

                $newFilePath = FILES_PATH . '/files' . $this->path[$path]. $pathOne;


                if (!is_dir($newFilePath)) {
                    if (!mkdir($newFilePath,0755)) {
                        return $this->fileName;
                    }
                }

                $newFilePath .= "/".$pathTwo;

                if (!is_dir($newFilePath)) {
                    if (!mkdir($newFilePath)) {
                        return $pathOne . "/" . $this->fileName;
                    }
                }
            }

        }

        if($fixed) return $this->path[$path].$fixed . '/' . $this->fileName;
        else return $this->path[$path].$pathOne."/".$pathTwo."/".$this->fileName;
    }

    /**
     * Move file
     *
     * @param String $tmpName
     * @return Boolean 
     */
    public function upload($tmpName)
    {
        if ($this->_file) {
            if (copy($tmpName,FILES_PATH . '/files' . $this->_file)) {
                unlink($tmpName);
            }
            elseif (function_exists('move_uploaded_file') and move_uploaded_file($tmpName, FILES_PATH . '/files' . $this->_file)) {
                # code...
            }
            elseif (rename($tmpName, FILES_PATH . '/files' . $this->_file)) {
                # code...
            }
            else {
                return false;
            }
            return true;
        }
    }

    /**
     * [makethumb 生成缩略图]
     * @param  [type] $srcfile [description]
     * @return [type]          [description]
     */
    public function makethumb($srcfile, $thumbwidth = 100 , $thumbheight = 100 ,$salt = 'thumb') {

        //判断文件是否存在
        if (!file_exists($srcfile)) {
            return false;
        }

        $dstfile = $srcfile . '.' . $salt;

        //缩略图大小
        $tow = intval($thumbwidth);
        $toh = intval($thumbheight);
        if($tow < 60) $tow = 60;
        if($toh < 60) $toh = 60;

        $make_max = 0;
        // max size
        $maxtow = 800;
        $maxtoh = 800;
        if($maxtow >= 300 && $maxtoh >= 300) {
            $make_max = 1;
        }
        
        //获取图片信息
        $im = '';
        if($data = getimagesize($srcfile)) {
            if($data[2] == 1) {
                $make_max = 0;//gif不处理
                if(function_exists("imagecreatefromgif")) {
                    $im = imagecreatefromgif($srcfile);
                }
            } elseif($data[2] == 2) {
                if(function_exists("imagecreatefromjpeg")) {
                    $im = imagecreatefromjpeg($srcfile);
                }
            } elseif($data[2] == 3) {
                if(function_exists("imagecreatefrompng")) {
                    $im = imagecreatefrompng($srcfile);
                }
            }
        }
        if(!$im) return false;
        
        $srcw = imagesx($im);
        $srch = imagesy($im);
        
        $towh = $tow/$toh;
        $srcwh = $srcw/$srch;
        if($towh <= $srcwh){
            $ftow = $tow;
            $ftoh = $ftow*($srch/$srcw);
            
            $fmaxtow = $maxtow;
            $fmaxtoh = $fmaxtow*($srch/$srcw);
        } else {
            $ftoh = $toh;
            $ftow = $ftoh*($srcw/$srch);
            
            $fmaxtoh = $maxtoh;
            $fmaxtow = $fmaxtoh*($srcw/$srch);
        }
        if($srcw <= $maxtow && $srch <= $maxtoh) {
            $make_max = 0;//不处理
        }
        if($srcw > $tow || $srch > $toh) {
            if(function_exists("imagecreatetruecolor") && function_exists("imagecopyresampled") && @$ni = imagecreatetruecolor($ftow, $ftoh)) {
                imagecopyresampled($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
                //大图片
                if($make_max && @$maxni = imagecreatetruecolor($fmaxtow, $fmaxtoh)) {
                    imagecopyresampled($maxni, $im, 0, 0, 0, 0, $fmaxtow, $fmaxtoh, $srcw, $srch);
                }
            } elseif(function_exists("imagecreate") && function_exists("imagecopyresized") && @$ni = imagecreate($ftow, $ftoh)) {
                imagecopyresized($ni, $im, 0, 0, 0, 0, $ftow, $ftoh, $srcw, $srch);
                //大图片
                if($make_max && @$maxni = imagecreate($fmaxtow, $fmaxtoh)) {
                    imagecopyresized($maxni, $im, 0, 0, 0, 0, $fmaxtow, $fmaxtoh, $srcw, $srch);
                }
            } else {
                return '';
            }
            if(function_exists('imagejpeg')) {
                imagejpeg($ni, $dstfile . ".jpeg");
                //大图片
                if($make_max) {
                    imagejpeg($maxni, $srcfile);
                }
            } elseif(function_exists('imagepng')) {
                imagepng($ni, $dstfile . '.png');
                //大图片
                if($make_max) {
                    imagepng($maxni, $srcfile);
                }
            }
            imagedestroy($ni);
            if($make_max) {
                imagedestroy($maxni);
            }
        }
        imagedestroy($im);

        if(!file_exists($dstfile)) {
            return '';
        } else {
            return $dstfile;
        }
    }

    /**
     * [makewatermark 图片水印]
     * @param  [type] $srcfile [description]
     * @return [type]          [description]
     */
    public function makewatermark($srcfile) {
        
        //水印图片
        $watermarkfile = empty($_SGLOBAL['setting']['watermarkfile'])?S_ROOT.'./image/watermark.png':$_SGLOBAL['setting']['watermarkfile'];
        if(!file_exists($watermarkfile) || !$water_info = getimagesize($watermarkfile)) {
            return '';
        }
        $water_w = $water_info[0];
        $water_h = $water_info[1];
        $water_im = '';
        switch($water_info[2]) {
            case 1:@$water_im = imagecreatefromgif($watermarkfile);break;
            case 2:@$water_im = imagecreatefromjpeg($watermarkfile);break;
            case 3:@$water_im = imagecreatefrompng($watermarkfile);break;
            default:break;
        }
        if(empty($water_im)) {
            return '';
        }

        //原图
        if(!file_exists($srcfile) || !$src_info = getimagesize($srcfile)) {
            return '';
        }
        $src_w = $src_info[0];
        $src_h = $src_info[1];
        $src_im = '';
        switch($src_info[2]) {
            case 1:
                //判断是否为动画
                $fp = fopen($srcfile, 'rb');
                $filecontent = fread($fp, filesize($srcfile));
                fclose($fp);
                if(strpos($filecontent, 'NETSCAPE2.0') === FALSE) {//动画图不加水印
                    @$src_im = imagecreatefromgif($srcfile);
                }
                break;
            case 2:@$src_im = imagecreatefromjpeg($srcfile);break;
            case 3:@$src_im = imagecreatefrompng($srcfile);break;
            default:break;
        }
        if(empty($src_im)) {
            return '';
        }
        
        //加水印的图片的长度或宽度比水印小150px
        if(($src_w < $water_w + 150) || ($src_h < $water_h + 150)) {
            return '';
        }
        
        //位置
        switch($_SGLOBAL['setting']['watermarkpos']) {
            case 1://顶端居左
                $posx = 0;
                $posy = 0;
                break;
            case 2://顶端居右
                $posx = $src_w - $water_w;
                $posy = 0;
                break;
            case 3://底端居左
                $posx = 0;
                $posy = $src_h - $water_h;
                break;
            case 4://底端居右
                $posx = $src_w - $water_w;
                $posy = $src_h - $water_h;
                break;
            default://随机
                $posx = mt_rand(0, ($src_w - $water_w));
                $posy = mt_rand(0, ($src_h - $water_h));
                break;
        }

        //设定图像的混色模式
        @imagealphablending($src_im, true);
        //拷贝水印到目标文件
        @imagecopy($src_im, $water_im, $posx, $posy, 0, 0, $water_w, $water_h);
        switch($src_info[2]) {
            case 1:@imagegif($src_im, $srcfile);break;
            case 2:@imagejpeg($src_im, $srcfile);break;
            case 3:@imagepng($src_im, $srcfile);break;
            default:return '';
        }
        @imagedestroy($water_im);
        @imagedestroy($src_im);
    }

    /**
     * Get store path
     *
     * @param String , $path
     * @return String , the store path.
     */
    public static function getRelativeImage($path)
    {
        $imagePath = \Yaf\Application::app()->getConfig()->toArray();
        return $imagePath['server']['imagesBook'].$path;
    }


        /**
     * Get BookImage Row
     *
     * @param Array , $option
     * @return Array
     */
    public function getBookImageRow($option = array())
    {
        if (!is_array($option) or !$option) return false;

        $sql = '';
        $i = 1;
        $count = count($option);
        foreach ($option as $key => $value) {
            if($i == $count) $sql .= "$key='" . $value . "'";
            else $sql .= "$key='" . $value . "' AND ";
            $i ++;
        }

        return $this->bookimage->where($sql)->fetchRow();
    }

    /**
     * Add BookImage Table
     *
     * @param Int ,$insertId , if $insertId = 0, then $insertId = lastest mysql insertid
     * @param Int ,$bid
     * @param Int ,$type
     *
     * @return Boolean or Int
     */
    public function addBookImage($insertId,$bid,$type = 1,$name = false)
    {
        if (!$insertId or !$bid) return 0;

        $fields = array(
            'bid'  => $bid,
            'pid'  => $insertId,
            'type' => $type
        );

        if ($data = $this->getBookImageRow(array('bid'=>$bid,'type'=>$type))) {
           return $this->updateBookImage($data['id'],$fields);
        }

        $name and $fields['name'] = $name;
        $this->bookimage->insert($fields);

        return $this->bookimage->insertId() ? $this->bookimage->insertId() : 0;
    }

    /**
     * Update BookImage Table
     *
     * @param Int ,pramary key
     * @param Array , $fields
     * @return Boolean
     */
    public function updateBookImage($id,$fields = array())
    {
        if(!is_array($fields) or isset($fields['id'])) return false;

        return $this->bookimage->where("id='$id'")->update($fields);
    }

    /**
     * Delete BookImage Row For pid
     *
     * @param Int ,$pid
     * @return Boolean
     */
    public function deleteBookImageForPid($pid)
    {
        if(!$pid) return false;
        return $this->bookimage->where("pid='$pid'")->delete();
    }

    /**
     * Get Images Row
     *
     * @param Array , $option
     * @return Array
     */
    public function getImagesRow($option = array())
    {
        if (!is_array($option) or !$option) return false;

        $sql = '';
        $i = 1;
        $count = count($option);
        foreach ($option as $key => $value) {
            if($i == $count) $sql .= " $key='" . $value . "' ";
            else $sql .= " $key='" . $value . "' AND ";
            $i ++;
        }

        return $this->images->where($sql)->fetchRow();
    }

    public function addImages()
    {

    }

    /**
     * Delete Images Row For pid
     *
     * @param Int ,$pid
     * @return Boolean
     */
    public function deleteImagesForPid($pid)
    {
        if(!$pid) return false;
        $item = $this->getImagesRow(array('pid'=> $pid));
        $is_delete = $this->images->where("pid='$pid'")->delete();
        if($is_delete) $this->unlink($item['path']);
        return $is_delete;
    }

}