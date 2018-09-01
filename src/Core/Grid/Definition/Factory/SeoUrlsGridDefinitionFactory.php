<?php



namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

final class SeoUrlsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'seo_urls';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Seo & urls', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        // TODO: Implement getColumns() method.
    }
}
