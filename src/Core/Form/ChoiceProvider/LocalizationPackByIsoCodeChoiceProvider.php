<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LocalizationPackByIsoCodeChoiceProvider provides localization pack choices with iso code values.
 */
final class LocalizationPackByIsoCodeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var LocalizationPackLoaderInterface
     */
    private $remoteLocalizationPackLoader;

    /**
     * @var LocalizationPackLoaderInterface
     */
    private $localLocalizationPackLoader;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param LocalizationPackLoaderInterface $remoteLocalizationPackLoader
     * @param LocalizationPackLoaderInterface $localLocalizationPackLoader
     * @param ConfigurationInterface $configuration
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        LocalizationPackLoaderInterface $localLocalizationPackLoader,
        ConfigurationInterface $configuration,
        TranslatorInterface $translator
    ) {
        $this->remoteLocalizationPackLoader = $remoteLocalizationPackLoader;
        $this->localLocalizationPackLoader = $localLocalizationPackLoader;
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * Get localization pack choices.
     *
     * @return array
     */
    public function getChoices()
    {
        $localizationPacks = $this->remoteLocalizationPackLoader->getLocalizationPackList();
        if (null === $localizationPacks) {
            $localizationPacks = $this->localLocalizationPackLoader->getLocalizationPackList();
        }

        $choices = [];

        if ($localizationPacks) {
            foreach ($localizationPacks as $pack) {
                $choices[(string) $pack->name] = (string) $pack->iso;
            }
        }

        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');

        $finder = (new Finder())
            ->files()
            ->depth('0')
            ->in($rootDir . '/localization')
            ->name('/^([a-z]{2})\.xml$/');

        foreach ($finder as $file) {
            [$iso] = explode('.', $file->getFilename());

            // if localization pack was not loaded yet and it exists locally
            // then add it to choices list
            if (!in_array($iso, $choices)) {
                $pack = $this->localLocalizationPackLoader->getLocalizationPack($iso);
                $packName = $this->translator->trans(
                    '%s (local)',
                    [
                        (string) $pack['name'],
                    ],
                    'Admin.International.Feature'
                );

                $choices[$packName] = $iso;
            }
        }

        // sort choices alphabetically
        ksort($choices);

        return $choices;
    }
}
