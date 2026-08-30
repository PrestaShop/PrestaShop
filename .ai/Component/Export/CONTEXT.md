# Export Component

## Purpose

Small infrastructure for writing tabular data to files in the back-office export directory. Currently only CSV output is implemented. Does not handle entity-specific data collection — callers build the `ExportableData` object themselves.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + CSV writer | `src/Core/Export/` |
| Only current consumer | `src/Core/SqlManager/Exporter/SqlRequestExporter.php` |

## Non-obvious patterns

- The only consumer today is `SqlRequestExporter` — the component is intentionally generic but lightly used
- `ExportDirectory` resolves `_PS_ADMIN_DIR_/export/` at runtime; output path is not configurable per-call

## Canonical examples

- `src/Core/Export/FileWriter/FileWriterInterface.php` + `src/Core/Export/FileWriter/ExportCsvFileWriter.php`
- `src/Core/Export/Data/ExportableData.php`

## Related

- [Import Component](../Import/CONTEXT.md) — counterpart for CSV ingestion
