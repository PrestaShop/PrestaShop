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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Main translation provider of the Back Office
 */
class BackOfficeProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [
            '^Admin[A-Z]',
            '^Modules[A-Z](.*)Admin',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            '#^Admin[A-Z]#',
            '#^Modules[A-Z](.*)Admin#',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'back';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
