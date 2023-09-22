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

namespace PrestaShopBundle\Bridge\Helper;

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AddFlashMessage
{
    private const TYPES = [
        'errors' => 'error',
        'warnings' => 'warning',
        'informations' => 'info',
        'confirmations' => 'success',
    ];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function addMessage(string $type, string $message): void
    {
        $type = self::TYPES[$type] ?? $type;

        try {
            $session = $this->requestStack->getCurrentRequest()->getSession();

            if (!$session instanceof Session) {
                throw new \LogicException(sprintf(
                    'You can use the flashbag only with the %s implementation of %s',
                    Session::class,
                    SessionInterface::class
                ));
            }

            $flashBag = $session->getFlashBag();

            //This condition is to avoid duplicate messages
            $messages = $flashBag->peekAll()[$type] ?? [];
            if (!in_array($message, $messages, true)) {
                $flashBag->add($type, $message);
            }
        } catch (SessionNotFoundException $e) {
            throw new \LogicException('You cannot use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".', 0, $e);
        }
    }
}
