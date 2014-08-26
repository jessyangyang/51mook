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
use \local\common\Pinyin;
use \mook\control\common\LinkManage;
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
    public function getCourseList($option = array(),$limit = 10,$page = 1, $order = false)
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

        $order = $order ? $order : "$table.dateline DESC";

        $list = $this->course->field("$table.cid,$table.title,$table.ccid,$table.uid,$table.private,$table.published,$table.verified,$table.dateline,$table.modified,$table.summary,$table.tags,$table.price,cc.name as category, m.username,ic.path as cover,ic.thumb, im.path as usercover, mi.summary as usersummary, cf.click as clickcount, cf.student as studentcount, cf.chapters as chapterscount")
            ->joinQuery('course_category as cc',"$table.ccid=cc.ccid")
            ->joinQuery('course_fields as cf',"$table.cid=cf.cid")
            ->joinQuery('images_course as ic',"$table.cover=ic.icid")
            ->joinQuery('members as m',"$table.uid=m.id")
            ->joinQuery('member_info as mi',"m.id=mi.id")
            ->joinQuery('images_member as im','m.id=im.uid')
            ->where($sql)->order($order)
            ->limit("$offset,$limit")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['usercover']) and $value['usercover']) {
                    $list[$key]['usercover_s'] = ImagesManage::getRealCoverSize($value['usercover']);
                    $list[$key]['usercover_m'] = ImagesManage::getRealCoverSize($value['usercover'],'medium');
                    $list[$key]['usercover'] = ImagesManage::getRelativeImage($value['usercover']);
                    
                    empty($list[$key]['usercover_s']) and $list[$key]['usercover_s'] = $list[$key]['usercover_m'];
                    empty($list[$key]['usercover_m']) and $list[$key]['usercover_m'] = $list[$key]['usercover'];
                }
                if (isset($value['title']) and $value['title']) {
                    $list[$key]['ptitle'] = $this->convert($value['title']);
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

    public function getCourseGroup($option = array(),$limit = 10,$page = 1)
    {
        $list = $this->getCourseList($option, $limit, $page);

        if ($list) {
            $datas = array();
            foreach ($list as $key => $value) {
                if($value['cid'] > 0) $datas[$value['category']][] = $value;
            }
            return $datas;
        }
        return $datas;
    }

    /**
     * [createCourse description]
     * @param boolean $data [description]
     */
    public function createCourse($uid , $data = false)
    {
        if (!$data and !$uid) return false;

        $course = $this->courseFilter($data);
        $fields = $this->courseFieldsFilter($data);

        if ($course) {

            !isset($course['ccid']) and $course['ccid'] = 0;
            $course['dateline'] = UPDATE_TIME;
            $course['modified'] = UPDATE_TIME;
            $course['uid'] = $uid;

            $this->course->begin();
            if ($cid = $this->addCourse($course)) {
                if ($fields) {
                    $fields['cid'] = $cid;
                    $this->course_fields->insert($fields);
                }
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
        $fields = $this->courseFieldsFilter($data);

        if ($courseFilter and count($courseFilter) > 0) {
            $courseFilter['modified'] = UPDATE_TIME;
            $this->course->begin();
            if ($this->course->where("cid='$cid'")->update($courseFilter)) {
                if ($fields) {
                    if ($this->getCourseFields($cid)) {
                        $this->course_fields->where("cid='$cid'")->update($fields);
                    }
                    else
                    {
                        $fields['cid'] = $cid;
                        $this->course_fields->insert($fields);
                    }
                }
                $this->course->commit();
                return true;
            }
            $this->course->rollback();
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
                $this->course_fields->where("cid=$cid")->delete();
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
        if (is_array($list)) {
            if (isset($list['url']) and $list['url']) {
                $url = parse_url($list['url']);
                $list['host'] = $url['host'];
            }
            else
            {
                $list['host'] = $_SERVER['HTTP_HOST'];
            }
            if (isset($list['title']) and $list['title']) {
                $list['ptitle'] = $this->convert($list['title']);
            }
            $list['host'] = preg_replace('/www./','',$list['host']);
            return $list;
        }
        return false;
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
            'summary' => isset($datas['summary']) ? $datas['summary'] : '',
            'wordcount' => isset($datas['wordcount']) ? $datas['wordcount'] : 0,
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
                $this->updateCourseFields(array('chapters' => '+'), $cid);
                return $ccid;
            }
            $this->course_chapter->rollback();
        }
        return false;
    }


    public function deleteArticle($ccid)
    {
        if ($article = $this->getArticleForID($ccid)) {
            $this->course_chapter->begin();
            if ($this->course_chapter->where("ccid='$ccid'")->delete()) {
                $this->course_chapter->commit();
                $this->updateCourseFields(array('chapters' => '-'), $article['cid']);
                return true;
            }
            $this->course_chapter->rollback();
        }
        return false;
    }



    /*Student*/

    /**
     * [getCourseStudentList description]
     * @param  array  $option [description]
     * @return [type]         [description]
     */
    public function getCourseStudentList($option = array())
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

        $table = $this->course_student->table;

        $list = $this->course_student->field("$table.csid,$table.uid,$table.cid,$table.dateline")
            ->where($sql)->order("$table.dateline DESC")->fetchList();

        return $list;
    }

    public function createCourseStudent($datas)
    {
        if (!$datas) return false;

        $arr = array(
            'uid' => $datas['uid'],
            'cid' => $datas['uid'],
            'dateline' => UPDATE_TIME);

        return $this->course_student->insert($arr);
    }

    public function deleteCourseStudentForId($csid)
    {
        return $this->course_student->where("csid='$csid'")->delete();
    }

    public function getCourseStudyList($option = array())
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

        $table = $this->course_study->table;

        $list = $this->course_study->field("$table.csid,$table.ccid,$table.dateline")
            ->where($sql)->order("$table.dateline DESC")->fetchList();

        return $list;
    }

    public function createCourseStudy($datas)
    {
        if (!$datas) return false;

        $arr = array(
            'ccid' => $datas['ccid'],
            'dateline' => UPDATE_TIME);

        return $this->course_study->insert($arr);
    }

    public function deleteCourseStudyForId($csid)
    {
        return $this->course_study->where("csid='$csid'")->delete();
    }

    public function getCourseFields($cid = false)
    {
        if ($cid) {
            return $this->course_fields->where("cid='$cid'")->fetchRow();
        }
        else
        {
            return $this->course_fields->fetchList();
        }
        return false;
    }

    public function createCourseFields($datas = array(), $cid)
    {
        if (!$datas and !$cid) return false;

        $fields = $this->courseFieldsFilter($datas);

        $this->course_fields->begin();
        $list = $this->getCourseFields($cid);

        if (!$list){
            $fields['cid'] = $cid;
            $this->course_fields->insert($fields);
            $this->course_fields->commit();
            return true;
        }
        else if ($this->course_fields->where("cid='$cid'")->update($fields)) {
            $this->course_fields->commit();
            return true;
        }

        $this->course_fields->rollback();
        return false;
    }

    /**
     * [updateCourseFields update integer field value]
     *
     * array (
     *     'student':"+", // "+" student + 1 , "-" student - 1
     *     'chapters': "-",
     *     'click': '+' 
     * )
     * 
     * @param  array  $datas [description]
     * @return [type]        [description]
     */
    public function updateCourseFields($datas = array(), $cid )
    {
        if (!$datas and !$cid) return false;

        $fields = $this->courseFieldsFilter($datas);

        $list = array(
            'student' => 0,
            'chapters' => 0,
            'click' => 0);


        $chapters = $this->getCourseFields($cid);

        if (!$chapters) {
            $this->course_fields->begin();
            $cid = $this->course_fields->insert(array('cid' => $cid));
            $this->course_fields->commit();
        }
        else
        {
            $list = $chapters;
            unset($list['cid']);
        }

        $count = $this->getChapterCountForCID($cid);

        foreach ($fields as $key => $value) {
            switch ($value) {
                case '+':
                    $list[$key] = $count + 1;
                    break;
                case '-':
                    $list[$key] = $count == 0 ? 0 : $count - 1;
                    break;
                default:
                    # code...
                    break;
            }
        }

        $this->course_fields->begin();
        if ($this->course_fields->where("cid=$cid")->update($list)) {
            $this->course_fields->commit();
            return true;
        }    
        $this->course_fields->rollback();
        return false;
    }

    public function deleteCourseFieldsForId($cid)
    {
        return $this->course_fields->where("cid='$cid'")->delete();
    }



    /*Category*/

    /**
     * [getCategory description]
     * @return [type] [description]
     */
    public function getCategory()
    {
    	return $this->course_category->order("sort")->fetchList();
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
        $list = array();
        if ($limit and $page) {
           $page = $page - 1;
           $offset = $limit * $page;
           $cid = $this->course_chapter->escapeString($cid);
           $list =  $this->course_chapter->where("cid='$cid'")->order('sort')->limit("$offset,$limit")->fetchList();
        }
        $list = $this->course_chapter->where("cid='$cid'")->order('sort')->fetchList();

        if (is_array($list) and count($list) > 0) {
            foreach ($list as $key => $value) {
                if (isset($value['url']) and $value['url']) {
                    $url = parse_url($value['url']);
                    $list[$key]['host'] = $url['host'];
                }
                else
                {
                    $list[$key]['host'] = $_SERVER['HTTP_HOST'];
                }
                $list[$key]['host'] = preg_replace('/www./','',$list[$key]['host']);

                $list[$key]['studytime'] = 0;
                if (isset($value['body']) and $value['body']) {
                    $list[$key]['studytime'] = round(mb_strlen($value['body'], 'UTF-8') / 300);
                }

                if (isset($value['title']) and $value['title']) {
                    $list[$key]['ptitle'] = $this->convert($value['title']);
                }
            }
            return $list;
        }
        return false;
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
        isset($data['published']) and $filter['published'] = $data['published'];
        isset($data['verified']) and $filter['verified'] = $data['verified'];
        isset($data['dateline']) and $filter['dateline'] = strtotime($data['dateline']);
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
        isset($data['body']) and $filter['body'] = $data['body'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    /**
     * [courseFieldsFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function courseFieldsFilter($data)
    {
        $filter = array();
        isset($data['click']) and $filter['click'] = $data['click'];
        isset($data['student']) and $filter['student'] = $data['student'];
        isset($data['chapters']) and $filter['chapters'] = $data['chapters'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    /**
     * [convert description]
     * @param  [type] $string [description]
     * @param  string $code   [description]
     * @return [type]         [description]
     */
    public function convert($string, $code = "utf-8")
    {
        $pinyin = Pinyin::instance();
        $string = $pinyin->convert($string, $code);
        if ($string) return $string;
        return false;
    }


    public function addLinkToArticle($cid, $url, $summary = false)
    {
        $htmls = LinkManage::link($url);

        if (!$htmls) return false;

        $count = $this->getChapterCountForCID($cid);


        $datas = array(
            'title' => $htmls['title'],
            'url' => $url,
            'body' => $htmls['content'],
            'wordcount' => $htmls['word_count'],
            'summary' => $summary,
            'sort' => $count ? $count : 0
        );

        $ccid = $this->createArticle($cid, $datas);

        if ($ccid) {
            return $this->getArticleForID($ccid);
        }

        return false;
    }
}