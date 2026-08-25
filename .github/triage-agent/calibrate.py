#!/usr/bin/env python3
"""Offline evaluation - how often does the rubric agree with the maintainers?

This is what turns "we could train the agent on 5 years of issues" into
something measurable. There is no fine-tuning involved: the closed, labelled
issues serve two purposes, as a pool to mine few-shot examples from and as a
held-out set to score against.

Three subcommands:

    fetch    pull closed issues carrying exactly one severity label
    mine     pick few-shot examples from the pool half
    eval     classify the held-out half through the Batch API and score it

Not part of the weekly workflow. Run it once to produce the numbers that decide
whether the weekly output is worth reading.
"""

from __future__ import annotations

import argparse
import collections
import json
import random
import re
import subprocess
import sys
import time
from datetime import datetime, timezone
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))

SEVERITIES = ["Critical", "Major", "Minor", "Trivial"]
SEVERITY_RANK = {level: index for index, level in enumerate(SEVERITIES)}

REPO = "PrestaShop/PrestaShop"

# The eval set is stratified rather than random. A random 400 out of 2 732 would
# contain roughly 10 Criticals, which is far too few to say anything about the
# class that actually matters.
EVAL_PER_CLASS = 100
FEWSHOT_PER_CLASS = 6

# Deterministic split, so re-running the evaluation after a prompt change
# compares like with like instead of reshuffling the ground under it.
SPLIT_SEED = 42


def run_gh(args: list[str], retries: int = 5) -> str:
    """Call gh, backing off when the search API's secondary rate limit trips.

    The search endpoint is throttled far more aggressively than the core API,
    and a corpus-wide fetch is exactly the shape that trips it. Backing off is
    the difference between a run that finishes and a run that dies two thirds
    of the way through.
    """
    for attempt in range(retries):
        result = subprocess.run(
            ["gh", *args], capture_output=True, text=True, check=False
        )
        if result.returncode == 0:
            return result.stdout

        stderr = result.stderr.strip()
        rate_limited = "rate limit" in stderr.lower() or "403" in stderr
        if not rate_limited or attempt == retries - 1:
            raise RuntimeError(f"gh failed: {stderr}")

        wait = 30 * (attempt + 1)
        print(f"    rate limited, waiting {wait}s...", file=sys.stderr)
        time.sleep(wait)

    raise RuntimeError("unreachable")


def fetch(args: argparse.Namespace) -> int:
    """Pull closed issues that carry exactly one severity label.

    Sharded by year on purpose: GitHub's search API returns at most 1000
    results per query, and `Minor` alone exceeds that over five years. Without
    the shard the corpus would come back silently truncated and the class
    balance would be wrong.
    """
    corpus: list[dict] = []
    start_year = int(args.since[:4])
    end_year = datetime.now(timezone.utc).year

    for severity in SEVERITIES:
        others = [s for s in SEVERITIES if s != severity]
        exclusions = " ".join(f"-label:{other}" for other in others)
        found = 0

        for year in range(start_year, end_year + 1):
            query = (
                f"repo:{REPO} is:issue is:closed label:{severity} {exclusions} "
                f"created:{year}-01-01..{year}-12-31"
            )
            page = 1
            while page <= 10:
                raw = run_gh([
                    "api", "-X", "GET", "search/issues",
                    "-f", f"q={query}",
                    "-F", "per_page=100",
                    "-F", f"page={page}",
                    "--jq", ".items[] | {number, title, body, labels: [.labels[].name]}",
                ])
                lines = [line for line in raw.strip().splitlines() if line]
                if not lines:
                    break
                for line in lines:
                    item = json.loads(line)
                    item["truth"] = severity
                    item["year"] = year
                    corpus.append(item)
                    found += 1
                if len(lines) < 100:
                    break
                page += 1
                time.sleep(3)
            time.sleep(3)

        print(f"  {found} {severity}", file=sys.stderr)

    args.out.parent.mkdir(parents=True, exist_ok=True)
    args.out.write_text(json.dumps(corpus, indent=2, ensure_ascii=False))
    print(f"\nWrote {args.out}: {len(corpus)} issues", file=sys.stderr)
    return 0


