/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {
  buildUpdateConfirmMessage,
  escapeHtml,
  formatModuleUpdateMessage,
} from '../../js/app/utils/module-update-message';

describe('ModuleUpdateMessage', () => {
  describe('escapeHtml', () => {
    it('leaves plain text alone', () => {
      expect(escapeHtml('Your settings will be reset.')).to.equal('Your settings will be reset.');
    });

    it('neutralises markup supplied by a module', () => {
      expect(escapeHtml('<img src=x onerror="alert(1)">')).to.equal(
        '&lt;img src=x onerror=&quot;alert(1)&quot;&gt;',
      );
    });

    it('escapes the ampersand before the entities it introduces', () => {
      expect(escapeHtml('&lt;')).to.equal('&amp;lt;');
    });
  });

  describe('buildUpdateConfirmMessage', () => {
    it('keeps the core advice on its own when no module said anything', () => {
      expect(buildUpdateConfirmMessage('Use maintenance mode.', '')).to.equal('Use maintenance mode.');
    });

    it('keeps the module message on its own in maintenance mode', () => {
      expect(buildUpdateConfirmMessage('', 'Settings will be reset.')).to.equal('Settings will be reset.');
    });

    it('separates the two with a blank line', () => {
      expect(buildUpdateConfirmMessage('Use maintenance mode.', 'Settings will be reset.')).to.equal(
        'Use maintenance mode.<br><br>Settings will be reset.',
      );
    });

    it('drops every empty part', () => {
      expect(buildUpdateConfirmMessage('', '')).to.equal('');
    });
  });

  describe('formatModuleUpdateMessage', () => {
    it('prefixes the message with the module name', () => {
      expect(formatModuleUpdateMessage('Dummy payment', 'Settings will be reset.')).to.equal(
        'Dummy payment: Settings will be reset.',
      );
    });

    it('escapes the module name as well as the message', () => {
      expect(formatModuleUpdateMessage('<b>Dummy</b>', '<i>reset</i>')).to.equal(
        '&lt;b&gt;Dummy&lt;/b&gt;: &lt;i&gt;reset&lt;/i&gt;',
      );
    });
  });
});
