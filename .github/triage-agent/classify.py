#!/usr/bin/env python3
"""Stage 2 - ask Claude for a severity proposal on each item of the week.

One request per item, with a structured-output schema so the result needs no
parsing heuristics. The system prompt carries the whole classification rubric
and is byte-stable across the run, so it is marked cacheable: the first item
pays for it, every later item reads it from cache.

This stage never writes to GitHub. Its only output is a JSON file.
"""

from __future__ import annotations

import argparse
import json
import os
import sys
import time
from pathlib import Path

try:
    import anthropic
except ImportError:  # pragma: no cover - dependency guidance
    sys.exit(
        "The 'anthropic' package is missing. Install it with:\n"
        "    pip install -r requirements.txt"
    )

HERE = Path(__file__).parent
PROMPTS = HERE / "prompts"

MODEL = "claude-opus-5"

# A classification verdict is a handful of fields. The ceiling only needs to
# leave room for adaptive thinking plus the JSON object.
MAX_TOKENS = 4000

# 'medium' effort suits a rubric-application task: the criteria are given, the
# work is matching a report against them rather than open-ended reasoning.
EFFORT = "medium"


def load_schemas() -> dict:
    return json.loads((PROMPTS / "schemas.json").read_text())


def truncate_body(text: str | None, limit: int = 6000) -> str:
    """Same body ceiling the collector applies, for callers that bypass it."""
    if not text:
        return ""
    text = text.strip()
    if len(text) <= limit:
        return text
    return text[:limit] + f"\n\n[... truncated, {len(text) - limit} more characters]"


def system_blocks(prompt_name: str) -> list[dict]:
    """Build the system prompt as a single cacheable block.

    The rubric is identical for every item in the run, so one ephemeral
    breakpoint at the end of it turns ~N full-price prompt reads into one.
    Everything that varies per item lives in the user message, after this
    breakpoint - putting any of it here would invalidate the cache on every
    single call.

    When `calibrate.py mine` has produced worked examples for this prompt they
    are appended into the same block: they are just as stable as the rubric, so
    they belong on the cached side of the breakpoint.
    """
    text = (PROMPTS / prompt_name).read_text()

    examples = PROMPTS / prompt_name.replace("_system.md", "_examples.md")
    if examples.exists():
        text += "\n\n" + examples.read_text()

    return [
        {
            "type": "text",
            "text": text,
            "cache_control": {"type": "ephemeral", "ttl": "1h"},
        }
    ]


def render_issue(issue: dict) -> str:
    """Lay out one issue for the model, in a stable field order."""
    lines = [
        f"# Issue #{issue['number']}: {issue['title']}",
        "",
        f"- URL: {issue['url']}",
        f"- Opened by: {issue['author']} ({issue['author_association']})",
        f"- Created: {issue['created_at']}",
        f"- Last updated: {issue['updated_at']} ({issue['days_since_update']} days ago)",
        f"- New this week: {'yes' if issue['is_new_this_week'] else 'no'}",
        f"- Existing labels: {', '.join(issue['labels']) or 'none'}",
        f"- Milestone: {issue.get('milestone') or 'none'}",
        f"- Comments: {issue['comment_count']}",
        f"- Reactions: {issue.get('reactions', 0)}",
        "",
        "## Body",
        "",
        issue["body"] or "_(empty)_",
    ]

    if issue.get("recent_comments"):
        lines += ["", "## Most recent comments", ""]
        for comment in issue["recent_comments"]:
            lines += [
                f"### {comment['author']} ({comment['association']}) on {comment['created_at']}",
                "",
                comment["body"] or "_(empty)_",
                "",
            ]

    pool = issue.get("duplicate_candidates_pool") or []
    lines += ["", "## Candidate duplicates", ""]
    if pool:
        lines.append(
            "You may only return numbers from this list, and only if the other "
            "issue describes the same underlying defect:"
        )
        lines += [f"- #{c['number']}: {c['title']}" for c in pool]
    else:
        lines.append("None supplied - return an empty list.")

    return "\n".join(lines)


