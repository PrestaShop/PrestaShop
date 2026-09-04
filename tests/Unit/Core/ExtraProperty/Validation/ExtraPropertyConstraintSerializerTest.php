<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintSerializer;
use ReflectionClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ExtraPropertyConstraintSerializerTest extends TestCase
{
    public function testAllowlistContainsOnlyMapperConstraintsAndRequiredAuxiliaryClasses(): void
    {
        $expected = array_values(array_unique([
            ...array_values(ExtraPropertyConstraintMapper::getAllowedConstraints()),
            Assert\Required::class,
            Assert\Optional::class,
            DateTime::class,
            DateTimeImmutable::class,
        ]));

        $this->assertSame($expected, ExtraPropertyConstraintSerializer::getAllowedClasses());
    }

    public function testEveryCallableOptionInAllowedConstraintsIsExplicitlyBlocked(): void
    {
        $detectedOptions = [];
        foreach (ExtraPropertyConstraintMapper::getAllowedConstraints() as $constraintClass) {
            $reflection = new ReflectionClass($constraintClass);
            foreach ($reflection->getProperties() as $property) {
                $declaresCallable = false !== stripos((string) $property->getDocComment(), 'callable')
                    || str_contains((string) $property->getType(), 'Closure');
                if (!$declaresCallable) {
                    continue;
                }

                $detectedOptions[] = sprintf('%s::$%s', $constraintClass, $property->getName());
                $this->assertContains(
                    $property->getName(),
                    ['callback', 'normalizer'],
                    sprintf(
                        'Callable option %s::$%s needs an explicit security decision.',
                        $constraintClass,
                        $property->getName()
                    )
                );
            }
        }

        sort($detectedOptions);
        $expectedOptions = [
            Assert\Choice::class . '::$callback',
            Assert\Email::class . '::$normalizer',
            Assert\Ip::class . '::$normalizer',
            Assert\Length::class . '::$normalizer',
            Assert\NotBlank::class . '::$normalizer',
            Assert\Regex::class . '::$normalizer',
            Assert\Url::class . '::$normalizer',
            Assert\Uuid::class . '::$normalizer',
        ];
        sort($expectedOptions);

        $this->assertSame(
            $expectedOptions,
            $detectedOptions,
            'The reviewed callable option list changed; classify every addition before updating this assertion.'
        );
    }

    public function testAllowedConstraintsDoNotIntroduceUnserializationMagicMethods(): void
    {
        foreach (ExtraPropertyConstraintMapper::getAllowedConstraints() as $constraintClass) {
            $reflection = new ReflectionClass($constraintClass);
            foreach (['__wakeup', '__unserialize', '__destruct', '__call'] as $method) {
                $this->assertFalse(
                    $reflection->hasMethod($method),
                    sprintf('Magic method %s::%s() needs an explicit security review.', $constraintClass, $method)
                );
            }
        }
    }

    public function testNestedConstraintsAndDateTimeValuesRoundTripWithoutLosingOptions(): void
    {
        $date = new DateTimeImmutable('2030-01-02 03:04:05+00:00');
        $constraints = [
            new Assert\Collection([
                'fields' => [
                    'name' => new Assert\Required([
                        new Assert\NotBlank(),
                        new Assert\Length(['min' => 2, 'max' => 64]),
                    ]),
                    'published_at' => new Assert\Optional([
                        new Assert\AtLeastOneOf([
                            new Assert\IsNull(),
                            new Assert\LessThan($date),
                        ]),
                    ]),
                ],
                'allowExtraFields' => false,
            ]),
        ];

        $decoded = ExtraPropertyConstraintSerializer::unserialize(
            ExtraPropertyConstraintSerializer::serialize($constraints)
        );

        $this->assertEquals($constraints, $decoded);
    }

    public function testEveryMapperConstraintClassRoundTrips(): void
    {
        $requiredOptions = [
            'Length' => 'Length(max: 10)',
            'Regex' => "Regex('/^a$/')",
            'Range' => 'Range(min: 0, max: 10)',
            'EqualTo' => 'EqualTo(1)',
            'NotEqualTo' => 'NotEqualTo(1)',
            'IdenticalTo' => 'IdenticalTo(1)',
            'NotIdenticalTo' => 'NotIdenticalTo(1)',
            'LessThan' => 'LessThan(1)',
            'LessThanOrEqual' => 'LessThanOrEqual(1)',
            'GreaterThan' => 'GreaterThan(1)',
            'GreaterThanOrEqual' => 'GreaterThanOrEqual(1)',
            'DivisibleBy' => 'DivisibleBy(2)',
            'CardScheme' => "CardScheme(['VISA'])",
            'Count' => 'Count(max: 10)',
            'Type' => "Type('string')",
            'All' => 'All[ NotBlank ]',
            'AtLeastOneOf' => 'AtLeastOneOf[ NotBlank, NotNull ]',
            'Collection' => 'Collection[ field: NotBlank ]',
            'Sequentially' => 'Sequentially[ NotBlank, Length(max: 10) ]',
            'TypedRegex' => "TypedRegex('generic_name')",
        ];
        $testedClasses = [];

        foreach (ExtraPropertyConstraintMapper::getAllowedNames() as $name) {
            $constraints = ExtraPropertyConstraintMapper::fromNames($requiredOptions[$name] ?? $name);
            $decoded = ExtraPropertyConstraintSerializer::unserialize(
                ExtraPropertyConstraintSerializer::serialize($constraints)
            );

            $this->assertEquals($constraints, $decoded, sprintf('%s must round-trip.', $name));
            $testedClasses[] = $constraints[0]::class;
        }

        $this->assertSame(
            array_values(ExtraPropertyConstraintMapper::getAllowedConstraints()),
            $testedClasses
        );
    }

    public function testCustomConstraintIsRejectedBeforeSerialization(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage(UnsupportedConstraint::class);

        ExtraPropertyConstraintSerializer::serialize([new UnsupportedConstraint()]);
    }

    public function testExecutableNormalizerAndCallbackOptionsAreRejected(): void
    {
        foreach ([
            new Assert\Length(['min' => 1, 'normalizer' => 'trim']),
            new Assert\Choice(['callback' => 'str_split']),
        ] as $constraint) {
            try {
                ExtraPropertyConstraintSerializer::serialize([$constraint]);
                $this->fail(sprintf('%s should have been rejected.', $constraint::class));
            } catch (InvalidExtraPropertyConstraintException $exception) {
                $this->assertStringContainsString('may execute a callable', $exception->getMessage());
            }
        }
    }

    public function testCallableOptionsInjectedInSerializedBlobAreRejectedOnRead(): void
    {
        foreach ([
            new Assert\Length(['min' => 1, 'normalizer' => 'system']),
            new Assert\Choice(['callback' => 'system']),
        ] as $tamperedConstraint) {
            $rejections = [];

            // Bypass the safe write path to reproduce a blob written directly to the database.
            $decoded = ExtraPropertyConstraintSerializer::unserialize(
                serialize([new Assert\NotBlank(), $tamperedConstraint]),
                static function (int|string|null $index, string $reason) use (&$rejections): void {
                    $rejections[] = [$index, $reason];
                }
            );

            $this->assertCount(1, $decoded);
            $this->assertInstanceOf(Assert\NotBlank::class, $decoded[0]);
            $this->assertCount(1, $rejections);
            $this->assertSame(1, $rejections[0][0]);
            $this->assertStringContainsString('may execute a callable', $rejections[0][1]);
        }
    }

    public function testPropertyPathInjectedInSerializedBlobIsRejectedOnRead(): void
    {
        $rejections = [];

        $decoded = ExtraPropertyConstraintSerializer::unserialize(
            serialize([
                new Assert\NotBlank(),
                new Assert\LessThan(['propertyPath' => 'sensitiveProperty']),
            ]),
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = [$index, $reason];
            }
        );

        $this->assertCount(1, $decoded);
        $this->assertInstanceOf(Assert\NotBlank::class, $decoded[0]);
        $this->assertSame(1, $rejections[0][0]);
        $this->assertStringContainsString('may traverse the validated object', $rejections[0][1]);
    }

    public function testRecursiveArrayGraphIsRejectedAtDepthLimit(): void
    {
        $payload = [];
        $payload['self'] = &$payload;

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage('exceeds the maximum depth');

        ExtraPropertyConstraintSerializer::serialize([
            new Assert\NotBlank(['payload' => $payload]),
        ]);
    }

    public function testInvalidNestedClassDropsWholeCompositeButKeepsValidSibling(): void
    {
        $rejections = [];
        $serialized = serialize([
            new Assert\NotBlank(),
            new Assert\All([new UnsupportedConstraint()]),
        ]);

        $decoded = ExtraPropertyConstraintSerializer::unserialize(
            $serialized,
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = [$index, $reason];
            }
        );

        $this->assertCount(1, $decoded);
        $this->assertInstanceOf(Assert\NotBlank::class, $decoded[0]);
        $this->assertCount(1, $rejections);
        $this->assertSame(1, $rejections[0][0]);
        $this->assertStringContainsString(UnsupportedConstraint::class, $rejections[0][1]);
    }

    public function testDisallowedWakeupIsNeverExecuted(): void
    {
        WakeupGadget::$wakeupCalls = 0;

        $decoded = ExtraPropertyConstraintSerializer::unserialize(serialize([new WakeupGadget()]));

        $this->assertNull($decoded);
        $this->assertSame(0, WakeupGadget::$wakeupCalls);
    }

    public function testMalformedPayloadIsRejectedWithoutEmittingAWarning(): void
    {
        $rejections = [];

        $decoded = ExtraPropertyConstraintSerializer::unserialize(
            'not-serialized',
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = [$index, $reason];
            }
        );

        $this->assertNull($decoded);
        $this->assertCount(1, $rejections);
        $this->assertNull($rejections[0][0]);
        $this->assertStringContainsString('Invalid serialized extra property constraints', $rejections[0][1]);
    }
}

final class UnsupportedConstraint extends Constraint
{
}

final class WakeupGadget
{
    public static int $wakeupCalls = 0;

    public function __wakeup(): void
    {
        ++self::$wakeupCalls;
    }
}
