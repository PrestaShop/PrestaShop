<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ExtraPropertyDefinitionRepositoryTest extends TestCase
{
    public function testHydrationLogsRejectedConstraintWithDefinitionContext(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->callback(
                    static fn (string $message): bool => str_contains($message, 'index 1')
                        && str_contains($message, 'definition #42')
                        && str_contains($message, 'product/demoextrafield/video')
                        && str_contains($message, RepositoryUnsupportedConstraint::class)
                ),
                [
                    'object_type' => 'extra_property_definition',
                    'object_id' => 42,
                ]
            );

        $repository = new TestableExtraPropertyDefinitionRepository(
            $this->createMock(Connection::class),
            'ps_',
            $logger
        );

        $definition = $repository->hydrate([
            'id_extra_property_definition' => 42,
            'entity_name' => 'product',
            'property_name' => 'video',
            'type' => 'string',
            'scope' => 'common',
            'module_name' => 'demoextrafield',
            'constraints' => serialize([
                new Assert\NotBlank(),
                new Assert\All([new RepositoryUnsupportedConstraint()]),
            ]),
        ]);

        $constraints = $definition->getConstraints();
        $this->assertCount(1, $constraints);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[0]);
    }
}

final class RepositoryUnsupportedConstraint extends Constraint
{
}

final class TestableExtraPropertyDefinitionRepository extends ExtraPropertyDefinitionRepository
{
    /**
     * @param array<string, mixed> $row
     */
    public function hydrate(array $row): ExtraPropertyDefinition
    {
        return $this->hydrateDefinition($row);
    }
}
