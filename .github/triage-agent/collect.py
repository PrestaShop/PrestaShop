#!/usr/bin/env python3
"""Stage 1 - gather the week's issues and pull requests from GitHub.

No model is involved here. Everything this stage does is deterministic and
reproducible, which is what lets the classification stage be re-run cheaply on a
frozen input while the prompt is being tuned.

This is also where cost is controlled: every item filtered out here is an item
nobody pays to classify. Filtered items are never dropped silently - each one is
recorded in `skipped` with a reason, so the report can show what was left out.
"""

from __future__ import annotations

import argparse
import json
import re
import subprocess
import sys
from datetime import datetime, timedelta, timezone
from pathlib import Path

REPO_OWNER = "PrestaShop"
REPO_NAME = "PrestaShop"

SEVERITY_LABELS = {"Critical", "Major", "Minor", "Trivial"}

# Accounts whose activity is machine-generated. Nothing they open needs a human
# pre-qualification pass.
BOT_AUTHORS = {
    "dependabot",
    "dependabot[bot]",
    "github-actions",
    "github-actions[bot]",
    "ps-jarvis",
    "renovate[bot]",
}

# Body text beyond this point almost never changes a severity call, and paying
# to send a 40 kB stack trace to the model is waste. The truncation is visible
# to the model so it knows the text was cut.
BODY_LIMIT = 6000
COMMENT_LIMIT = 1200

SEARCH_QUERY = """
query($q: String!, $cursor: String) {
  search(query: $q, type: ISSUE, first: 50, after: $cursor) {
    issueCount
    pageInfo { hasNextPage endCursor }
    nodes {
      __typename
      ... on Issue {
        number title body url state createdAt updatedAt
        author { login }
        authorAssociation
        labels(first: 30) { nodes { name } }
        milestone { title }
        reactions { totalCount }
        comments(last: 3) {
          totalCount
          nodes { author { login } authorAssociation body createdAt }
        }
      }
      ... on PullRequest {
        number title body url state isDraft createdAt updatedAt
        author { login }
        authorAssociation
        baseRefName
        additions deletions changedFiles
        reviewDecision
        labels(first: 30) { nodes { name } }
        milestone { title }
        comments(last: 3) {
          totalCount
          nodes { author { login } authorAssociation body createdAt }
        }
        reviews(last: 5) {
          nodes { author { login } state submittedAt }
        }
        commits(last: 1) { nodes { commit { committedDate } } }
      }
    }
  }
}
"""


def run_gh(args: list[str]) -> str:
    """Call the gh CLI, surfacing its stderr when it fails."""
    result = subprocess.run(
        ["gh", *args], capture_output=True, text=True, check=False
    )
    if result.returncode != 0:
        raise RuntimeError(
            f"gh {' '.join(args)} failed ({result.returncode}):\n{result.stderr.strip()}"
        )
    return result.stdout


def graphql_search(query_string: str) -> list[dict]:
    """Page through a GitHub search query and return every node."""
    nodes: list[dict] = []
    cursor: str | None = None

    while True:
        args = [
            "api", "graphql",
            "-f", f"query={SEARCH_QUERY}",
            "-F", f"q={query_string}",
        ]
        if cursor:
            args += ["-F", f"cursor={cursor}"]

        payload = json.loads(run_gh(args))
        if "errors" in payload:
            raise RuntimeError(f"GraphQL errors: {payload['errors']}")

        search = payload["data"]["search"]
        nodes.extend(n for n in search["nodes"] if n)

        if not search["pageInfo"]["hasNextPage"]:
            break
        cursor = search["pageInfo"]["endCursor"]

        # GitHub search caps out at 1000 results; going past that means the
        # window is too wide and the caller should narrow it rather than get a
        # silently truncated set.
        if len(nodes) >= 1000:
            print(
                "warning: hit GitHub's 1000-result search cap - narrow --since",
                file=sys.stderr,
            )
            break

    return nodes


