<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AttachmentControllerCore extends FrontController
{
    public function postProcess(): void
    {
        $attachment = new Attachment(Tools::getValue('id_attachment'), $this->context->language->id);
        if (!$attachment->id) {
            Tools::redirect('index.php');
        }

        Hook::exec('actionDownloadAttachment', ['attachment' => &$attachment]);

        $attachmentPath = _PS_DOWNLOAD_DIR_ . $attachment->file;

        if (!file_exists($attachmentPath)) {
            Tools::redirect('pagenotfound');
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $filenameFallback = mb_convert_encoding($attachment->file_name, 'ISO-8859-1');

        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $attachment->mime);
        header('Content-Length: ' . filesize($attachmentPath));
        header('Content-Disposition: attachment; filename="' . $filenameFallback . '"; filename*=utf8\'\' ' . urlencode($attachment->file_name));
        @set_time_limit(0);
        readfile($attachmentPath);

        exit;
    }
}
