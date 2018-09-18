{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

{if isset($cms_breadcrumb)}
	<ul class="breadcrumb cat_bar">
		{$cms_breadcrumb}
	</ul>
{/if}

<div class="empty-state">
	<div class="empty-state__left shape-one">
		<img src="../img/admin/empty-states/content@3x.png" alt="Content">
	</div>
	<div class="empty-state__right">
		<h2>Create meaningful content</h2>
		<p>PrestaShop enables you to create content pages as easily as you would create product pages. You can add static pages in the top menu using the "Main menu" module.</p>
		<button>Learn more</button>
	</div>
	<i class="empty-state__close material-icons">close</i>
</div>

{$content}

{if isset($url_prev)}
	<script type="text/javascript">
	$(document).ready(function () {
		var re = /url_preview=(.*)/;
		var url = re.exec(window.location.href);
		if (typeof url !== 'undefined' && url !== null && typeof url[1] !== 'undefined' && url[1] === "1")
			window.open("{$url_prev}", "_blank");
	});
	</script>
{/if}
