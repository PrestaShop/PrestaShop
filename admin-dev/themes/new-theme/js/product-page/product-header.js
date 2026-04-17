/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

export default function () {
  const $defaultArrowWidth = 35;
  const $arrow = $('.js-arrow');
  const $tabs = $('.js-tabs');
  const $navTabs = $('.js-nav-tabs');

  let $positions;
  let $moveTo = 0;
  let $tabWidth = 0;
  let $navWidth = $defaultArrowWidth;
  let $widthWithTabs = 0;

  $navTabs.find('li').each((index, item) => {
    $navWidth += $(item).width();
  });

  $widthWithTabs = $navWidth + ($defaultArrowWidth * 2);

  $navTabs.width($widthWithTabs);

  $navTabs.find('[data-toggle="tab"]').on('click', (e) => {
    if (!$(e.target).hasClass('active')) {
      $('#form_content > .form-contenttab').removeClass('active');
    }
  });

  $arrow.on('click', (e) => {
    if ($arrow.is(':visible')) {
      $tabWidth = $tabs.width();
      $positions = $navTabs.position();

      $moveTo = '-=0';
      if ($(e.currentTarget).hasClass('right-arrow')) {
        if (($tabWidth - $positions.left) < $navWidth) {
          $moveTo = `-=${$tabWidth}`;
        }
      } else if ($positions.left < $defaultArrowWidth) {
        $moveTo = `+=${$tabWidth}`;
      }

      $navTabs.animate(
        {
          left: $moveTo,
        },
        400,
        'easeOutQuad',
        () => {
          if ($(e.currentTarget).hasClass('right-arrow')) {
            $('.left-arrow').addClass('visible');
            $('.right-arrow').removeClass('visible');
          } else {
            $('.right-arrow').addClass('visible');
            $('.left-arrow').removeClass('visible');
          }
        },
      );
    }
  });
}
