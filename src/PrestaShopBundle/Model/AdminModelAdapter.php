<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Model;

/**
 * This form class is responsible to map the form data to the posted object.
 *
 * For this parent class, only hook sub fields are handled.
 */
class AdminModelAdapter
{
    /**
     * modelMapper
     * Map form data to object model
     *
     * This parent method will return only hook sub array.
     *
     * @return array Transformed form data to model attempt
     */
    public function getHookData()
    {
        // Hook fields are kept.
        if (array_key_exists('hook', $_POST)) {
            $hookFields = $_POST['hook'];
            return array('hook' => $hookFields);
        }
        return [];
    }
}
