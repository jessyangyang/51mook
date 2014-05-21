<?php
/**
* AdminBookManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\admin;

use \mook\control\common\ImagesManage;

use \lib\models\collection\Collection;
use \lib\models\collection\CollectionCategory;
use \lib\models\collection\CollectionAllowBlog;
use \mook\control\admin\AdminBookManage;

use \Yaf\Registry;

class AdminCollectionManage extends \mook\control\common\BlogManage
{
	protected $collection;
    protected $collectionCategory;
    protected $collectionAllowBlog;

    /**
     * Instance construct
     */
    function __construct() {
        $this->collection = Collection::instance();
        $this->collectionCategory = CollectionCategory::instance();
        $this->collectionAllowBlog = CollectionAllowBlog::instance();
    }

    function __destruct() {
        $this->collection = NULL;
        $this->collectionCategory = NULL;
        $this->collectionAllowBlog = NULL;
    }

    /**
     * [getCollectionCategory description]
     * @return [type] [description]
     */
    public function getCollectionCategory()
    {
        return $this->collectionCategory->fetchList();
    }

    public function getCollectionCategoryForID($ccid)
    {
        return $this->collectionCategory->where("ccid='$ccid'")->fetchRow();
    }

    public function addCollectionCategory($data = array())
    {
        if($data) $data['dateline'] = UPDATE_TIME;
        return $this->collectionCategory->insert($this->collectionCategoryFilter($data));
    }

    public function updateCollectionCategory($ccid, $data = array())
    {
        if ($data) {
            return $this->collectionCategory->where("ccid='$ccid'")->update($this->collectionCategoryFilter($data));
        }
        return false;
    }

    public function deleteCollectionCategory($ccid)
    {
        return $this->collectionCategory->where("ccid=$ccid")->delete();
    }

    /**
     * [getCollectionAllowBlog description]
     * @return [type] [description]
     */
    public function getCollectionAllowBlog()
    {
        return $this->collectionAllowBlog->fetchList();
    }

    public function getCollectionAllowBlogForID($cabid)
    {
        return $this->collectionAllowBlog->where("cabid='$cabid'")->fetchRow();
    }

    public function addCollectionAllowBlog($data = array())
    {
        if ($data) $data['dateline'] = UPDATE_TIME;
        return $this->collectionAllowBlog->insert($this->collectionAllowBlogFilter($data));
    }

    public function updateCollectionAllowBlog($cabid, $data = array())
    {
        if ($cabid and $data) return $this->collectionAllowBlog->where("cabid='$cabid'")->update($this->collectionAllowBlogFilter($data));
        return false;
    }

    public function deleteCollectionAllowBlog($cabid)
    {
        return $this->collectionAllowBlog->where("cabid='$cabid'")->delete();
    }

    /**
     * [getCollectionList description]
     * @param  array   $option [description]
     * @param  integer $limit  [description]
     * @param  integer $page   [description]
     * @return [type]          [description]
     */
    public function getCollectionList($option = array(),$limit = 10,$page = 1)
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

        $table = $this->collection->table;

        $list = $this->collection->field("$table.ctid,$table.cabid,$table.title,$table.author,$table.ccid,$table.url,$table.dateline,cc.name as category,cab.name as type")
            ->joinQuery('collection_category as cc',"$table.ccid=cc.ccid")
            ->joinQuery('collection_allow_blog as cab',"$table.cabid=cab.cabid")
            ->where($sql)->order("$table.dateline")
            ->limit("$offset,$limit")->fetchList();

        if (is_array($list)) return $list;

        return false;
    }

    public function addCollection($data = array())
    {
        if ($data) {
            $data['dateline'] = UPDATE_TIME;
            return $this->collection->insert($this->collectionFilter($data));
        }
        return false;
    }

    public function updateCollection($ctid, $data = array())
    {
        if ($data) {
            return $this->collection->where("ctid='$ctid'")->update($this->collectionFilter($data));
        }
        return false;
    }

    public function deleteCollection($ctid)
    {
        return $this->collection->where("ctid='$ctid'")->delete();
    }


    public function createBookFromBlog($ctid, $uid, $year = 2014, $page = 1)
    {
        $collection = $this->getCollectionList(array('ctid' => $ctid),1,1);

        if (!$collection) return false;

        $datas = self::loadSinaBlog($collection[0]['url'], $year, $page);

        if (!$datas) return false;

        $book = array();
        $book['title'] = $collection[0]['title'] . " " . $year;
        $book['cid'] = $collection[0]['ccid'];
        $book['pubtime'] = UPDATE_TIME;
        $book['author'] = $collection[0]['author'];
 
        $bookControl = AdminBookManage::instance();

        if ($bid = $bookControl->createBook($uid, $book)) {
            foreach ($datas as $key => $value) {
                $value['type'] = 1;
                $value['sort'] = $key + 1;
                $bookControl->createArticle($bid,$value);
            }
        }
    }

    /**
     * [collectionFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function collectionFilter($data)
    {
        $filter = array();
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['author']) and $filter['author'] = $data['author'];
        isset($data['ccid']) and $filter['ccid'] = $data['ccid'] ? $data['ccid'] : 0;
        isset($data['cabid']) and $filter['cabid'] = $data['cabid'] ? $data['cabid'] : 0;
        isset($data['url']) and $filter['url'] = $data['url'];
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    public function collectionCategoryFilter($data)
    {
        $filter = array();
        isset($data['name']) and $filter['name'] = $data['name'];
        isset($data['sort']) and $filter['sort'] = $data['sort'] ? $data['sort'] : 0;
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    public function collectionAllowBlogFilter($data)
    {
        $filter = array();
        isset($data['name']) and $filter['name'] = $data['name'];
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        if (count($filter) > 0) return $filter;
        return false; 
    }
}