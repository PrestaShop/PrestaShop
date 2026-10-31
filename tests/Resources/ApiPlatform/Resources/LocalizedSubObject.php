<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\Validator\Constraints as Assert;

class LocalizedSubObject
{
    public int $id;

    #[LocalizedValue]
    #[Assert\All([new Assert\Length(max: 20)])]
    public array $names;

    public ?LocalizedSubObject $child = null;
}
