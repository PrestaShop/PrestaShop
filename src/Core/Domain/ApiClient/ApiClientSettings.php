<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient;

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

/**
 * Defines settings for API Client.
 */
class ApiClientSettings
{
    public const MAX_CLIENT_ID_LENGTH = 255;
    public const MAX_CLIENT_NAME_LENGTH = 255;
    public const MAX_DESCRIPTION_LENGTH = FormattedTextareaType::LIMIT_TEXT_UTF8;
}
