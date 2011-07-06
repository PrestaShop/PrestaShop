<?php

$configPath = '../../../config/config.inc.php';
if (file_exists($configPath))
{
	include('../../../config/config.inc.php');
	if (!Tools::getValue('token') || Tools::getValue('token') != Configuration::get('EBAY_SECURITY_TOKEN'))
		die('ERROR :X');

	$levelExists = array();
	for ($i = 0; $i <= 5; $i++)
		if ($_GET['level'] >= $i)
		{
			if ($i == 0)
				$eBayCategoryListLevel = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ebay_category` WHERE `level` = 1 AND `id_category_ref` = `id_category_ref_parent`');
			else
				$eBayCategoryListLevel = Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'ebay_category` WHERE `level` = '.(int)($i + 1).' AND `id_category_ref_parent` IN (SELECT `id_category_ref` FROM `'._DB_PREFIX_.'ebay_category` WHERE `id_ebay_category` = '.(int)($_GET['level'.$i]).')');
			if ($eBayCategoryListLevel)
			{
				$levelExists[$i + 1] = true;
				echo '<select name="category'.(int)$_GET['id_category'].'" id="categoryLevel'.(int)($i + 1).'-'.(int)$_GET['id_category'].'" rel="'.(int)$_GET['id_category'].'" style="font-size: 12px; width: 160px;" OnChange="changeCategoryMatch('.(int)($i + 1).', '.(int)$_GET['id_category'].');">
					<option value="0">'.('No category selected').'</option>';
					foreach ($eBayCategoryListLevel as $ec)
						echo '<option value="'.(int)$ec['id_ebay_category'].'" '.((isset($_GET['level'.($i + 1)]) && $_GET['level'.($i + 1)] == $ec['id_ebay_category']) ? 'selected="selected"' : '').'>'.$ec['name'].($ec['is_multi_sku'] == 1 ? ' *' : '').'</option>';
				echo '</select> ';
			}
		}

	if (!isset($levelExists[$_GET['level'] + 1]))
		echo '<input type="hidden" name="category'.(int)$_GET['id_category'].'" value="'.(int)$_GET['level'.$_GET['level']].'" />';

}
else
	echo 'ERROR';

