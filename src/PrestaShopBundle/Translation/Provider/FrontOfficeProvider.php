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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Main translation provider for the Front Office
 */
class FrontOfficeProvider extends AbstractProvider
{
    const DEFAULT_THEME_NAME = 'classic';

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory
    ) {
        $translationDomains = [
            '^Shop*',
            '^Modules(.*)Shop',
        ];

        $filenameFilters = [
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        ];

        $defaultResourceDirectory = $resourceDirectory . DIRECTORY_SEPARATOR . 'default';

        parent::__construct(
            $databaseLoader,
            $resourceDirectory,
            $translationDomains,
            $filenameFilters,
            $defaultResourceDirectory
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'front';
    }

    /**
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $themeName = null): MessageCatalogueInterface
    {
        if (null === $themeName) {
            $themeName = self::DEFAULT_THEME_NAME;
        }

        return parent::getUserTranslatedCatalogue($themeName);
    }
}