def split_corpus(corpus: list[dict]) -> tuple[list[dict], list[dict]]:
    """Stratified, deterministic split into a few-shot pool and an eval set."""
    by_class: dict[str, list[dict]] = collections.defaultdict(list)
    for item in corpus:
        by_class[item["truth"]].append(item)

    rng = random.Random(SPLIT_SEED)
    pool: list[dict] = []
    evaluation: list[dict] = []

    for severity in SEVERITIES:
        items = sorted(by_class[severity], key=lambda i: i["number"])
        rng.shuffle(items)
        take = min(EVAL_PER_CLASS, len(items) // 2)
        evaluation.extend(items[:take])
        pool.extend(items[take:])

    return pool, evaluation


# The issue template's fixed preamble is the same on every report and carries no
# signal. Left in, it would be roughly a third of every mined example.
BOILERPLATE = re.compile(
    r"###?\s*Prerequisites.*?(?=###?\s*(?:Describe|Expected|Steps|Additional)|$)",
    re.IGNORECASE | re.DOTALL,
)
CHECKBOX = re.compile(r"-\s*\[[xX ]\]\s*[^\n]*", re.MULTILINE)
HTML_COMMENT = re.compile(r"<!--.*?-->", re.DOTALL)
IMAGE_MD = re.compile(r"!\[[^\]]*\]\([^)]*\)")


def strip_boilerplate(body: str) -> str:
    """Drop the parts of an issue body that are identical on every report."""
    body = HTML_COMMENT.sub(" ", body or "")
    body = BOILERPLATE.sub(" ", body)
    body = CHECKBOX.sub(" ", body)
    body = IMAGE_MD.sub("[screenshot]", body)
    return " ".join(body.split())


def mine(args: argparse.Namespace) -> int:
    """Pick few-shot examples from the pool half only.

    Drawing examples from the eval half would leak the answers and make the
    accuracy number meaningless.
    """
    corpus = json.loads(args.corpus.read_text())
    pool, evaluation = split_corpus(corpus)

    rng = random.Random(SPLIT_SEED)
    chosen: list[dict] = []

    for severity in SEVERITIES:
        candidates = [item for item in pool if item["truth"] == severity]
        # Prefer reports with enough text to be worth showing as an example.
        candidates = [
            c for c in candidates
            if len(strip_boilerplate(c.get("body") or "")) > 200
        ]
        rng.shuffle(candidates)
        chosen.extend(candidates[:FEWSHOT_PER_CLASS])

    lines = [
        "# Worked examples",
        "",
        "Real issues from this repository, with the severity the maintainers",
        "actually applied. Use them to calibrate the boundaries - especially",
        "Critical vs Major, where the workaround question decides it.",
        "",
    ]
    for item in chosen:
        body = strip_boilerplate(item.get("body") or "")[:600]
        lines += [
            f"## {item['truth']} — {item['title']}",
            "",
            body or "_(no description)_",
            "",
        ]

    args.out.parent.mkdir(parents=True, exist_ok=True)
    args.out.write_text("\n".join(lines))
    print(
        f"Wrote {args.out}: {len(chosen)} examples mined from a pool of "
        f"{len(pool)} (eval set held out: {len(evaluation)})",
        file=sys.stderr,
    )
    return 0


def eval_set(args: argparse.Namespace) -> int:
    """Score the rubric against the held-out set via the Batch API."""
    # Imported here rather than at module scope so that `fetch` and `mine`,
    # which need nothing but the gh CLI, run without the SDK installed.
    import anthropic

    import classify

    corpus = json.loads(args.corpus.read_text())
    _, evaluation = split_corpus(corpus)
    if args.limit:
        evaluation = evaluation[: args.limit]

    schemas = classify.load_schemas()
    system = classify.system_blocks("severity_system.md")
    client = anthropic.Anthropic()

    requests = []
    for item in evaluation:
        rendered = classify.render_issue({
            "number": item["number"],
            "title": item["title"],
            "url": f"https://github.com/{REPO}/issues/{item['number']}",
            "body": classify.truncate_body(item.get("body")),
            "author": "unknown",
            "author_association": "NONE",
            "labels": [],
            "milestone": None,
            "created_at": "unknown",
            "updated_at": "unknown",
            "days_since_update": 0,
            "is_new_this_week": False,
            "comment_count": 0,
            "reactions": 0,
            "recent_comments": [],
            "duplicate_candidates_pool": [],
        })
        requests.append({
            "custom_id": f"issue-{item['number']}",
            "params": {
                "model": classify.MODEL,
                "max_tokens": classify.MAX_TOKENS,
                "system": system,
                "thinking": {"type": "adaptive"},
                "output_config": {"effort": classify.EFFORT, "format": schemas["issue"]},
                "messages": [{"role": "user", "content": rendered}],
            },
        })

    print(f"Submitting {len(requests)} requests as a batch...", file=sys.stderr)
    batch = client.messages.batches.create(requests=requests)
    print(f"  batch id: {batch.id}", file=sys.stderr)

    while True:
        batch = client.messages.batches.retrieve(batch.id)
        if batch.processing_status == "ended":
            break
        print(f"  status: {batch.processing_status}, waiting...", file=sys.stderr)
        time.sleep(30)

    # Results arrive in arbitrary order; key by custom_id, never by position.
    predictions: dict[int, str] = {}
    errors = 0
    for result in client.messages.batches.results(batch.id):
        number = int(result.custom_id.split("-", 1)[1])
        if result.result.type != "succeeded":
            errors += 1
            continue
        try:
            verdict = classify.extract_json(result.result.message)
            predictions[number] = verdict["severity"]
        except (ValueError, KeyError, RuntimeError):
            errors += 1

    report = score(evaluation, predictions, errors)
    args.out.parent.mkdir(parents=True, exist_ok=True)
    args.out.write_text(report)
    print(f"\nWrote {args.out}", file=sys.stderr)
    print(report)
    return 0


def score(evaluation: list[dict], predictions: dict[int, str], errors: int) -> str:
    matrix: dict[tuple[str, str], int] = collections.Counter()
    scored = 0

    for item in evaluation:
        predicted = predictions.get(item["number"])
        if predicted is None:
            continue
        matrix[(item["truth"], predicted)] += 1
        scored += 1

    if not scored:
        return "# Calibration\n\nNo items scored.\n"

    exact = sum(matrix[(level, level)] for level in SEVERITIES)
    off_by_one = sum(
        count
        for (truth, predicted), count in matrix.items()
        if abs(SEVERITY_RANK[truth] - SEVERITY_RANK[predicted]) == 1
    )

    lines = [
        "# Calibration against maintainer labels",
        "",
        f"Held-out set: **{scored}** closed issues, each carrying exactly one "
        "severity label applied by a maintainer. Few-shot examples were mined "
        "from a disjoint pool, so none of these were shown to the model.",
        "",
        f"- Exact agreement: **{exact}/{scored}** ({exact / scored:.1%})",
        f"- Off by one level: {off_by_one}/{scored} ({off_by_one / scored:.1%})",
        f"- Off by two or more: {scored - exact - off_by_one}/{scored} "
        f"({(scored - exact - off_by_one) / scored:.1%})",
    ]
    if errors:
        lines.append(f"- Failed to classify: {errors}")
    lines.append("")

    lines += [
        "## Confusion matrix",
        "",
        "Rows are what the maintainers labelled, columns are what the agent "
        "proposed.",
        "",
        "| maintainer \\ agent | " + " | ".join(SEVERITIES) + " | recall |",
        "|---|" + "---|" * (len(SEVERITIES) + 1),
    ]
    for truth in SEVERITIES:
        row_total = sum(matrix[(truth, p)] for p in SEVERITIES)
        cells = [str(matrix[(truth, p)]) for p in SEVERITIES]
        recall = matrix[(truth, truth)] / row_total if row_total else 0
        lines.append(f"| **{truth}** | " + " | ".join(cells) + f" | {recall:.0%} |")

    lines += ["", "## Per-class precision", ""]
    lines += ["| level | precision | proposed n |", "|---|---|---|"]
    for level in SEVERITIES:
        column_total = sum(matrix[(t, level)] for t in SEVERITIES)
        precision = matrix[(level, level)] / column_total if column_total else 0
        lines.append(f"| {level} | {precision:.0%} | {column_total} |")

    critical_row = sum(matrix[("Critical", p)] for p in SEVERITIES)
    critical_recall = matrix[("Critical", "Critical")] / critical_row if critical_row else 0
    lines += [
        "",
        "## Reading this",
        "",
        f"**Critical recall is {critical_recall:.0%}** — of the issues maintainers "
        "called Critical, that share was also proposed Critical by the agent. "
        "This is the number that decides whether the top of the weekly list can "
        "be trusted; a miss here is an issue the sheriff never sees ranked.",
        "",
        "Exact agreement understates usefulness and off-by-two overstates harm: "
        "a Major proposed as Critical costs one glance, a Critical proposed as "
        "Minor is the failure that matters. Weigh the matrix, not the headline "
        "percentage.",
        "",
        "The maintainer labels are themselves inconsistent across five years and "
        "many people, so this measures agreement with past practice, not truth.",
        "",
    ]

    return "\n".join(lines)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    sub = parser.add_subparsers(dest="command", required=True)

    p_fetch = sub.add_parser("fetch", help="Pull the labelled historical corpus.")
    p_fetch.add_argument("--since", default="2021-01-01")
    p_fetch.add_argument("--out", type=Path, default=Path("out/corpus.json"))
    p_fetch.set_defaults(func=fetch)

    p_mine = sub.add_parser("mine", help="Mine few-shot examples from the pool half.")
    p_mine.add_argument("--corpus", type=Path, default=Path("out/corpus.json"))
    p_mine.add_argument("--out", type=Path, default=Path("prompts/severity_examples.md"))
    p_mine.set_defaults(func=mine)

    p_eval = sub.add_parser("eval", help="Score the rubric on the held-out set.")
    p_eval.add_argument("--corpus", type=Path, default=Path("out/corpus.json"))
    p_eval.add_argument("--out", type=Path, default=Path("out/eval.md"))
    p_eval.add_argument("--limit", type=int, default=0)
    p_eval.set_defaults(func=eval_set)

    args = parser.parse_args()
    return args.func(args)


if __name__ == "__main__":
    sys.exit(main())
