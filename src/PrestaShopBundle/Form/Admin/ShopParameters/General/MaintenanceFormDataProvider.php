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
namespace PrestaShopBundle\Form\Admin\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Shop\MaintenanceConfiguration;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * This class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > General > Maintenance" page.
 */
class MaintenanceFormDataProvider
{
    /**
     * @var MaintenanceConfiguration
     */
    protected $maintenanceConfiguration;

    public function __construct(
        MaintenanceConfiguration $maintenanceConfiguration
    )
    {
        $this->maintenanceConfiguration = $maintenanceConfiguration;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array(
            'general' => $this->maintenanceConfiguration->getConfiguration(),
        );
    }

    /**
     * Persists form Data in Database and Filesystem
     *
     * @param array $data
     * @return array $errors if data can't persisted an array of errors messages
     * @throws UndefinedOptionsException
     */
    public function setData(array $data)
    {
        return $this->maintenanceConfiguration->updateConfiguration($data['general']);
    }
}
