---
step: 3
title: "Adapter Layer"
previous: step-02-domain-layer.md
next: step-04-behat-tests.md
deliverable: "src/Adapter/{Domain}/ with Repository and all command/query handler implementations, registered for autoconfiguration"
---

# Step 3 — Adapter Layer

The adapter layer lives in `src/Adapter/{Domain}/`. It bridges Core domain contracts (step 2) with the legacy ObjectModel layer. Handlers here implement the interfaces defined in step 2 and use `#[AsCommandHandler]` / `#[AsQueryHandler]` attributes for automatic registration via Symfony Messenger.

Read `@.ai/Component/CQRS/CONTEXT.md` for handler placement, repository conventions, and DI registration. For multistore-aware repositories see `@.ai/MULTISTORE.md`.

## Why this step before Behat

The Behat suite (step 4) calls into the command and query buses — it cannot run until handlers exist. Once the handlers and repository are in place and registered, Behat becomes the truth-meter for the whole CQRS layer.

## Skills to invoke

| Skill | Produces |
|---|---|
| `create-doctrine-repository` | The single class that touches the database. Choice of base class depends on multistore tier discovered in the audit. |
| `implement-cqrs-handlers` | Handler implementations for every command and query interface, each delegating to the repository |
| `register-cqrs-services` | Confirm autoconfiguration via attributes; add manual DI for the repository or any service that needs explicit wiring |

## Orchestration notes

- Repository is the **only** class that talks to `Db::getInstance()` or to the legacy ObjectModel for state mutation. Handlers must not bypass it.
- Multistore tier (1/2/3) was decided during audit — pick the base repository class accordingly. Do not retrofit later.
- Every domain exception thrown from a handler must come from the hierarchy created in step 2 — no `\Exception` catches in handlers, no untyped throws.

## Gate to next step

- [ ] Repository covers every read and write the manifest lists
- [ ] Every handler interface from step 2 has an implementation
- [ ] Handlers are auto-discovered (no missing `#[AsCommandHandler]` / `#[AsQueryHandler]`)
- [ ] No SQL or ObjectModel mutation outside the repository
- [ ] `php bin/console prestashop:list:commands-and-queries` lists every new command and query
