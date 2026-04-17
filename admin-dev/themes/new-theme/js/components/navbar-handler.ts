/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * This component watches a navigation bar and is able to link it alternative links, and automatic switch
 * on page load.
 *
 * You can add button with class tab-link when they are clicked the tab target is fetched
 * from the button's data property targetTab (so data-target-tab), it then search for a tab
 * which targets matches in the navbar and simulates a click on it.
 *
 * Alternatively it also checks on page load if a hash matches a tab and activates it if one is found,
 * and of course the hash is kept in sync when the navbar or alternative links are used.
 */
export default class NavbarHandler {
  tabPrefix?: string;

  $navigationContainer: JQuery;

  constructor($navigationContainer: JQuery, tabPrefix: string = 'tab-') {
    // We use a tab prefix for hastag so that on reload the page doesn't auto scroll to the anchored element
    this.tabPrefix = tabPrefix;
    this.$navigationContainer = $navigationContainer;

    this.watchNavbar();
    this.watchTabLinks();
    this.switchOnPageLoad();
  }

  public getHashTarget(): string {
    const {hash} = document.location;

    return hash.replace(`#${this.tabPrefix}`, '#');
  }

  public switchToTarget(target: string): void {
    if (!target) {
      return;
    }

    const matchingTabs = $(`[href="${target}"]`, this.$navigationContainer);

    if (matchingTabs.length <= 0) {
      return;
    }

    const tabLink = matchingTabs.first();
    this.switchToTab(tabLink);
  }

  private switchToTab(tab: JQuery): void {
    tab.click();
    this.updateBrowserHash(<string>tab.attr('href'));
  }

  private updateBrowserHash(target: string): void {
    // Better use this rather than pushState because the hashchange event can be listened
    window.location.hash = target.replace('#', `#${this.tabPrefix}`);
  }

  private watchNavbar(): void {
    this.$navigationContainer.on(
      'shown.bs.tab',
      (event: JQueryEventObject) => {
        // @ts-ignore-next-line
        if (event.target.hash) {
          // @ts-ignore-next-line
          this.updateBrowserHash(event.target.hash);
        }
      },
    );
  }

  private watchTabLinks(): void {
    $('.tab-link').on('click', (event) => {
      event.preventDefault();
      const target = $(event.target).attr('href');

      if (!target) {
        return;
      }

      this.switchToTarget(`${target}`);
    });
  }

  private switchOnPageLoad(): void {
    const errorTabs = $('.has-error', this.$navigationContainer);

    if (errorTabs.length) {
      const errorTab = $('a[role="tab"]', errorTabs.first()).first();
      this.switchToTab(errorTab);
    } else {
      const {hash} = document.location;
      const target = hash.replace(`#${this.tabPrefix}`, '#');
      this.switchToTarget(target);
    }
  }
}
