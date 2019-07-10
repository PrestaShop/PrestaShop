## Naming conventions

###Selectors
Selectors are used in every page and are stored as attributes of the class. They should be named following this convention :

\*page\*\_\*name\*\_\*type\*

For example, a button used to submit the main form in the order page should be named: `order_submitMainForm_button`.

###Methods
Methods are used in a POM class to execute some logic inherent to the page, to add a level of abstraction and to make tests themselves agnostic. This organization lets us have tests that don't reference anything page-level and just use lambda methods names (like `login()` or `submitForm()`).
They should be named following this convention:

\*action\*()

For example, the method allowing a user to login in the BO should be named: `login()`. Since it's belonging to the `BO_login` page, it's obvious that it targets the form in the login page. However, at the test level, we don't need to know what's happening exactly in the page to allow us to `login`. All the low-level logic is handled by the page object. 

###Pages
Pages names are the first hint you get when browsing pages lists, so it's really **important** that the name conveys the most information.

They should be named following this convention:

\*office\*\_\*pagename\*

For example, the order page in the BO should be named `BO_login`.

It's *critical* that the page name allow us to find immediately what application page it refers to.
