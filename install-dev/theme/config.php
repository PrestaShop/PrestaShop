<?php

$documentationLink = 'https://devdocs.prestashop.com/';
$blogLink = 'https://build.prestashop.com/';

return [
    'links' => [
        'documentation' => $documentationLink,
    ],
    'header.links' => [
        $documentationLink => $this->translator->trans('Documentation', array(), 'Install'),
        $blogLink => $this->translator->trans('Blog', array(), 'Install'),
    ],
    'footer.links' => [
        'http://prestashop-project.org/' => 'PrestaShop Project',
        $documentationLink => $this->translator->trans('Documentation', array(), 'Install'),
    ],
];
