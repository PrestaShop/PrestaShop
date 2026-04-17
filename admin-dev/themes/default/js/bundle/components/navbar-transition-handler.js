/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Toggle a class on $mainMenu after the end of an event (transition, animation...)
 * @param {jQuery element} $navBar - The navbar item which get a css transition property.
 * @param {jQuery element} $mainMenu - The menu inside the $navBar element.
 * @param {string} endTransitionEvent - The name of the event.
 * @param {jQuery element} $body - The body of the page.
 * @method showNavBarContent - Toggle the class based on event and if body got a class.
 * @method toggle - Add the listener if there is no transition launched yet.
 * @return {Object} The object with methods wich permit to toggle on specific event.
*/
// eslint-disable-next-line
function NavbarTransitionHandler($navBar, $mainMenu, endTransitionEvent, $body) {
  this.$body = $body;
  this.transitionFired = false;
  this.$navBar = $navBar.get(0);
  this.$mainMenu = $mainMenu;
  this.endTransitionEvent = endTransitionEvent;

  this.showNavBarContent = (event) => {
    if (event.propertyName !== 'width') {
      return;
    }

    this.$navBar.removeEventListener(this.endTransitionEvent, this.showNavBarContent);
    const isSidebarClosed = this.$body.hasClass('page-sidebar-closed');
    this.$mainMenu.toggleClass('sidebar-closed', isSidebarClosed);
    this.transitionFired = false;
  };

  this.toggle = () => {
    if (!this.transitionFired) {
      this.$navBar.addEventListener(this.endTransitionEvent, this.showNavBarContent.bind(this));
    } else {
      this.$navBar.removeEventListener(this.endTransitionEvent, this.showNavBarContent);
    }

    this.transitionFired = !this.transitionFired;
  };
}
