<?php
/**
 * Created by PhpStorm.
 * User: sarunas
 * Date: 18.5.25
 * Time: 14.14
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use SimpleXMLElement;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LocalizationPackLoader is responsible for loading localization packs from remote and local servers
 */
final class LocalizationPackLoader implements LocalizationPackLoaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalizationPacks()
    {
        $xmlLocalizationPacks = $this->loadRemoteLocalizationPacks();

        // if localization pack data could not be loaded from remote
        // then fallback to local localization pack data
        if (null === $xmlLocalizationPacks) {
            $xmlLocalizationPacks = $this->loadLocalLocalizationPacks();
        }

        $loadedFromRemoteLocalizations = [];
        $localizationPacks = [];

        foreach ($xmlLocalizationPacks as $xmlLocalizationPack) {
            $iso = (string) $xmlLocalizationPack->iso;
            $localizationPacks[] = [
                'iso_localization_pack' => $iso,
                'name' => (string) $xmlLocalizationPack->name,
            ];
            $loadedFromRemoteLocalizations[$iso] = true;
        }

        if (empty($localizationPacks)) {
            return null;
        }

        $localLocalizationPacks = $this->getLocalLocalizationFiles($loadedFromRemoteLocalizations);
        if (!empty($localLocalizationPacks)) {
            $localizationPacks = array_merge(
                $localizationPacks,
                $localLocalizationPacks
            );
        }

        // sort packs alphabetically
        usort($localizationPacks, function ($pack1, $pack2) {
            return $pack1['name'] > $pack2['name'];
        });

        return $localizationPacks;
    }

    /**
     * Load localization packs from remote
     *
     * @return SimpleXMLElement|null
     */
    private function loadRemoteLocalizationPacks()
    {
        $apiUrl = $this->configuration->get('_PS_API_URL_');

        $xmlLocalizationPacks = $this->loadXml($apiUrl.'/rss/localization.xml');
        if (!$xmlLocalizationPacks) {
            return null;
        }

        return $xmlLocalizationPacks;
    }

    /**
     * Load localization packs from local
     *
     * @return SimpleXMLElement|null
     */
    private function loadLocalLocalizationPacks()
    {
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $localizationFile = $rootDir.'/localization/localization.xml';
        if (!file_exists($localizationFile)) {
            return null;
        }

        return $this->loadXml($localizationFile);
    }

    /**
     * Get local localization .xml files if there are any
     *
     * @param array $excludeLocalizations
     *
     * @return array
     */
    private function getLocalLocalizationFiles(array $excludeLocalizations)
    {
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $finder = new Finder();
        $finder
            ->files()
            ->depth('1')
            ->in($rootDir.'/localization')
            ->name('/^([a-z]{2})\.xml$/');

        $localLocalizationPacks = [];
        foreach ($finder as $file) {
            list($iso) = explode('.', $file->getFilename());

            if (!isset($excludeLocalizations[$iso])) {
                $xmlPack = $this->loadXml($file->getPathname());

                $localizationPackName = $this->translator->trans(
                    '%s (local)',
                    [
                        (string) $xmlPack['name']
                    ],
                    'Admin.International.Feature'
                );

                $localLocalizationPacks[] = [
                    'iso_localization_pack' => $iso,
                    'name' => $localizationPackName,
                ];
            }
        }

        return $localLocalizationPacks;
    }

    /**
     * Loads XML from local or remote file
     *
     * @param string $file
     *
     * @return SimpleXMLElement|null
     */
    private function loadXml($file)
    {
        $xml = simplexml_load_file($file);

        if (false === $xml) {
            return null;
        }

        return $xml;
    }
}
