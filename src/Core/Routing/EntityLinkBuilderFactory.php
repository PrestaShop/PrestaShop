<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Routing;

use PrestaShop\PrestaShop\Core\Routing\Exception\BuilderNotFoundException;

/**
 * Class EntityLinkBuilderFactory is able to return the builder for an entity.
 */
class EntityLinkBuilderFactory
{
    /**
     * @var EntityLinkBuilderInterface[]
     */
    private $builders;

    /**
     * @param array $builders
     */
    public function __construct(array $builders)
    {
        $this->builders = $builders;
    }

    /**
     * @param string $entity
     *
     * @return EntityLinkBuilderInterface
     *
     * @throws BuilderNotFoundException
     */
    public function getBuilderFor($entity)
    {
        foreach ($this->builders as $builder) {
            if ($builder->canBuild($entity)) {
                return $builder;
            }
        }

        throw new BuilderNotFoundException(sprintf('Can not find a builder for entity %s', $entity));
    }
}
