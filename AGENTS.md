# PrestaShop — AI Agent Instructions

This project uses a centralized AI context architecture. All conventions, patterns, and rules are maintained in the `.ai/` folder.

## Getting started

1. Read [.ai/CONTEXT.md](.ai/CONTEXT.md) for project-wide rules, architecture, and an index of all domain/component contexts.
2. Read [.ai/STRUCTURE.md](.ai/STRUCTURE.md) to understand how the `.ai/` folder is organized.
3. When working on a specific domain or component, read the corresponding `CONTEXT.md` file listed in the index.

## Key rules

- Follow the CQRS pattern for all new back-office features
- No business logic in controllers — delegate to Handlers
- No legacy ObjectModel in new code — use Doctrine entities
- All PHP files must use `declare(strict_types=1);`
- Classes are `final` by default
