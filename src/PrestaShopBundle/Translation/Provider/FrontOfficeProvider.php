<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Main translation provider for the Front Office
 */
class FrontOfficeProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    const DEFAULT_THEME_NAME = 'classic';

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [
            '^Shop*',
            '^Modules(.*)Shop',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'front';
    }

    /**
     * @param null $themeName
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        if (null === $themeName) {
            $themeName = self::DEFAULT_THEME_NAME;
        }

        return parent::getDatabaseCatalogue($themeName);
    }

    /**{@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
