<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Validator;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ImportRequestValidatorInterface describes a request validator for import process.
 */
interface ImportRequestValidatorInterface
{
    /**
     * Validate a request for import.
     *
     * @param Request $request
     */
    public function validate(Request $request);
}
