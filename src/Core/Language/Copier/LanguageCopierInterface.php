<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Language\Copier;

/**
 * Interface LanguageCopierInterface defines a language copier.
 */
interface LanguageCopierInterface
{
    /**
     * Copies a language with given config.
     *
     * @param LanguageCopierConfigInterface $config
     *
     * @return array of errors if any occurred, empty array otherwise
     */
    public function copy(LanguageCopierConfigInterface $config);
}
