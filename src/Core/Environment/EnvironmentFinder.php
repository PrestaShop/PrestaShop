<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Environment;

use PrestaShop\PrestaShop\Core\Exception\EnvironmentNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\NotImplementedException;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

final class EnvironmentFinder implements EnvironmentFinderInterface
{
    protected static $availableEnvironementTypes = null;
    protected static $environementTypesCache = null;

    /**
     * @var TranslatorInterface
     */
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public const ENVIRONMENT_TYPES_NAMESPACE = 'PrestaShop\PrestaShop\Core\Environment\Type\\';
    public const ENVIRONMENT_TYPES_PATH = _PS_ROOT_DIR_ . '/src/Core/Environment/Type';

    /**
     * @throws EnvironmentNotFoundException
     * @throws \Exception
     */
    public function findById(string $environmentId): EnvironmentTypeInterface
    {
        if (isset(self::$environementTypesCache[$environmentId])) {
            return self::$environementTypesCache[$environmentId];
        }

        $environmentClassName = $this->getFullEnvironmentClassNameById($environmentId);

        if (class_exists($environmentClassName)) {
            try {
                $environmentObject = new $environmentClassName($this->translator);

                if (!is_a($environmentObject, EnvironmentTypeInterface::class)) {
                    throw new NotImplementedException('Invalid environment ' . $environmentId . '. Environment must implement ' . EnvironmentTypeInterface::class);
                }

                self::$environementTypesCache[$environmentId] = $environmentObject;

                return $environmentObject;
            } catch (\Exception $e) {
                throw new EnvironmentNotFoundException('Error during found environment ' . $environmentClassName . ' : ' . $e->getMessage());
            }
        }

        throw new EnvironmentNotFoundException('Environment ' . $environmentClassName . ' not found ');
    }

    /**
     * @throws EnvironmentNotFoundException
     */
    public function findAll(): array
    {
        if (null != self::$availableEnvironementTypes) {
            return self::$availableEnvironementTypes;
        }

        $finder = new Finder();
        $finder->files()
            ->in(self::ENVIRONMENT_TYPES_PATH)
            ->name('*.php')
        ;

        $environments = [];

        foreach ($finder as $file) {
            $environments[] = $this->findById($file->getFilenameWithoutExtension());
        }

        self::$availableEnvironementTypes = $environments;

        return $environments;
    }

    private function getFullEnvironmentClassNameById(string $environmentId): string
    {
        return self::ENVIRONMENT_TYPES_NAMESPACE . $environmentId;
    }
}
