{*
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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

					</div>
				</div>
{if $display_footer}
				{$HOOK_FOOTER}
				<div id="footer">
					<div style="float:left;margin-left:10px;padding-top:6px">
						<a href="http://www.prestashop.com/" target="_blank" style="font-weight:700;color:#666666">PrestaShop&trade; {$ps_version}</a><br />
						<span style="font-size:10px">{l s='Load time: '}{$end_time}s</span>
					</div>
					<div style="float:right;height:40px;margin-right:10px;line-height:38px;vertical-align:middle">
						{if $iso_is_fr}
							<span style="color: #812143; font-weight: bold;">Questions / Renseignements / Formations :</span> <strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h
						{/if}
						|&nbsp;<a href="http://www.prestashop.com/en/contact_us/" target="_blank" class="footer_link">{l s='Contact'}</a>
						|&nbsp;<a href="http://forge.prestashop.com" target="_blank" class="footer_link">{l s='Bug Tracker'}</a>
						|&nbsp;<a href="http://www.prestashop.com/forums/" target="_blank" class="footer_link">{l s='Forum'}</a>	
					</div>
				</div>
			</div>
		</div>
		<div id="ajax_confirmation" style="display:none"></div>
		{* ajaxBox allows*}
		<div id="ajaxBox" style="display:none"></div>
		<script>
		function doAjaxAction(action, fromSelector)
		{
		}
		</script>
{/if}

	<script type="text/javascript">
		$(document).ready(function(){
			var message = $('.toolbarHead');
			var view = $(window);

				// bind only if message exists. placeholder will be its parent
				view.bind("scroll resize", function(e)
				{
					message.each(function(el){

					if (message.length)
					{
						placeholder = $(this).parent();
						if(e.type == 'resize')
							$(this).css('width', $(this).width());
	
						placeholderTop = placeholder.offset().top;
						var viewTop = view.scrollTop() + 15;

						if ((viewTop > placeholderTop) && !$(this).hasClass("fix-toolbar"))
						{
							$(this).css('width', $(this).width());
							$(this).addClass("fix-toolbar");
						}
						else if ( (viewTop <= placeholderTop) && $(this).hasClass("fix-toolbar"))
						{
							$(this).removeClass("fix-toolbar");
						}
					}
					});
				});
		});
		
		</script>
	</body>
</html>
