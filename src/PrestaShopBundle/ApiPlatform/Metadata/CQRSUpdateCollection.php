<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Metadata\CollectionOperationInterface;
use Attribute;

/**
 * A CQRSUpdate answering a collection: the list read back by its CQRS query after the command, like a
 * CQRSGetCollection (e.g. replacing the rows of a parent entity and answering them).
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CQRSUpdateCollection extends CQRSUpdate implements CollectionOperationInterface
{
}
