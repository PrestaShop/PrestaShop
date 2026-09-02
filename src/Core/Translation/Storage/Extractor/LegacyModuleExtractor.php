<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Extractor;

use PrestaShop\TranslationToolsBundle\Translation\Extractor\PhpExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\SmartyExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Extractor\TwigExtractor;
use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Able to convert old translation files (in translations/es.php) into
 * Symfony MessageCatalogue objects.
 */
final class LegacyModuleExtractor implements LegacyModuleExtractorInterface
{
    /**
     * @var PhpExtractor the PHP Code extractor
     */
    private $phpExtractor;

    /**
     * @var SmartyExtractor the Smarty Code extractor
     */
    private $smartyExtractor;

    /**
     * @var TwigExtractor the Twig Code extractor
     */
    private $twigExtractor;

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @var array
     */
    private $catalogueExtractExcludedDirectories;

    /**
     * @param PhpExtractor $phpExtractor
     * @param SmartyExtractor $smartyExtractor
     * @param TwigExtractor $twigExtractor
     * @param string $modulesDirectory
     * @param array $catalogueExtractExcludedDirectories
     */
    public function __construct(
        PhpExtractor $phpExtractor,
        SmartyExtractor $smartyExtractor,
        TwigExtractor $twigExtractor,
        string $modulesDirectory,
        array $catalogueExtractExcludedDirectories
    ) {
        $this->phpExtractor = $phpExtractor;
        $this->smartyExtractor = $smartyExtractor;
        $this->twigExtractor = $twigExtractor;
        $this->modulesDirectory = $modulesDirectory;
        $this->catalogueExtractExcludedDirectories = $catalogueExtractExcludedDirectories;
    }

    /**
     * {@inheritdoc}
     *
     * WARNING: The domains in the returned catalogue are dot-separated
     */
    public function extract(string $moduleName, string $locale): MessageCatalogue
    {
        $extractedCatalogue = new MessageCatalogue($locale);

        $this->phpExtractor
            ->setExcludedDirectories($this->catalogueExtractExcludedDirectories)
            ->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);
        $extractedCatalogue = $this->postprocessPhpCatalogue($extractedCatalogue, $moduleName);

        $this->smartyExtractor
            ->setExcludedDirectories($this->catalogueExtractExcludedDirectories)
            ->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);
        $this->twigExtractor
            ->setExcludedDirectories($this->catalogueExtractExcludedDirectories)
            ->extract($this->modulesDirectory . '/' . $moduleName, $extractedCatalogue);

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
    private function postprocessPhpCatalogue(MessageCatalogue $extractedCatalogue, string $moduleName): MessageCatalogue
    {
        $defaultDomain = 'messages';

        if (!in_array($defaultDomain, $extractedCatalogue->getDomains())) {
            return $extractedCatalogue;
        }

        $newDomain = DomainHelper::buildModuleDomainFromLegacySource($moduleName, '');

        $allWordings = $extractedCatalogue->all();

        // move default domain into the new domain (avoiding to overwrite existing translations)
        $allWordings[$newDomain] = (isset($allWordings[$newDomain]) && is_array($allWordings[$newDomain]))
            ? array_merge($allWordings[$newDomain], $allWordings[$defaultDomain])
            : $allWordings[$defaultDomain];

        unset($allWordings[$defaultDomain]);

        return new MessageCatalogue($extractedCatalogue->getLocale(), $allWordings);
    }
}
