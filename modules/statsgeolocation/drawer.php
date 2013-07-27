<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
include(dirname(__FILE__).'/../../config/config.inc.php');
$img_path="./img/map.png";

function loadBaseImage($img_path)
{
	$img_size = getimagesize($img_path);
	$img_png = imagecreatefrompng($img_path); if (!$img_png) exit(1);

	$img_tc = imagecreatetruecolor($img_size[0], $img_size[1]);
	imagealphablending($img_tc, false);
	imagesavealpha($img_tc, true);
	imagecopy($img_tc, $img_png, 0, 0, 0, 0, $img_size[0], $img_size[1]);
	imagedestroy($img_png);
	
	if (function_exists('imageantialias'))
		imageantialias($img_tc, true);
	return ($img_tc);
}

function drawImage($image)
{
	header("Content-type: image/png");
	imagepng($image);
	imagedestroy($image);
}

function drawCircle($image, $x, $y, $size)
{
	$color = imagecolorallocate($image, 255, 122, 56);
	imagefilledellipse($image, $x, $y, $size, $size, $color); 
}

function drawCircles($image)
{
	$max = 12;
	$min = 2;
	$gap = ($max - $min);
	$total = getTotalElements();
	$result = getCoords();
	
	foreach ($result as $row)
		drawCircle($image, $row['x'], $row['y'], $min + ($gap * ($row['total'] / $total)));
}

function getTotalElements()
{
	$result = Db::getInstance()->executeS('SELECT COUNT(`id_address`) as total FROM `'._DB_PREFIX_.'address` WHERE deleted = 0 AND id_customer IS NOT NULL AND id_customer != 0');
	return (isset($result[0]) ? $result[0]['total'] : 0);
}
	
function getCoords()
{
	return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `x`, `y`, COUNT(`id_address`) AS total 
								FROM `'._DB_PREFIX_.'address` a
								LEFT JOIN `'._DB_PREFIX_.'location_coords` lc ON lc.`id_country`=a.`id_country`
								WHERE deleted = 0 AND id_customer IS NOT NULL AND id_customer != 0
								GROUP BY a.`id_country`
								ORDER BY `total` DESC'));
}

$image = loadBaseImage($img_path);
drawCircles($image);
drawImage($image);

