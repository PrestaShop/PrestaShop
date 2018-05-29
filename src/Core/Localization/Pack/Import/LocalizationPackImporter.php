<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Import;

use PrestaShop\PrestaShop\Core\Localization\Pack\Factory\LocalizationPackFactoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Pack\Loader\LocalizationPackLoaderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LocalizationPackImporter is responsible for importing localization pack
 */
final class LocalizationPackImporter implements LocalizationPackImporterInterface
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
     * @var LocalizationPackFactoryInterface
     */
    private $localizationPackFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param LocalizationPackLoaderInterface $remoteLocalizationPackLoader
     * @param LocalizationPackLoaderInterface $localLocalizationPackLoader
     * @param LocalizationPackFactoryInterface $localizationPackFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        LocalizationPackLoaderInterface $remoteLocalizationPackLoader,
        LocalizationPackLoaderInterface $localLocalizationPackLoader,
        LocalizationPackFactoryInterface $localizationPackFactory,
        TranslatorInterface $translator
    ) {
        $this->remoteLocalizationPackLoader = $remoteLocalizationPackLoader;
        $this->localLocalizationPackLoader = $localLocalizationPackLoader;
        $this->localizationPackFactory = $localizationPackFactory;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function import(LocalizationPackImportConfig $config)
    {
        $pack = null;

        if ($config->shouldDownloadPackData()) {
            $pack = $this->remoteLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );
        }

        if (null === $pack) {
            $pack = $this->localLocalizationPackLoader->getLocalizationPack(
                $config->getCountryIsoCode()
            );

            if (null === $pack) {
                $error = $this->trans('Cannot load the localization pack.', 'Admin.International.Notification');

                return [$error];
            }
        }

        $localizationPack = $this->localizationPackFactory->createNew();

        $localizationPack->loadLocalisationPack(
            $pack,
            $config->getContentToImport(),
            $installMode = false,
            $config->getCountryIsoCode()
        );

        return $localizationPack->getErrors();
    }

    /**
     * Translate message
     *
     * @param string $message
     * @param string $domain
     * @param array $params
     *
     * @return string
     */
    private function trans($message, $domain, array $params = [])
    {
        return $this->translator->trans($message, $params, $domain);
    }
}
