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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\TranslatorInterface;

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
            list($iso) = explode('.', $file->getFilename());

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
