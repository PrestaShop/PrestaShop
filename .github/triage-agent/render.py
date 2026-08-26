#!/usr/bin/env python3
"""Stage 3 - turn the classified week into the three things humans read.

  summary.md   the GitHub Actions job summary, the full record
  slack.json   a Block Kit payload, the short version that fits in a message
  board.json   the payload a future board writer would consume

Nothing here talks to GitHub or Slack. This stage only formats.
"""

from __future__ import annotations

import argparse
import json
import sys
from datetime import datetime
from pathlib import Path

SEVERITY_ORDER = ["Critical", "Major", "Minor", "Trivial"]
SEVERITY_EMOJI = {
    "Critical": ":red_circle:",
    "Major": ":large_orange_circle:",
    "Minor": ":large_yellow_circle:",
    "Trivial": ":white_circle:",
}

# Only a bug report gets a place on the severity ladder. Everything else is
# real work, but it is backlog rather than triage, and mixing it into the
# severity bands is what makes a weekly list unreadable.
NON_BUG_KINDS = ["feature_request", "support_request", "maintainer_task", "not_actionable"]
NON_BUG_LABEL = {
    "feature_request": "Feature requests",
    "support_request": "Support requests",
    "maintainer_task": "Maintainer tasks",
    "not_actionable": "Not actionable",
}

ATTENTION_ORDER = ["blocking", "soon", "routine"]
ATTENTION_LABEL = {"blocking": "Blocking", "soon": "Soon", "routine": "Routine"}
ATTENTION_EMOJI = {
    "blocking": ":red_circle:",
    "soon": ":large_orange_circle:",
    "routine": ":white_circle:",
}

# Published list prices per million tokens for the model this runs on. Cache
# reads bill at a tenth of the input rate and cache writes at 1.25x, so a run
# with good cache behaviour costs far less than the raw input count suggests.
PRICE_INPUT_PER_MTOK = 5.00
PRICE_OUTPUT_PER_MTOK = 25.00
CACHE_READ_MULTIPLIER = 0.1
CACHE_WRITE_MULTIPLIER = 1.25

# How many items per band survive into the Slack message. Anything beyond this
# is announced, never silently dropped.
SLACK_ITEMS_PER_BAND = 8


def estimate_cost(usage: dict) -> float:
    plain_in = usage.get("input_tokens", 0)
    cache_write = usage.get("cache_creation_input_tokens", 0)
    cache_read = usage.get("cache_read_input_tokens", 0)
    out = usage.get("output_tokens", 0)

    return (
        plain_in * PRICE_INPUT_PER_MTOK
        + cache_write * PRICE_INPUT_PER_MTOK * CACHE_WRITE_MULTIPLIER
        + cache_read * PRICE_INPUT_PER_MTOK * CACHE_READ_MULTIPLIER
        + out * PRICE_OUTPUT_PER_MTOK
    ) / 1_000_000


def one_sentence(text: str, limit: int = 200) -> str:
    """Keep rationales to the single sentence the prompt asked for."""
    text = " ".join((text or "").split())
    if len(text) > limit:
        text = text[: limit - 1].rstrip() + "…"
    return text


def bug_reports(issues: list[dict]) -> list[dict]:
    return [i for i in issues if i["verdict"].get("kind", "bug_report") == "bug_report"]


def non_bug_reports(issues: list[dict]) -> list[dict]:
    return [i for i in issues if i["verdict"].get("kind", "bug_report") != "bug_report"]


def group_issues(issues: list[dict]) -> dict[str, list[dict]]:
    grouped: dict[str, list[dict]] = {level: [] for level in SEVERITY_ORDER}
    for issue in bug_reports(issues):
        grouped.setdefault(issue["verdict"]["severity"], []).append(issue)
    return grouped


def group_prs(prs: list[dict]) -> dict[str, list[dict]]:
    grouped: dict[str, list[dict]] = {level: [] for level in ATTENTION_ORDER}
    for pr in prs:
        grouped.setdefault(pr["verdict"]["attention"], []).append(pr)
    return grouped


