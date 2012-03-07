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

function checkLangPack(token){
	if ($('#iso_code').val().length == 2)
	{
		$('#lang_pack_loading').show();
		$('#lang_pack_msg').hide();
		doAdminAjax(
			{
				controller:'AdminLanguages',
				action:'checkLangPack',
				token:token,
				ajax:1,
				iso_lang:$('#iso_code').val(), 
				ps_version:$('#ps_version').val()
			},
			function(ret)
			{
				$('#lang_pack_loading').hide();
				ret = $.parseJSON(ret);
				if( ret.status == 'ok')
				{
					content = $.parseJSON(ret.content);
					message = langPackOk + ' <b>'+content['name'] + '</b>) :'
						+'<br />' + langPackVersion + ' ' + content['version']
						+ ' <a href="http://www.prestashop.com/download/lang_packs/gzip/' + content['version'] + '/'
						+ $('#iso_code').val()+'.gzip" target="_blank" class="link">'+download+'</a><br />' + langPackInfo;
					$('#lang_pack_msg').html(message);
					$('#lang_pack_msg').show();
				}
				else
					showErrorMessage(ret.error);
			}
		 );
	 }
}

