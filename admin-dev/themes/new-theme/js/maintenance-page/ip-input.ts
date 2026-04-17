/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const {$} = window;

const addRemoteAddr = (event: JQueryEventObject): void => {
  const target = <HTMLElement>event.target;
  const input = $(target).prev('input');
  const inputValue = <string>input.val() || '';
  const ip = target.dataset.ip || '';

  if (inputValue && inputValue !== '') {
    if (inputValue.indexOf(ip) < 0) {
      input.val(`${input.val()},${ip}`);
    }
  } else {
    input.val(ip);
  }
};

export default {
  addRemoteAddr,
  init: (): void => {
    $('body').on('click', '.add_ip_button', addRemoteAddr);
  },
};
