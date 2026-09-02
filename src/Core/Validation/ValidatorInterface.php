<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Validation;

interface ValidatorInterface
{
    /**
     * Check if HTML is clean.
     *
     * @param string $html
     * @param array $options
     *
     * @return bool
     */
    public function isCleanHtml($html, array $options = []);

    /**
     * Check if Module name is valid.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isModuleName($name);
}
