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
class GetFileControllerCore extends FrontController
{
    protected const MIME_TYPES = [
        'ez' => 'application/andrew-inset',
        'hqx' => 'application/mac-binhex40',
        'cpt' => 'application/mac-compactpro',
        'doc' => 'application/msword',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc' => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'bcpio' => 'application/x-bcpio',
        'vcd' => 'application/x-cdlink',
        'pgn' => 'application/x-chess-pgn',
        'cpio' => 'application/x-cpio',
        'csh' => 'application/x-csh',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dxr' => 'application/x-director',
        'dvi' => 'application/x-dvi',
        'spl' => 'application/x-futuresplash',
        'gtar' => 'application/x-gtar',
        'hdf' => 'application/x-hdf',
        'js' => 'application/x-javascript',
        'skp' => 'application/x-koan',
        'skd' => 'application/x-koan',
        'skt' => 'application/x-koan',
        'skm' => 'application/x-koan',
        'latex' => 'application/x-latex',
        'nc' => 'application/x-netcdf',
        'cdf' => 'application/x-netcdf',
        'sh' => 'application/x-sh',
        'shar' => 'application/x-shar',
        'swf' => 'application/x-shockwave-flash',
        'sit' => 'application/x-stuffit',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc' => 'application/x-sv4crc',
        'tar' => 'application/x-tar',
        'tcl' => 'application/x-tcl',
        'tex' => 'application/x-tex',
        'texinfo' => 'application/x-texinfo',
        'texi' => 'application/x-texinfo',
        't' => 'application/x-troff',
        'tr' => 'application/x-troff',
        'roff' => 'application/x-troff',
        'man' => 'application/x-troff-man',
        'me' => 'application/x-troff-me',
        'ms' => 'application/x-troff-ms',
        'ustar' => 'application/x-ustar',
        'src' => 'application/x-wais-source',
        'xhtml' => 'application/xhtml+xml',
        'xht' => 'application/xhtml+xml',
        'zip' => 'application/zip',
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'kar' => 'audio/midi',
        'mpga' => 'audio/mpeg',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'aif' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'm3u' => 'audio/x-mpegurl',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'ra' => 'audio/x-realaudio',
        'wav' => 'audio/x-wav',
        'pdb' => 'chemical/x-pdb',
        'xyz' => 'chemical/x-xyz',
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'ief' => 'image/ief',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'png' => 'image/png',
        'tiff' => 'image/tiff',
        'tif' => 'image/tif',
        'djvu' => 'image/vnd.djvu',
        'djv' => 'image/vnd.djvu',
        'wbmp' => 'image/vnd.wap.wbmp',
        'ras' => 'image/x-cmu-raster',
        'pnm' => 'image/x-portable-anymap',
        'pbm' => 'image/x-portable-bitmap',
        'pgm' => 'image/x-portable-graymap',
        'ppm' => 'image/x-portable-pixmap',
        'rgb' => 'image/x-rgb',
        'xbm' => 'image/x-xbitmap',
        'xpm' => 'image/x-xpixmap',
        'xwd' => 'image/x-windowdump',
        'igs' => 'model/iges',
        'iges' => 'model/iges',
        'msh' => 'model/mesh',
        'mesh' => 'model/mesh',
        'silo' => 'model/mesh',
        'wrl' => 'model/vrml',
        'vrml' => 'model/vrml',
        'css' => 'text/css',
        'html' => 'text/html',
        'htm' => 'text/html',
        'asc' => 'text/plain',
        'txt' => 'text/plain',
        'rtx' => 'text/richtext',
        'rtf' => 'text/rtf',
        'sgml' => 'text/sgml',
        'sgm' => 'text/sgml',
        'tsv' => 'text/tab-seperated-values',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        'etx' => 'text/x-setext',
        'xml' => 'text/xml',
        'xsl' => 'text/xml',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'mxu' => 'video/vnd.mpegurl',
        'avi' => 'video/x-msvideo',
        'movie' => 'video/x-sgi-movie',
        'ice' => 'x-conference-xcooltalk',
    ];

    /** @var bool */
    protected $display_header = false;
    /** @var bool */
    protected $display_footer = false;

    /**
     * Initialize the controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if (isset($this->context->employee) && $this->context->employee->isLoggedBack() && Tools::getValue('file')) {
            // Admin can directly access to file
            $filename = Tools::getValue('file');
            if (!Validate::isSha1($filename)) {
                die(Tools::displayError());
            }
            $file = _PS_DOWNLOAD_DIR_ . (string) preg_replace('/\.{2,}/', '.', $filename);
            $filename = ProductDownload::getFilenameFromFilename(Tools::getValue('file'));
            if (empty($filename)) {
                $newFileName = Tools::getValue('filename');
                if (!empty($newFileName)) {
                    $filename = Tools::getValue('filename');
                } else {
                    $filename = 'file';
                }
            }

            if (!file_exists($file)) {
                Tools::redirect('index.php');
            }
        } else {
            if (!($key = Tools::getValue('key'))) {
                $this->displayCustomError('Invalid key.');
            }

            Tools::setCookieLanguage();
            if (!$this->context->customer->isLogged()) {
                if (!Tools::getValue('secure_key') && !Tools::getValue('id_order')) {
                    Tools::redirect('index.php?controller=authentication&back=get-file.php%26key=' . $key);
                } elseif (Tools::getValue('secure_key') && Tools::getValue('id_order')) {
                    $order = new Order((int) Tools::getValue('id_order'));
                    if (!Validate::isLoadedObject($order)) {
                        $this->displayCustomError('Invalid key.');
                    }
                    if ($order->secure_key != Tools::getValue('secure_key')) {
                        $this->displayCustomError('Invalid key.');
                    }
                } else {
                    $this->displayCustomError('Invalid key.');
                }
            }

            /* Key format: <sha1-filename>-<hashOrder> */
            $tmp = explode('-', $key);
            if (count($tmp) != 2) {
                $this->displayCustomError('Invalid key.');
            }

