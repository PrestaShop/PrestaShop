<?php
/*
* 2007-2011 PrestaShop
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/*
 * TODO : move HTML code in template files
 */

class HelperCore
{
	public static $translationsKeysForAdminCategorieTree = array(
		 'Home', 'selected', 'selecteds', 'Collapse All', 'Expand All', 'Check All', 'Uncheck All'
	);
	
	/**
	 *
	 * @param type $trads values of translations keys
	 *					For the moment, translation are not automatic
	 * @param type $selected_cat array of selected categories
	 *					Format
	 *						Array
							(
								 [0] => 1
								 [1] => 2
	 						)
	 *					OR
							Array
							(
								 [1] => Array
									  (
											[id_category] => 1
											[name] => Home page
											[link_rewrite] => home
									  )
							)
	 * @param type $input_name name of input
	 * @return string 
	 */
	public static function renderAdminCategorieTree($trads, $selected_cat = array(), $input_name = 'categoryBox')
	{
		$html = '
		<script src="../js/jquery/treeview/jquery.treeview.js" type="text/javascript"></script>
		<script src="../js/jquery/treeview/jquery.treeview.async.js" type="text/javascript"></script>
		<script src="../js/jquery/treeview/jquery.treeview.edit.js" type="text/javascript"></script>
		<script src="../js/admin-categories-tree.js" type="text/javascript"></script>
		<script type="text/javascript">
			var inputName = "'.$input_name.'";
			var selectedCat = "'.implode(',', array_keys($selected_cat)).'";
		</script>
		<script type="text/javascript">
			var selectedLabel = \''.$trads['selected'].'\';
			var home = \''.$trads['Home'].'\';
		</script>
		<link type="text/css" rel="stylesheet" href="../css/jquery.treeview.css" />
		';
		
		$html .= '
		<div style="background-color:#F4E6C9; width:99%;padding:5px 0 5px 5px;">
			<a href="#" id="collapse_all" >'.$trads['Collapse All'].'</a>
			 - <a href="#" id="expand_all" >'.$trads['Expand All'].'</a>
			 - <a href="#" id="check_all" >'.$trads['Check All'].'</a>
			 - <a href="#" id="uncheck_all" >'.$trads['Uncheck All'].'</a>
		</div>
		';
		
		$home_is_selected = false;
		foreach($selected_cat AS $cat)
		{
			if (is_array($cat))
			{
				if  ($cat['id_category'] != 1)
					$html .= '<input type="hidden" name="'.$input_name.'[]" value="'.$cat['id_category'].'" >';
				else
					$home_is_selected = true;
			}
			else
			{
				if  ($cat != 1)
					$html .= '<input type="hidden" name="'.$input_name.'[]" value="'.$cat.'" >';
				else
					$home_is_selected = true;
			}
		}
		$html .= '
			<ul id="categories-treeview" class="filetree">
				<li id="1" class="hasChildren">
					<span class="folder"> <input type="checkbox" name="'.$input_name.'[]" value="1" '.($home_is_selected ? 'checked' : '').' onclick="clickOnCategoryBox($(this));" /> '.$trads['Home'].'</span>
					<ul>
						<li><span class="placeholder">&nbsp;</span></li>	
				  </ul>
				</li>
			</ul>';
		return $html;
	}
}
