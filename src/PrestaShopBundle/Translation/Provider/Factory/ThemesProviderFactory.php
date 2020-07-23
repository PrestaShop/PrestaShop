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

namespace PrestaShopBundle\Translation\Provider\Factory;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShopBundle\Translation\Extractor\ThemeExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\ProviderInterface;
use PrestaShopBundle\Translation\Provider\Strategy\ThemesType;
use PrestaShopBundle\Translation\Provider\Strategy\TypeInterface;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Filesystem\Filesystem;

class ThemesProviderFactory implements ProviderFactoryInterface
{
    /**
     * @var ProviderInterface
     */
    private $frontOfficeProvider;
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;
    /**
     * @var ThemeExtractorInterface
     */
    private $themeExtractor;
    /**
     * @var ThemeRepository
     */
    private $themeRepository;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $themeResourcesDir;

    public function __construct(
        ProviderInterface $frontOfficeProvider,
        DatabaseTranslationLoader $databaseTranslationLoader,
        ThemeExtractorInterface $themeExtractor,
        ThemeRepository $themeRepository,
        Filesystem $filesystem,
        string $themeResourcesDir
    ) {
        $this->frontOfficeProvider = $frontOfficeProvider;
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->themeExtractor = $themeExtractor;
        $this->themeRepository = $themeRepository;
        $this->filesystem = $filesystem;
        $this->themeResourcesDir = $themeResourcesDir;
    }

    /**
     * {@inheritdoc}
     */
    public function implements(TypeInterface $strategy): bool
    {
        return $strategy instanceof ThemesType;
    }

    /**
     * {@inheritdoc}
     */
    public function build(TypeInterface $providerType): ProviderInterface
    {
        /* @var ThemesType $providerType */
        return new ThemeProvider(
            $this->frontOfficeProvider,
            $this->databaseTranslationLoader,
            $this->themeExtractor,
            $this->themeRepository,
            $this->filesystem,
            $this->themeResourcesDir,
            $providerType->getThemeName()
        );
    }
}
