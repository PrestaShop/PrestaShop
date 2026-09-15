<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template T of object
 */
final class IdToEntityTransformer implements DataTransformerInterface
{
    /**
     * @param class-string<T> $class
     */
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly string $class,
    ) {
        $this->assertIsSingleColumnIdentifier();
    }

    /**
     * @return T|null
     */
    public function transform(mixed $value): ?object
    {
        if (null === $value) {
            return null;
        }

        if (null === $entity = $this->manager->find($this->class, $value)) {
            throw new TransformationFailedException('Entity not found.');
        }

        return $entity;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof $this->class) {
            throw new TransformationFailedException(\sprintf('Expected an instance of "%s".', $this->class));
        }

        return $this->readId($value);
    }

    private function readId(object $entity): mixed
    {
        $metadata = $this->manager->getClassMetadata($entity::class);
        $ids = $metadata->getIdentifierValues($entity);
        $id = current($ids);

        if (!\is_object($id) || !$metadata->hasAssociation(array_key_first($ids))) {
            return $id;
        }

        return $this->readId($id);
    }

    private function assertIsSingleColumnIdentifier(): void
    {
        if (!$this->manager->getClassMetadata($this->class)->isIdentifierComposite) {
            return;
        }

        throw new LogicException(\sprintf('The "%s" does not support entities with composite identifiers.', self::class));
    }
}
