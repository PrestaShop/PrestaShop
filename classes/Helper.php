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
	 * 					(
	 *							 [0] => 1
	 *						 [1] => 2
	 *					)
	 * 					OR
	 *					Array
	 *					(
	 *						 [1] => Array
	 *							  (
	 *									[id_category] => 1
	 *									[name] => Home page
	 *									[link_rewrite] => home
	 *							  )
	 *					)
	 * @param type $input_name name of input
	 * @return string
	 */
	public static function renderAdminCategorieTree($trads, $selected_cat = array(), $input_name = 'categoryBox', $use_radio = false)
	{
		if (!$use_radio)
			$input_name = $input_name.'[]';
		
		$html = '
		<script src="../js/jquery/treeview/jquery.treeview.js" type="text/javascript"></script>
		<script src="../js/jquery/treeview/jquery.treeview.async.js" type="text/javascript"></script>
		<script src="../js/jquery/treeview/jquery.treeview.edit.js" type="text/javascript"></script>
		<script src="../js/admin-categories-tree.js" type="text/javascript"></script>
		<script type="text/javascript">
			var inputName = "'.$input_name.'";
		';
		if (sizeof($selected_cat) > 0)
		{
			if (isset($selected_cat[0]))
				$html .= 'var selectedCat = "'.implode(',', $selected_cat).'"';
			else
				$html .= 'var selectedCat = "'.implode(',', array_keys($selected_cat)).'"';
		}
		$html .= '
			var selectedLabel = \''.$trads['selected'].'\';
			var home = \''.$trads['Home'].'\';
			var use_radio = '.(int)$use_radio.';
		</script>
		<link type="text/css" rel="stylesheet" href="../css/jquery.treeview.css" />
		';

		$html .= '
		<div style="background-color:#F4E6C9; width:99%;padding:5px 0 5px 5px;">
			<a href="#" id="collapse_all" >'.$trads['Collapse All'].'</a>
			 - <a href="#" id="expand_all" >'.$trads['Expand All'].'</a>
			'.(!$use_radio ? '
			 - <a href="#" id="check_all" >'.$trads['Check All'].'</a>
			 - <a href="#" id="uncheck_all" >'.$trads['Uncheck All'].'</a>
			' : '').'
		</div>
		';

		$home_is_selected = false;
		foreach($selected_cat AS $cat)
		{
			if (is_array($cat))
			{
				if  ($cat['id_category'] != 1)
					$html .= '<input type="hidden" name="'.$input_name.'" value="'.$cat['id_category'].'" >';
				else
					$home_is_selected = true;
			}
			else
			{
				if  ($cat != 1)
					$html .= '<input type="hidden" name="'.$input_name.'" value="'.$cat.'" >';
				else
					$home_is_selected = true;
			}
		}
		$html .= '
			<ul id="categories-treeview" class="filetree">
				<li id="1" class="hasChildren">
					<span class="folder"> <input type="'.(!$use_radio ? 'checkbox' : 'radio').'" name="'.$input_name.'" value="1" '.($home_is_selected ? 'checked' : '').' onclick="clickOnCategoryBox($(this));" /> '.$trads['Home'].'</span>
					<ul>
						<li><span class="placeholder">&nbsp;</span></li>
				  </ul>
				</li>
			</ul>';
		return $html;
	}

	/**
	* Create a select input field
	*
	* @param array $values
	* @param array $html_options any key => value options
	* @param array $select_options
	* - key: the array value that will be used as a key in my select (optional)
	* - value: the array value that will be used as a label in my select (optional)
	* - empty: the label displayed as an empty value (optional)
	* - selected: the key corresponding to the selected value  (optional)
	*
	* @return string html content
	*/
	public static function selectInput(array $values, array $html_options = array(), array $select_options = array())
	{
		// options management
		$options = self::buildHtmlOptions($html_options);
		$select_html = '<select '.$options.'>';

		if (isset($select_options['key']))
			$use_key = $select_options['key'];

		if (isset($select_options['value']))
			$use_value = $select_options['value'];

		if (isset($select_options['empty']))
			$select_html .= '<option value="">'.$select_options['empty'].'</option>';

		// render options fields
		foreach ($values as $key => $value)
		{
			$current_key = isset($use_key) ? $value[$use_key] : $key;
			$current_value = isset($use_value) ? $value[$use_value] : $value;

			if (isset($select_options['selected']) && $select_options['selected'] == $current_key)
				$selected = 'selected="selected"';
			else
				$selected = '';

			$select_html .= '<option value="'.Tools::htmlentitiesUTF8($current_key).'" '.$selected.'>'.Tools::htmlentitiesUTF8($current_value).'</option>';
		}

		$select_html .= '</select>';

		return $select_html;
	}

	/**
	* Create html a string containing html options
	* eg: buildHtmlOptions(array('name' => 'myInputName', 'id' => 'myInputId'));
	*     return => 'name="myInputName" id="myInputId"'
	*
	* @param array $html_options
	*
	* @return string
	*/
	protected static function buildHtmlOptions(array $html_options)
	{
		$html = '';

		foreach ($html_options as $html_option => $value)
				$html .= Tools::htmlentitiesUTF8($html_option).'="'.Tools::htmlentitiesUTF8($value).'" ';

		return rtrim($html, ' ');
	}
}

