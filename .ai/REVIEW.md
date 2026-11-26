# PrestaShop Core: review checklist

How a pull request on the Core is rated and commented, whoever reviews it: a person, an AI assistant
following the `review-pr` skill, or an automated reviewer given this file as a guideline. The conventions
live in [`CONTEXT.md`](CONTEXT.md) and the domain and component contexts it indexes; this file does not
repeat them. It says where the procedure is, then how to rate and write a finding.

## How to review

The procedure (gathering the PR, choosing the contexts with `check-conventions.php`, reading the final
code, verifying each finding, posting) is the [`review-pr` skill](skills/review-pr/SKILL.md). A PR on
`ps_apiresources` follows the module's own `.ai/REVIEW.md` and `review-api-pr` skill.

## Severity and comment format

| Severity | Examples | Review event |
|----------|----------|--------------|
| 🔴 Blocker | a backward compatibility break outside a major version ([`CONTEXT.md`](CONTEXT.md) → "Branching & Versioning", ADR 0017), including a new constructor or signature change on a class that is not `final` and that modules may extend; a wrong target branch; a new behaviour or a bug fix without a test ([`CONTEXT.md`](CONTEXT.md) → "Testing"); business logic in a controller; a new ObjectModel or `Db::getInstance()` in new code ([`CONTEXT.md`](CONTEXT.md) → "Coding standards"); lock-file changes unrelated to the PR ([`CONTEXT.md`](CONTEXT.md) → "PR hygiene"); a correctness or security defect the PR introduces | Request changes |
| 🟠 Major | a departure from a convention a `.ai/` file states; a missing test case for a path the PR changes; production code shaped for a test; a `FAIL` line of `check-conventions.php` | Request changes |
| 🟡 Minor | naming, wording, taste; optional, and only when few | Approve, with the points listed |
| 💡 Follow-up | a defect found on the way that the PR did not introduce, an idea out of its scope | none: never blocks |

- The level says how much the finding matters; it is never raised to match the event. A review asking for
  a Blocker or a Major requests changes; a review left with Minor points only approves, so the author is
  not held up; follow-ups never change the event.
- A departure the PR description explains (a generated file, a false positive of `check-conventions.php`,
  a convention the fix cannot follow without breaking compatibility) is judged on its reason: accepted, it
  is no finding and counts as an explained deviation; rejected, it keeps its level.
- An automated reviewer only comments: the event is the human reviewer's.
- One finding per comment: its marker and level in bold first (`🟠 **Major:**`), then what breaks and for
  which input, the rule (the `.ai/` file and section), the evidence (file and line in the final branch, a
  payload, an output), the fix. Nothing about what is already correct.
- A finding no `.ai/` file states says so: the rule is added to the right `CONTEXT.md` in the same PR
  ([`STRUCTURE.md`](STRUCTURE.md) → "Maintenance").
