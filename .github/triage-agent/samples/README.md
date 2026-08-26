# Sample output

`2026-08-18-summary.md` is `render.py` run over the real week of 18–25 August
2026 on `PrestaShop/PrestaShop`, kept as evidence for the spike in
[#42138](https://github.com/PrestaShop/PrestaShop/issues/42138).

**Read the provenance before reading the verdicts.** Stage 1 (`collect.py`) and
stage 3 (`render.py`) are the real scripts against the real repository — the
item list, the skip list and the layout are exactly what a scheduled run
produces.

Stage 2 was **not** run: no `ANTHROPIC_API_KEY` was available in the
environment where this branch was built. The severity and attention verdicts
were instead produced by applying `prompts/severity_system.md` and
`prompts/pr_triage_system.md` by hand:

- the 22 issue verdicts are individual judgements, one per issue
- the 82 PR verdicts come from applying the PR rubric mechanically to the
  collected metadata (labels, review decision, author association, idle days)

So this file demonstrates the shape and the usefulness of the output, and it
exercised the rubric enough to find two real problems — see the spike report on
the issue. It is **not** a measurement of how well the model classifies. That
needs `classify.py` and `calibrate.py eval` against a real key.

This file was rendered before `looks_like_regression` was added to the schema,
so it carries no regression markers. The layout is otherwise current.

Regenerate with:

```bash
python collect.py --since 2026-08-18
python classify.py
python render.py
```
