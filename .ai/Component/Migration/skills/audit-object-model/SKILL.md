---
name: audit-object-model
description: >
  Read the legacy ObjectModel class for the domain and extract its database
  schema, field definitions, multilingual fields, and validation rules. This is
  the authoritative source for what columns the Doctrine repository must handle.
produces: "DB schema map, relation list, multilingual field list, validation rules from ObjectModel"
subagent: recommended
---

# audit-object-model

## Instructions

1. Open `classes/{Domain}.php` — read the `$definition` array fully.
2. Extract the `table` name — this is the primary DB table.
3. List every field in `$definition['fields']`: field name, type, validate rule, required flag, lang flag, shop flag.
4. Identify multilingual fields (`'lang' => true`) — these need `TranslatableType` in the form and multilingual handling in handlers.
5. Identify shop-scoped fields (`'shop' => true`) — these require multistore-aware repository methods.
6. If `classes/lang/{Domain}Lang.php` exists, read it and confirm the lang table structure.
7. Note any `hasMany` or `hasManyToMany` relations defined — these become sub-resources (handled by dedicated commands and repositories when the domain is non-trivial).
8. Record every `$validateFields()` rule — these inform ValueObject validation.
9. Output: structured schema map (table, columns, lang columns, shop columns, relations, validation rules).

## Rules

- Never modify ObjectModel files — read-only audit
- Flag any field with a custom validate method (`Validate::isXxx`) — these need a custom ValueObject
- Note `$identifier` field name — it becomes the `{Domain}Id` ValueObject
- Relations declared in ObjectModel that link to another entity's table are sub-resource candidates