def needs_attention(data: dict) -> list[dict]:
    """The short list the sheriff reads first, if they read nothing else."""
    flagged = []
    for issue in data["issues"]:
        verdict = issue["verdict"]
        reasons = []
        if verdict.get("security_suspicion"):
            reasons.append("possible security report")
        if verdict.get("needs_human_now"):
            reasons.append("flagged as needing a look this week")
        is_bug = verdict.get("kind", "bug_report") == "bug_report"
        if is_bug and verdict.get("severity") == "Critical":
            reasons.append("proposed Critical")
        # A regression outranks an older bug of the same severity when the
        # Dev/PM/QA meeting sets priority, so it is worth the sheriff's week
        # even at Major.
        if is_bug and verdict.get("looks_like_regression") and verdict.get(
            "severity"
        ) in ("Critical", "Major"):
            reasons.append("looks like a regression")
        if reasons:
            flagged.append({"item": issue, "reasons": reasons})

    # Deliberately narrow on the PR side. "Blocking" PRs already have their own
    # section, and a wrong target branch is hygiene rather than urgency - both
    # were tried here and between them they buried the handful of items that
    # genuinely need the sheriff. An unanswered community PR is the one PR
    # signal that is invisible everywhere else.
    for pr in data["pull_requests"]:
        if pr["verdict"].get("is_community_pr_unanswered"):
            flagged.append({
                "item": pr,
                "reasons": ["community PR with no maintainer response"],
            })

    return flagged


def wrong_branch(data: dict) -> list[dict]:
    return [
        pr for pr in data["pull_requests"]
        if pr["verdict"].get("target_branch_looks_wrong")
    ]


def low_confidence(data: dict) -> list[dict]:
    return [
        item
        for item in data["issues"] + data["pull_requests"]
        if item["verdict"].get("confidence") == "low"
    ]


def window_end(data: dict) -> datetime:
    """End of the collected window: the explicit --until, else collection time."""
    if data.get("until"):
        return datetime.strptime(data["until"], "%Y-%m-%d")
    return datetime.fromisoformat(data["collected_at"])


