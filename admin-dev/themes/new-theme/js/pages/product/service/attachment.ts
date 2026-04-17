/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';

const router = new Router();
const {$} = window;

/**
 * @param attachmentId
 *
 * @returns {Promise<*|jQuery>}
 */
export const getAttachmentInfo = async (attachmentId: number): Promise<Record<string, any>> => $.get(
  router.generate('admin_attachments_attachment_info', {attachmentId}),
);

export default {
  getAttachmentInfo,
};
