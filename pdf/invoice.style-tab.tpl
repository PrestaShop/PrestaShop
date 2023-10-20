{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}

{block name="variables"}
	{assign var=color_header value="#F0F0F0"}
	{assign var=color_border value="#000000"}
	{assign var=color_border_lighter value="#CCCCCC"}
	{assign var=color_line_even value="#FFFFFF"}
	{assign var=color_line_odd value="#F9F9F9"}
	{assign var=font_size_text value="9pt"}
	{assign var=font_size_header value="9pt"}
	{assign var=font_size_product value="9pt"}
	{assign var=height_header value="20px"}
	{assign var=table_padding value="4px"}
{/block}

{block name="styles"}
	<style>
		table, th, td {
			margin: 0!important;
			padding: 0!important;
			vertical-align: middle;
			font-size: {$font_size_text};
			white-space: nowrap;
		}

		table.product {
			border: 1px solid {$color_border};
			border-collapse: collapse;
		}

		table#addresses-tab tr td {
			font-size: large;
		}

		table#summary-tab {
			padding: {$table_padding};
			border: 1pt solid {$color_border};
		}
		table#total-tab {
			padding: {$table_padding};
			border: 1pt solid {$color_border};
		}
		table#note-tab {
			padding: {$table_padding};
			border: 1px solid {$color_border};
		}
		table#note-tab td.note{
			word-wrap: break-word;
		}
		table#tax-tab {
			padding: {$table_padding};
			border: 1pt solid {$color_border};
		}
		table#payment-tab,
		table#shipping-tab {
			padding: {$table_padding};
			border: 1px solid {$color_border};
		}

		th.product {
			border-bottom: 1px solid {$color_border};
		}

		tr.discount th.header {
			border-top: 1px solid {$color_border};
		}

		tr.product td {
			border-bottom: 1px solid {$color_border_lighter};
		}

		tr.color_line_even {
			background-color: {$color_line_even};
		}

		tr.color_line_odd {
			background-color: {$color_line_odd};
		}

		tr.customization_data td {
		}

		td.product {
			vertical-align: middle;
			font-size: {$font_size_product};
		}

		th.header {
			font-size: {$font_size_header};
			height: {$height_header};
			background-color: {$color_header};
			vertical-align: middle;
			text-align: center;
			font-weight: bold;
		}

		th.header-right {
			font-size: {$font_size_header};
			height: {$height_header};
			background-color: {$color_header};
			vertical-align: middle;
			text-align: right;
			font-weight: bold;
		}

		th.payment,
		th.shipping {
			background-color: {$color_header};
			vertical-align: middle;
			font-weight: bold;
		}

		th.tva {
			background-color: {$color_header};
			vertical-align: middle;
			font-weight: bold;
		}

		tr.separator td {
			border-top: 1px solid #000000;
		}

		.left {
			text-align: left;
		}

		.fright {
			float: right;
		}

		.right {
			text-align: right;
		}

		.center {
			text-align: center;
		}

		.bold {
			font-weight: bold;
		}

		.border {
			border: 1px solid black;
		}

		.no_top_border {
			border-top:hidden;
			border-bottom:1px solid black;
			border-left:1px solid black;
			border-right:1px solid black;
		}

		.grey {
			background-color: {$color_header};

		}

		/* This is used for the border size */
		.white {
			background-color: #FFFFFF;
		}

		.big,
		tr.big td{
			font-size: 110%;
		}

		.small, table.small th, table.small td {
			font-size:small;
		}
	</style>
{/block}
