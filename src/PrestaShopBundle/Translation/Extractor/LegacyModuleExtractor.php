<?php

/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Extractor;

use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Able to convert old translation files (in translations/es.php) into
 * Symfony MessageCatalogue objects.
 */
final class LegacyModuleExtractor implements LegacyModuleExtractorInterface
{
    /**
     * @var ExtractorInterface the PHP Code extractor
     */
    private $phpExtractor;

    /**
     * @var ExtractorInterface the Smarty Code extractor
     */
    private $smartyExtractor;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @param ExtractorInterface $phpExtractor
     * @param ExtractorInterface $smartyExtractor
     * @param string $modulesDirectory
     */
    public function __construct(
        ExtractorInterface $phpExtractor,
        ExtractorInterface $smartyExtractor,
        $modulesDirectory
    ) {
        $this->phpExtractor = $phpExtractor;
        $this->smartyExtractor = $smartyExtractor;
        $this->modulesDirectory = $modulesDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function extract($moduleName, $locale)
    {
        $catalogueForExtraction = new MessageCatalogue($locale);
        $this->phpExtractor->extract($this->modulesDirectory . '/' . $moduleName, $catalogueForExtraction);
        $this->smartyExtractor->extract($this->modulesDirectory . '/' . $moduleName, $catalogueForExtraction);

        $catalogue = new MessageCatalogue($locale);
        $catalogue->add($catalogueForExtraction->all('messages'), 'Modules' . Container::camelize($moduleName));

        return $catalogue;
    }
}