def truncate(text: str | None, limit: int) -> str:
    if not text:
        return ""
    text = text.strip()
    if len(text) <= limit:
        return text
    return text[:limit] + f"\n\n[... truncated, {len(text) - limit} more characters]"


def label_names(node: dict) -> list[str]:
    return [label["name"] for label in node.get("labels", {}).get("nodes", [])]


def author_login(node: dict) -> str:
    author = node.get("author")
    return author["login"] if author else "ghost"


def simplify_comments(node: dict) -> list[dict]:
    comments = node.get("comments", {})
    return [
        {
            "author": author_login(comment),
            "association": comment.get("authorAssociation"),
            "created_at": comment.get("createdAt"),
            "body": truncate(comment.get("body"), COMMENT_LIMIT),
        }
        for comment in comments.get("nodes", [])
    ]


def days_since(timestamp: str, now: datetime) -> int:
    parsed = datetime.fromisoformat(timestamp.replace("Z", "+00:00"))
    return (now - parsed).days


# Words that carry no signal when looking for a duplicate. Searching for "the"
# and "when" returns the whole tracker.
STOPWORDS = {
    "the", "and", "for", "with", "when", "from", "that", "this", "have", "has",
    "not", "are", "but", "you", "your", "its", "it's", "can", "cant", "does",
    "doesn't", "doesnt", "after", "before", "into", "there", "then", "than",
    "was", "were", "issue", "bug", "problem", "error", "prestashop", "shop",
}


def title_keywords(title: str, limit: int = 5) -> list[str]:
    words = re.findall(r"[A-Za-z][A-Za-z0-9_-]{2,}", title.lower())
    seen: list[str] = []
    for word in words:
        if word in STOPWORDS or word in seen:
            continue
        seen.append(word)
        if len(seen) == limit:
            break
    return seen


def find_duplicate_candidates(issue: dict, exclude: int) -> list[dict]:
    """Pre-fetch similar open issues so the model picks from a real list.

    Grounding this in a search result is what stops the model inventing issue
    numbers - it can only choose from what we hand it.
    """
    keywords = title_keywords(issue["title"])
    if len(keywords) < 2:
        return []

    query = (
        f"repo:{REPO_OWNER}/{REPO_NAME} is:issue is:open "
        + " ".join(keywords)
        + " in:title"
    )
    try:
        raw = run_gh([
            "api", "-X", "GET", "search/issues",
            "-f", f"q={query}",
            "-F", "per_page=20",
            "--jq", ".items[] | {number, title}",
        ])
    except RuntimeError as exc:
        print(f"  duplicate search failed for #{exclude}: {exc}", file=sys.stderr)
        return []

    candidates = []
    for line in raw.strip().splitlines():
        if not line:
            continue
        item = json.loads(line)
        if item["number"] != exclude:
            candidates.append(item)
    return candidates[:10]


