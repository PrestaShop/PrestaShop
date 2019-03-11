# Modern MJML theme

This theme is a trial to use mjml template, it should not be used in production as it requires
to have the mjml binary installed. Event though our MJML converter transformation is able to use a
client and the MJML api as a fallback this binds your PrestaShop to an external API which is bad
practice.

This theme was used to generate the modern theme and export it as a twig theme using a custom Symfony
command. You can use it as a base if you want to create a MJML based template but you will have to convert
it to a twig template (which are managed by core).

