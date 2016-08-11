import $ from 'jquery'

export default function () {
    let addPageLinksToNavigationBar = (nav) => {
        let pageTemplate = $(nav).find('.tpl');
        pageTemplate.removeClass('tpl');
        let pageLinkTemplate = pageTemplate.clone();
        pageTemplate.remove();
        pageLinkTemplate.removeClass('hide');

        let pageIndex;
        let pageLink;
        let pageLinkAnchor;
        let totalPages = $(nav).parent().find('.page').length;
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

    $('.translation-domain nav').each((navIndex, nav) => {
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

        $(nav).find('.page-item').click((event) => {
            let pageLink = $(event.target);
            let domain = pageLink.parents('.translation-domain');
            let pageItem = pageLink.parent();
            let pageIndex = pageItem.data('page-index');

            pageItem.parent().find('.active').removeClass('active');
            pageItem.addClass('active');

            hideActivePageInDomain(domain);
            showPageInDomain(pageIndex, domain);
        });
    });
}