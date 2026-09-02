<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class MetaFormDataHandler is responsible to handle creation and update logic for meta form.
 */
final class MetaFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws MetaException
     */
    public function create(array $data)
    {
        $addMetaCommand = (new AddMetaCommand($data['page_name']))
            ->setLocalisedPageTitle($data['page_title'])
            ->setLocalisedMetaDescription($data['meta_description'])
            ->setLocalisedRewriteUrls($data['url_rewrite'])
        ;

        /** @var MetaId $metaId */
        $metaId = $this->commandBus->handle($addMetaCommand);

        return $metaId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws MetaException
     */
    public function update($metaId, array $data)
    {
        $editMetaCommand = (new EditMetaCommand((int) $metaId))
            ->setPageName($data['page_name'])
            ->setLocalisedPageTitles($data['page_title'])
            ->setLocalisedMetaDescriptions($data['meta_description'])
            ->setLocalisedRewriteUrls($data['url_rewrite'])
        ;

        $this->commandBus->handle($editMetaCommand);
    }
}
