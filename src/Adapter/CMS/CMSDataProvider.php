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

namespace PrestaShop\PrestaShop\Adapter\CMS;

use CMS;

/**
 * Class CMSDataProvider provides CMS data using legacy code.
 */
class CMSDataProvider
{
    /**
     * Gets all CMS pages.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getCMSPages($languageId = null)
    {
        return CMS::listCms($languageId);
    }

    /**
     * Gets one CMS object by ID.
     *
     * @param int $cmsId
     *
     * @return CMS
     */
    public function getCMSById($cmsId)
    {
        return new CMS($cmsId);
    }

    /**
     * Gets CMS choices for choice type.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getCMSChoices($languageId = null)
    {
        $choices = [];

        foreach ($this->getCMSPages($languageId) as $cms) {
            $choices[$cms['meta_title']] = $cms['id_cms'];
        }

        return $choices;
    }
}
