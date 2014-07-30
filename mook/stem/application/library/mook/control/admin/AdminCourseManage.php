<?php
/**
* AdminCourseManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\admin;

use \mook\control\common\ImagesManage;
use \lib\dao\CourseControl;

use \lib\models\course\Course;
use \lib\models\course\CourseChapter;
use \lib\models\course\CourseCategory;

use \Yaf\Registry;

class AdminCourseManage extends CourseControl
{
	const VERSION = "1.0";

	// Instance Self
    protected static $instance;

    public static function instance()
    {
        return self::$instance ? self::$instance : new AdminCourseManage();
    }

    /**
     * Instance construct
     */
    function __construct() {
        parent::__construct();
    }

    function __destruct() {
        parent::__destruct();
    }

    /**
     * [getCourseCount description]
     * @param  array  $option [description]
     * @return [type]         [description]
     */
    public function getCourseCount($option = array())
    {
        $sql = '';

        if (is_array($option) or $option)
        {
            $i = 1;
            $count = count($option);
            foreach ($option as $key => $value) {
                if($i == $count) $sql .= "$key='" . $value . "'";
                else $sql .= "$key='" . $value . "' AND ";
                $i ++;
            }
        }

        $count = $this->course->query("select count(*) from course");
        return $count ? $count[0]['count(*)'] : 0;
    }

    /**
     * [getCourseList description]
     * @param  array   $option [description]
     * @param  integer $limit  [description]
     * @param  integer $page   [description]
     * @return [type]          [description]
     */
    public function getCourseList($option = array(),$limit = 10,$page = 1)
    {
        $sql = '';

        if (is_array($option) or $option)
        {
            $i = 1;
            $count = count($option);
            foreach ($option as $key => $value) {
                if($i == $count) $sql .= "$key='" . $value . "'";
                else $sql .= "$key='" . $value . "' AND ";
                $i ++;
            }
        }

        $offset = $page == 1 ? 0 : ($page - 1)*$limit;

        $table = $this->course->table;

        $list = $this->course->field("$table.cid,$table.title,$table.ccid,$table.uid,$table.private,$table.verified,$table.dateline,$table.modified,$table.summary,$table.tags,$table.price,cc.name as category, m.username,ic.path as cover,ic.thumb, im.path as usercover")
            ->joinQuery('course_category as cc',"$table.ccid=cc.ccid")
            ->joinQuery('images_course as ic',"$table.cover=ic.icid")
            ->joinQuery('members as m',"$table.uid=m.id")
            ->joinQuery('images_member as im','m.id=im.uid')
            ->where($sql)->order("$table.dateline DESC")
            ->limit("$offset,$limit")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['usercover']) and $value['usercover']) {
                    $list[$key]['usercover'] = ImagesManage::getRealCoverSize($value['usercover']);
                }
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover'] = ImagesManage::getRelativeImage($value['cover']);
                }
                if (isset($value['thumb']) and $value['thumb'] == 1) {
                    $list[$key]['cover'] = ImagesManage::getRealCoverSize($value['cover'],'medium','jpg');
                }
                if (isset($value['published']) and $value['published']) {
                    $list[$key]['published'] = $this->changedCourseStatus(intval($value['published']));
                }
                if (isset($value['verified']) and $value['verified']) {
                    $list[$key]['verified'] = $this->changedCourseVerified(intval($value['verified']));
                }
            }
            return $list;
        }

        return false;
    }

    /**
     * [getCourseRow description]
     * @param  array  $option [description]
     * @return [type]         [description]
     */
    public function getCourseRow($option = array())
    {
        $list = $this->getCourseList($option,1,1);
        if ($list) return $list[0];
        return false;
    }

    /**
     * [createCourse description]
     * @param boolean $data [description]
     */
    public function createCourse($uid , $data = false)
    {
        if (!$data and !$uid) return false;

        $course = $this->courseFilter($data);

        if ($course) {

            !isset($course['ccid']) and $course['ccid'] = 0;
            $course['dateline'] = UPDATE_TIME;
            $course['modified'] = UPDATE_TIME;
            $course['uid'] = $uid;

            $this->course->begin();
            $cid = $this->addCourse($course);

            if($cid) {
                $this->course->commit();
                return $cid;
            }
            $this->course->rollback();
        }
        return false;
    }

    /**
     * [updateCourse description]
     * @param  boolean $data [description]
     * @return [type]        [description]
     */
    public function updateCourse($cid , $data = false)
    {
        if (!$cid) return false;

        $courseFilter = $this->courseFilter($data);

        if ($courseFilter and count($courseFilter) > 0) {
            $courseFilter['modified'] = UPDATE_TIME;
            return $this->course->where("cid='$cid'")->update($courseFilter);
        }
        return false;
    }

    /**
     * [deleteCourse description]
     * @param  [type] $cid [description]
     * @return [type]      [description]
     */
    public function deleteCourse($cid)
    {
        $course = $this->getCourseRow(array('course.cid' => $cid));
        if ($course) {
            $this->course->begin();

            if ($this->course->where("cid=$cid")->delete())
            {
                $this->course->commit();
                return true;
            }
            $this->course->rollback();
        }
        return false;
    }


    /*Article*/

    /**
     * [getArticleForID description]
     * @param  [type] $ccid [description]
     * @return [type]      [description]
     */
    public function getArticleForID($ccid)
    {
        $table = $this->course_chapter->table;
        $list =  $this->course_chapter->where("ccid='$ccid'")->fetchRow();
        return $list ? $list : false;
    }

    /**
     * [createArticle description]
     * @param  [type] $uid  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createArticle($cid, $datas)
    {
        if (!$cid and !is_array($datas)) return false;

        $data = array(
            'ccid' => isset($datas['ccid']) ? $datas['ccid'] : false,
            'title' => isset($datas['title']) ? $datas['title'] : '',
            'url' => isset($datas['url']) ? $datas['url'] : '',
            'body' => isset($datas['body']) ? $datas['body'] : '',
            'sort' => isset($datas['sort']) ? $datas['sort'] : ''
        );

        $chapter  = $this->courseChapterFilter($data);

        if ($chapter) $chapter['cid'] = $cid;

        if (isset($data['ccid']) and $data['ccid']){
            $chapter['modified'] = UPDATE_TIME;

            $this->course_chapter->begin();
            if ($this->course_chapter->where("ccid='". $data['ccid'] ."'")->update($chapter))
            {
                $this->course_chapter->commit();
                return $data['ccid'];
            }
            $this->course_chapter->rollback();
        }
        else {
            $chapter['dateline'] = UPDATE_TIME;
            $chapter['modified'] = UPDATE_TIME;

            $this->course_chapter->begin();
            if ($ccid = $this->course_chapter->insert($chapter)) {
                $this->course_chapter->commit();
                return $ccid;
            }
            $this->course_chapter->rollback();
        }
        return false;
    }


    public function deleteArticle($ccid)
    {
        if ($this->getArticleForID($ccid)) {
            $this->course_chapter->begin();
            if ($this->course_chapter->where("ccid='$ccid'")->delete()) {
                $this->course_chapter->commit();
                return true;
            }
            $this->course_chapter->rollback();
        }
        return false;
    }

    /*Category*/

    /**
     * [getCategory description]
     * @return [type] [description]
     */
    public function getCategory()
    {
    	return $this->course_category->fetchList();
    }

    public function getCategoryCount()
    {
        return $this->course_category->count('*');
    }

    public function getCategoryForId($ccid)
    {
        return $this->course_category->where("ccid='$ccid'")->fetchRow();
    }

    /**
     * [addCategory description]
     * @param boolean $datas [description]
     */
    public function addCategory($datas = false)
    {
        if (!$datas) return false;

        $arr = array(
            'name' => $datas['name'],
            'sort' => ($datas['sort'] ? $datas['sort'] : 0),
            'cover' => 0,
            'dateline' => UPDATE_TIME);

        return $this->course_category->insert($arr);
    }

    public function updateCategory($ccid, $datas)
    {
        if(!is_array($datas)) return false;

        $arr = array(
            'name' => $datas['name'],
            'sort' => $datas['sort']);

        return $this->course_category->where("ccid='$ccid'")->update($arr);
    }

    public function deleteCategoryForId($ccid)
    {
        return $this->course_category->where("ccid='$ccid'")->delete();
    }

    /**
     * [getChapterForCID description]
     * @param  [type]  $cid   [description]
     * @param  boolean $limit [description]
     * @param  boolean $page  [description]
     * @return [type]         [description]
     */
    public function getChapterForCID($cid, $limit = false, $page = false)
    {
        if ($limit and $page) {
           $page = $page - 1;
           $offset = $limit * $page;
           $cid = $this->course_chapter->escapeString($cid);
           return $this->course_chapter->where("cid='$cid'")->order('sort')->limit("$offset,$limit")->fetchList();
        }
        return $this->course_chapter->where("cid='$cid'")->order('sort')->fetchList();
    }

    public function getChapterCountForCID($cid)
    {
        $table = $this->course_chapter->table;
        $cid = $this->course_chapter->escapeString($cid);
        $list = $this->course_chapter->query("select count(*) from $table where cid='$cid'");
        if ($list) return $list[0]['count(*)'];
        return false;
    }

    public function getChapterForTitle($title)
    {
        $table = $this->course_chapter->table;
        return $this->course_chapter->where(array('title' => $title))->fetchRow();
    }


    public function updateChapterSort($cid, $data)
    {
        if (!is_array($data)) return false;

        $ids = implode(',', array_values($data));
        $sql = "UPDATE course_chapter SET sort = CASE ccid ";
        foreach ($data as $id => $ordinal) {
            $sql .= sprintf("WHEN %d THEN %d ", $ordinal, $id);
        }
        $sql .= "END WHERE ccid IN ($ids)";
        return $this->course_chapter->query($sql);
    }


         /**
     * [courseFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function courseFilter($data)
    {
        $filter = array();
        isset($data['ccid']) and $filter['ccid'] = $data['ccid'] ? $data['ccid'] : 0;
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['uid']) and $filter['uid'] = $data['uid'];
        isset($data['private']) and $filter['private'] = $data['private'];
        isset($data['published']) and $filter['published'] = strtotime($data['published']);
        isset($data['verified']) and $filter['verified'] = $data['verified'];
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        isset($data['modified']) and $filter['modified'] = $data['modified'];
        isset($data['summary']) and $filter['summary'] = $data['summary'];
        isset($data['cover']) and $filter['cover'] = $data['cover'];
        isset($data['tags']) and $filter['tags'] = $data['tags'];
        isset($data['price']) and $filter['price'] = $data['price'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    /**
     * [courseChapter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function courseChapterFilter($data)
    {
        $filter = array();
        isset($data['cid']) and $filter['cid'] = $data['cid'];
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['type']) and $filter['type'] = $data['type'] ? $data['type'] : 1;
        isset($data['sort']) and $filter['sort'] = $data['sort'] ? $data['sort'] : 0;
        isset($data['summary']) and $filter['summary'] = $data['summary'];
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        isset($data['modified']) and $filter['modified'] = $data['modified'];
        isset($data['url']) and $filter['url'] = $data['url'];
        isset($data['wordcount']) and $filter['wordcount'] = $data['wordcount'];
        isset($data['summary']) and $filter['summary'] = $data['summary'];
        isset($data['body']) and $filter['body'] = $data['body'];
        if (count($filter) > 0) return $filter;
        return false; 
    }
}