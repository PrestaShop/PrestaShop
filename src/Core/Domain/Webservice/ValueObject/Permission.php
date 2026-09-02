<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject;

/**
 * Defines available permissions for Webservice keys
 */
class Permission
{
    /**
     * @var string Permission to view resource
     */
    public const VIEW = 'GET';

    /**
     * @var string Permission to view resource
     */
    public const FAST_VIEW = 'HEAD';

    /**
     * @var string Permission to modify existing resource
     */
    public const MODIFY = 'PUT';

    /**
     * @var string Permission to patch existing resource
     */
    public const PATCH = 'PATCH';

    /**
     * @var string Permission to add new resource
     */
    public const ADD = 'POST';

    /**
     * @var string Permission to delete existing resource
     */
    public const DELETE = 'DELETE';
}
