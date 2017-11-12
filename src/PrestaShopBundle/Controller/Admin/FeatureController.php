<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Admin controller for the Feature pages.
 */
class FeatureController extends FrameworkBundleAdminController
{
    /**
     * Get all values for a given feature.
     *
     * @param int $idFeature The feature Id
     *
     * @return JsonResponse features list
     */
    public function getFeatureValuesAction($idFeature)
    {
        $response = new JsonResponse();
        $locales = $this->container->get('prestashop.adapter.legacy.context')->getLanguages();
        $data = array();

        if ($idFeature == 0) {
            return $response;
        }

        $featuresValues = $this->container->get('prestashop.adapter.data_provider.feature')->getFeatureValuesWithLang($locales[0]['id_lang'], $idFeature);

        if (count($featuresValues) !== 0) {
            $data['0'] = array(
                'id' => 0,
                'value' => $this->get('translator')->trans('Choose a value', array(), 'Admin.Catalog.Feature'),
            );
        }

        foreach ($featuresValues as $featureValue) {
            if (isset($featureValue['custom']) && $featureValue['custom'] == 1) {
                continue;
            }
            $data[] = array(
                'id' => $featureValue['id_feature_value'],
                'value' => $featureValue['value'],
            );
        }

        $response->setData($data);

        return $response;
    }
}