            $hash = $tmp[1];

            if (!($info = OrderDetail::getDownloadFromHash($hash))) {
                $this->displayCustomError('This product does not exist in our store.');
            }

            /* check whether order has been paid, which is required to download the product */
            $order = new Order((int) $info['id_order']);
            $state = $order->getCurrentOrderState();
            if (!$state || !$state->paid) {
                $this->displayCustomError('This order has not been paid.');
            }

            // Check whether the order was made by the current user
            // If the order was made by a guest, skip this step
            $customer = new Customer((int) $order->id_customer);
            if (!$customer->is_guest && $order->secure_key !== $this->context->customer->secure_key) {
                Tools::redirect('index.php?controller=authentication&back=get-file.php%26key=' . $key);
            }

            /* Product no more present in catalog */
            if (!isset($info['id_product_download']) || empty($info['id_product_download'])) {
                $this->displayCustomError('This product has been deleted.');
            }

            if (!Validate::isFileName($info['filename']) || !file_exists(_PS_DOWNLOAD_DIR_ . $info['filename'])) {
                $this->displayCustomError('This file no longer exists.');
            }

            if (isset($info['product_quantity_refunded'], $info['product_quantity_return']) &&
                ($info['product_quantity_refunded'] > 0 || $info['product_quantity_return'] > 0)) {
                $this->displayCustomError('This product has been refunded.');
            }

            $now = time();

            $product_deadline = (int) strtotime($info['download_deadline']);
            if ($now > $product_deadline && $info['download_deadline'] != '0000-00-00 00:00:00') {
                $this->displayCustomError('The product deadline is in the past.');
            }

            $customer_deadline = (int) strtotime($info['date_expiration']);
            if ($now > $customer_deadline && $info['date_expiration'] != '0000-00-00 00:00:00') {
                $this->displayCustomError('Expiration date has passed, you cannot download this product');
            }

            if ($info['download_nb'] >= $info['nb_downloadable'] && $info['nb_downloadable']) {
                $this->displayCustomError('You have reached the maximum number of allowed downloads.');
            }

            /* Access is authorized -> increment download value for the customer */
            OrderDetail::incrementDownload($info['id_order_detail']);

            $file = _PS_DOWNLOAD_DIR_ . $info['filename'];
            $filename = $info['display_filename'];
        }

        $this->sendFile($file, $filename);
    }

    protected function sendFile(string $file, string $filename, bool $forceDownload = true): void
    {
        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        /* Set headers for download */
        header('Content-Transfer-Encoding: binary');
        header('Content-Type: ' . $this->getMimeType($file, $filename));
        header('Content-Length: ' . filesize($file));
        if ($forceDownload) {
            header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        //prevents max execution timeout, when reading large files
        @set_time_limit(0);
        $fp = fopen($file, 'rb');

        if ($fp && is_resource($fp)) {
            while (!feof($fp)) {
                echo fgets($fp, 16384);
            }
        }

        exit;
    }

    private function getMimeType(string $file, string $filename): string
    {
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME);
            $mimeType = @finfo_file($finfo, $file);
            @finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mimeType = @mime_content_type($file);
        } elseif (function_exists('exec')) {
            $mimeType = trim(@exec('file -b --mime-type ' . escapeshellarg($file)));
            if (!$mimeType) {
                $mimeType = trim(@exec('file --mime ' . escapeshellarg($file)));
            }
            if (!$mimeType) {
                $mimeType = trim(@exec('file -bi ' . escapeshellarg($file)));
            }
        }

        if (empty($mimeType)) {
            $bName = basename($filename);
            $bName = explode('.', $bName);
            $bName = strtolower($bName[count($bName) - 1]);

            $mimeType = static::MIME_TYPES[$bName] ?? 'application/octet-stream';
        }

        return $mimeType;
    }

    /**
     * Display an error message with js
     * and redirect using js function.
     *
     * @param string $msg
     */
    protected function displayCustomError(string $msg)
    {
        $translations = [
            'Invalid key.' => $this->trans('Invalid key.', [], 'Shop.Notifications.Error'),
            'This product does not exist in our store.' => $this->trans('This product does not exist in our store.', [], 'Shop.Notifications.Error'),
            'This product has been deleted.' => $this->trans('This product has been deleted.', [], 'Shop.Notifications.Error'),
            'This file no longer exists.' => $this->trans('This file no longer exists.', [], 'Shop.Notifications.Error'),
            'This product has been refunded.' => $this->trans('This product has been refunded.', [], 'Shop.Notifications.Error'),
            'The product deadline is in the past.' => $this->trans('The product deadline is in the past.', [], 'Shop.Notifications.Error'),
            'Expiration date exceeded' => $this->trans('The product expiration date has passed, preventing you from download this product.', [], 'Shop.Notifications.Error'),
            'Expiration date has passed, you cannot download this product' => $this->trans('Expiration date has passed, you cannot download this product.', [], 'Shop.Notifications.Error'),
            'You have reached the maximum number of allowed downloads.' => $this->trans('You have reached the maximum number of downloads allowed.', [], 'Shop.Notifications.Error'),
        ]; ?>
        <script type="text/javascript">
        //<![CDATA[
        alert("<?php echo isset($translations[$msg]) ? html_entity_decode($translations[$msg], ENT_QUOTES, 'utf-8') : html_entity_decode($msg, ENT_QUOTES, 'utf-8'); ?>");
        window.location.href = '<?php echo __PS_BASE_URI__; ?>';
        //]]>
        </script>
        <?php
        exit();
    }
}
