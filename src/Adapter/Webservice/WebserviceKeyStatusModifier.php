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

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use Symfony\Component\Translation\TranslatorInterface;
use Validate;
use WebserviceKey;

/**
 * Class WebserviceKeyStatusModifier is responsible for modifying webservice account status.
 */
final class WebserviceKeyStatusModifier
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * WebserviceKeyStatusModifier constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Toggles status for webservice key entity.
     *
     * @param int $columnId - an id which identifies the required entity to be modified
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @return string[] - if empty when process of status change was successful
     *
     */
    public function toggleStatus($columnId)
    {
        $webserviceKey = new WebserviceKey($columnId);

        if (!Validate::isLoadedObject($webserviceKey)) {
            $error = $this->translator
                ->trans(
                    'An error occurred while updating the status for an object.',
                    [],
                    'Admin.Notifications.Error'
                ).
                WebserviceKey::$definition['table'].
                $this->translator->trans('(cannot load object)', [], 'Admin.Notifications.Error');

            return [$error];
        }

        if (!$webserviceKey->toggleStatus()) {
            $error = $this->translator
                ->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error')
            ;

            return [$error];
        }

        return [];
    }

    /**
     * Updates status for multiple fields.
     *
     * @param array $columnIds
     * @param int   $status
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @return bool
     *
     */
    public function setStatus(array $columnIds, $status)
    {
        $result = true;
        foreach ($columnIds as $columnId) {
            $webserviceKey = new WebserviceKey($columnId);
            $webserviceKey->active = $status;
            $result &= $webserviceKey->update();
        }

        return $result;
    }
}
