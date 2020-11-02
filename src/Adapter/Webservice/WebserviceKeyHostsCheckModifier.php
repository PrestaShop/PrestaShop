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

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use Symfony\Component\Translation\TranslatorInterface;
use Validate;
use WebserviceKey;

/**
 * Class WebserviceKeyHostsCheckModifier is responsible for modifying webservice account hosts check.
 */
final class WebserviceKeyHostsCheckModifier
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * WebserviceKeyHostsCheckModifier constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Toggles host check for webservice key entity.
     *
     * @param int $columnId - an id which identifies the required entity to be modified
     *
     * @return string[] - if empty when process of hosts check change was successful
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function toggleHostsCheck($columnId)
    {
        $webserviceKey = new WebserviceKey($columnId);
        $webserviceKey->hosts_check = !$webserviceKey->hosts_check;
        if (!Validate::isLoadedObject($webserviceKey)) {
            $error = $this->translator
                ->trans(
                    'An error occurred while updating the object.',
                    [],
                    'Admin.Notifications.Error'
                ) .
                WebserviceKey::$definition['table'] .
                $this->translator->trans('(cannot load object)', [], 'Admin.Notifications.Error');

            return [$error];
        }

        if (!$webserviceKey->save()) {
            $error = $this->translator
                ->trans('An error occurred while performing this action.', [], 'Admin.Notifications.Error');

            return [$error];
        }

        return [];
    }
}
