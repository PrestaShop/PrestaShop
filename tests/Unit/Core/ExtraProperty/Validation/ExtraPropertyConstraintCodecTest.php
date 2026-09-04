<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCodec;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ExtraPropertyConstraintCodecTest extends TestCase
{
    /**
     * The persisted form is the DSL, so no PHP deserialization primitive may remain on this path.
     */
    public function testNoPhpDeserializationRemainsOnTheConstraintPath(): void
    {
        $sources = [
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Validation/ExtraPropertyConstraintCodec.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Validation/ExtraPropertyConstraintMapper.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Definition/ExtraPropertyDefinition.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Definition/ExtraPropertyDefinitionRepository.php',
        ];

        foreach ($sources as $source) {
            $code = (string) file_get_contents($source);
            // Strip comments so the documentation explaining the absence does not trip the check.
            $code = (string) preg_replace('#/\*.*?\*/|//[^\n]*#s', '', $code);

            $this->assertDoesNotMatchRegularExpression(
                '/\b(un)?serialize\s*\(/',
                $code,
                sprintf('%s must not call serialize()/unserialize().', basename($source))
            );
        }
    }

    /**
     * @dataProvider losslessConstraintProvider
     *
     * @param list<Constraint> $constraints
     */
    public function testConstraintsSurviveTheStorageRoundTrip(string $_label, array $constraints): void
    {
        $encoded = ExtraPropertyConstraintCodec::encode($constraints);
        $this->assertIsString($encoded);

        $decoded = ExtraPropertyConstraintCodec::decodeStrict($encoded);

        // var_export keeps scalar types apart, which a loose comparison would not.
        $this->assertSame(var_export($constraints, true), var_export($decoded, true));
    }

    public static function losslessConstraintProvider(): iterable
    {
        yield 'plain constraints' => ['plain', [new Assert\NotBlank(), new Assert\Length(['min' => 2, 'max' => 64])]];

        yield 'composite with its own options' => ['composite options', [
            new Assert\Collection([
                'fields' => ['name' => new Assert\NotBlank(), 'code' => new Assert\Length(['max' => 5])],
                'allowExtraFields' => true,
                'allowMissingFields' => true,
            ]),
        ]];

        yield 'composite list option' => ['at least one of', [
            new Assert\AtLeastOneOf([
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
                'includeInternalMessages' => false,
            ]),
        ]];

        yield 'explicit Optional wrapper' => ['optional wrapper', [
            new Assert\Collection(['fields' => [
                'a' => new Assert\Required([new Assert\NotBlank()]),
                'b' => new Assert\Optional([new Assert\Email()]),
            ]]),
        ]];

        yield 'scalar typing is preserved' => ['scalar typing', [
            new Assert\Choice(['choices' => ['1', '2']]),
            new Assert\Count(['max' => 10]),
        ]];
    }

    public function testEveryPublicConstraintSurvivesTheRoundTrip(): void
    {
        $samples = [
            'Length' => 'Length(max: 10)', 'Regex' => "Regex('/^a$/')", 'Range' => 'Range(min: 0, max: 10)',
            'EqualTo' => 'EqualTo(1)', 'NotEqualTo' => 'NotEqualTo(1)', 'IdenticalTo' => 'IdenticalTo(1)',
            'NotIdenticalTo' => 'NotIdenticalTo(1)', 'LessThan' => 'LessThan(1)', 'LessThanOrEqual' => 'LessThanOrEqual(1)',
            'GreaterThan' => 'GreaterThan(1)', 'GreaterThanOrEqual' => 'GreaterThanOrEqual(1)', 'DivisibleBy' => 'DivisibleBy(2)',
            'CardScheme' => "CardScheme(['VISA'])", 'Count' => 'Count(max: 10)', 'Type' => "Type('string')",
            'All' => 'All[ NotBlank ]', 'AtLeastOneOf' => 'AtLeastOneOf[ NotBlank, NotNull ]',
            'Collection' => 'Collection[ field: NotBlank ]', 'Sequentially' => 'Sequentially[ NotBlank, Length(max: 10) ]',
            'TypedRegex' => "TypedRegex('generic_name')",
        ];

        foreach (ExtraPropertyConstraintMapper::getAllowedNames() as $name) {
            $constraints = ExtraPropertyConstraintMapper::fromNames($samples[$name] ?? $name);
            $this->assertIsArray($constraints);

            $decoded = ExtraPropertyConstraintCodec::decodeStrict(ExtraPropertyConstraintCodec::encode($constraints));

            $this->assertSame(
                var_export($constraints, true),
                var_export($decoded, true),
                sprintf('%s must survive the storage round-trip.', $name)
            );
        }
    }

    /**
     * @dataProvider refusedConstraintProvider
     */
    public function testConstraintsThatCannotBeStoredSafelyAreRefused(Constraint $constraint, string $expectedMessage): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        ExtraPropertyConstraintCodec::encode([$constraint]);
    }

    public static function refusedConstraintProvider(): iterable
    {
        $normalizer = new Assert\Length(['max' => 5]);
        $normalizer->normalizer = 'system';
        yield 'callable normalizer' => [$normalizer, '/may execute a callable/'];

        $callback = new Assert\Choice(['choices' => ['a']]);
        $callback->callback = 'system';
        yield 'callable callback' => [$callback, '/may execute a callable/'];

        $propertyPath = new Assert\LessThan(1);
        $propertyPath->propertyPath = 'secret';
        yield 'property path' => [$propertyPath, '/traverse the validated object/'];

        $payload = new Assert\NotBlank();
        $payload->payload = ['anything'];
        yield 'payload' => [$payload, '/payload/'];

        $groups = new Assert\NotBlank();
        $groups->groups = ['Custom'];
        yield 'custom validation group' => [$groups, '/groups are not supported/'];

        yield 'object option' => [new Assert\LessThan(new DateTimeImmutable('2030-01-01')), '/cannot be represented/'];

        yield 'constraint outside the grammar' => [new CodecUnsupportedConstraint(), '/not part of the extra property constraint grammar/'];
    }

    public function testSelfReferencingCompositeIsRefused(): void
    {
        $composite = new Assert\All([new Assert\NotBlank()]);
        $composite->constraints[] = $composite;

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/exceeds the maximum depth/');

        ExtraPropertyConstraintCodec::encode([$composite]);
    }

    /**
     * A tampered registry row must not be able to reintroduce an executable option, and must not
     * take its valid neighbours down with it.
     */
    public function testTamperedStoredValueLosesOnlyTheOffendingConstraint(): void
    {
        $rejections = [];

        $decoded = ExtraPropertyConstraintCodec::decodeTolerant(
            "Url\nLength(max: 5, normalizer: 'system')\nNotBlank",
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = [$index, $reason];
            }
        );

        $this->assertIsArray($decoded);
        $this->assertCount(2, $decoded);
        $this->assertInstanceOf(Assert\Url::class, $decoded[0]);
        $this->assertInstanceOf(Assert\NotBlank::class, $decoded[1]);

        $this->assertCount(1, $rejections);
        $this->assertSame(1, $rejections[0][0]);
        $this->assertStringContainsString('may execute a callable', $rejections[0][1]);
    }

    public function testMalformedStoredValueIsReportedWithoutThrowing(): void
    {
        $rejections = [];

        $decoded = ExtraPropertyConstraintCodec::decodeTolerant(
            '}{ not a constraint',
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = $reason;
            }
        );

        $this->assertNull($decoded);
        $this->assertCount(1, $rejections);
    }

    public function testOversizedStoredValueIsRefusedBeforeParsing(): void
    {
        $rejections = [];

        $decoded = ExtraPropertyConstraintCodec::decodeTolerant(
            str_repeat('NotBlank,', ExtraPropertyConstraintMapper::maxRawLength()),
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = $reason;
            }
        );

        $this->assertNull($decoded);
        $this->assertStringContainsString('maximum length', $rejections[0]);
    }

    /**
     * The wrappers Collection generates are part of the grammar but must never be offered as
     * constraints a merchant can pick, nor accepted at the top level of a definition.
     */
    public function testCollectionWrappersStayInternal(): void
    {
        $this->assertNotContains('Required', ExtraPropertyConstraintMapper::getAllowedNames());
        $this->assertNotContains('Optional', ExtraPropertyConstraintMapper::getAllowedNames());

        $rejections = [];
        $decoded = ExtraPropertyConstraintCodec::decodeTolerant(
            'Required[ NotBlank ]',
            static function (int|string|null $index, string $reason) use (&$rejections): void {
                $rejections[] = $reason;
            }
        );

        $this->assertNull($decoded);
        $this->assertStringContainsString('Unknown extra property constraint "Required"', $rejections[0]);
    }
}

final class CodecUnsupportedConstraint extends Constraint
{
}
