<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

/**
 * Describes an exception mapped by exceptionToStatus, in the documented responses and in the error payloads.
 */
trait ExceptionDescriptionTrait
{
    /**
     * ShopNotFoundException => "Shop not found", ShopException => "Shop error"
     */
    protected function getExceptionDescription(string $exceptionClass): string
    {
        $shortName = substr((string) strrchr('\\' . $exceptionClass, '\\'), 1);
        if (str_ends_with($shortName, 'Exception') && 'Exception' !== $shortName) {
            $shortName = substr($shortName, 0, -strlen('Exception'));
        }

        $description = ucfirst(strtolower(trim((string) preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', ' ', $shortName))));

        // A base exception only names its domain
        return str_contains($description, ' ') ? $description : $description . ' error';
    }
}
