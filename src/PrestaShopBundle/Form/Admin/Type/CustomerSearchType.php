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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\Admin\Sell\Customer\SearchedCustomerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerSearchType extends EntitySearchInputType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        parent::__construct($translator);
        $this->router = $router;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'label' => $this->trans('Apply to all customers', 'Admin.Global'),
            'layout' => EntitySearchInputType::LIST_LAYOUT,
            'entry_type' => SearchedCustomerType::class,
            'allow_delete' => false,
            'limit' => 1,
            'disabling_switch' => true,
            'switch_state_on_disable' => 'on',
            'disabling_switch_event' => null,
            'disabled_value' => function ($data) {
                return empty($data[0]['id_customer']);
            },
            'remote_url' => $this->router->generate('admin_customers_search', ['customer_search' => '__QUERY__']),
            'placeholder' => $this->trans('Search customer', 'Admin.Actions'),
            'suggestion_field' => 'fullname_and_email',
            'required' => false,
        ]);
    }
}
