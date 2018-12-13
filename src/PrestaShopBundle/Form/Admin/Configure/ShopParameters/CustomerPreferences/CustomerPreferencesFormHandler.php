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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\Form\FormHandler;
use PrestaShopBundle\Entity\Repository\TabRepository;

/**
 * Class manages "Configure > Shop Parameters > Customer Settings" page
 * form handling.
 */
final class CustomerPreferencesFormHandler extends FormHandler
{
    /**
     * @var TabRepository
     */
    private $tabRepository;

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        $errors = parent::save($data);

        if (empty($errors)) {
            $this->handleB2bUpdate($data['general']['enable_b2b_mode']);
        }

        return $errors;
    }

    /**
     * @param TabRepository $tabRepository
     */
    public function setTabRepository(TabRepository $tabRepository)
    {
        $this->tabRepository = $tabRepository;
    }

    /**
     * Based on B2b mode, we need to enable/disable some tabs.
     *
     * @param bool $b2bMode Current B2B mode status
     *
     * @throws \InvalidArgumentException
     */
    private function handleB2bUpdate($b2bMode)
    {
        $b2bTabs = ['AdminOutstanding'];
        foreach ($b2bTabs as $tabName) {
            $this->tabRepository->changeStatusByClassName($tabName, (bool) $b2bMode);
        }
    }
}
