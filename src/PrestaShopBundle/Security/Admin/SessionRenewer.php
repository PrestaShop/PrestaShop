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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;

/**
 * Because PS don't use Symfony login feature, we use this service to fix CVE-2022-24895. This class will be deprecated
 * when BO login/logout will use full Symfony process
 *
 * @internal
 */
final class SessionRenewer
{
    /**
     * @var ClearableTokenStorageInterface
     */
    private $storage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param ClearableTokenStorageInterface $storage
     * @param SessionInterface $session
     */
    public function __construct(ClearableTokenStorageInterface $storage, SessionInterface $session)
    {
        $this->storage = $storage;
        $this->session = $session;
    }

    /**
     * Change PHPSESSID and clear tokens registered in session
     *
     * @return void
     */
    public function renew(): void
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $this->session->migrate(true);
        $this->storage->clear();
    }
}
