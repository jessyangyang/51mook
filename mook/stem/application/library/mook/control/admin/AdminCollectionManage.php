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

use \Yaf\Registry;

class AdminCollectionManage \mook\control\common\BlogManage
{
	protected $collection;
    protected $collectionCategory;

    /**
     * Instance construct
     */
    function __construct() {
        $this->collection = Collection::instance();
        $this->collectionCategory = CollectionCategory::instance();
    }

    function __destruct() {
        $this->collection = NULL;
        $this->collectionCategory = NULL;
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

        $list = $this->collection->field("$table.ctid,$table.title,$table.author,$table.ccid,$table.url,$table.blog_id,$table.dateline,cc.name as category")
            ->joinQuery('collection_category as cc',"$table.ccid=cc.ccid")
            ->where($sql)->order("$table.dateline")
            ->limit("$offset,$limit")->fetchList();

        if (is_array($list)) return $list;

        return false;
    }

    public function addCollection($data = array())
    {
        if ($data) {
            return $this->collection->insert($this->collectionFilter($data));
        }
        return false;
    }

    public function updateCollection($ctid, $data = array())
    {
        if ($data) {
            return $this->collection->update($this->collectionFilter($data));
        }
        return false;
    }

    public function deleteCollection($ctid)
    {
        return $this->collection->where("ctid='$ctid'")->delete();
    }


    public function collectionFilter($data)
    {
        $filter = array();
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['author']) and $filter['author'] = $data['author'];
        isset($data['ccid']) and $filter['ccid'] = $data['ccid'] ? $data['ccid'] : 0;
        isset($data['url']) and $filter['url'] = $data['url'];
        isset($data['blog_id']) and $filter['blog_id'] = $data['blog_id'];
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
}