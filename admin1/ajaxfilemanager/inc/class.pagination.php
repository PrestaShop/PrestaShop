<?php
/**
 * Pagination Class
 * @author Logan Cai  (cailongqun [at] yahoo [dot] com [dot] cn)
 * @since 27/Nov/20006
 *
 */
class pagination
{
	var $totalItems = 0;
	var $itemsPerPage = 30;
	var $currentPage = 1;
	var $friendlyUrl = false;
	var $baseUrl = "";
	var $pageIndex = "page";
	var $groupLimit = 5;
	var $excludedQueryStrings = array();
	var $totalPages = 0;
	var $url = "";
	var $previousText = "Previous";
	var $nextText = "Next";
	var $lastText = "Last";
	var $firstText = "First";
	var $limitIndex ='limit';
	var $limits = array(5, 10, 20, 30, 50, 80, 150, 999);


	/**
	 * Contructor
	 *
	 * @param boolean $friendlyUrl set the returned url
	 * as search engine friendly or Normal Url
	 */
	function pagination($friendlyUrl=false)
	{
		$this->friendlyUrl = $friendlyUrl;
		$this->__resetCurrentPage();
	}

	/**
	 * set maximum number of items per page
	 *
	 * @param integer $value maximum number of items per page
	 */
	function setLimit($value)
	{
		$this->itemsPerPage = (int)($value);
	}
	/**
	 * get maximum number of items per page
	 *
	 * @return integer
	 */
	function getLimit()
	{
		return $this->itemsPerPage;
	}

	/**
	 * set the total number of items
	 *
	 * @param integer $value the total number of items
	 */
	function setTotal($value)
	{
		$this->totalItems = (int)($value);
	}
	/**
	 * get the total number of items
	 *
	 * @return integer total number of items
	 */
	function getTotal()
	{
		return $this->totalItems;
	}
	/**
	 * get total pages will be used to display all records
	 *
	 */
	function getTotalPages()
	{

		$output = floor(($this->totalItems / $this->itemsPerPage ));
		if($this->totalItems % $this->itemsPerPage)
		{
			$output++;
		}
		return $output;
	}

	/**
	 * Set the index of URL Query String
	 *
	 * @param string $value e.g. page
	 */
	function setPageIndex($value)
	{
		$this->pageIndex = $value;
		$this->__resetCurrentPage();
	}


	function getPageIndex()
	{
		return $this->pageIndex;
	}
	/**
	 * initiate or reset the current page number
	 *
	 */
	function __resetCurrentPage()
	{
		$this->currentPage = ((isset($_GET[$this->pageIndex]) && (int)($_GET[$this->pageIndex]) > 0)?(int)($_GET[$this->pageIndex]):1);
	}

	/**
	 * set the base url used in the links, default is $PHP_SELF
	 *
	 * @param string $value the base url
	 */
	function setUrl($value="")
	{
		if(empty($value))
		{
			if($this->friendlyUrl)
			{
				$this->url = "http://" . $_SERVER['HTTP_HOST'] . "/";
			}else
			{
				$this->url = $_SERVER['PHP_SELF'];
			}
		}else
		{
			$this->url = $value;
		}

	}

	/**
	 * get the base url variable
	 *
	 * @return string the base url
	 */
	function getUrl()
	{

		if(empty($this->url))
		{
			$this->setUrl();

		}
		return $this->url;
	}

	/**
	 * set base url for pagination links after exculed those keys
	 * identified on excluded query strings
	 */
	function __setBaseUrl()
	{

		if(empty($this->url))
		{
			$this->getUrl();
		}

		if($this->friendlyUrl)
		{
			$this->baseUrl = $this->getUrl();
		}else
		{

			$appendingQueryStrings = array();
			$this->excludedQueryStrings[$this->pageIndex] =$this->pageIndex;
			foreach($_GET as $k=>$v)
			{
				if((array_search($k, $this->excludedQueryStrings) === false ))
				{
					$appendingQueryStrings[$k] = $k . "=" . $v;
				}
			}
			if(sizeof($appendingQueryStrings))
			{
				$this->baseUrl = $this->__appendQueryString($this->url, implode("&", $appendingQueryStrings));
			}else
			{
				$this->baseUrl = $this->getUrl();
			}

		}


	}
	/**
	 * get base url for pagination links aftr excluded those key
	 * identified on excluded query strings
	 *
	 */
	function __getBaseUrl()
	{

		if(empty($this->baseUrl))
		{

			$this->__setBaseUrl();
		}
		return $this->baseUrl;
	}


