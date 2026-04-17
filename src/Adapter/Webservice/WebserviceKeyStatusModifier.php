<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use PrestaShopDatabaseException;
use PrestaShopException;
use Symfony\Contracts\Translation\TranslatorInterface;
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
     * @return string[] - if empty when process of status change was successful
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
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
                ) .
                WebserviceKey::$definition['table'] .
                $this->translator->trans('(cannot load object)', [], 'Admin.Notifications.Error');

            return [$error];
        }

        if (!$webserviceKey->toggleStatus()) {
            $error = $this->translator
                ->trans('An error occurred while updating the status.', [], 'Admin.Notifications.Error');

            return [$error];
        }

        return [];
    }

    /**
     * Updates status for multiple fields.
     *
     * @param array $columnIds
     * @param bool $status
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
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
