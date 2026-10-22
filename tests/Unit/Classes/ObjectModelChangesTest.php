<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use ObjectModel;
use ObjectModelCore;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ObjectModelChangesTestEntity extends ObjectModel
{
    public $quantity;
    public $price;
    public $active;
    public $reference;
    public $date_upd;
    public $nullable_note;
    public $name;

    public static $definition = [
        'table' => 'changes_test_entity',
        'primary' => 'id_changes_test_entity',
        'multilang' => true,
        'fields' => [
            'quantity' => ['type' => self::TYPE_INT],
            'price' => ['type' => self::TYPE_FLOAT],
            'active' => ['type' => self::TYPE_BOOL],
            'reference' => ['type' => self::TYPE_STRING],
            'date_upd' => ['type' => self::TYPE_DATE],
            'nullable_note' => ['type' => self::TYPE_STRING, 'allow_null' => true],
            'name' => ['type' => self::TYPE_STRING, 'lang' => true],
        ],
    ];
}

class ObjectModelChangesTest extends TestCase
{
    public function testUnchangedValuesWithDifferentTypesAreNotReported(): void
    {
        // Values hydrated from database are strings
        $persisted = $this->createEntity([
            'quantity' => '3',
            'price' => '9.900000',
            'active' => '1',
            'reference' => 'REF',
            'date_upd' => '0000-00-00 00:00:00',
            'nullable_note' => null,
            'name' => [1 => 'Name', 2 => 'Nom'],
        ]);
        $current = $this->createEntity([
            'quantity' => 3,
            'price' => 9.9,
            'active' => true,
            'reference' => 'REF',
            'date_upd' => null,
            'nullable_note' => null,
            'name' => [1 => 'Name', 2 => 'Nom'],
        ]);

        $this->assertSame([], $this->computeChanges($current, $persisted));
    }

    public function testChangedValuesAreReportedWithOldAndNewValues(): void
    {
        $persisted = $this->createEntity([
            'quantity' => '3',
            'active' => '1',
            'nullable_note' => null,
            'name' => [1 => 'Name', 2 => 'Nom'],
        ]);
        $current = $this->createEntity([
            'quantity' => 5,
            'active' => false,
            'nullable_note' => 'note',
            'name' => [1 => 'Name', 2 => 'Nouveau nom'],
        ]);

        $this->assertSame([
            'quantity' => ['old' => '3', 'new' => 5],
            'active' => ['old' => '1', 'new' => false],
            'nullable_note' => ['old' => null, 'new' => 'note'],
            'name' => ['old' => [2 => 'Nom'], 'new' => [2 => 'Nouveau nom']],
        ], $this->computeChanges($current, $persisted));
    }

    public function testSingleLanguageObjectReportsLangFieldIndexedByLanguage(): void
    {
        $persisted = $this->createEntity(['name' => 'Nom'], 2);
        $current = $this->createEntity(['name' => 'Nouveau nom'], 2);

        $this->assertSame(
            ['name' => ['old' => [2 => 'Nom'], 'new' => [2 => 'Nouveau nom']]],
            $this->computeChanges($current, $persisted)
        );
    }

    public function testOnlyFieldsToUpdateAreReported(): void
    {
        $persisted = $this->createEntity([
            'quantity' => '3',
            'reference' => 'REF',
            'name' => [1 => 'Name', 2 => 'Nom'],
        ]);
        $current = $this->createEntity([
            'quantity' => 5,
            'reference' => 'NEW-REF',
            'name' => [1 => 'New name', 2 => 'Nouveau nom'],
        ]);
        $current->setFieldsToUpdate(['quantity' => true, 'name' => [2 => true]]);

        $this->assertSame([
            'quantity' => ['old' => '3', 'new' => 5],
            'name' => ['old' => [2 => 'Nom'], 'new' => [2 => 'Nouveau nom']],
        ], $this->computeChanges($current, $persisted));
    }

    public function testLangFieldNotUpdatedPerLanguageIsNotReported(): void
    {
        $persisted = $this->createEntity([
            'quantity' => '3',
            'name' => [1 => 'Name', 2 => 'Nom'],
        ]);
        $current = $this->createEntity([
            'quantity' => 5,
            'name' => [1 => 'New name', 2 => 'Nouveau nom'],
        ]);
        // formatFields() only writes lang fields flagged per language, so `true` writes nothing
        $current->setFieldsToUpdate(['quantity' => true, 'name' => true]);

        $this->assertSame(
            ['quantity' => ['old' => '3', 'new' => 5]],
            $this->computeChanges($current, $persisted)
        );
    }

    private function createEntity(array $values, ?int $idLang = null): ObjectModelChangesTestEntity
    {
        $entity = new ObjectModelChangesTestEntity();
        // The constructor would check the language against the database
        (fn () => $this->id_lang = $idLang)->call($entity);
        foreach ($values as $field => $value) {
            $entity->{$field} = $value;
        }

        return $entity;
    }

    private function computeChanges(ObjectModel $current, ObjectModel $persisted): array
    {
        return (new ReflectionMethod(ObjectModelCore::class, 'computeChanges'))->invoke($current, $persisted);
    }
}