def render_markdown(data: dict) -> str:
    week_end = window_end(data).strftime("%Y-%m-%d")
    issues = data["issues"]
    prs = data["pull_requests"]
    out: list[str] = []

    out += [
        f"# Weekly pre-qualification — {data['since']} to {week_end}",
        "",
        f"`{data['repository']}` · {len(bug_reports(issues))} bug reports, "
        f"{len(non_bug_reports(issues))} other issues and {len(prs)} open "
        "pull requests touched this week.",
        "",
        "> **These are proposals.** This run applied no label and wrote to no "
        "board. Every severity below is a suggestion for the sheriff to accept, "
        "correct, or ignore.",
        "",
    ]

    flagged = needs_attention(data)
    out += [f"## Needs you this week ({len(flagged)})", ""]
    if flagged:
        for entry in flagged:
            item = entry["item"]
            out.append(
                f"- [#{item['number']}]({item['url']}) {item['title']} — "
                f"**{', '.join(entry['reasons'])}**"
            )
    else:
        out.append("_Nothing flagged._")
    out.append("")

    out += ["## Issues by proposed severity", ""]
    grouped = group_issues(issues)
    for level in SEVERITY_ORDER:
        bucket = grouped.get(level, [])
        out += [f"### {level} ({len(bucket)})", ""]
        if not bucket:
            out += ["_None._", ""]
            continue
        out += ["| Issue | Confidence | Next step | Why |", "|---|---|---|---|"]
        for issue in bucket:
            verdict = issue["verdict"]
            flags = []
            if verdict.get("looks_like_regression"):
                flags.append("regression")
            if verdict.get("security_suspicion"):
                flags.append("security?")
            marker = f" `{'` `'.join(flags)}`" if flags else ""
            out.append(
                f"| [#{issue['number']}]({issue['url']}) {issue['title']}{marker} "
                f"| {verdict['confidence']} "
                f"| {verdict['suggested_status']} "
                f"| {one_sentence(verdict['rationale'])} |"
            )
        out.append("")

    others = non_bug_reports(issues)
    if others:
        out += [
            f"## Issues that are not bug reports ({len(others)})",
            "",
            "Real work, but backlog rather than triage - severity does not "
            "apply, so they are listed separately instead of padding the bands "
            "above.",
            "",
        ]
        for kind in NON_BUG_KINDS:
            bucket = [i for i in others if i["verdict"]["kind"] == kind]
            if not bucket:
                continue
            out += [f"**{NON_BUG_LABEL[kind]} ({len(bucket)})**", ""]
            for issue in bucket:
                out.append(
                    f"- [#{issue['number']}]({issue['url']}) {issue['title']} — "
                    f"{one_sentence(issue['verdict']['rationale'])}"
                )
            out.append("")

    out += ["## Pull requests by attention needed", ""]
    grouped_prs = group_prs(prs)
    for level in ATTENTION_ORDER:
        bucket = grouped_prs.get(level, [])
        out += [f"### {ATTENTION_LABEL[level]} ({len(bucket)})", ""]
        if not bucket:
            out += ["_None._", ""]
            continue
        out += ["| PR | Waiting on | Idle | Why |", "|---|---|---|---|"]
        for pr in bucket:
            verdict = pr["verdict"]
            out.append(
                f"| [#{pr['number']}]({pr['url']}) {pr['title']} "
                f"| {verdict['waiting_on']} "
                f"| {pr['days_since_update']}d "
                f"| {one_sentence(verdict['rationale'])} |"
            )
        out.append("")

    misrouted = wrong_branch(data)
    if misrouted:
        out += [
            f"## Branch check ({len(misrouted)})",
            "",
            "Labelled a bug fix but opened against `develop`, so the fix would "
            "skip the next patch release. Legitimate when the bug only exists "
            "in unreleased code — worth a glance, not an alarm.",
            "",
        ]
        for pr in misrouted:
            out.append(
                f"- [#{pr['number']}]({pr['url']}) {pr['title']} — "
                f"`{pr['base_branch']}`"
            )
        out.append("")

    uncertain = low_confidence(data)
    out += [f"## Low confidence — the agent was guessing ({len(uncertain)})", ""]
    if uncertain:
        out += [
            "These are the items where the proposal is least trustworthy. "
            "They are worth a human read regardless of the level assigned.",
            "",
        ]
        for item in uncertain:
            verdict = item["verdict"]
            level = verdict.get("severity") or verdict.get("attention")
            out.append(
                f"- [#{item['number']}]({item['url']}) {item['title']} — "
                f"proposed `{level}`: {one_sentence(verdict['rationale'])}"
            )
    else:
        out.append("_None._")
    out.append("")

    skipped = data.get("skipped", [])
    out += [
        f"## Not classified ({len(skipped)})",
        "",
        "<details>",
        "<summary>Why each item was left out</summary>",
        "",
    ]
    for entry in skipped:
        out.append(f"- #{entry['number']} ({entry['type']}) — {entry['reason']}")
    out += ["", "</details>", ""]

    failures = data.get("failures", [])
    if failures:
        out += [f"## Classification failures ({len(failures)})", ""]
        for failure in failures:
            out.append(
                f"- #{failure['number']} ({failure['type']}) — {failure['error']}"
            )
        out.append("")

    usage = data.get("usage", {})
    cost = estimate_cost(usage)
    out += [
        "## Run",
        "",
        f"- Model: `{data['model']}`, effort `{data['effort']}`",
        f"- Tokens: {usage.get('input_tokens', 0):,} in "
        f"({usage.get('cache_read_input_tokens', 0):,} read from cache), "
        f"{usage.get('output_tokens', 0):,} out",
        f"- Estimated cost at list price: **${cost:.2f}** "
        f"(~${cost * 52:.0f}/year at this weekly volume)",
        "",
    ]

    return "\n".join(out)


