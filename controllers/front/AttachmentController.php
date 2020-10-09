<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class AttachmentControllerCore extends FrontController
{
    public function postProcess()
    {
        $a = new Attachment(Tools::getValue('id_attachment'), $this->context->language->id);
        if (!$a->id) {
            Tools::redirect('index.php');
        }

        Hook::exec('actionDownloadAttachment', ['attachment' => &$a]);

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $a->mime);
        header('Content-Length: ' . filesize(_PS_DOWNLOAD_DIR_ . $a->file));
        header('Content-Disposition: attachment; filename="' . utf8_decode($a->file_name) . '"');
        @set_time_limit(0);
        $this->readfileChunked(_PS_DOWNLOAD_DIR_ . $a->file);
        exit;
    }

    /**
     * @see   http://ca2.php.net/manual/en/function.readfile.php#54295
     */
    public function readfileChunked($filename, $retbytes = true)
    {
        // how many bytes per chunk
        $chunksize = 1 * (1024 * 1024);
        $buffer = '';
        $totalBytes = 0;

        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) {
                $totalBytes += strlen($buffer);
            }
        }
        $status = fclose($handle);
        if ($retbytes && $status) {
            // return num. bytes delivered like readfile() does.
            return $totalBytes;
        }

        return $status;
    }
}
