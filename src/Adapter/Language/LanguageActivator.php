<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use Language;
use PrestaShop\PrestaShop\Core\Language\LanguageActivatorInterface;

/**
 * Class LanguageActivator is responsible for activating/deactivating language.
 */
final class LanguageActivator implements LanguageActivatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function enable($langId)
    {
        $this->setActive($langId, true);
    }

    /**
     * {@inheritdoc}
     */
    public function disable($langId)
    {
        $this->setActive($langId, false);
    }

    /**
     * Enable/disable language.
     *
     * @param int $langId
     * @param bool $status
     */
    private function setActive($langId, $status)
    {
        $lang = new Language((int) $langId);

        if ($lang->active !== $status) {
            $lang->active = $status;
            $lang->save();
        }
    }
}
