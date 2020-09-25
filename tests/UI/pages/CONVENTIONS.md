## Naming conventions

### Selectors
Selectors are used in every page and are stored as attributes of the class. They should be named following this convention :

*nametype\* in `camelCase`.

For example, a button used to submit the main form in the order page should be named: `submitMainFormButton`.

### Methods
Methods are used in a POM class to execute some logic inherent to the page, to add a level of abstraction and to make tests themselves agnostic. This organization lets us have tests that don't reference anything page-level and just use lambda methods names (like `login()` or `submitForm()`).
They should be named following this convention:

\*action\*()

For example, the method allowing a user to login in the BO should be named: `login()`. Since it's belonging to the `Login` page, it's obvious that it targets the form in the login page. However, at the test level, we don't need to know what's happening exactly in the page to allow us to `login`. All the low-level logic is handled by the page object. 

### Pages
Pages names are the first hint you get when browsing pages lists, so it's really **important** that the name conveys the most information.

They should be named following this convention:

\*pagename\*

For example, the login page in the BO should be named `Login`.

It's *critical* that the page name allow us to find immediately what application page it refers to.

### Types
Each selector must belong to a certain type. The whole list (not exhaustive) is here :
- button
- link
- block
- image
- icon
- text
- modal
- *other HTML elements...*


- table
- table-header
- table-cell
- table-row
- *other table elements...*


- form
- input
- select
- radio
- checkbox
- *other forms inputs...*


### Coding conventions
Variables representing pages should always be named the same as the page they're referencing, but in **camelCase**. For example, your test script might look like this:
```javascript
const LoginPage = require('BO/login.js');

const loginPage = new LoginPage();

...

test('should login in the BO', () => {
  loginPage.setCredentials(EMAIL, PASSWD);
  loginPage.login();
});
```
