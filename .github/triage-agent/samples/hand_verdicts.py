#!/usr/bin/env python3
"""Produce the sample's `classified.json` without calling the API.

**This is not part of the pipeline.** It exists so the committed sample is
reproducible: no `ANTHROPIC_API_KEY` was available when the spike was built, so
the verdicts in `2026-08-18-summary.md` were produced by applying
`prompts/severity_system.md` and `prompts/pr_triage_system.md` by hand rather
than by running `classify.py`.

Keeping the producer in the repository means the verdicts can be audited and the
sample regenerated, instead of being asserted in prose. Once a key exists this
file has no reason to survive - delete it and regenerate the sample with the
real `classify.py`.

    python collect.py --since 2026-08-18 --until 2026-08-25 --no-duplicates
    python samples/hand_verdicts.py
    python render.py
"""

import json
import sys
import time
from pathlib import Path

HERE = Path(__file__).resolve().parent
OUT = HERE.parent / "out"

# number: (kind, severity, confidence, category, component, status, security,
#          regression, needs_now, duplicates, rationale)
#
# Only #41511 is marked a regression: it is the one report in this week that
# makes an actual version-to-version claim ("with >= 9.1 versions", after an
# upgrade). Several others concern upgrades or recent releases without ever
# saying the behaviour used to work, and the rubric asks for the comparison,
# not merely a recent version number.
ISSUES = {
    41568: ("maintainer_task", "Trivial", "high", "CO", "none", "ready", False, False, False, [],
            "Internal migration task on ShopConstraint handling, not a merchant-facing defect."),
    42076: ("maintainer_task", "Trivial", "high", "WS", "none", "ready", False, False, False, [],
            "Checklist tracking Admin API endpoints already delivered by linked PRs."),
    42397: ("bug_report", "Major", "medium", "WS", "Shipping", "ready", False, False, False, [],
            "Corrupts order line quantities, but only behind the improved_shipment beta flag which is off by default."),
    42410: ("bug_report", "Major", "medium", "BO", "Shipping", "ready", False, False, False, [],
            "Hardcoded zero quantity skews the carrier list and therefore the shipping cost; limited to the beta flag."),
    42138: ("maintainer_task", "Trivial", "high", "CO", "none", "ready", False, False, False, [],
            "Spike ticket for this very agent, no merchant impact."),
    41971: ("maintainer_task", "Trivial", "high", "BO", "Dashboard", "ready", False, False, False, [],
            "Spike on the dashboard migration pattern, no defect reported."),
    22240: ("maintainer_task", "Trivial", "high", "CO", "International", "ready", False, False, False, [],
            "Umbrella epic listing other tax-rules issues."),
    42408: ("bug_report", "Major", "high", "BO", "International", "ready", False, False, False, [],
            "Fatal TypeError makes the BO email translations page unreachable with no obvious workaround."),
    40357: ("bug_report", "Minor", "high", "BO", "Advanced parameters", "ready", False, False, False, [],
            "Tolerable slowdown on a diagnostics page that is not on any essential shop operation."),
    42401: ("bug_report", "Minor", "high", "BO", "Shipping", "ready", False, False, False, [],
            "Wrong currency symbol displayed; the amounts themselves are correct and a fix PR already exists."),
    42399: ("bug_report", "Major", "medium", "BO", "Order", "TBR", False, False, True, [42400, 41380],
            "504 on the BO carts page leaves the merchant unable to consult carts, with two open perf PRs pointing at the same query."),
    42079: ("bug_report", "Major", "medium", "CO", "Catalog", "TBR", False, False, False, [],
            "empty() misreads the pack-only stock type so pack quantities are wrong, which can oversell stock."),
    42394: ("support_request", "Trivial", "high", "FO", "Modules", "NMI", False, False, False, [],
            "Merchant asking for help on an 8.0.4 shop with a third-party theme, no core defect demonstrated."),
    42391: ("bug_report", "Major", "medium", "FO", "Shipping", "TBR", False, False, False, [],
            "A default-active bundled module returns HTTP 400 on a rendering checkout page, which misleads monitoring and crawlers."),
    42387: ("bug_report", "Major", "high", "BO", "Shipping", "Needs Specs", False, False, False, [],
            "Changing the delivery address leaves the shipment cost stale, which impacts what the customer pays."),
    42389: ("bug_report", "Major", "medium", "BO", "Modules", "TBR", False, False, False, [],
            "Unbounded session-file growth can exhaust inodes and take the whole server down, but only on shops running ps_facebook."),
    42385: ("maintainer_task", "Trivial", "high", "BO", "Advanced parameters", "ready", False, False, False, [],
            "Internal task to add transactions to the migrated importer."),
    41511: ("bug_report", "Major", "high", "FO", "Design", "TBR", False, True, False, [],
            "Malformed srcset breaks image rendering across the front office from 9.1 onwards, where it worked before."),
    42379: ("bug_report", "Major", "medium", "IN", "none", "TBR", False, False, False, [],
            "Upgrading without maintenance mode detected leaves the shop taking orders mid-upgrade, risking inconsistent data."),
    32142: ("bug_report", "Minor", "high", "BO", "Design", "ready", False, False, False, [],
            "Wrong logo in emails for a multistore setup, which is opt-in and has an obvious workaround."),
    42376: ("bug_report", "Major", "medium", "IN", "none", "TBR", False, False, True, [],
            "Merchants cannot see the 8.2.8 security release in Update Assistant, which delays security patching."),
    42371: ("bug_report", "Minor", "high", "BO", "Design", "TBR", False, False, False, [],
            "The <section> tag is stripped from CMS pages; using a div is a reasonable workaround."),
}

