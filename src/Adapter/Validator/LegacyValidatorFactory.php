<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Validator;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Service factories used to assemble the Symfony validator inside the hand-built front-office legacy container, which
 * (unlike the Symfony kernels) does not get a `validator` from FrameworkBundle.
 *
 * Raw-value validation (`validate($value, $constraints)`) needs no class metadata, so a plain ValidatorBuilder with a
 * container-backed, failure-tolerant constraint-validator factory is enough. No translator is set: messages fall back
 * to their (untranslated) templates, which is acceptable for the FO path where they surface in exceptions/logs.
 */
final class LegacyValidatorFactory
{
    public static function create(ContainerInterface $container, ?LoggerInterface $logger = null): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory(new GracefulConstraintValidatorFactory($container, $logger))
            ->getValidator();
    }

    /**
     * Builds CleanHtmlValidator, whose constructor needs the resolved PS_ALLOW_HTML_IFRAME flag (the SF kernels wire
     * this through a DI expression, which is awkward to express in the hand-built container — a factory is simpler).
     */
    public static function createCleanHtmlValidator(ConfigurationInterface $configuration): CleanHtmlValidator
    {
        return new CleanHtmlValidator((bool) $configuration->get('PS_ALLOW_HTML_IFRAME'));
    }
}
