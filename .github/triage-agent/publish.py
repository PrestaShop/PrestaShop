#!/usr/bin/env python3
"""Stage 4 - post the rendered Block Kit payload to Slack.

Dry run is the default. Posting requires an explicit --publish, so the only way
this run reaches a human channel is if someone asked for it.

This is the only stage in the pipeline that sends anything anywhere, and it
still writes nothing to GitHub.
"""

from __future__ import annotations

import argparse
import json
import os
import sys
import urllib.error
import urllib.request
from pathlib import Path


def post_to_slack(webhook: str, payload: dict, timeout: int = 30) -> None:
    request = urllib.request.Request(
        webhook,
        data=json.dumps(payload).encode("utf-8"),
        headers={"Content-Type": "application/json"},
        method="POST",
    )
    try:
        with urllib.request.urlopen(request, timeout=timeout) as response:
            body = response.read().decode("utf-8", "replace").strip()
            if response.status != 200 or body != "ok":
                raise RuntimeError(f"Slack returned {response.status}: {body}")
    except urllib.error.HTTPError as exc:
        detail = exc.read().decode("utf-8", "replace").strip()
        raise RuntimeError(f"Slack rejected the payload ({exc.code}): {detail}") from exc
    except urllib.error.URLError as exc:
        raise RuntimeError(f"Could not reach Slack: {exc.reason}") from exc


def describe(payload: dict) -> str:
    """A readable outline of what would be posted, for the dry run."""
    lines = [f"text: {payload['text']}", f"blocks: {len(payload['blocks'])}"]
    for block in payload["blocks"]:
        if block["type"] == "header":
            lines.append(f"  [header]  {block['text']['text']}")
        elif block["type"] == "section":
            first = block["text"]["text"].splitlines()[0]
            body = block["text"]["text"].splitlines()[1:]
            lines.append(f"  [section] {first} ({len(body)} lines)")
        elif block["type"] == "context":
            lines.append(f"  [context] {block['elements'][0]['text']}")
    return "\n".join(lines)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--slack", type=Path, default=Path("out/slack.json"))
    parser.add_argument(
        "--publish",
        action="store_true",
        help="Actually post. Without this flag the payload is only described.",
    )
    args = parser.parse_args()

    payload = json.loads(args.slack.read_text())

    if not args.publish:
        print("DRY RUN - nothing was sent. Payload outline:\n", file=sys.stderr)
        print(describe(payload))
        print("\nRe-run with --publish to post it.", file=sys.stderr)
        return 0

    webhook = os.environ.get("SLACK_WEBHOOK_URL")
    if not webhook:
        print(
            "SLACK_WEBHOOK_URL is not set - refusing to publish.\n"
            "Set it, or drop --publish for a dry run.",
            file=sys.stderr,
        )
        return 1

    post_to_slack(webhook, payload)
    print("Posted to Slack.", file=sys.stderr)
    return 0


if __name__ == "__main__":
    sys.exit(main())