	/**
	 * get the first item number
	 *
	 * @return interger the first item number displayed within current page
	 */
	function getFirstItem()
	{
		$output = 0;
		$temStartItemNumber = (($this->currentPage - 1) * $this->itemsPerPage + 1);
		if($this->totalItems && $temStartItemNumber <= $this->totalItems )
		{

			$output = $temStartItemNumber;
		}
		return $output;
	}
	/**
	 * get the last item number displayed within current page
	 *
	 * @return interger the last item number
	 */
	function getLastItem()
	{
		$output = 0;
		$temEndItemNumber = (($this->currentPage) * $this->itemsPerPage);
		if($this->totalItems)
		{
			if($temEndItemNumber <= $this->totalItems)
			{
				$output = $temEndItemNumber;
			}else
			{
				$output = $this->totalItems;
			}

		}
		return $output;
	}
	/**
	 * set  page groupings limit
	 * used for previous 1 2 3 4 5 next
	 *
	 * @param unknown_type $value
	 */
	function setGroupLimit($value)
	{
		$this->groupLimit = (int)($value);
	}
	/**
	 * get page grouping limit
	 *
	 * @return integer the page grouping limit
	 */
	function getGroupLimit()
	{
		return $this->groupLimit;
	}
	/**
	 * get the page offset number
	 * used for Query . e.g SELECT SQL_CALC_FOUND_ROWS *
	 * 						FROM mytable LIMIT getPageOffset(), getItemsPerPage()
	 *
	 * @return iner
	 */
	function getPageOffset()
	{
		return (($this->currentPage - 1)  * $this->itemsPerPage);
	}
	/**
	 * get the last url if any
	 * @return  string the last url
	 */
	function getLastUrl()
	{

		$url = "";
		$totalPages = $this->getTotalPages();
		if($this->currentPage < $totalPages)
		{
			$url = $this->__getBaseUrl();

			if($this->friendlyUrl)
			{
				$url .= $this->pageIndex . $totalPages . "/";
			}else
			{
				$url = $this->__appendQueryString($url, $this->pageIndex . "=" . $totalPages);
			}
			$url = sprintf('<a href="%s" class="pagination_last"><span>%s</span></a>',
			$url,
			$this->lastText);
		}
		return $url;
	}




	/**
	 * get the first url if any
	 * @return string the first url
	 */

	function getFirstUrl()
	{
		$url = "";
		if($this->currentPage > 1)
		{
			$url = $this->__getBaseUrl();
			if($this->friendlyUrl)
			{
				$url .= $this->pageIndex .  "1/";
			}else
			{
				$url = $this->__appendQueryString($url, $this->pageIndex . "=1");
			}
			$url = sprintf('<a href="%s" class="pagination_first"><span>%s</span></a>',
			$url,
			$this->firstText);

		}
		return $url;
	}

	/**
	 * get the previous page url if anywhere
	 *
	 * @param array $excludedQueryStrings excluded the value from $_GET
	 * @return string the previous page url
	 */
	function getPreviousUrl()
	{
		$url = "";
		if($this->currentPage > 1 && $this->totalItems > 0 )
		{
			$url = $this->__getBaseUrl();
			if($this->friendlyUrl)
			{
				$url .= $this->pageIndex . ($this->currentPage - 1) . "/";
			}else
			{
				$url = $this->__appendQueryString($url, $this->pageIndex . "=" . ($this->currentPage -1));
			}
			$url = sprintf('<a href="%s" class="pagination_previous"><span>%s</span></a>',
			$url,
			$this->previousText);

		}

		return $url;
	}
	/**
	 * get the next page url if anywhere
	 *
	 * @param array $excludedQueryStrings excluded the value from $_GET
	 * @return string the next page url
	 */
	function getNextUrl()
	{
		$url = "";
		if($this->totalItems > ($this->currentPage * $this->itemsPerPage))
		{
			$url = $this->__getBaseUrl();
			if($this->friendlyUrl)
			{
				$url .= $this->pageIndex . ($this->currentPage + 1) . "/";
			}else
			{
				$url = $this->__appendQueryString($url, $this->pageIndex . "=" . ($this->currentPage + 1));
			}
			$url = sprintf('<a href="%s" class="pagination_next"><span>%s</span></a>',
			$url,
			$this->nextText);
		}
		return $url;

	}

