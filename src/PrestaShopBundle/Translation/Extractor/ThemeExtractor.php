<?php

/**
 * 2007-2016 PrestaShop.
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Extractor;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Dumper\PhpDumper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Class ThemeExtractor.
 *
 * Extract all theme translations from Theme templates
 *
 * @todo: Smarty plugins need to be translated, too.
 */
class ThemeExtractor
{
    private $catalog;
    private $smartyExtractor;

    public function __construct(SmartyExtractor $smartyExtractor)
    {
        $this->smartyExtractor = $smartyExtractor;
        $this->catalog = new MessageCatalogue('en-US');
    }

    public function extract(Theme $theme, $format = 'array')
    {
        $themeDirectory = $theme->getDirectory().'templates';
        $options = array('path' => $themeDirectory);

        $this->smartyExtractor->extract($themeDirectory, $this->catalog);

        switch ($format) {
            case 'xliff': return;
                break;
            case 'array': return (new PhpDumper())->dump($this->catalog, $options);
                break;
            default:
                throw new \LogicException(sprintf('The format %s is not supported', $format));
        }
    }

    public function getCatalog()
    {
        return $this->catalog;
    }
}
