<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