def render_pull_request(pr: dict) -> str:
    """Lay out one pull request for the model, in a stable field order."""
    reviews = pr.get("reviews") or []
    lines = [
        f"# Pull request #{pr['number']}: {pr['title']}",
        "",
        f"- URL: {pr['url']}",
        f"- Opened by: {pr['author']} ({pr['author_association']})",
        f"- Created: {pr['created_at']}",
        f"- Last updated: {pr['updated_at']} ({pr['days_since_update']} days ago)",
        f"- New this week: {'yes' if pr['is_new_this_week'] else 'no'}",
        f"- Base branch: {pr.get('base_branch')}",
        f"- Draft: {'yes' if pr.get('is_draft') else 'no'}",
        f"- Review decision: {pr.get('review_decision') or 'none yet'}",
        f"- Size: {pr.get('changed_files')} files, "
        f"+{pr.get('additions')}/-{pr.get('deletions')}",
        f"- Last commit: {pr.get('last_commit_at') or 'unknown'}",
        f"- Existing labels: {', '.join(pr['labels']) or 'none'}",
        f"- Milestone: {pr.get('milestone') or 'none'}",
        f"- Comments: {pr['comment_count']}",
        "",
        "## Reviews (most recent last)",
        "",
    ]
    if reviews:
        lines += [
            f"- {r['author']}: {r['state']} on {r['submitted_at']}" for r in reviews
        ]
    else:
        lines.append("None.")

    lines += ["", "## Description", "", pr["body"] or "_(empty)_"]

    if pr.get("recent_comments"):
        lines += ["", "## Most recent comments", ""]
        for comment in pr["recent_comments"]:
            lines += [
                f"### {comment['author']} ({comment['association']}) on {comment['created_at']}",
                "",
                comment["body"] or "_(empty)_",
                "",
            ]

    return "\n".join(lines)


def classify_one(
    client: anthropic.Anthropic,
    system: list[dict],
    schema: dict,
    user_text: str,
) -> tuple[dict, dict]:
    """Classify a single item, retrying on transient API failures."""
    last_error: Exception | None = None

    for attempt in range(3):
        try:
            message = client.messages.parse(
                model=MODEL,
                max_tokens=MAX_TOKENS,
                system=system,
                thinking={"type": "adaptive"},
                output_config={"effort": EFFORT, "format": schema},
                messages=[{"role": "user", "content": user_text}],
            )
        except (anthropic.RateLimitError, anthropic.APIConnectionError) as exc:
            last_error = exc
            wait = 2 ** attempt * 5
            print(f"    transient error ({type(exc).__name__}), retrying in {wait}s",
                  file=sys.stderr)
            time.sleep(wait)
            continue
        except anthropic.APIStatusError as exc:
            # 400/404 are our bug (bad schema, bad model id) - do not retry.
            raise RuntimeError(f"API rejected the request: {exc}") from exc

        if message.stop_reason == "refusal":
            raise RuntimeError(
                f"Model refused to answer: {getattr(message, 'stop_details', None)}"
            )

        verdict = extract_json(message)
        usage = {
            "input_tokens": message.usage.input_tokens,
            "output_tokens": message.usage.output_tokens,
            "cache_creation_input_tokens": getattr(
                message.usage, "cache_creation_input_tokens", 0
            ) or 0,
            "cache_read_input_tokens": getattr(
                message.usage, "cache_read_input_tokens", 0
            ) or 0,
        }
        return verdict, usage

    raise RuntimeError(f"Gave up after 3 attempts: {last_error}")


def extract_json(message) -> dict:
    """Pull the structured object out of the response.

    Tool inputs and structured outputs may escape JSON differently across
    models, so this always goes through json.loads rather than any string
    handling of its own.
    """
    parsed = getattr(message, "parsed_output", None)
    if parsed is not None:
        return parsed if isinstance(parsed, dict) else json.loads(json.dumps(parsed))

    for block in message.content:
        if block.type == "text":
            return json.loads(block.text)

    raise RuntimeError("No text block in response to parse")


