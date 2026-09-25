<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Processor;

use ApiPlatform\Metadata\Post;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\GenerateApiClientSecretCommand;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use stdClass;

class CommandProcessorTest extends TestCase
{
    private const API_CLIENT_ID = 42;

    /**
     * @dataProvider getScalarCommandResults
     */
    public function testScalarCommandResultIsForwardedToTheApiResource(mixed $commandResult): void
    {
        $payload = $this->processCommandReturning($commandResult, []);

        $this->assertArrayHasKey('_commandResult', $payload);
        $this->assertSame($commandResult, $payload['_commandResult']);
        $this->assertSame(self::API_CLIENT_ID, $payload['apiClientId']);
    }

    /**
     * The scalar result is only usable if the CQRS command mapping can reach it, the same way
     * CQRSQueryMapping can reach "_queryResult" for scalar query results.
     *
     * @dataProvider getScalarCommandResults
     */
    public function testScalarCommandResultCanBeMappedByTheCommandMapping(mixed $commandResult): void
    {
        $payload = $this->processCommandReturning($commandResult, ['[_commandResult]' => '[secret]']);

        $this->assertArrayHasKey('secret', $payload);
        $this->assertSame($commandResult, $payload['secret']);
    }

    public function getScalarCommandResults(): iterable
    {
        yield 'non empty string' => ['generated-secret'];
        yield 'non zero int' => [7];
        yield 'empty string' => [''];
        yield 'zero int' => [0];
        yield 'false' => [false];
    }

    /**
     * Runs the processor against a command bus returning $commandResult, and returns the normalized
     * array that ends up being denormalized into the ApiResource DTO.
     */
    private function processCommandReturning(mixed $commandResult, array $commandMapping): array
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturn($commandResult);

        $contextParametersProvider = $this->createMock(ContextParametersProvider::class);
        $contextParametersProvider->method('getContextParameters')->willReturn([]);

        // The decorated serializer returns scalars and arrays untouched, so only the normalization
        // mapping matters here and it is performed by the real mapper.
        $serializer = $this->createMock(CQRSApiSerializer::class);
        $serializer->method('normalize')->willReturnCallback(
            function (mixed $object, ?string $format = null, array $context = []): mixed {
                (new NormalizationMapper())->mapNormalizedData($object, $context);

                return $object;
            }
        );

        $denormalizedPayload = [];
        $serializer->method('denormalize')->willReturnCallback(
            function (mixed $data) use (&$denormalizedPayload): stdClass {
                $denormalizedPayload = $data;

                return new stdClass();
            }
        );

        $operation = new Post(
            class: stdClass::class,
            extraProperties: [
                'CQRSCommand' => GenerateApiClientSecretCommand::class,
                'CQRSCommandMapping' => $commandMapping,
            ],
        );

        $processor = new CommandProcessor($commandBus, $serializer, $contextParametersProvider);
        $processor->process(new GenerateApiClientSecretCommand(self::API_CLIENT_ID), $operation, ['apiClientId' => self::API_CLIENT_ID]);

        return $denormalizedPayload;
    }
}
