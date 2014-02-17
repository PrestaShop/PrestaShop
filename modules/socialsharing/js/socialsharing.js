/**
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
function socialsharing_twitter_click(message)
{
	if (typeof message === 'undefined')
		message = encodeURIComponent(location.href);
	window.open('https://twitter.com/intent/tweet?text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function socialsharing_facebook_click(message)
{
	window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(location.href), 'sharer', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_google_click(message)
{
	window.open('https://plus.google.com/share?url=' + encodeURIComponent(location.href), 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_pinterest_click(message)
{
	window.open('http://www.pinterest.com/pin/create/button/?url=' + encodeURIComponent(location.href), 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}