	/**
	 * get the group page links  e.g. 1,2,3,4,5
	 * return format
	 * <a class="pagination_group" href='yoururl'>1</a>
	 * <a class="pagination_group active" href='#'>2</a>
	 * <a class="pagination_group" href='yoururl'>3</a>
	 */
	function getGroupUrls()
	{
		$output = "";
		if($this->totalItems > 0)
		{
			$displayedPages = 0;
			$url = $this->__getBaseUrl();
			$totalPages = $this->getTotalPages();
			// find halfway point
			$groupLimitHalf = floor($this->groupLimit / 2);
			// determine what item/page we start with
			$itemStart = $this->currentPage - $groupLimitHalf;
			$itemStart = ($itemStart > 0 && $itemStart <= $totalPages)?$itemStart:1;
			$itemEnd = $itemStart;

			while($itemEnd < ($itemStart + $this->groupLimit - 1) && $itemEnd < $totalPages)
			{
				$itemEnd++;
			}


			if($totalPages > ($itemEnd - $itemStart))
			{
				for($i = $itemStart; $i > 1 && ($itemEnd - $itemStart + 1) < $this->groupLimit; $i--)
				{
					$itemStart--;
				}
			}

			for($item = $itemStart; $item <= $itemEnd; $item++)
			{
				if($item != $this->currentPage)
				{//it is not the active link
					if($this->friendlyUrl)
					{
						$temUrl = $url . $this->pageIndex . $item .   "/";
					}else
					{
						$temUrl  = $this->__appendQueryString($url, $this->pageIndex . "=" . $item);
					}
					$output .= sprintf(' <a class="pagination_group" href="%s"><span>%d</span></a> ', $temUrl, $item);
				}else
				{//active link
					$output .= sprintf(' <a class="pagination_group pagination_active" href="#"><span>%d</span></a> ', $item);
				}
			}
		}
		return $output;
	}
	/**
	 * set the text of previous page link
	 *
	 * @param string $value
	 */
	function setPreviousText($value)
	{
		$this->previousText = $value;
	}
	/**
	 * set the text of first page link
	 *
	 * @param string $value
	 */
	function setFirstText($value)
	{
		$this->firstText = $value;
	}
	/**
	 * set the text of next page link
	 *
	 * @param string $value
	 */

	function setNextText($value)
	{
		$this->nextText = $value;
	}
	/**
	 * set the text of last page link
	 *
	 * @param string $value
	 */
	function setLastText($value)
	{
		$this->lastText = $value;
	}

	/**
	 * set the excluded query string from $_GET;
	 *
	 * @param array the lists of the query string keys
	 */

	function setExcludedQueryString($values = array())
	{
		$this->excludedQueryStrings = $values;
	}

	function getExcludedQueryString()
	{
		return $this->excludedQueryStrings;
	}


	/**
	 * add extra query stiring to a url
	 * @param string $baseUrl
	 * @param string $extra the query string added to the base url
	 */
	function __appendQueryString($baseUrl, $extra)
	{
		$output = trim($baseUrl);
		if(strpos($baseUrl, "?") !== false)
		{
			$output .= "&" . $extra;
		}else
		{
			$output .= "?" . $extra;
		}
		return $output;
	}
	/**
	 * return the html
	 *
	 * @param integer $type
	 */
	function getPaginationHTML($type=1, $cssClass="pagination")
	{
		$output = '';
		$output .= "<div class=\"pagination_content\"><p class=\"$cssClass\">\n";
		switch($type)
		{
			case "2":
				$output .= "<span class=\"pagination_summany\">" . $this->getFirstItem() . " to " . $this->getLastItem() . " of " . $this->getTotal() . " results.</span> ";	
			if($previousUrl = $this->getPreviousUrl())
			{
				$output .= " " . $previousUrl;
			}

			if($nextUrl = $this->getNextUrl())
			{
				$output .= " " . $nextUrl;
			}							
				break;
			case 1:
				//get full summary pagination
			default:
				$output .= "<span class=\"pagination_summany\">" . $this->getFirstItem() . "/" . $this->getLastItem() . " (" . $this->getTotal() . ")</span> ";
			if($firstUrl = $this->getFirstUrl())
			{
				$output .= " " . $firstUrl;
			}
			if($previousUrl = $this->getPreviousUrl())
			{
				$output .= " " . $previousUrl;
			}

			if($groupUrls = $this->getGroupUrls())
			{
				$output .= " " . $groupUrls;
			}
			if($nextUrl = $this->getNextUrl())
			{
				$output .= " " . $nextUrl;
			}
			if($lastUrl = $this->getLastUrl())
			{
				$output .= " " . $lastUrl;
			}
			$itemPerPage = '';
			$itemPerPage .= "<select name=\"" . $this->limitIndex . "\" id=\"limit\" class=\"input inputLimit\" onchange=\"changePaginationLimit();\">\n";
			foreach ($this->limits as $v)
			{
				$itemPerPage .= "<option value=\"" . $v . "\" " . ($v==$this->itemsPerPage?'selected="selected"':'') . ">" . $v . "</option>\n";
			}
			$itemPerPage .= "</select>\n"; 
			$output .= "<span class=\"pagination_items_per_page\">";
			$output .= sprintf(PAGINATION_ITEMS_PER_PAGE, $itemPerPage);
			$output .= "</span>";
			$output .= "<span class=\"pagination_parent\"><a href=\"#\" onclick=\"goParentFolder();\" id=\"pagination_parent_link\" title=\"" . PAGINATION_GO_PARENT . "\">&nbsp;</a></span>";
		}

		$output .= "</p></div>";
		return $output;
	}

}
?>