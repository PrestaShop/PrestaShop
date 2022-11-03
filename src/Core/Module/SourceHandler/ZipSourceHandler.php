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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorException;
use Symfony\Contracts\Translation\TranslatorInterface;
use ZipArchive;

class ZipSourceHandler implements SourceHandlerInterface
{
    private const AUTHORIZED_MIME = [
        'application/zip',
        'application/x-gzip',
        'application/gzip',
        'application/x-gtar',
        'application/x-tgz',
    ];

    private const MODULE_REGEX = '/^(.*)\/\1\.php$/i'; // module_name/module_name.php

    /** @var string */
    protected $modulePath;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(string $modulePath, TranslatorInterface $translator)
    {
        $this->modulePath = rtrim($modulePath, '/') . '/';
        $this->translator = $translator;
    }

    public function canHandle($source): bool
    {
        return is_file($source) && in_array(mime_content_type($source), self::AUTHORIZED_MIME);
    }

    public function getModuleName($source): string
    {
        $zip = new ZipArchive();
        if ($zip->open($source) === true) {
            for ($i = 0; $i < $zip->numFiles; ++$i) {
                if (preg_match(self::MODULE_REGEX, $zip->getNameIndex($i), $matches)) {
                    $zip->close();

                    return $matches[1];
                }
            }
            $zip->close();
        }

        throw new ModuleErrorException(
            $this->translator->trans(
                'This file does not seem to be a valid module zip',
                [],
                'Admin.Modules.Notification'
            )
        );
    }

    public function handle(string $source): void
    {
        $zip = new ZipArchive();
        if ($zip->open($source) !== true || !$zip->extractTo($this->modulePath) || !$zip->close()) {
            throw new ModuleErrorException(
                $this->translator->trans(
                    'Cannot extract module in %path%. %error%',
                    [
                        '%path%' => $this->modulePath,
                        '%error%' => @$zip->getStatusString() ?: '', // Since php 8.0 getStatusString cannot return false nor a warning
                    ],
                    'Admin.Modules.Notification'
                )
            );
        }
    }
}
