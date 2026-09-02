<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Validator;

use PrestaShop\PrestaShop\Core\Import\Exception\UnavailableImportFileException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportRequestValidator is responsible for validating import request.
 */
final class ImportRequestValidator implements ImportRequestValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(Request $request)
    {
        if (!$request->request->has('csv')) {
            throw new UnavailableImportFileException();
        }
    }
}