def slack_payload(data: dict, run_url: str | None) -> dict:
    week_end = window_end(data).strftime("%b %d")
    week_start = datetime.strptime(data["since"], "%Y-%m-%d").strftime("%b %d")
    issues = data["issues"]
    prs = data["pull_requests"]

    blocks: list[dict] = [
        {
            "type": "header",
            "text": {
                "type": "plain_text",
                "text": f"Weekly pre-qualification · {week_start} – {week_end}",
            },
        },
        {
            "type": "context",
            "elements": [
                {
                    "type": "mrkdwn",
                    "text": (
                        f"*{len(bug_reports(issues))}* bug reports · "
                        f"*{len(prs)}* PRs · "
                        "_proposals only, nothing was labelled_"
                    ),
                }
            ],
        },
    ]

    def band(title: str, lines: list[str], total: int) -> None:
        if total > SLACK_ITEMS_PER_BAND:
            lines.append(
                f"_+ {total - SLACK_ITEMS_PER_BAND} more in the run summary_"
            )
        blocks.append({"type": "divider"})
        blocks.append(
            {
                "type": "section",
                "text": {"type": "mrkdwn", "text": f"*{title}*\n" + "\n".join(lines)},
            }
        )

    flagged = needs_attention(data)
    if flagged:
        band(
            f"Needs you this week ({len(flagged)})",
            [
                f"{SEVERITY_EMOJI['Critical']} <{e['item']['url']}|"
                f"#{e['item']['number']}> {e['item']['title'][:80]} — "
                f"_{', '.join(e['reasons'])}_"
                for e in flagged[:SLACK_ITEMS_PER_BAND]
            ],
            len(flagged),
        )

    grouped = group_issues(issues)
    for level in SEVERITY_ORDER:
        bucket = grouped.get(level, [])
        if not bucket:
            continue
        band(
            f"Issues · proposed {level} ({len(bucket)})",
            [
                f"{SEVERITY_EMOJI[level]} <{i['url']}|#{i['number']}> "
                f"{i['title'][:80]} — "
                f"_{one_sentence(i['verdict']['rationale'], 110)}_"
                for i in bucket[:SLACK_ITEMS_PER_BAND]
            ],
            len(bucket),
        )

    grouped_prs = group_prs(prs)
    for level in ATTENTION_ORDER:
        if level == "routine":
            continue  # routine PRs are healthy; they do not need a Slack line
        bucket = grouped_prs.get(level, [])
        if not bucket:
            continue
        band(
            f"Pull requests · {ATTENTION_LABEL[level]} ({len(bucket)})",
            [
                f"{ATTENTION_EMOJI[level]} <{p['url']}|#{p['number']}> "
                f"{p['title'][:80]} — waiting on "
                f"*{p['verdict']['waiting_on']}*, {p['days_since_update']}d idle"
                for p in bucket[:SLACK_ITEMS_PER_BAND]
            ],
            len(bucket),
        )

    footer = f"Model `{data['model']}` · run at {data['classified_at']}"
    if run_url:
        footer += f" · <{run_url}|full summary>"
    blocks += [
        {"type": "divider"},
        {"type": "context", "elements": [{"type": "mrkdwn", "text": footer}]},
    ]

    return {
        "text": (
            f"Weekly pre-qualification: {len(issues)} issues, {len(prs)} PRs "
            f"({week_start} – {week_end})"
        ),
        "blocks": blocks,
    }


def board_payload(data: dict) -> dict:
    """What a future board writer would consume.

    Unused by this POC on purpose - producing it now is what makes adding a
    board target later a small change rather than a redesign.
    """
    return {
        "generated_at": data["classified_at"],
        "week_of": data["since"],
        "note": "Proposals. No board field was written by this run.",
        "items": [
            {
                "content_url": item["url"],
                "number": item["number"],
                "type": item["type"],
                "ai_kind": item["verdict"].get("kind"),
                "ai_severity": item["verdict"].get("severity"),
                "ai_attention": item["verdict"].get("attention"),
                "ai_confidence": item["verdict"]["confidence"],
                "ai_rationale": one_sentence(item["verdict"]["rationale"]),
                "ai_suggested_status": item["verdict"].get("suggested_status"),
                "ai_looks_like_regression": item["verdict"].get("looks_like_regression"),
            }
            for item in data["issues"] + data["pull_requests"]
        ],
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--classified", type=Path, default=Path("out/classified.json"))
    parser.add_argument("--out-dir", type=Path, default=Path("out"))
    parser.add_argument(
        "--run-url",
        default=None,
        help="Link back to the workflow run, shown in the Slack footer.",
    )
    args = parser.parse_args()

    data = json.loads(args.classified.read_text())
    args.out_dir.mkdir(parents=True, exist_ok=True)

    summary = args.out_dir / "summary.md"
    slack = args.out_dir / "slack.json"
    board = args.out_dir / "board.json"

    summary.write_text(render_markdown(data))
    slack.write_text(
        json.dumps(slack_payload(data, args.run_url), indent=2, ensure_ascii=False)
    )
    board.write_text(json.dumps(board_payload(data), indent=2, ensure_ascii=False))

    print(f"Wrote {summary}, {slack}, {board}", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())
