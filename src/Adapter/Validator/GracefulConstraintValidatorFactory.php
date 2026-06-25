<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Validator;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Throwable;

/**
 * A container-backed constraint-validator factory that tolerates validators it cannot build.
 *
 * The front-office legacy container (PrestaShop\PrestaShop\Adapter\ContainerBuilder) is hand-built and does not
 * register every service the full Symfony kernels do. Symfony built-in validators and the PS validators registered
 * for the FO container (TypedRegex, CleanHtml) resolve normally; a validator whose dependencies are absent here
 * (e.g. DefaultLanguageValidator → LanguageContext) would otherwise throw when the parent factory does `new $class()`.
 * Instead of fataling, we log a warning and return a no-op: the constraint is still fully enforced wherever the full
 * container runs (back-office Symfony pages and the Admin API).
 */
final class GracefulConstraintValidatorFactory extends ContainerConstraintValidatorFactory
{
    /** @var array<string, NoOpConstraintValidator> no-op validators for un-buildable constraints, keyed by validator FQCN */
    private array $skipped = [];

    public function __construct(
        ContainerInterface $container,
        private readonly ?LoggerInterface $logger = null,
    ) {
        parent::__construct($container);
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $name = $constraint->validatedBy();
        if (isset($this->skipped[$name])) {
            return $this->skipped[$name];
        }

        try {
            return parent::getInstance($constraint);
        } catch (Throwable $exception) {
            $message = sprintf(
                'ExtraProperty: constraint "%s" cannot be validated in the front-office legacy container and was '
                . 'skipped (it is still enforced on Symfony pages and the Admin API): %s',
                $name,
                $exception->getMessage(),
            );

            // Log once per validator (the no-op is cached below). A PSR logger is used when one is injected (e.g.
            // tests); the FO container has none, and PrestaShop's legacy logger must NOT be used here — it fires hooks
            // from inside validation (PrestaShopLogger::addLog → Hook::exec). error_log() has no such side effects.
            if (null !== $this->logger) {
                $this->logger->warning($message);
            } else {
                error_log($message);
            }

            return $this->skipped[$name] = new NoOpConstraintValidator();
        }
    }
}
