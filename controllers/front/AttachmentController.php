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

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        // The path is resolved after the hook, so that a module changing the attachment cannot point outside the download directory
        $filePath = $attachment->getFilePath();
        if (null === $filePath || !is_file($filePath)) {
            Tools::redirect('index.php');
        }

        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $attachment->mime);
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . utf8_decode($attachment->file_name) . '"');
        @set_time_limit(0);
        $this->readfileChunked($filePath);
        exit;
    }

    /**
     * @see   http://ca2.php.net/manual/en/function.readfile.php#54295
     */
    public function readfileChunked(string $filename, bool $retbytes = true)
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
