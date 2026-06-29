<?php

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\String\UnicodeString;

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

        while (ob_get_level() && @ob_end_clean()) {
        }

        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            $attachment->file_name,
            (new UnicodeString($attachment->file_name))->ascii(),
        );

        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $attachment->mime);
        header('Content-Length: ' . filesize($attachmentPath));
        header('Content-Disposition: ' . $disposition);
        @set_time_limit(0);
        readfile($attachmentPath);

        exit;
    }
}
