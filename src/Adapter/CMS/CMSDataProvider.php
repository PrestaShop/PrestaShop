<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            $choices[$cms['meta_title']] = (int) $cms['id_cms'];
        }

        return $choices;
    }
}
