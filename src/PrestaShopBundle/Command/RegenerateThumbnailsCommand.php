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

namespace PrestaShopBundle\Command;

use Exception;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand as RegenerateThumbnailsCommandBus;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ImageDomain;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ValueError;

/**
 * Console command to regenerate thumbnails
 */
#[AsCommand(
    name: 'prestashop:thumbnails:regenerate',
    description: 'Regenerate thumbnails')
]
class RegenerateThumbnailsCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'image',
                InputArgument::REQUIRED,
                sprintf('Allowed images (%s)', implode(', ', ImageDomain::getAllowedValues()))
            )
            ->addArgument('image-type', InputArgument::OPTIONAL, 'Image format ID (0 for all)', null)
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'Erase previous images before regenerating');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $image = $input->getArgument('image');
        $imageTypeId = $input->getArgument('image-type');
        $erase = (bool) $input->getOption('delete');

        try {
            $image = ImageDomain::from($image);
        } catch (ValueError) {
            $io->error(
                sprintf(
                    'Unknown image type "%s". Allowed types are: %s',
                    $image,
                    implode(', ', ImageDomain::getAllowedValues())
                )
            );

            return self::FAILURE;
        }

        $imageTypeValue = 0;

        if (null !== $imageTypeId) {
            if (!is_numeric($imageTypeId)) {
                $io->error(sprintf('Image format ID "%s" is invalid. It must be a numeric value.', $imageTypeId));

                return self::FAILURE;
            }

            $imageTypeId = (int) $imageTypeId;

            if (0 === $imageTypeId) {
                $imageTypeValue = 0;
            } else {
                try {
                    $imageTypeValueObject = new ImageTypeId($imageTypeId);
                    $imageTypeValue = $imageTypeValueObject->getValue();
                } catch (ImageTypeException $e) {
                    $io->error($e->getMessage());

                    return self::FAILURE;
                }
            }
        }

        try {
            $this->commandBus->handle(new RegenerateThumbnailsCommandBus($image->value, $imageTypeValue, $erase));
            $io->info('The thumbnails were successfully regenerated.');
        } catch (Exception $e) {
            $io->error('Unable to regenerate thumbnails : ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
