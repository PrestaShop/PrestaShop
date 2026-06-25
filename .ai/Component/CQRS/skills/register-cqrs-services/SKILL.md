---
name: register-cqrs-services
description: >
  Register all domain handlers and the repository in the Symfony DI container.
  Handlers are auto-discovered via #[AsCommandHandler] / #[AsQueryHandler] attributes.
  Trigger: "register CQRS services for {Domain}".
needs: [create-cqrs-commands, create-cqrs-queries, implement-cqrs-handlers, create-doctrine-repository]
produces: "DI YAML service registrations for repository and handler interface bindings"
---

# register-cqrs-services

## Instructions

1. Find the correct YAML file (check where other domain services are registered — typically per-domain or grouped by layer).
2. Register `{Domain}Repository` with `autowire: true` and `autoconfigure: true`.
3. Bind handler interfaces to their concrete implementations.
4. Handlers with `#[AsCommandHandler]` / `#[AsQueryHandler]` attributes are automatically registered with the Symfony Messenger bus — no manual tags needed. Just ensure `autoconfigure: true` is set.
5. Run `php bin/console debug:container | grep {domain}` to verify registration.

## Rules

Handler auto-registration via attributes is documented in [CQRS/CONTEXT.md](../../CONTEXT.md#handlers). Skill-specific reminders:

- Register handler interface → concrete class bindings so DI can resolve them
- Each handler must have exactly one attribute — duplicate registration causes errors
