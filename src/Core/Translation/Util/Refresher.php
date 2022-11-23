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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Util;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShopBundle\Translation\TranslatorComponent;

class Refresher
{
    /**
     * @var string
     */
    private $translationsCacheDir;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var LegacyContext
     */
    private $context;

    public function __construct(
        string $translationsCacheDir,
        ModuleRepository $moduleRepository,
        LegacyContext $context
    ) {
        $this->translationsCacheDir = $translationsCacheDir;
        $this->moduleRepository = $moduleRepository;
        $this->context = $context;
    }

    /**
     * This method will clear the translations cache, reload the active modules and return fresh translators objects.
     * The array of translators returned is indexed by languageId.
     * This method can be useful to use translations of a newly enabled module.
     *
     * @return TranslatorComponent[]
     */
    public function getFreshTranslators(): array
    {
        // Clean translations cache
        $cacheFiles = \Symfony\Component\Finder\Finder::create()
            ->files()
            ->in($this->translationsCacheDir)
            ->depth('==0')
            ->name('*');

        (new \Symfony\Component\Filesystem\Filesystem())->remove($cacheFiles);

        // Reload list of active modules
        // This will allow to have the correct active modules when executing TranslatorLanguageLoaader::loadLanguage
        $this->moduleRepository->getActiveModulesPaths(false);

        $translators = [];
        // Reload translations for each shop language
        foreach ($this->context->getLanguages() as $lang) {
            $translators[$lang['id_lang']] = \Context::getContext()->getTranslatorFromLocale($lang['locale']);
        }

        return $translators;
    }
}
