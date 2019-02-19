<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Able to search translations for a specific translation domains across
 * multiple sources
 */
class SearchProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * Get domain.
     *
     * @deprecated since 1.7.6, to be removed in 1.8.x
     *
     * @return mixed
     */
    public function getDomain()
    {
        @trigger_error(
            'getDomain function is deprecated and will be removed in 1.8.x',
            E_USER_DEPRECATED
        );

        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^' . $this->domain];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#^' . $this->domain . '#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'search';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }

    /**
     * @deprecated since 1.7.6, to be removed in 1.8.x
     *
     * @return string
     */
    public function getModuleDirectory()
    {
        @trigger_error(
            'getModuleDirectory function is deprecated and will be removed in 1.8.x',
            E_USER_DEPRECATED
        );

        return $this->modulesDirectory;
    }

    /**
     * @param string $modulesDirectory the path to the modules directory
     */
    public function setModulesDirectory($modulesDirectory)
    {
        $this->modulesDirectory = $modulesDirectory;
    }
}
