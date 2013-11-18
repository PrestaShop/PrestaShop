<?php

$products = array(
	'Fur_Coat' => array(
		'Color' => array('Grey'),
		'Size' => array('S', 'M', 'L')
	)
);

$content_product_attribute = '';
$content_product_attribute_combination = '';

foreach ($products as $product => $attribute_groups)
{
	$default_on = 1;
	$pa_id = 1;
	$combinations = createCombinations($attribute_groups);

	$pa_id = 1;
	$pac_id = 1;
	foreach ($combinations as $attributes)
	{
		foreach ($attributes as $attribute_value)
		{
			$content_product_attribute_combination .= '<product_attribute_combination id="pac_'.$pac_id.'" id_attribute="'.$attribute_value.'" id_product_attribute="pa_'.$product.'_'.$pa_id.'"/>'."\n";
			++$pac_id;
		}
		$content_product_attribute .= '<product_attribute id="pa_'.$product.'_'.$pa_id.'" id_product="'.$product.'" reference="" supplier_reference="" ean13="" upc="" wholesale_price="0.000000" price="0.000000" ecotax="0.000000" quantity="100" weight="0" unit_price_impact="0.00" default_on="'.(string)$default_on.'" minimal_quantity="1" available_date="0000-00-00"><location/></product_attribute>'."\n";
		$default_on = 0;

		++$pa_id;
	}
}

echo "This is an XML file, look at the source!\n\n";
echo $content_product_attribute;
echo "\n\n";
echo $content_product_attribute_combination;

function createCombinations($list)
{
	if (count($list) <= 1)
		return count($list) ? array_map(create_function('$v', 'return (array($v));'), array_shift($list)) : $list;
	$res = array();
	$first = array_pop($list);
	foreach ($first as $attribute)
	{
		$tab = createCombinations($list);
		foreach ($tab as $to_add)
			$res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
	}
	return $res;
}