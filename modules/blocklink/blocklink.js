/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function linkEdition(linkId)
{
 	getE('id').value = linkId;
 	getE('url').value = links[linkId][0];
 	getE('newWindow').checked = links[linkId][1];
	var beg = parseInt(getE('languageFirst').value);
 	for (var i = 0; i <= parseInt(getE('languageNb').value - 1); i++)
 		getE('textInput_'+ (beg + i)).value = links[linkId][i + 2];
 	getE('submitLinkUpdate').disabled = '';
 	getE('submitLinkUpdate').setAttribute('class', 'button');
 	/* ##### IE */
 	getE('submitLinkUpdate').setAttribute('className', 'button');
}

function linkDeletion(linkId)
{
 	document.location.replace(currentUrl+'&id='+linkId+'&token='+token);
}