def run(args: argparse.Namespace) -> int:
    week = json.loads(args.week.read_text())
    schemas = load_schemas()
    client = anthropic.Anthropic()

    issue_system = system_blocks("severity_system.md")
    pr_system = system_blocks("pr_triage_system.md")

    issues = week["issues"]
    prs = week["pull_requests"]
    if args.limit:
        issues = issues[: args.limit]
        prs = prs[: args.limit]

    totals = {
        "input_tokens": 0,
        "output_tokens": 0,
        "cache_creation_input_tokens": 0,
        "cache_read_input_tokens": 0,
    }
    failures: list[dict] = []

    def process(items, kind, system, schema, renderer):
        results = []
        for index, item in enumerate(items, start=1):
            print(f"  [{index}/{len(items)}] {kind} #{item['number']}: "
                  f"{item['title'][:70]}", file=sys.stderr)
            try:
                verdict, usage = classify_one(
                    client, system, schema, renderer(item)
                )
            except RuntimeError as exc:
                print(f"    FAILED: {exc}", file=sys.stderr)
                failures.append({
                    "number": item["number"], "type": kind, "error": str(exc)
                })
                continue

            for key in totals:
                totals[key] += usage[key]

            results.append({
                "number": item["number"],
                "type": kind,
                "title": item["title"],
                "url": item["url"],
                "author": item["author"],
                "author_association": item["author_association"],
                "labels": item["labels"],
                "created_at": item["created_at"],
                "updated_at": item["updated_at"],
                "days_since_update": item["days_since_update"],
                "is_new_this_week": item["is_new_this_week"],
                "base_branch": item.get("base_branch"),
                "verdict": verdict,
            })
        return results

    print(f"Classifying {len(issues)} issues...", file=sys.stderr)
    classified_issues = process(
        issues, "issue", issue_system, schemas["issue"], render_issue
    )

    print(f"Classifying {len(prs)} pull requests...", file=sys.stderr)
    classified_prs = process(
        prs, "pull_request", pr_system, schemas["pull_request"], render_pull_request
    )

    output = {
        "repository": week["repository"],
        "since": week["since"],
        "collected_at": week["collected_at"],
        "classified_at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
        "model": MODEL,
        "effort": EFFORT,
        "issues": classified_issues,
        "pull_requests": classified_prs,
        "skipped": week["skipped"],
        "failures": failures,
        "usage": totals,
    }

    args.out.parent.mkdir(parents=True, exist_ok=True)
    args.out.write_text(json.dumps(output, indent=2, ensure_ascii=False))

    cached = totals["cache_read_input_tokens"]
    print(f"\nWrote {args.out}", file=sys.stderr)
    print(
        f"Tokens: {totals['input_tokens']} in, {totals['output_tokens']} out, "
        f"{totals['cache_creation_input_tokens']} cache-write, {cached} cache-read",
        file=sys.stderr,
    )
    if cached == 0 and len(classified_issues) + len(classified_prs) > 1:
        print(
            "warning: zero cache reads across a multi-item run - the system "
            "prompt is being invalidated between calls",
            file=sys.stderr,
        )
    if failures:
        print(f"{len(failures)} item(s) failed to classify", file=sys.stderr)
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--week", type=Path, default=Path("out/week.json"))
    parser.add_argument("--out", type=Path, default=Path("out/classified.json"))
    parser.add_argument(
        "--limit", type=int, default=0,
        help="Classify only the first N issues and N PRs (for eyeballing the prompt).",
    )
    args = parser.parse_args()

    if not os.environ.get("ANTHROPIC_API_KEY"):
        print(
            "note: ANTHROPIC_API_KEY is not set; the SDK will fall back to an "
            "`ant auth login` profile if one exists",
            file=sys.stderr,
        )

    return run(args)


if __name__ == "__main__":
    sys.exit(main())