MAINTAINERS = {"MEMBER", "OWNER", "COLLABORATOR"}


def pr_verdict(pr: dict) -> dict:
    """The PR rubric applied mechanically to the collected metadata."""
    labels = set(pr["labels"])
    decision = pr.get("review_decision")
    idle = pr["days_since_update"]
    community = pr["author_association"] not in MAINTAINERS
    reviews = pr.get("reviews") or []

    if {"Waiting for author", "Waiting for rebase"} & labels or decision == "CHANGES_REQUESTED":
        waiting = "author"
    elif {"Waiting for QA", "Waiting for QA by Community"} & labels:
        waiting = "QA"
    elif {"Waiting for PM", "Needs Specs"} & labels:
        waiting = "PM"
    elif "Waiting for UX" in labels:
        waiting = "UX"
    elif decision == "APPROVED":
        waiting = "nobody"
    else:
        waiting = "reviewer"

    unanswered = community and not reviews and pr["comment_count"] <= 1
    # A bug fix aimed at develop skips the next patch release - unless the bug
    # only exists in unreleased code, which metadata alone cannot tell us.
    misrouted = "Bug fix" in labels and pr["base_branch"] == "develop"

    if unanswered:
        attention, why = "blocking", f"community PR opened with no review and no maintainer reply, {idle}d idle"
    elif waiting == "nobody" and "Blocked" not in labels:
        attention, why = "blocking", "approved with nothing left waiting on it - ready to merge"
    elif misrouted:
        attention, why = "soon", "labelled a bug fix but opened against develop, so it would skip the next patch release"
    elif "Blocked" in labels:
        attention, why = "soon", "explicitly marked Blocked, needs someone to unblock it"
    elif waiting == "author":
        attention, why = "routine", f"waiting on the author after review feedback, {idle}d idle"
    elif waiting == "QA":
        attention, why = "soon", "dev-approved and sitting in the QA queue"
    elif waiting == "PM":
        attention, why = "soon", "waiting on a product decision"
    else:
        attention, why = "routine", f"awaiting a first or follow-up review, {idle}d idle"

    return {
        "attention": attention,
        "confidence": "low" if (pr.get("is_draft") or (pr.get("changed_files") or 0) > 40) else "medium",
        "rationale": why,
        "waiting_on": waiting,
        "target_branch_looks_wrong": misrouted,
        "is_community_pr_unanswered": unanswered,
        "metadata_incomplete": len(pr["body"] or "") < 200,
    }


CARRIED = (
    "number", "title", "url", "author", "author_association", "labels",
    "created_at", "updated_at", "days_since_update", "is_new_this_week",
)


def main() -> int:
    week = json.loads((OUT / "week.json").read_text())

    issues = []
    for issue in week["issues"]:
        verdict = ISSUES.get(issue["number"])
        if verdict is None:
            print(f"no hand verdict for #{issue['number']} - skipped", file=sys.stderr)
            continue
        kind, sev, conf, cat, comp, status, sec, reg, now, dup, why = verdict
        issues.append(
            {k: issue[k] for k in CARRIED}
            | {
                "type": "issue",
                "base_branch": None,
                "verdict": {
                    "kind": kind, "severity": sev, "confidence": conf,
                    "rationale": why, "category": cat, "component": comp,
                    "suggested_status": status, "security_suspicion": sec,
                    "looks_like_regression": reg,
                    "duplicate_candidates": dup, "needs_human_now": now,
                },
            }
        )

    prs = [
        {k: pr[k] for k in CARRIED}
        | {"type": "pull_request", "base_branch": pr["base_branch"],
           "verdict": pr_verdict(pr)}
        for pr in week["pull_requests"]
    ]

    (OUT / "classified.json").write_text(json.dumps({
        "repository": week["repository"],
        "since": week["since"],
        "until": week.get("until"),
        "collected_at": week["collected_at"],
        "classified_at": time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime()),
        "model": "hand-applied rubric (no API key available)",
        "effort": "n/a",
        "issues": issues,
        "pull_requests": prs,
        "skipped": week["skipped"],
        "failures": [],
        "usage": {"input_tokens": 0, "output_tokens": 0,
                  "cache_creation_input_tokens": 0, "cache_read_input_tokens": 0},
    }, indent=2, ensure_ascii=False))

    print(f"wrote out/classified.json: {len(issues)} issues, {len(prs)} PRs", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())