def collect(since: str, want_duplicates: bool) -> dict:
    now = datetime.now(timezone.utc)
    query = f"repo:{REPO_OWNER}/{REPO_NAME} updated:>={since} sort:updated-desc"

    print(f"Searching: {query}", file=sys.stderr)
    nodes = graphql_search(query)
    print(f"  {len(nodes)} raw results", file=sys.stderr)

    issues: list[dict] = []
    pull_requests: list[dict] = []
    skipped: list[dict] = []

    for node in nodes:
        number = node["number"]
        kind = "issue" if node["__typename"] == "Issue" else "pull_request"
        author = author_login(node)
        labels = label_names(node)

        # A closed item has already been dealt with; the sheriff pre-qualifies
        # what is still open.
        if node["state"] != "OPEN":
            skipped.append({
                "number": number, "type": kind, "title": node["title"],
                "reason": f"not open (state={node['state'].lower()})",
            })
            continue

        if author in BOT_AUTHORS:
            skipped.append({
                "number": number, "type": kind, "title": node["title"],
                "reason": f"bot author ({author})",
            })
            continue

        common = {
            "number": number,
            "type": kind,
            "title": node["title"],
            "url": node["url"],
            "body": truncate(node.get("body"), BODY_LIMIT),
            "author": author,
            "author_association": node.get("authorAssociation"),
            "labels": labels,
            "milestone": (node.get("milestone") or {}).get("title"),
            "created_at": node["createdAt"],
            "updated_at": node["updatedAt"],
            "days_since_update": days_since(node["updatedAt"], now),
            "is_new_this_week": node["createdAt"] >= f"{since}T00:00:00Z",
            "comment_count": node.get("comments", {}).get("totalCount", 0),
            "recent_comments": simplify_comments(node),
        }

        if kind == "issue":
            # A severity label means a maintainer has already ruled on this one.
            already_rated = SEVERITY_LABELS.intersection(labels)
            if already_rated:
                skipped.append({
                    "number": number, "type": kind, "title": node["title"],
                    "reason": f"already rated by a maintainer ({', '.join(sorted(already_rated))})",
                })
                continue

            common["reactions"] = node.get("reactions", {}).get("totalCount", 0)
            issues.append(common)
        else:
            reviews = node.get("reviews", {}).get("nodes", [])
            commits = node.get("commits", {}).get("nodes", [])
            common.update({
                "is_draft": node.get("isDraft", False),
                "base_branch": node.get("baseRefName"),
                "additions": node.get("additions"),
                "deletions": node.get("deletions"),
                "changed_files": node.get("changedFiles"),
                "review_decision": node.get("reviewDecision"),
                "reviews": [
                    {
                        "author": author_login(review),
                        "state": review.get("state"),
                        "submitted_at": review.get("submittedAt"),
                    }
                    for review in reviews
                ],
                "last_commit_at": (
                    commits[0]["commit"]["committedDate"] if commits else None
                ),
            })
            pull_requests.append(common)

    if want_duplicates:
        print(f"Looking for duplicate candidates on {len(issues)} issues...", file=sys.stderr)
        for issue in issues:
            issue["duplicate_candidates_pool"] = find_duplicate_candidates(
                issue, issue["number"]
            )
    else:
        for issue in issues:
            issue["duplicate_candidates_pool"] = []

    return {
        "repository": f"{REPO_OWNER}/{REPO_NAME}",
        "collected_at": now.isoformat(),
        "since": since,
        "counts": {
            "issues": len(issues),
            "pull_requests": len(pull_requests),
            "skipped": len(skipped),
        },
        "issues": issues,
        "pull_requests": pull_requests,
        "skipped": skipped,
    }


def resolve_since(value: str) -> str:
    """Accept either '7d' or an explicit YYYY-MM-DD date."""
    match = re.fullmatch(r"(\d+)d", value)
    if match:
        delta = timedelta(days=int(match.group(1)))
        return (datetime.now(timezone.utc) - delta).strftime("%Y-%m-%d")
    datetime.strptime(value, "%Y-%m-%d")  # raises if malformed
    return value


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "--since", default="7d",
        help="Window start: '7d' for the past week, or an explicit YYYY-MM-DD (UTC).",
    )
    parser.add_argument(
        "--out", type=Path, default=Path("out/week.json"),
        help="Where to write the collected week.",
    )
    parser.add_argument(
        "--no-duplicates", action="store_true",
        help="Skip the per-issue duplicate-candidate search (one API call per issue).",
    )
    args = parser.parse_args()

    since = resolve_since(args.since)
    data = collect(since, want_duplicates=not args.no_duplicates)

    args.out.parent.mkdir(parents=True, exist_ok=True)
    args.out.write_text(json.dumps(data, indent=2, ensure_ascii=False))

    counts = data["counts"]
    print(
        f"\nWrote {args.out}: {counts['issues']} issues, "
        f"{counts['pull_requests']} PRs, {counts['skipped']} skipped "
        f"(since {since})",
        file=sys.stderr,
    )
    return 0


if __name__ == "__main__":
    sys.exit(main())
