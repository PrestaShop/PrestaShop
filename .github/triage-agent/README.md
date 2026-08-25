# Weekly triage agent

Ranks the past week's issues and pull requests so the sheriff reads a sorted
list instead of the whole tracker. Runs from
[`cron_weekly_triage_agent.yml`](../workflows/cron_weekly_triage_agent.yml)
every Monday.

**It proposes; it never decides.** The workflow holds no write scope on GitHub
— no label is applied, no board field is set, no comment is posted. The output
is a job summary, a Slack message, and a JSON artifact.

Built for the spike in
[#42138](https://github.com/PrestaShop/PrestaShop/issues/42138).

## The pipeline

Four stages, each writing a file into `out/`. They are separate on purpose: the
prompt is the part that needs iterating, and re-running stage 2 against a frozen
`week.json` costs nothing and changes nothing upstream.

| Stage | Command | Writes | Calls the API |
|---|---|---|---|
| Collect | `python collect.py --since 7d` | `out/week.json` | no |
| Classify | `python classify.py` | `out/classified.json` | yes, one request per item |
| Render | `python render.py` | `out/summary.md`, `out/slack.json`, `out/board.json` | no |
| Publish | `python publish.py --publish` | Slack | no |

```bash
pip install -r requirements.txt
export ANTHROPIC_API_KEY=...          # stage 2 only
python collect.py --since 7d
python classify.py --limit 5          # eyeball a few before paying for the lot
python classify.py
python render.py
python publish.py                     # dry run; add --publish to actually post
```

`collect.py` needs an authenticated `gh`; every other GitHub read goes through
it.

## What the model is asked

Two rubrics, in `prompts/`:

- [`severity_system.md`](prompts/severity_system.md) — the project's [official
  severity classification](https://build.prestashop-project.org/news/2019/severity-classification/)
  reproduced verbatim, then the project-specific reading of its
  "percentage of users" thresholds, the clauses that override them (security,
  data loss, broken E2E, law compliance, money), and how to judge a workaround.
  This file is the substance of the spike.
- [`pr_triage_system.md`](prompts/pr_triage_system.md) — severity does not apply
  to a PR, so this one ranks *sheriff attention needed* and, above all, who the
  PR is actually waiting on.

`prompts/severity_examples.md` holds 24 worked examples (6 per level) mined from
closed issues the maintainers labelled themselves. It is generated, not written
by hand — see below — and `classify.py` appends it to the rubric inside the same
cached block.

Output shapes live in [`prompts/schemas.json`](prompts/schemas.json) and are
enforced as structured outputs, so nothing downstream parses free text.

## Iterating on a prompt

```bash
python collect.py --since 7d      # once
vim prompts/severity_system.md
python classify.py --limit 5      # cheap loop
python render.py && less out/summary.md
```

Two things to watch when editing:

- **Keep per-item content out of the system prompt.** Anything that varies
  between items belongs in the user message. Put it in the rubric and the cache
  is invalidated on every single call — `classify.py` warns when a multi-item
  run reports zero cache reads.
- **Order matters for caching.** The stable rubric comes first, the mined
  examples after it, and the single `cache_control` breakpoint sits at the end
  of both.

## Calibration

`calibrate.py` answers "how often does the rubric agree with the maintainers?"
using the ~2 300 closed issues that carry exactly one severity label. There is
no fine-tuning: the corpus is split once, deterministically, into a pool that
few-shot examples are mined from and a held-out set that is scored against.

```bash
python calibrate.py fetch     # ~2 300 issues, sharded by year, several minutes
python calibrate.py mine      # regenerates prompts/severity_examples.md
python calibrate.py eval      # Batch API, 50% cost, writes out/eval.md
```

Run `mine` again after changing the split or the corpus; the examples file is
committed so a normal run does not need the corpus.

Read the confusion matrix, not the headline percentage. The corpus is heavily
imbalanced (68 Critical against 1 378 Minor), so plain accuracy looks good
while saying nothing. The numbers that matter are **Critical recall** — a
Critical proposed as Minor is an issue the sheriff never sees ranked — and the
**off-by-one rate**, since a Major proposed as Critical costs one glance and
nothing more.

The ground truth is five years of labels from many different people, so this
measures agreement with past practice rather than correctness.

## Cost

Roughly **$2–3 per week** at the observed volume (~20 issues and ~80 PRs), which
is a little over $100 a year. The rubric plus examples is ~7 500 tokens and is
identical for every item in a run, so caching does most of the work: the first
item pays for it and the rest read it back at a tenth of the price.

`render.py` prints the real figure from the run's own `usage` at the bottom of
every summary. Trust that over this paragraph.

## Known limits

- **The weekly window cannot surface a long-stalled PR.** Selecting on
  `updated:>=D-7` means a PR nobody has touched for eight months is invisible
  precisely because nobody touched it. This list is for what moved, not for what
  is rotting; finding the latter needs a separate query.
- **Duplicate detection is only as good as the candidate search.** The model may
  only pick from a keyword-search shortlist supplied by `collect.py`, which
  makes it unable to invent an issue number but also unable to find a duplicate
  that shares no title keywords.
- **PR classification reads metadata, not diffs.** It can say a PR has been
  waiting on a reviewer for a month; it cannot say whether the change is any
  good.
- **Severity is proposed from the report, not from a reproduction.** An
  overstated report and a genuine Critical read alike on paper. That is what
  `TBR` and the confidence field are for.
