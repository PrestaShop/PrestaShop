| Questions     | Answers |
| ------------- | ------- |
| Branch?       | develop |
| Description?  | Modernizes the legacy `jquery.alerts` plugin (2008) used across the back office for deletion confirmations and other dialogs. The dialog keeps its public API (`jAlert` / `jConfirm` / `jPrompt` and the `$.alerts` properties) but is visually updated (rounded card, shadow, dark overlay, real `<button>` elements) and made accessible: `role="dialog"`, `aria-modal`, focus trap, Escape/Enter handling, focus restore. Unused GIF images are removed. |
| Type?         | improvement |
| Category?     | BO |
| BC breaks?    | No — dialog markup keeps the historical `#popup_*` ids, the API and callbacks are unchanged. |
| Fixed ticket? | - |
| How to test?  | In the back office, open a list with delete actions (e.g. Klienci → Obsługa klienta, check rows → delete) and trigger the confirmation dialog. Verify: modern styling, Tab/Shift+Tab stays inside the dialog, Escape cancels, Enter confirms, focus returns to the trigger element, jPrompt still works (e.g. any legacy prompt usage). |
| Possible impacts? | Back-office dialogs only; custom CSS targeting `#popup_*` ids keeps working. |

---

## PR about the problem

The `jquery.alerts` plugin bundled in `js/jquery/plugins/alerts/` is from 2008 and its rendering looks like PrestaShop 1.4: a gray 5px-bordered box with GIF background images (`title.gif`, `info.gif`, `important.gif`, `help.gif`) and raw `<input type="button">` elements. It is still the dialog used for e.g. "Delete selected items?" in many admin list pages.

## What this PR changes

- **Visual redesign** (`jquery.alerts.css`): modern rounded card with shadow, dark overlay (fixed position), primary/secondary buttons, styled prompt input, `prefers-reduced-motion` support.
- **Accessibility** (`jquery.alerts.js`): `role="dialog"`, `aria-modal`, `aria-labelledby`, focus trapped inside the dialog (Tab/Shift+Tab wrap), Escape cancels, Enter confirms, focus returns to the previously focused element on close.
- **Code cleanup**: `position: fixed` overlay (no manual height tracking), simpler centering, real `<button>` elements instead of `<input>`.
- **Removed unused images** (no longer referenced).
- **ESLint**: file now passes `js/.eslintrc.js` (legacy-style rules explicitly disabled in a header comment, consistent with the legacy `js/` directory).
- **No BC break**: public API (`jAlert`/`jConfirm`/`jPrompt`, `$.alerts.*` properties) and the `#popup_*` DOM ids are unchanged; `js/admin.js` and legacy controllers keep working as before.

Verified in a real browser: confirm (Tak/Nie), alert, prompt (default value, cancel → `null`, Enter in input), Escape/Enter, focus trap, and focus restore all behave correctly.
