<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\ClassMethod\OptionalParametersAfterRequiredRector;
use Rector\Config\RectorConfig;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php55\Rector\Class_\ClassConstantToSelfClassRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php56\Rector\FuncCall\PowToExpRector;
use Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector;
use Rector\Php70\Rector\FunctionLike\ExceptionHandlerTypehintRector;
use Rector\Php70\Rector\If_\IfToSpaceshipRector;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php70\Rector\Ternary\TernaryToNullCoalescingRector;
use Rector\Php71\Rector\FuncCall\CountOnNullRector;
use Rector\Php71\Rector\TryCatch\MultiExceptionCatchRector;
use Rector\Php72\Rector\FuncCall\GetClassOnNullRector;
use Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php74\Rector\Ternary\ParenthesizeNestedTernaryRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Class_\StringableForToStringRector;
use Rector\Php80\Rector\FunctionLike\MixedTypeRector;
use Rector\Php80\Rector\FunctionLike\UnionTypesRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php80\Rector\Ternary\GetDebugTypeRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Php81\Rector\FunctionLike\IntersectionTypesRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src/',
    ]);

    $rectorConfig->sets(
        [LevelSetList::UP_TO_PHP_81]
    );

    /**
     * Ignored rules for now as grouping them would create a huge PR impossible to review,
     * so I documented them here, I we can process them one by one.
     */
    $rectorConfig->skip([
        ReadOnlyPropertyRector::class => '*',
        ClosureToArrowFunctionRector::class => '*',
        InlineConstructorDefaultToPropertyRector::class => '*',
        ClassPropertyAssignToConstructorPromotionRector::class => '*',
        UnionTypesRector::class => '*',
        MixedTypeRector::class => '*',
        TypedPropertyFromAssignsRector::class => '*',
        FinalizePublicClassConstantRector::class => '*',
        CountOnNullRector::class => '*',
        AddDefaultValueForUndefinedVariableRector::class => '*',
        NullToStrictStringFuncCallArgRector::class => '*',
        JsonThrowOnErrorRector::class => '*',
        IntersectionTypesRector::class => '*',
        ReturnNeverTypeRector::class => '*',
        ChangeSwitchToMatchRector::class => '*',
        TernaryToNullCoalescingRector::class => '*',
        RemoveUnusedVariableInCatchRector::class => '*',
        StringableForToStringRector::class => '*',
        StringClassNameToClassConstantRector::class => '*',
        IfToSpaceshipRector::class => '*',
        FirstClassCallableRector::class => '*',
        ExceptionHandlerTypehintRector::class => '*',
        ThisCallOnStaticMethodToStaticCallRector::class => '*',
        MultiExceptionCatchRector::class => '*',
        TernaryToElvisRector::class => '*',
        NullCoalescingOperatorRector::class => '*',
        ArraySpreadInsteadOfArrayMergeRector::class => '*',
        AddLiteralSeparatorToNumberRector::class => '*',
        ParenthesizeNestedTernaryRector::class => '*',
        GetDebugTypeRector::class => '*',
        PowToExpRector::class => '*',
        GetClassOnNullRector::class => '*',
        ClassConstantToSelfClassRector::class => '*',
        OptionalParametersAfterRequiredRector::class => '*',
    ]);

};
