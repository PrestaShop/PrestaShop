<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class PageNotFoundControllerCore extends FrontController
{
    public $php_self = 'pagenotfound';
    public $page_name = 'pagenotfound';
    public $ssl = true;

    private function endsWithImageExtension($string)
    {
        return preg_match('/\.(gif|jpe?g|png|ico)$/i', $string);
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        if ($this->endsWithImageExtension(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
            $this->context->cookie->disallowWriting();

            if (
                !isset($_SERVER['REDIRECT_URL']) ||
                !$this->endsWithImageExtension($_SERVER['REDIRECT_URL'])
            ) {
                // In most cases, the webserver is apache and the
                // rewritten image URL that we need to look at is inside
                // REDIRECT_URL.
                //
                // However, apache + php-fpm and probably other servers
                // will use REQUEST_URI.
                $_SERVER['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];
            }
            if (preg_match('#'.__PS_BASE_URI__.'(\d+)-([\w-]+)/[^/]+\.jpg$#', $_SERVER['REDIRECT_URL'], $matches)) {
                // Sometimes (when URL rewriting is on)
                // we don't have access to the original image path,
                // so we need to reverse-engineer it.
                $_SERVER['REDIRECT_URL'] = '/p/'.Image::getImgFolderStatic($matches[1]).$matches[1].'-'.$matches[2].'.jpg';
            }

            if (preg_match('#/p[0-9/]*/([0-9]+)\-([_a-zA-Z]*)\.(png|jpe?g|gif)$#', $_SERVER['REDIRECT_URL'], $matches)) {
                // Backward compatibility since we suffixed the template image with _default
                if (Tools::strtolower(substr($matches[2], -8)) != '_default') {
                    header('Location: '.$this->context->link->getImageLink('', $matches[1], $matches[2]), true, 302);
                    exit;
                } else {
                    $image_type = ImageType::getByNameNType($matches[2], 'products');
                    if ($image_type && count($image_type)) {
                        $root = _PS_PROD_IMG_DIR_;
                        $folder = Image::getImgFolderStatic($matches[1]);
                        $file = $matches[1];
                        $ext = '.'.$matches[3];

                        $source_image_path = $root.$folder.$file.$ext;
                        $resized_image_path = $root.$folder.$file.'-'.$matches[2].$ext;

                        if (file_exists($source_image_path)) {
                            $successfully_resized = ImageManager::resize(
                                $source_image_path,
                                $resized_image_path,
                                (int)$image_type['width'],
                                (int)$image_type['height']
                            );

                            if ($successfully_resized) {
                                header('HTTP/1.1 200 Found');
                                header('Status: 200 Found');
                                header('Content-Type: image/jpg');
                                readfile($resized_image_path);
                                exit;
                            }
                        }
                    }
                }
            } elseif (preg_match('#/c/([0-9]+)\-([_a-zA-Z]*)\.(png|jpe?g|gif)$#', $_SERVER['REDIRECT_URL'], $matches)) {
                $image_type = ImageType::getByNameNType($matches[2], 'categories');
                if ($image_type && count($image_type)) {
                    $root = _PS_CAT_IMG_DIR_;
                    $file = $matches[1];
                    $ext = '.'.$matches[3];

                    if (file_exists($root.$file.$ext)) {
                        if (ImageManager::resize($root.$file.$ext, $root.$file.'-'.$matches[2].$ext, (int)$image_type['width'], (int)$image_type['height'])) {
                            header('HTTP/1.1 200 Found');
                            header('Status: 200 Found');
                            header('Content-Type: image/jpg');
                            readfile($root.$file.'-'.$matches[2].$ext);
                            exit;
                        }
                    }
                }
            }

            header('Content-Type: image/gif');
            readfile(_PS_IMG_DIR_.'404.gif');
            exit;
        } elseif (in_array(Tools::strtolower(substr($_SERVER['REQUEST_URI'], -3)), array('.js', 'css'))) {
            $this->context->cookie->disallowWriting();
            exit;
        }

        parent::initContent();

        $this->setTemplate('errors/404.tpl');
    }

    protected function canonicalRedirection($canonical_url = '')
    {
        // 404 - no need to redirect to the canonical url
    }

    protected function sslRedirection()
    {
        // 404 - no need to redirect
    }
}
