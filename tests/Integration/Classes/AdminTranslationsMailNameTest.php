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

namespace Tests\Integration\Classes;

use AdminTranslationsController;
use PHPUnit\Framework\TestCase;
use PrestaShopException;
use ReflectionClass;
use ReflectionMethod;
use Validate;

/**
 * When saving e-mail translations, the template name comes from the submitted array key
 * (core_mail[tpl][<name>] / module_mail[tpl][<name>]) and is used to build the destination
 * file path. That name must always be checked with Validate::isTplName(), so a value such as
 * "../../themes/pwn" cannot write a file outside the mails directory. The check used to sit
 * inside the "name contains a module prefix" branch, so a key with no pipe (or a pipe at the
 * first position) skipped it entirely.
 */
class AdminTranslationsMailNameTest extends TestCase
{
    /**
     * @dataProvider invalidMailNameProvider
     */
    public function testMailNameEscapingTheMailsDirectoryIsRejected(string $mailName): void
    {
        // Guard the premise: isTplName itself must reject the traversal name.
        self::assertSame(0, (int) Validate::isTplName($mailName));

        $controller = (new ReflectionClass(AdminTranslationsController::class))->newInstanceWithoutConstructor();

        // Minimal state read by submitTranslationsMails before the name is validated.
        $translator = new class() {
            public function trans($id, array $parameters = [], $domain = null, $locale = null): string
            {
                return (string) $id;
            }
        };
        $this->setProperty($controller, 'translator', $translator);
        $this->setProperty($controller, 'theme_selected', false);
        $this->setProperty($controller, 'type_selected', 'mails');
        $this->setProperty($controller, 'translations_informations', [
            'mails' => ['dir' => sys_get_temp_dir() . '/ps-mail-tpl-' . uniqid() . '/'],
        ]);

        $_POST['core_mail'] = ['tpl' => [$mailName => 'proof']];
        unset($_POST['module_mail'], $_POST['subject'], $_GET['module_mail']);

        $method = new ReflectionMethod($controller, 'submitTranslationsMails');
        $method->setAccessible(true);

        try {
            $this->expectException(PrestaShopException::class);
            $method->invoke($controller);
        } finally {
            unset($_POST['core_mail']);
        }
    }

    public static function invalidMailNameProvider(): array
    {
        return [
            'traversal, no pipe' => ['../../themes/mailtpl'],
            'traversal, pipe at first position' => ['|../../themes/mailtpl'],
        ];
    }

    private function setProperty(object $object, string $name, $value): void
    {
        $property = (new ReflectionClass($object))->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }
}
