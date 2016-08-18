import Jets from 'jets/jets';
import $ from 'jquery';

export default function() {
    $(() => {
        $('.search-translation form').submit(function () {
            $('#jetsContent form').addClass('hide');

            const keywords = $('#jetsSearch').val().toLowerCase();
            $('#jetsContent > [data-jets*="' + keywords + '"]').removeClass('hide');
        });

        $('.search-translation input[type=reset]').click(function () {
            $('#jetsSearch').val('');
            $('#jetsContent form').addClass('hide');
        })
    });

    return new Jets({
        searchTag: '#jetsSearch',
        contentTag: '#jetsContent',
        callSearchManually: true,
        manualContentHandling: function (tag) {
            return $(tag).find('verbatim')[0].innerText;
        }
    });
}