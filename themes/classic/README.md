# PrestaShop Starter Theme

**CHANGE OF WORKFLOW**

We realized it was not such a good idea to have the StarterTheme as a separate project from PrestaShop so we've merged the StarterTheme back into PrestaShop and are trying a different workflow.

# New Workflow

1. development happens on the `feat/starter-theme` branch of https://github.com/djfm/PrestaShop, so make your PRs there
2. the `feat/starter-theme` branch of https://github.com/djfm/PrestaShop will be rebased on the `develop` branch of `PrestaShop/PrestaShop` from time to time, meaning `push -f`'s, so be sure to pull with rebase so that things go smoothly
3. only cleaned up versions of `djfm/feat/starter-theme` will be pushed to `PrestaShop/PrestaShop/feat-starter-theme`
