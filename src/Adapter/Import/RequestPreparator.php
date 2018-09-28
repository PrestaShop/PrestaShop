<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Import;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestPreparator prepares the request for legacy import controller.
 */
class RequestPreparator
{
    /**
     * Prepares the request for the legacy import action.
     *
     * @param Request $request
     *
     * @return array
     */
    public function prepare(Request $request)
    {
        $formData = $request->request->get('form')['import_data_configuration'];
        $request->request->remove('form');

        $request->getSession()->set('truncate', $formData['truncate']);
        $request->getSession()->set('forceIDs', $formData['forceIDs']);
        $request->getSession()->set('regenerate', $formData['regenerate']);
        $request->getSession()->set('sendemail', $formData['sendemail']);
        $request->getSession()->set('match_ref', $formData['match_ref']);
        $request->getSession()->set('skip', $formData['skip']);
        $request->getSession()->set('type_value', $formData['type_value']);
        $request->getSession()->set('import', true);
    }
}
