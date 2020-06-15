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
import MultiPagination from './multi-pagination';

export default function () {
    let fixedOffset = $('.header-toolbar').height() + $('.main-header').height();
    const MAX_PAGINATION = 20;

    let addPageLinksToNavigationBar = (nav) => {
        let pageTemplate = $(nav).find('.tpl');
        pageTemplate.removeClass('tpl');
        let pageLinkTemplate = pageTemplate.clone();
        pageTemplate.remove();
        pageLinkTemplate.removeClass('hide');

        let pageIndex;
        let pageLink;
        let pageLinkAnchor;
        let totalPages = $(nav).parents('.translation-domains').find('.page').length;

        if (totalPages === 1) {
          return $('.pagination').addClass('hide');
        }
        else {
          $('.pagination').removeClass('hide');
        }

        let i;
        for (i = 1; i < totalPages; i++) {
            pageIndex = i + 1;
            pageLink = pageLinkTemplate.clone();
            pageLink.attr('data-page-index', pageIndex);
            pageLinkAnchor = pageLink.find('a');
            pageLinkAnchor.html(pageIndex);

            $(nav).find('.pagination').append(pageLink);
        }
    };

    // Fix internal navigation to anchors
    // by adding offset of fixed header height
    // @See also http://stackoverflow.com/a/13067009/282073
    let scrollToPreviousPaginationBar = (paginationBar, link) => {
        let paginationBarTop = paginationBar.getBoundingClientRect().top;
        window.scrollTo(window.pageXOffset, window.pageYOffset + paginationBarTop - fixedOffset);
    };

    $('.translation-domain .go-to-pagination-bar').click((event) => {
        let paginationBar = $(event.target).parents('.translation-domain').find('.pagination')[0];
        scrollToPreviousPaginationBar(paginationBar, event.target);

        return false;
    });

    $('.translation-domains nav').each((navIndex, nav) => {
        addPageLinksToNavigationBar(nav);

        let hideActivePageInDomain = (domain) => {
            let page = domain.find('.page[data-status=active]');
            $(page).addClass('hide');
            $(page).attr('data-status', 'inactive');
        };

        let showPageInDomain = (pageIndex, domain) => {
            let targetPage = domain.find('.page[data-page-index=' + pageIndex + ']');
            $(targetPage).removeClass('hide');
            $(targetPage).attr('data-status', 'active');
        };

        $(nav).find('.page-link').click((event) => {
            let paginationBar = $(event.target).parents('.pagination')[0];
            scrollToPreviousPaginationBar(paginationBar, event.target);
        });

        $(nav).find('.page-item').click((event) => {
            let pageLink = $(event.target);
            let domain = pageLink.parents('.translation-domains').find('.translation-forms');
            let pageItem = pageLink.parent();
            let pageIndex = pageItem.data('page-index');

            $(`[data-page-index=${pageIndex}]`).addClass('active');
            $(`[data-page-index=${pageIndex}]`).siblings().removeClass('active');

            pageItem.parent().find('.active').removeClass('active');
            pageItem.addClass('active');

            hideActivePageInDomain(domain);
            showPageInDomain(pageIndex, domain);

            return false;
        });
    });

    if($('.translation-domains').find('.page').length > MAX_PAGINATION) {
      $('.page-item.hide').removeClass('hide');
      $('.pagination').each((index, pagination)=> {
          let lastItem = $(pagination).find('.page-item:last-child');
          $(pagination).find('.js-next-arrow').insertAfter(lastItem).removeClass('hide');
          MultiPagination($(pagination));
      });
    }
}
