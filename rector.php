<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\ClassConstFetch\ClassOnThisVariableObjectRector;
use Rector\Php80\Rector\Identical\StrEndsWithRector;
use Rector\Php80\Rector\Identical\StrStartsWithRector;
use Rector\Php80\Rector\NotIdentical\StrContainsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

//    $rectorConfig->sets(
//        [LevelSetList::UP_TO_PHP_81]
//    );
//    $rectorConfig->rule(ClassPropertyAssignToConstructorPromotionRector::class);
    $rectorConfig->rule(StrContainsRector::class);
    $rectorConfig->rule(StrStartsWithRector::class);
    $rectorConfig->rule(ClassOnThisVariableObjectRector::class);
    $rectorConfig->rule(StrEndsWithRector::class);


//    $rectorConfig->ruleWithConfiguration(\Rector\Php80\Rector\Class_\DoctrineAnnotationClassToAttributeRector::class, [
//        new AnnotationToAttribute(AdminSecurity::class)
//    ]);
//
//    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
//        \PrestaShopBundle\Security\Annotation\AdminSecurity::class => \PrestaShop\PrestaShop\Core\Security\Attributes\Security::class,
//    ]);
};
