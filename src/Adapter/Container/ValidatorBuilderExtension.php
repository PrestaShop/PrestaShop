<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Container;

use PrestaShop\PrestaShop\Adapter\Validator\LegacyValidatorFactory;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Registers a Symfony `validator` in the hand-built front-office legacy container.
 *
 * The three Symfony kernels get `validator` from FrameworkBundle, but the FO legacy container
 * (PrestaShop\PrestaShop\Adapter\ContainerBuilder) does not — so ExtraProperty (and any other) validation would have
 * no validator there. We assemble one with a container-backed, failure-tolerant constraint-validator factory
 * (GracefulConstraintValidatorFactory): Symfony built-in validators resolve via `new`, the PS validators whose
 * dependencies exist in this container are registered under their FQCN (what Constraint::validatedBy() returns), and
 * validators that cannot be built here (e.g. DefaultLanguageValidator → LanguageContext) are skipped + logged.
 *
 * Note: this can't be a CompilerPass — extensions must run before compilation (same reason as DoctrineBuilderExtension).
 */
final class ValidatorBuilderExtension implements ContainerBuilderExtensionInterface
{
    public function build(ContainerBuilder $container)
    {
        // PS constraint validators whose dependencies are available in the FO container, registered under their FQCN
        // id so GracefulConstraintValidatorFactory resolves them from the container (must be public, otherwise they
        // are pruned as unused since nothing references them at compile time — only the factory `get()`s them).
        $container->register(TypedRegexValidator::class, TypedRegexValidator::class)
            ->setPublic(true)
            ->setArguments([new Reference('prestashop.adapter.legacy.configuration')]);

        $container->register(CleanHtmlValidator::class, CleanHtmlValidator::class)
            ->setPublic(true)
            ->setFactory([LegacyValidatorFactory::class, 'createCleanHtmlValidator'])
            ->setArguments([new Reference('prestashop.adapter.legacy.configuration')]);

        // The validator itself, assembled by LegacyValidatorFactory from the container (for the constraint-validator
        // factory). No logger is injected: skipped constraints fall back to error_log() (the FO container has no
        // Monolog logger, and the legacy logger fires hooks from inside validation — see GracefulConstraintValidatorFactory).
        $container->register('validator', ValidatorInterface::class)
            ->setPublic(true)
            ->setFactory([LegacyValidatorFactory::class, 'create'])
            ->setArguments([new Reference('service_container')]);

        // So both `@validator` and a ValidatorInterface type-hint resolve, like in the Symfony kernels.
        $container->setAlias(ValidatorInterface::class, 'validator')->setPublic(true);
    }
}
