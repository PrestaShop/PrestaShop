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
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintNormalizer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class ExtraPropertyConstraintNormalizerTest extends TestCase
{
    private ExtraPropertyConstraintNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ExtraPropertyConstraintNormalizer();
    }

    /**
     * The persisted form is the DSL, so no PHP deserialization primitive may remain on this path.
     */
    public function testNoPhpDeserializationRemainsOnTheConstraintPath(): void
    {
        $sources = [
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Validation/ExtraPropertyConstraintNormalizer.php',
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
        $encoded = $this->normalizer->normalize($constraints);
        $this->assertIsString($encoded);

        $decoded = $this->normalizer->denormalize($encoded)->getConstraints();

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

            $decoded = $this->normalizer->denormalize($this->normalizer->normalize($constraints))->getConstraints();

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

        $this->normalizer->normalize([$constraint]);
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

        yield 'constraint outside the grammar' => [new NormalizerUnsupportedConstraint(), '/not part of the extra property constraint grammar/'];
    }

    public function testSelfReferencingCompositeIsRefused(): void
    {
        $composite = new Assert\All([new Assert\NotBlank()]);
        $composite->constraints[] = $composite;

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/exceeds the maximum depth/');

        $this->normalizer->normalize([$composite]);
    }

    /**
     * A tampered registry row must not be able to reintroduce an executable option, and must not
     * take its valid neighbours down with it.
     */
    public function testTamperedStoredValueLosesOnlyTheOffendingConstraint(): void
    {
        $decoded = $this->normalizer->denormalize("Url\nLength(max: 5, normalizer: 'system')\nNotBlank");

        $constraints = $decoded->getConstraints();
        $this->assertIsArray($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Url::class, $constraints[0]);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);

        $this->assertTrue($decoded->hasRejections());
        $this->assertCount(1, $decoded->getRejections());
        $this->assertSame(1, $decoded->getRejections()[0]['index']);
        $this->assertStringContainsString('may execute a callable', $decoded->getRejections()[0]['reason']);
    }

    public function testMalformedStoredValueIsReportedWithoutThrowing(): void
    {
        $decoded = $this->normalizer->denormalize('}{ not a constraint');

        $this->assertNull($decoded->getConstraints());
        $this->assertCount(1, $decoded->getRejections());
    }

    public function testOversizedStoredValueIsRefusedBeforeParsing(): void
    {
        $decoded = $this->normalizer->denormalize(
            str_repeat('NotBlank,', ExtraPropertyConstraintMapper::MAX_RAW_LENGTH)
        );

        $this->assertNull($decoded->getConstraints());
        $this->assertStringContainsString('maximum length', $decoded->getRejections()[0]['reason']);
    }

    public function testTooManyTopLevelConstraintsAreRefused(): void
    {
        $decoded = $this->normalizer->denormalize(
            implode(',', array_fill(0, ExtraPropertyConstraintMapper::MAX_TOKENS + 1, 'NotBlank'))
        );

        $this->assertNull($decoded->getConstraints());
        $this->assertStringContainsString('maximum of', $decoded->getRejections()[0]['reason']);
    }

    /**
     * A valid definition decodes without any rejection to report.
     */
    public function testCleanStoredValueReportsNoRejection(): void
    {
        $decoded = $this->normalizer->denormalize("NotBlank\nLength(max: 10)");

        $this->assertCount(2, (array) $decoded->getConstraints());
        $this->assertFalse($decoded->hasRejections());
        $this->assertSame([], $decoded->getRejections());
    }

    /**
     * The wrappers Collection generates are part of the grammar but must never be offered as
     * constraints a merchant can pick, nor accepted at the top level of a definition.
     */
    public function testCollectionWrappersStayInternal(): void
    {
        $this->assertNotContains('Required', ExtraPropertyConstraintMapper::getAllowedNames());
        $this->assertNotContains('Optional', ExtraPropertyConstraintMapper::getAllowedNames());

        $decoded = $this->normalizer->denormalize('Required[ NotBlank ]');

        $this->assertNull($decoded->getConstraints());
        $this->assertStringContainsString(
            'Unknown extra property constraint "Required"',
            $decoded->getRejections()[0]['reason']
        );
    }

    /**
     * @dataProvider supportedStoredValueProvider
     */
    public function testSupportsDenormalizationInspectsTheStoredValue(string $raw, bool $expected): void
    {
        $this->assertSame(
            $expected,
            $this->normalizer->supportsDenormalization($raw, ExtraPropertyConstraintNormalizer::SUPPORTED_TYPE)
        );
    }

    public static function supportedStoredValueProvider(): iterable
    {
        yield 'known constraints' => ["NotBlank\nLength(max: 10)", true];
        yield 'composite with its own options' => ['Collection(allowExtraFields: true)[ a: NotBlank ]', true];
        yield 'empty value' => ['', true];
        yield 'unknown name' => ['NotAConstraint', false];
        yield 'callable option' => ["Length(max: 5, normalizer: 'system')", false];
        yield 'property path option' => ["LessThan(propertyPath: 'secret')", false];
        yield 'internal wrapper at the top level' => ['Required[ NotBlank ]', false];
        yield 'malformed' => ['}{ garbage', false];
        yield 'too deep' => [str_repeat('All[', 40) . 'NotBlank' . str_repeat(']', 40), false];
    }

    public function testSupportsDenormalizationRejectsAnotherType(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization('NotBlank', 'SomethingElse'));
    }

    public function testSupportsNormalizationInspectsTheConstraints(): void
    {
        $this->assertTrue($this->normalizer->supportsNormalization([new Assert\NotBlank(), new Assert\Length(['max' => 5])]));
        $this->assertTrue($this->normalizer->supportsNormalization([]));
        $this->assertTrue($this->normalizer->supportsNormalization(null));

        $callable = new Assert\Length(['max' => 5]);
        $callable->normalizer = 'system';
        $this->assertFalse($this->normalizer->supportsNormalization([$callable]));

        $this->assertFalse($this->normalizer->supportsNormalization([new NormalizerUnsupportedConstraint()]));
    }

    /**
     * The support checks answer "does this look like mine?", never "will this normalize?". Anything
     * relying on the second — the registry guard in particular — must normalize for real, or it
     * accepts constraints that fail once the storage column already exists.
     */
    public function testSupportChecksAreShallowerThanNormalization(): void
    {
        // A keyed map: every name and option is fine, only the rendering cannot represent it.
        $constraints = [new Assert\Choice(['choices' => ['a' => 1, 'b' => 2]])];

        $this->assertTrue($this->normalizer->supportsNormalization($constraints));

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->normalizer->normalize($constraints);
    }

    /**
     * The registry guard must refuse exactly what save() would refuse, or a definition can pass the
     * guard and then fail once its storage column has already been created.
     */
    public function testTheRegistryGuardRefusesEverythingNormalizeRefuses(): void
    {
        // Keyed map: the object graph walk accepts it, the rendering cannot represent it.
        $constraints = [new Assert\Choice(['choices' => ['a' => 1, 'b' => 2]])];

        $normalizeFailed = false;
        try {
            $this->normalizer->normalize($constraints);
        } catch (InvalidExtraPropertyConstraintException) {
            $normalizeFailed = true;
        }
        $this->assertTrue($normalizeFailed, 'This fixture is meant to be refused by normalize().');

        // The registry runs this very call as its guard, which is what keeps it equivalent to save().
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->normalizer->normalize($constraints);
    }
}

final class NormalizerUnsupportedConstraint extends Constraint
{
}
