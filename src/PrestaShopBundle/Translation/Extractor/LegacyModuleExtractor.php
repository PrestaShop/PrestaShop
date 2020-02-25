<?php

/**
 * 2007-2019 PrestaShop SA and Contributors
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

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
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
     * @var ExtractorInterface the Twig Code extractor
     */
    private $twigExtractor;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @param ExtractorInterface $phpExtractor
     * @param ExtractorInterface $smartyExtractor
     * @param ExtractorInterface $twigExtractor
     * @param string $modulesDirectory
     */
    public function __construct(
        ExtractorInterface $phpExtractor,
        ExtractorInterface $smartyExtractor,
        ExtractorInterface $twigExtractor,
        $modulesDirectory
    ) {
        $this->phpExtractor = $phpExtractor;
        $this->smartyExtractor = $smartyExtractor;
        $this->twigExtractor = $twigExtractor;
        $this->modulesDirectory = $modulesDirectory;
    }

    /**
     * {@inheritdoc}
     *
     * WARNING: The domains in the returned catalogue are dot-separated
     */
    public function extract($moduleName, $locale)
    {
        $extractedCatalogue = new MessageCatalogue($locale);

        $this->phpExtractor->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);
        $extractedCatalogue = $this->postprocessPhpCatalogue($extractedCatalogue, $moduleName);

        $this->smartyExtractor->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);
        $this->twigExtractor->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);

        return $extractedCatalogue;
    }

    /**
     * Modules usually don't use domain names when calling the l() function in PHP files.
     * Therefore, the PHP extractor will stores those calls in the default domain named "messages".
     * This process moves all wordings in the "messages" domain to the inferred module domain.
     *
     * @param MessageCatalogue $extractedCatalogue
     * @param string $moduleName
     *
     * @return MessageCatalogue
     */
    private function postprocessPhpCatalogue(MessageCatalogue $extractedCatalogue, $moduleName)
    {
        $defaultDomain = 'messages';

        if (!in_array($defaultDomain, $extractedCatalogue->getDomains())) {
            return $extractedCatalogue;
        }

        $newDomain = DomainHelper::buildModuleDomainFromLegacySource($moduleName, '');

        $allWordings = $extractedCatalogue->all();

        // move default domain into the new domain (avoiding to overwrite existing translations)
        $allWordings[$newDomain] = (isset($allWordings[$newDomain]))
            ? array_merge($allWordings[$newDomain], $allWordings[$defaultDomain])
            : $allWordings[$defaultDomain];

        unset($allWordings[$defaultDomain]);

        return new MessageCatalogue($extractedCatalogue->getLocale(), $allWordings);
    }
}
