<?php
/**
* BookControllers  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace lib\dao;

use \lib\models\course\Course;
use \lib\models\course\CourseChapter;
use \lib\models\course\CourseCategory;

use \lib\dao\ImageControl;

class CourseControl {
	const VERSION = 1.0;
    const PATH_FOLDER = '/files/course';

    const COURSE_WATTING_PUBLISH_STATE = 1;
    const COURSE_PUBLISHING_STATE = 2;
    const COURSE_UNPUBLISHED_STATE = 3;
    const COURSE_PUBLISHED_STATE = 4;

    const COURSE_SALEOFF_STATE = 1;
    const COURSE_UNVERIFY_STATE = 2;
    const COURSE_VERIFIED_STATE = 3;


    public $course;
    public $course_chapter;
    public $course_category;


    /**
     * Instance construct
     */
    function __construct() {

        $this->course = Course::instance();
        $this->course_chapter = CourseChapter::instance();
        $this->course_category = CourseCategory::instance();
        $this->images = new ImageControl();
    }

    /**
    * Class destructor
    *
    * @return void
    * @TODO make sure elements in the destructor match the current class elements
    */
    function __destruct() {
        $this->course = NULL;
        $this->course_chapter = NULL;
        $this->course_category = NULL;
        $this->images = NULL;
    }

    /**
     * Add Course Table
     *
     * @param Array , fields
     *
     * @return Boolean or Int
     */
    public function addCourse($fields = array())
    {
        if (!is_array($fields) or !$fields) return false;
        return $this->course->insert($fields);
    }

    /**
     * Update Course Table
     *
     * @param Int ,pramary key
     * @param Array , $fields
     * @return Boolean
     */
    public function updateCourse($cid,$fields = array())
    {
        print_r($fields);
        if(!is_array($fields) or isset($fields['cid'])) return false;
        return $this->course->where("cid='$cid'")->update($fields);
    }

    /**
     * Delete Course For Bid
     *
     * @param Int ,$cid
     * @return Boolean
     */
    public function deleteCourseForCid($cid)
    {
        if(!$cid) return false;
        return $this->course->where("cid='$cid'")->delete();
    }

     /**
     * [changedCourseStatus description]
     * @param  [type] $status [description]
     * @return [String]       [description]
     */
    public function changedCourseStatus($status = 1)
    {
        $state = '等待审核';
        switch ($status) {
            case self::COURSE_WATTING_PUBLISH_STATE:
                $state = "等待审核";
                break;
            case self::COURSE_PUBLISHING_STATE:
                $state = "审核中";
                break;
            case self::COURSE_UNPUBLISHED_STATE:
                $state = "审核失败";
                break;
            case self::COURSE_PUBLISHED_STATE:
                $state = "审核通过";
                break;
            default:
                $state = "等待审核";
                break;
        }
        return $state;
    }

    /**
     * [changedCourseVerified description]
     * @param  integer $status [description]
     * @return [type]          [description]
     */
    public function changedCourseVerified($status = 1)
    {
        $state = '未发布';
        switch ($status) {
            case self::COURSE_SALEOFF_STATE:
                $state = "已下架";
                break;
            case self::COURSE_UNVERIFY_STATE:
                $state = "未发布";
                break;
            case self::COURSE_VERIFIED_STATE:
                $state = "已发布";
                break;
            default:
                $state = "已下架";
                break;
        }
        return $state;
    }

}