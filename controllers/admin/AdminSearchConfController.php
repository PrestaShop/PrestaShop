<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSearchConfControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		$current_file_name = array_reverse(explode('/', $_SERVER['SCRIPT_NAME']));
		$cron_url = Tools::getHttpHost(true, true).__PS_BASE_URI__.
			substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -strlen($current_file_name['0'])).
			'searchcron.php?full=1&token='.substr(_COOKIE_KEY_, 34, 8);
		list($total, $indexed) = Db::getInstance()->getRow('SELECT COUNT(*) as "0", SUM(indexed) as "1" FROM '._DB_PREFIX_.'product');

		$this->options = array(
			'indexation' => array(
				'title' => $this->l('Indexation'),
				'icon' => 'search',
				'info' =>
						$this->l('The "indexed" products have been analysed by PrestaShop and will appear in the results of the front office search.').'<br />
						'.$this->l('Indexed products:').' <b>'.(int)$indexed.' / '.(int)$total.'</b>.
						</p>
						<p>'.$this->l('Building the product index can take a few minutes or more.')
						.$this->l('If your server stop the process before it ends, you can resume the indexation by clicking "Add missing products".').'</p>
						-&gt; <a href="searchcron.php?token='.substr(_COOKIE_KEY_, 34, 8).'&redirect=1" class="bold">'.
							$this->l('Add missing products to index.').'</a><br />
						-&gt; <a href="searchcron.php?full=1&token='.substr(_COOKIE_KEY_, 34, 8).'&redirect=1" class="bold">'.
							$this->l('Re-build entire index.').'</a><br /><br />
						'.$this->l('You can set a cron job that will re-build your index using the following URL:').' <a href="'.$cron_url.'">'.$cron_url.'</a>'
			),
			'search' => array(
				'title' =>	$this->l('Search'),
				'icon' =>	'search',
				'fields' =>	array(
					'PS_SEARCH_AJAX' => array(
						'title' => $this->l('Ajax search'),
						'validation' => 'isBool',
						'type' => 'bool',
						'cast' => 'intval',
						'desc' => $this->l('Enable the ajax search for your visitors.').'<br />'.
							$this->l('With the ajax search, the first 10 products matching the user query will appear in real time below the input field.')),
					'PS_INSTANT_SEARCH' => array(
						'title' => $this->l('Instant search:'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool',
						'desc' => $this->l('Enable the instant search for your visitors.').'<br />'.
								$this->l('With the instant search, the results will appear immediatly while the user write his query.')),
					'PS_SEARCH_MINWORDLEN' => array(
						'title' => $this->l('Minimum word length'),
						'desc' => $this->l('Only words from this size will be indexed.'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_BLACKLIST' => array(
						'title' => $this->l('Blacklisted words'),
						'size' => 35,
						'validation' => 'isGenericName',
						'desc' => $this->l('Please enter the words separated by a "|".'),
						'type' => 'textLang'
					)
				),
				'submit' => array()
			),
			'relevance' => array(
				'title' =>	$this->l('Weight'),
				'icon' =>	'weight',
				'info' =>
						$this->l('The "weight" represents its importance and relevance for the ranking of the products when try a new search.').'<br />
						'.$this->l('A word with a weight of 8 will have 4 times more value than a word with a weight of 2.').'<br /><br />
						'.$this->l('That\'s why we advise to set a greater weight for words which appear in the name or reference of a products than the ones of the description. Thus, the search results will be as precised and releant as possible.'),
				'fields' =>	array(
					'PS_SEARCH_WEIGHT_PNAME' => array(
						'title' => $this->l('Product name weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_REF' => array(
						'title' => $this->l('Reference weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_SHORTDESC' => array(
						'title' => $this->l('Short description weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_DESC' => array(
						'title' => $this->l('Description weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_CNAME' => array(
						'title' => $this->l('Category weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_MNAME' => array(
						'title' => $this->l('Manufacturer weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_TAG' => array(
						'title' => $this->l('Tags weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_ATTRIBUTE' => array(
						'title' => $this->l('Attributes weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					),
					'PS_SEARCH_WEIGHT_FEATURE' => array(
						'title' => $this->l('Features weight'),
						'size' => 4,
						'validation' => 'isUnsignedInt',
						'type' => 'text',
						'cast' => 'intval'
					)
				)
			),
		);
	}
}
