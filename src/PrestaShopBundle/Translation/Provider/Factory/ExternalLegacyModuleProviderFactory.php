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

use PrestaShopBundle\Translation\Extractor\LegacyModuleExtractorInterface;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\ExternalLegacyModuleProvider;
use PrestaShopBundle\Translation\Provider\ProviderInterface;
use PrestaShopBundle\Translation\Provider\Type\ExternalLegacyModuleType;
use PrestaShopBundle\Translation\Provider\Type\TypeInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

class ExternalLegacyModuleProviderFactory implements ProviderFactoryInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;
    /**
     * @var string
     */
    private $modulesDirectory;
    /**
     * @var string
     */
    private $translationsDirectory;
    /**
     * @var LoaderInterface
     */
    private $legacyFileLoader;
    /**
     * @var LegacyModuleExtractorInterface
     */
    private $legacyModuleExtractor;

    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        string $modulesDirectory,
        string $translationsDirectory,
        LoaderInterface $legacyFileLoader,
        LegacyModuleExtractorInterface $legacyModuleExtractor
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->modulesDirectory = $modulesDirectory;
        $this->translationsDirectory = $translationsDirectory;
        $this->legacyFileLoader = $legacyFileLoader;
        $this->legacyModuleExtractor = $legacyModuleExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function implements(TypeInterface $providerType): bool
    {
        return $providerType instanceof ExternalLegacyModuleType;
    }

    /**
     * {@inheritdoc}
     */
    public function build(TypeInterface $providerType): ProviderInterface
    {
        if (!$this->implements($providerType)) {
            throw new \RuntimeException(sprintf('Invalid provider type given: %s', get_class($providerType)));
        }

        /* @var ExternalLegacyModuleType $providerType */
        return new ExternalLegacyModuleProvider(
            $this->databaseTranslationLoader,
            $this->modulesDirectory,
            $this->translationsDirectory,
            $this->legacyFileLoader,
            $this->legacyModuleExtractor,
            $providerType->getModuleName()
        );
    }
}
