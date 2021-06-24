/**
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import dashboardMap from '@pages/dashboard/dashboardMap';

export default function getBlogRss() {
  const readMore = $(dashboardMap.news).data('readMore');
  $.ajax({
    url: $(dashboardMap.news).data('url'),
    dataType: 'json',
    success: (data) => {
      if (typeof data !== 'undefined' && data !== null && !data.has_errors) {
        data.rss.forEach((article) => {
          const articleHtml = `
            <article>
              <h4>
                <a href="${article.link}" target="_blank" rel="noopener noreferrer nofollow">
                  ${article.title}
                </a>
              </h4>
              <span class="text-muted">
                ${article.date}
              </span>
              <p>
                ${article.short_desc}
              </p>
              <a href="${article.link}">${readMore}</a>
            </article>
            <hr/>
          `;
          $(`${dashboardMap.news} ${dashboardMap.newsContent}`).append(articleHtml);
        });
      } else {
        $(dashboardMap.news).hide();
      }
    },
  });
}
