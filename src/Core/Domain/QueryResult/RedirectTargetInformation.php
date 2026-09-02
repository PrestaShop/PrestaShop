<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QueryResult;

/**
 * Details about the entity used for product redirection (can be a product or a category)
 */
class RedirectTargetInformation
{
    public const PRODUCT_TYPE = 'product';
    public const CATEGORY_TYPE = 'category';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $image;

    /**
     * @param int $id
     * @param string $type
     * @param string $name
     * @param string $image
     */
    public function __construct(
        int $id,
        string $type,
        string $name,
        string $image
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
}
