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

namespace PrestaShopBundle\Twig\Component;

use Configuration;
use Context;
use Link;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Profile;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/notifications_center.html.twig')]
class NotificationsCenter
{
    protected ?bool $showNewOrders = null;
    protected ?bool $showNewCustomers = null;
    protected ?bool $showNewMessages = null;
    protected ?string $noOrderTip = null;
    protected ?string $noCustomerTip = null;
    protected ?string $noCustomerMessageTip = null;
    protected readonly Link $link;
    protected array|false|null $accesses = null;

    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly LegacyContext $legacyContext
    ) {
        $this->link = $legacyContext->getContext()->link;
    }

    /**
     * @return bool
     */
    public function isShowNewOrders(): bool
    {
        if ($this->showNewOrders === null) {
            $this->showNewOrders = Configuration::get('PS_SHOW_NEW_ORDERS') && ($this->getAccesses()['AdminOrders']['view'] ?? null);
        }

        return $this->showNewOrders;
    }

    /**
     * @return bool
     */
    public function isShowNewCustomers(): bool
    {
        if ($this->showNewCustomers === null) {
            $this->showNewCustomers = Configuration::get('PS_SHOW_NEW_CUSTOMERS') && ($this->getAccesses()['AdminCustomers']['view'] ?? null);
        }

        return $this->showNewCustomers;
    }

    /**
     * @return bool
     */
    public function isShowNewMessages(): bool
    {
        if ($this->showNewMessages === null) {
            $this->showNewMessages = Configuration::get('PS_SHOW_NEW_MESSAGES') && ($this->getAccesses()['AdminCustomerThreads']['view'] ?? null);
        }

        return $this->showNewMessages;
    }

    /**
     * @return string
     */
    public function getNoOrderTip(): string
    {
        if ($this->noOrderTip === null) {
            $this->noOrderTip = $this->getNotificationTip('order');
        }

        return $this->noOrderTip;
    }

    /**
     * @return string
     */
    public function getNoCustomerTip(): string
    {
        if ($this->noCustomerTip === null) {
            $this->noCustomerTip = $this->getNotificationTip('customer');
        }

        return $this->noCustomerTip;
    }

    /**
     * @return string
     */
    public function getNoCustomerMessageTip(): string
    {
        if ($this->noCustomerMessageTip === null) {
            $this->noCustomerMessageTip = $this->getNotificationTip('customer_message');
        }

        return $this->noCustomerMessageTip;
    }

    protected function getNotificationTip(string $type): string
    {
        $tips = [
            'order' => [
                $this->translator->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                    [
                        '[1]' => '<strong>',
                        '[/1]' => '</strong>',
                        '[2]' => '<a href="' . $this->link->getAdminLink('AdminCarts', true, [], ['action' => 'filterOnlyAbandonedCarts']) . '">',
                        '[/2]' => '</a>',
                        '[3]' => '<br>',
                    ],
                    'Admin.Navigation.Notification'
                ),
            ],
            'customer' => [
                $this->translator->trans('Are you active on social media these days?', [], 'Admin.Navigation.Notification'),
            ],
            'customer_message' => [
                $this->translator->trans('Seems like all your customers are happy :)', [], 'Admin.Navigation.Notification'),
            ],
        ];

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    protected function getAccesses(): array|false
    {
        if ($this->accesses === null) {
            $this->accesses = Profile::getProfileAccesses(Context::getContext()->employee->id_profile, 'class_name');
        }

        return $this->accesses;
    }
}
