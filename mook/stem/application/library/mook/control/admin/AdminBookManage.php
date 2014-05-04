<?php
/**
* MembersManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control\admin;

use \mook\control\common\ImagesManage;
use \lib\dao\BookControllers;

use \lib\models\BookMenu;
use \lib\models\BookCategory;
use \lib\models\BookChapter;

use \Yaf\Registry;

class AdminBookManage extends BookControllers
{
	protected $bookCategory;
    protected $bookChapter;
    /**
     * Instance construct
     */
    function __construct() {
        parent::__construct();
        $this->bookCategory = BookCategory::instance();
        $this->bookChapter = BookChapter::instance();
    }

    function __destruct() {
        parent::__destruct();
        $this->bookCategory = NULL;
        $this->bookChapter = NULL;
    }

    /**
     * [getBookList description]
     * @param  array   $option [description]
     * @param  integer $limit  [description]
     * @param  integer $page   [description]
     * @return [type]          [description]
     */
    public function getBookList($option = array(),$limit = 10,$page = 1)
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

        $table = $this->book->table;

        $list = $this->book->field("$table.bid,$table.cid,$table.title,$table.author,bc.name as category,$table.pubtime,$table.isbn,$table.press,bi.apple_price as price,$table.summary,bi.tags,bi.price,bf.verified,bf.published,m.username,ib.path as cover,im.path as usercover")
            ->joinQuery('book_fields as bf',"$table.bid=bf.bid")
            ->joinQuery('book_info as bi',"$table.bid=bi.bid")
            ->joinQuery('book_category as bc',"$table.cid=bc.cid")
            ->joinQuery('images_book as ib',"$table.cover=ib.ibid")
            ->joinQuery('members as m','bf.uid=m.id')
            ->joinQuery('images_member as im','m.id=im.uid')
            ->where($sql)->order("$table.published")
            ->limit("$offset,$limit")->fetchList();

        if (is_array($list)) {
            foreach ($list as $key => $value) {
                if (isset($value['usercover']) and $value['usercover']) {
                    $list[$key]['usercover'] = ImagesManage::getRealCoverSize($value['usercover']);
                }
                if (isset($value['cover']) and $value['cover']) {
                    $list[$key]['cover'] = ImagesManage::getRelativeImage($value['cover']);
                }
                if (isset($value['published']) and $value['published']) {
                    $list[$key]['published'] = $this->changedBookStatus(intval($value['published']));
                }
                if (isset($value['verified']) and $value['verified']) {
                    $list[$key]['verified'] = $this->changedBookVerified(intval($value['verified']));
                }
            }
            return $list;
        }

        return false;
    }

    /**
     * Get BookRow Row
     *
     * @param Array , $option
     * @return Array
     */
    public function getBookRow($option = array())
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

        $table = $this->book->table;

        $list = $this->book->field("$table.bid,$table.cid,bc.name,$table.title,$table.author,$table.pubtime,$table.isbn,$table.press,f.subtitle,f.oldtitle,f.apple_price as price,$table.summary,f.translator,f.tags,f.copyright,f.download_path as path,f.designer,f.proofreader,f.wordcount,f.dateline,bf.uid,bf.verified,bf.published,m.username,ib.path as cover")
            ->joinQuery("book_info as f","$table.bid=f.bid")
            ->joinQuery('book_fields as bf',"$table.bid=bf.bid")
            ->joinQuery('book_category as bc',"$table.cid=bc.cid")
            ->joinQuery('images_book as ib',"$table.cover=ib.ibid")
            ->joinQuery('members as m','bf.uid=m.id')
            ->where($sql)->limit(1)->fetchList();

        if ($list and is_array($list)) {
            if (isset($list[0]['cover']) and $list[0]['cover']) {
                $list[0]['cover'] = ImagesManage::getRelativeImage($list[0]['cover']);
            }
            if (isset($list[0]['published']) and $list[0]['published']) {
                $list[0]['published'] = $this->changedBookStatus(intval($list[0]['published']));
            }
            if (isset($list[0]['verified']) and $list[0]['verified']) {
                $list[0]['verified'] = $this->changedBookVerified(intval($list[0]['verified']));
            }
            return $list[0];
        }

        return false;
    }

        /**
     * [createBook description]
     * @param boolean $data [description]
     */
    public function createBook($uid , $data = false)
    {
        if (!$data and !$uid) return false;

        $books = $this->booksFilter($data);

        if ($books) {

            $books['cid'] = 0;
            $books['published'] = UPDATE_TIME;

            $this->book->begin();
            $bid = $this->addBooks($books);

            if($bid and $biid = $this->bookinfo->insert(array('bid' => $bid))) {
                if ($biid and $bfid = $this->bookfields->insert(array('bid' => $bid,'uid' => $uid))) {
                    $this->book->commit();
                    return $bid;
                }
            }
            $this->book->rollback();
        }
        return false;
    }

    /**
     * [updateUserInfo description]
     * @param  boolean $data [description]
     * @return [type]        [description]
     */
    public function updateBook($bid , $data = false)
    {
        if (!$bid) return false;

        $bookFilter = $this->booksFilter($data);

        $bookInfoFilter = $this->booksInfoFilter($data);

        $bookFieldsFilter = $this->booksFieldsFilter($data);

        if ($bookFilter and count($bookFilter) > 0) {
            return $this->book->where("bid='$bid'")->update($bookFilter);
        }

        if ($bookInfoFilter and count($bookInfoFilter) > 0) {
            return $this->bookinfo->where("bid='$bid'")->update($bookInfoFilter);

        }

        if ($bookFieldsFilter and count($bookFieldsFilter) > 0) {
            return $this->bookfields->where("bid='$bid'")->update($bookFieldsFilter);
        }
    }

    /**
     * [deleteBook description]
     * @param  [type] $bid [description]
     * @return [type]      [description]
     */
    public function deleteBook($bid)
    {
        $book = $this->getBookRow(array('books.bid' => $bid));
        if ($book) {
            $this->book->begin();

            if ($this->bookinfo->where("bid=$bid")->delete() and $this->bookfields->where("bid=$bid")->delete() and $this->book->where("bid=$bid")->delete())
            {
                $this->book->commit();
                return true;
            }
            $this->book->rollback();
        }
        return false;
    }


    /**
     * [getArticleForID description]
     * @param  [type] $mid [description]
     * @return [type]      [description]
     */
    public function getArticleForID($mid)
    {
        $table = $this->menu->table;
        $list =  $this->menu->joinQuery('book_chapter as bc',"bc.menu_id = $table.id")
                ->where("id='$mid'")->fetchList();

        if (is_array($list)) return $list[0];
        return false;
    }

    /**
     * [createArticle description]
     * @param  [type] $uid  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function createArticle($bid, $datas)
    {
        if (!$bid and !is_array($datas)) return false;

        $data = array(
            'mid' => isset($datas['mid']) ? $datas['mid'] : false,
            'title' => isset($datas['title']) ? $datas['title'] : '',
            'body' => isset($datas['body']) ? $datas['body'] : '');

        $menus  = $this->booksMenuFilter($data);

        if ($menus) $menus['bid'] = $bid;


        if (isset($data['mid']) and $data['mid']){
            $menus['modified'] = UPDATE_TIME;
            $this->menu->begin();

            if ($this->menu->where("id='". $data['mid'] ."'")->update($menus))
            {
                $body = $this->bookChapter->escapeString($data['body']);
                if ($bookmenu = $this->bookChapter->where("menu_id='". $data['mid'] ."'")->fetchRow()) {
                    $this->bookChapter->where("menu_id='". $bookmenu['menu_id'] ."'")->update(array('body'=> $body));
                    $this->menu->commit();
                    return $bookmenu['menu_id'];
                }
                else
                {
                    $aid = $this->bookChapter->insert(array('menu_id' => $data['mid'],'body' => $body));
                    $this->menu->commit();
                    return $aid;
                }
            }
            $this->menu->rollback();
        }
        else {
            $menus['dateline'] = UPDATE_TIME;
            $this->menu->begin();

            if ($mid = $this->menu->insert($menus)) {
                $body = $this->bookChapter->escapeString($data['body']);
                $aid = $this->bookChapter->insert(array('menu_id' => $mid,'body' => $body));
                if ($aid) {
                    $this->menu->commit();
                    return $mid;
                }
            }
            $this->menu->rollback();
        }
        return false;
    }


    public function deleteArticle($mid)
    {
        if ($this->getArticleForID($mid)) {
            // $this->menu->begin();
            if ($this->menu->where("id='$mid'")->delete() and $this->bookChapter->where("menu_id='$mid'")->delete()) {
                // $this->menu->commit();
                return true;
            }
            // $this->menu->rollback();
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
    	return $this->bookCategory->fetchList();
    }

    public function getCategoryCount()
    {
        return $this->bookCategory->count('*');
    }

    /**
     * [getCategoryForId description]
     * @param  [type] $bcid [description]
     * @return [type]       [description]
     */
    public function getCategoryForId($cid)
    {
        return $this->bookCategory->where("cid='$cid'")->fetchRow();
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
            'type' => 0,
            'sort' => ($datas['sort'] ? $datas['sort'] : 0),
            'pid' => 0,
            'dateline' => UPDATE_TIME);

        return $this->bookCategory->insert($arr);
    }

    public function updateCategory($cid, $datas)
    {
        if(!is_array($datas)) return false;

        $arr = array(
            'name' => $datas['name'],
            'sort' => $datas['sort']);

        return $this->bookCategory->where("cid='$cid'")->update($arr);
    }

    public function deleteCategoryForId($cid)
    {
        return $this->bookCategory->where("cid='$cid'")->delete();
    }


        /**
     * [getMenusForBookID description]
     * @param  [type] $bid [description]
     * @return [type]      [description]
     */
    public function getMenusForBookID($bid, $limit = false, $page = false)
    {
        if ($limit and $page) {
           $page = $page - 1;
           $offset = $limit * $page;
           return $this->menu->where("bid='$bid'")->order('sort')->limit("$offset,$limit")->fetchList();
        }
        return $this->menu->where("bid='$bid'")->order('sort')->fetchList();
    }

    /**
     * [getMenusCountForBookID description]
     * @param  [type] $bid [description]
     * @return [type]      [description]
     */
    public function getMenusCountForBookID($bid)
    {
        $table = $this->menu->table;
        $list = $this->menu->query("select count(*) from $table where bid='$bid'");
        if ($list) return $list[0]['count(*)'];
        return false;
    }


    public function updateMenusSort($bid, $data)
    {
        if (!is_array($data)) return false;

        $ids = implode(',', array_values($data));
        $sql = "UPDATE book_menu SET sort = CASE id ";
        foreach ($data as $id => $ordinal) {
            $sql .= sprintf("WHEN %d THEN %d ", $ordinal, $id);
        }
        $sql .= "END WHERE id IN ($ids)";
        return $this->menu->query($sql);
    }
    



     /**
     * [booksfilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function booksFilter($data)
    {
        $filter = array();
        isset($data['cid']) and $filter['cid'] = $data['cid'];
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['author']) and $filter['author'] = $data['author'];
        isset($data['press']) and $filter['press'] = $data['press'];
        isset($data['pubtime']) and $filter['pubtime'] = strtotime($data['pubtime']);
        isset($data['isbn']) and $filter['isbn'] = $data['isbn'];
        isset($data['summary']) and $filter['summary'] = $data['summary'];
        isset($data['cover']) and $filter['cover'] = $data['cover'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    /**
     * [booksInfoFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function booksInfoFilter($data)
    {
        $filter = array();
        isset($data['subtitle']) and $filter['subtitle'] = $data['subtitle'];
        isset($data['oldtitle']) and $filter['oldtitle'] = $data['oldtitle'];
        isset($data['translator']) and $filter['translator'] = $data['translator'];
        isset($data['price']) and $filter['price'] = $data['price'];
        isset($data['apple_price']) and $filter['apple_price'] = $data['apple_price'];
        isset($data['wordcount']) and $filter['wordcount'] = $data['wordcount'];
        isset($data['tags']) and $filter['tags'] = $data['tags'];
        isset($data['copyright']) and $filter['copyright'] = $data['copyright'];
        isset($data['proofreader']) and $filter['proofreader'] = $data['proofreader'];
        isset($data['designer']) and $filter['designer'] = $data['designer'];
        if (count($filter) > 0) return $filter;
        return false; 
    }

    /**
     * [booksFieldsFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function booksFieldsFilter($data)
    {
        $filter = array();
        isset($data['mofidied']) and $filter['mofidied'] = $data['mofidied'];
        isset($data['verified']) and $filter['verified'] = $data['verified'];
        isset($data['published']) and $filter['published'] = $data['published'];
        isset($data['download_count']) and $filter['download_count'] = $data['download_count'];
        if (count($filter) > 0) return $filter;
        return false;
    }

    /**
     * [booksActicleFilter description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function booksMenuFilter($data)
    {
        $filter = array();
        isset($data['bid']) and $filter['bid'] = $data['bid'];
        isset($data['title']) and $filter['title'] = $data['title'];
        isset($data['type']) and $filter['type'] = $data['type'] ? $data['type'] : 1;
        isset($data['sort']) and $filter['sort'] = $data['sort'] ? $data['sort'] : 0;
        isset($data['summary']) and $filter['summary'] = $data['summary'];
        isset($data['dateline']) and $filter['dateline'] = $data['dateline'];
        isset($data['modified']) and $filter['modified'] = $data['modified'];
        if (count($filter) > 0) return $filter;
        return false; 
    }



}