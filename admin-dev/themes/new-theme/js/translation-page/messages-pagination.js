/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery'

export default function () {
    let fixedOffset = $('.header-toolbar').height() + $('.main-header').height();

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

        if (totalPages > 10) {
          $(nav).parent().addClass('relative-position');
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

            pageItem.parent().find('.active').removeClass('active');
            pageItem.addClass('active');

            hideActivePageInDomain(domain);
            showPageInDomain(pageIndex, domain);

            return false;
        });
    });
}
