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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class TabChoiceProvider provides Tab choices with name values.
 *
 * This class is used for choosing Default page when creating employee
 */
final class TabChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $tabs;

    /**
     * @param array $tabs
     */
    public function __construct(array $tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->tabs as $tab) {
            if (!empty($tab['children'])) {
                $choices[$tab['name']] = [];

                foreach ($tab['children'] as $childTab) {
                    if ($childTab['name']) {
                        $choices[$tab['name']][$childTab['name']] = $childTab['id_tab'];
                    }
                }
            } else {
                $choices[$tab['name']] = $tab['id_tab'];
            }
        }

        return $choices;
    }
}
