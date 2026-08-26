# Sample output

`2026-08-18-summary.md` is this pipeline run over the real week of 18–25 August
2026 on `PrestaShop/PrestaShop`, kept as evidence for the spike in
[#42138](https://github.com/PrestaShop/PrestaShop/issues/42138).

## Read the provenance before reading the verdicts

Stage 1 (`collect.py`) and stage 3 (`render.py`) are the real scripts against
the real repository — the item list, the skip list and the layout are exactly
what a scheduled run produces.

Stage 2 was **not** run: no `ANTHROPIC_API_KEY` was available in the environment
where this branch was built. The verdicts come from `hand_verdicts.py` instead,
which applies `prompts/severity_system.md` and `prompts/pr_triage_system.md` by
hand:

- the 22 issue verdicts are individual judgements, one per issue
- the 72 PR verdicts apply the PR rubric mechanically to the collected metadata
  (labels, review decision, author association, idle days)

So this file demonstrates the shape and the usefulness of the output, and
exercising the rubric on real reports is what surfaced the `kind` field, the
narrowed attention list and the branch-check section. It is **not** a
measurement of how well the model classifies — that needs `classify.py` and
`calibrate.py eval` against a real key.

`hand_verdicts.py` is committed so those judgements can be audited rather than
asserted in prose. Once a key exists, delete it and regenerate with the real
`classify.py`.

## Regenerating

```bash
python collect.py --since 2026-08-18 --until 2026-08-25 --no-duplicates
python samples/hand_verdicts.py     # or: python classify.py, with a key
python render.py
cp out/summary.md samples/2026-08-18-summary.md
```

`--until` matters. Without it the window runs to today and collects a different
set, so the file would no longer be the week it claims to be.

One caveat on exactness: the original collection used a relative `7d` window and
caught 10 pull requests updated late on 25 August that an explicit
`2026-08-18..2026-08-25` range excludes. The 22 issues are identical either way.
