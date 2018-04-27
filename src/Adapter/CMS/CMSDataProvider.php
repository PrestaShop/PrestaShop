<?php

namespace PrestaShop\PrestaShop\Adapter\CMS;

use CMS;

/**
 * Class CMSDataProvider provides CMS data using legacy code
 */
class CMSDataProvider
{
    public function getCMSPages($languageId)
    {
        return CMS::listCms($languageId);
    }

    public function getCMSById($cmsId)
    {
        return new CMS($cmsId);
    }
}
