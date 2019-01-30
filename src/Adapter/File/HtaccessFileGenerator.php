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

namespace PrestaShop\PrestaShop\Adapter\File;

use PrestaShop\PrestaShop\Adapter\Cache\CacheClearer;
use PrestaShop\PrestaShop\Adapter\Tools;

/**
 * Class HtaccessFileGenerator is responsible for generating htaccess file with its default content.
 */
class HtaccessFileGenerator
{
    /**
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var bool
     */
    private $multipleViewsConfiguration;

    /**
     * HtaccessFileGenerator constructor.
     *
     * @param CacheClearer $cacheClearer
     * @param Tools $tools
     * @param bool $multipleViewsConfiguration
     */
    public function __construct(CacheClearer $cacheClearer, Tools $tools, $multipleViewsConfiguration)
    {
        $this->cacheClearer = $cacheClearer;
        $this->tools = $tools;
        $this->multipleViewsConfiguration = $multipleViewsConfiguration;
    }

    /**
     * Generates htaccess file and its content.
     *
     * @param bool|null $disableMultiView if null, rely on the Shop configuration
     *
     * @return bool
     */
    public function generateFile($disableMultiView = null)
    {
        if (null === $disableMultiView) {
            $disableMultiView = $this->multipleViewsConfiguration;
        }

        $isGenerated = $disableMultiView ? $this->tools->generateHtaccessWithMultiViews() : $this->tools->generateHtaccessWithoutMultiViews();

        if ($isGenerated) {
            $this->cacheClearer->clearAllCaches();
        }

        return $isGenerated;
    }
}
