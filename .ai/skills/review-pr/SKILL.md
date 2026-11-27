---
name: review-pr
description: >
  Reviews a PrestaShop Core pull request against the .ai/ conventions: gathers the PR and its CI, picks the
  contexts with check-conventions.php, reads the final code, verifies every finding, then drafts and posts
  the review. Trigger: "review PR #N", "review this pull request", "code review of #N", "pre-review my PR".
allowed-tools: Read, Grep, Glob, Bash
needs: []
produces: "A review of the PR (verdict, conventions line, findings rated with .ai/REVIEW.md), posted on GitHub only once the user approves the draft."
---

# Review a PrestaShop Core pull request

Read [`.ai/REVIEW.md`](../../REVIEW.md) first: it rates and formats every finding. The conventions the
findings refer to are in [`.ai/CONTEXT.md`](../../CONTEXT.md) and the contexts step 2 selects. A PR on
`ps_apiresources` is reviewed with that module's `review-api-pr` skill instead.

## Inputs

Ask only for what is missing: the PR number (or URL), and whether to post the review or only report it.
Never post, approve or request changes without the user's go on the draft. Never approve your own PR.

## Steps

### 1. Gather before reading any code

```bash
gh pr view <N> --repo PrestaShop/PrestaShop --json title,body,author,baseRefName,headRefName,labels,files,reviews,mergeable,milestone
gh pr view <N> --repo PrestaShop/PrestaShop --comments
gh pr checks <N> --repo PrestaShop/PrestaShop
```

- Read the existing reviews and threads: confirm or contradict a point another reviewer made, with
  evidence; never restate it.
- Write the CI state down. A red job is blamed on the PR only after comparing it with recent runs of the
  same workflow on the base branch (`gh run list --workflow <id> --branch <base>`) and their test counts;
  a known flaky job is said to be one.
- A core PR paired with a `ps_apiresources` PR (see the `link-apiresources-fork` skill) is reviewed with
  its pair: read both.

### 2. Load the conventions the diff is judged on

```bash
git fetch <remote> <base> pull/<N>/head:pr-<N>
php .ai/bin/check-conventions.php <remote>/<base> --head pr-<N>
```

`<remote>` is the remote pointing to `PrestaShop/PrestaShop`, `<base>` the PR's own base branch (`9.2.x` for
a bug fix, `develop` otherwise).

- Read in full every file the script lists, before the diff: the root `CONTEXT.md`, each matched Domain or
  Component `CONTEXT.md` with the companions it links, the cross-cutting guides. They load on demand only.
- The skills a matched context lists are the expected pattern: a new grid, form or handler that departs from
  its `create-*` skill needs a reason.

### 3. Hygiene and coding standards

The same run reports them as `FAIL` and `CHECK` lines: a `FAIL` becomes a finding unless the PR description
explains it and the reason holds (`REVIEW.md` → explained deviations), a `CHECK` gets a look and becomes a
finding or is accepted with its reason. Then check by hand what no script decides:
lists kept sorted, noise commits to squash, the milestone (the next release of the base branch).

### 4. Read the final branch, not the patch

The patch carries reverted hunks and shifted lines; a convention is judged on the final files, where the
compliant code may sit in untouched lines.

```bash
git show pr-<N>:<path>
git diff <remote>/<base>...pr-<N> -- <path>
```

A large PR (about 30 files or 1500 lines and more) is split into coherent clusters (domain, wiring,
controllers and templates, front end, tests), each judged against the contexts that apply to its files.

### 5. Run it on the target branch

Replay the "How to test" of the PR on a shop of its base branch: at least its main path and every case a
finding relies on. Another branch is no substitute. Note what was run, on which branch, and what was not;
put the shop back as it was. A PR with no runtime effect (docs, `.ai/`, CI) says so instead.

### 6. Method

- **Every predicate** (filter, regex, allowlist, denylist, condition) is followed to its sink and run
  against hostile input rather than read. When it filters a dictionary, the key is an input as much as the
  value.
- **Baseline before blaming**: a failing test is the PR's only if it passes on the base branch, in the
  same environment, on a freshly provisioned test database.
- **Each convention of the loaded contexts**, file by file, rather than a read for general quality.
- **Backward compatibility** on public signatures, service ids, hook names and parameters, template
  variables, and the classes modules may extend.
- **Multistore**, whenever the diff writes configuration or scopes a query: read
  [`MULTISTORE.md`](../../MULTISTORE.md), save from one shop then from all shops, and read the stored rows.

### 7. Verify every finding before writing it down

Open the final code each finding is about, and the code it calls, and confirm it; check a claim about a
tool with the tool itself. Drop what does not survive. Keep, for each survivor: the file, its line in the
final branch, what breaks for which input, and its severity from `REVIEW.md`.

### 8. Draft, then post

Show the draft to the user first. The body, in English:

```markdown
Review <marker> <changes requested | approved with nits | no change requested | approved>

#### Checks

**.ai conventions:** <🟢 | 🟢, n deviation(s) explained | 🔴 n departures, see the findings>

| Source | Result |
|---|---|
| Contexts read | <the .ai/ files> |
| check-conventions.php | <🟢 clean | 🟢 n line(s) accepted: <why> | 🔴 n FAIL, see the findings> |
| Deviations | <only when the PR explains some: 🟢 n explained, accepted> |

**Tested on:** <base branch @ sha, single shop or multistore>
**Run:** <what was replayed, test suites and counts>
**Not run:** <what, and why>

#### Findings

- <one item per finding, as REVIEW.md formats it>

#### Open questions

- <if any>
```

- The first line carries the marker of the most severe finding; the review event follows the table of
  `REVIEW.md`. Leave out an empty section.
- Each finding is an inline comment anchored on a line the diff shows (GitHub rejects the whole review
  when one comment is not), with one list item in the body; a finding outside the diff goes in the body
  only.
- Reply to existing threads first, one short reply each (`fixed in <sha>`, or why not), then post the
  review:

  ```bash
  gh api repos/PrestaShop/PrestaShop/pulls/<N>/reviews --method POST --input review.json
  ```

- Every reference to another repository is written `owner/repo#N`.
- Labels: on approve, remove `Waiting for author` only; `Waiting for QA` is set by kanbanbot, never by
  hand. A review asking for a rebase also sets `Waiting for rebase`.
- Read the review back (`gh api repos/PrestaShop/PrestaShop/pulls/<N>/reviews --paginate`) and check that
  its inline comments landed.

## Exit checklist

For the one running the skill, never published:

- [ ] Every finding verified against the final branch, rated and written as `REVIEW.md` says.
- [ ] The contexts listed by `check-conventions.php` actually read.
- [ ] The PR run on its base branch, and the body says what was run and what was not.
- [ ] The event matches the most severe finding; no approval without the user's go.
- [ ] Thread replies before the review; inline comments on diff lines only; review read back.
