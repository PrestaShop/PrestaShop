<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    protected function configure()
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
