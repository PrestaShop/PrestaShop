/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
export const showGrowl = (type, message, durationTime) => {
  const duration = undefined !== durationTime ? durationTime : 2000;

  if (type === 'success') {
    window.$.growl({
      title: '',
      size: 'large',
      message,
      duration,
    });
  } else {
    window.$.growl[type]({
      title: '',
      size: 'large',
      message,
      duration,
    });
  }
};

export default showGrowl;
