<?php
/**
* MembersManage  Class 
*
* @package     DuyuMvc
* @author      Jess
* @version     1.0
* @license     http://wiki.duyu.com/duyuMvc
*/

namespace mook\control;

class pagesControl
{
	// items count
	protected $itemCount;

	// offset count
    protected $perPageCount;

    protected $currentPage;

    // group page
    protected $pageRange = 10;

    protected $baseUrl;

    protected $pageKey = 'page';

    private $isRest = false;

    public function __construct ($url, $total, $perPage = 20, $page = 1 ,$get = false) {
        $this->setItemCount($total);
        $this->setPerPageCount($perPage);
        $this->isRest = $get;
        $maxPage = ceil($total / $perPage) ? : 1;
        $this->setCurrentPage($page <= 0 ? 1 : ($page > $maxPage ? $maxPage : $page));
        $this->setBaseUrl($url);

    }

    public function setItemCount ($count) {
        $this->itemCount = $count;
        return $this;
    }

    public function setPerPageCount ($count) {
        $this->perPageCount = $count;
        return $this;
    }

    public function getPerPageCount () {
        return $this->perPageCount;
    }

    public function setCurrentPage ($page) {
        $this->currentPage = $page;
        return $this;
    }

    public function setPageRange ($range) {
        $this->pageRange = $range;
        return $this;
    }

    public function setBaseUrl ($url) {
        if ($this->isRest) {
            $this->baseUrl = '?limit=' . $this->getPerPageCount();
        }
        else 
        {
            $this->baseUrl = $url . '/' . $this->getPerPageCount();
        }
    }

    public function getPageUrl ($page) {
        if ($this->isRest) 
        {
            return $this->baseUrl . '&page=' . $page;
        }
        return $this->baseUrl . '/' . $page;
    }

    public function getPageRange () {
        return $this->pageRange;
    }

    public function getCurrentPage () {
        return $this->currentPage;
    }

    public function getFirstPage () {
        return 1;
    }

    public function getLastPage () {
        return ceil($this->itemCount / $this->perPageCount);
    }

    public function getPreviousPage () {
        $diff = $this->getCurrentPage() - $this->getFirstPage();
        return $diff > 0 ? $this->getCurrentPage() - 1 : $this->getFirstPage();
    }

    public function getNextPage () {
        $diff = $this->getLastPage() - $this->getCurrentPage();
        return $diff > 0 ? $this->getCurrentPage() + 1 : $this->getLastPage();
    }

    public function getOffsetCount () {
        return ($this->getCurrentPage() - 1) * $this->perPageCount;
    }

    public function getItemCount () {
        return $this->itemCount;
    }

    public function getPages () {
        $previousRange = round($this->getPageRange() / 2);
        $nextRange = $this->getPageRange() - $previousRange - 1;

        $start = $this->getCurrentPage() - $previousRange;
        $start = $start <= 0 ? 1 : $start;

        $pages = range($start, $this->getCurrentPage());

        $end = $this->getCurrentPage() + $nextRange;
        $end = $end > $this->getLastPage() ? $this->getLastPage() : $end;

        if ($this->getCurrentPage() + 1 <= $end) {
            $pages = array_merge($pages, range($this->getCurrentPage() + 1, $end));
        }
        return $pages;
    }
}