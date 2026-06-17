# Oh My School — TODO

> Auto-generated dari [MILESTONE.md](./MILESTONE.md).  
> Centang task yang sudah selesai.

---

## Milestone 0 — Project Foundation

### Deliverables
- [x] Project repository initialized.
- [x] Application architecture defined.
- [x] Database connection configured.
- [x] Authentication and authorization implemented (Filament auth).
- [x] Admin panel prepared (Studio panel).
- [x] Basic application layout implemented (Filament layout).
- [x] Public website layout implemented (welcome page + service cards).

### Acceptance Criteria
- [x] Administrator can login securely.
- [x] Admin and public routes are separated.
- [x] Base navigation and layout are ready.
- [x] Application is ready for feature development.

---

## Milestone 1 — Service Management

### Service CRUD
- [x] Create service.
- [x] Update service.
- [x] Delete service.
- [x] Enable or disable service (ToggleColumn).

### Service Properties
- [x] Name.
- [x] Slug.
- [x] Icon (Phosphor Icon via Guava IconPicker / filafly).
- [x] Short description.
- [x] Display order.
- [x] Active status.
- [x] Availability start date.
- [x] Availability end date.
- [x] Search mode (OR / AND).
- [x] Maximum search attempts.
- [x] Search lock duration.

### Acceptance Criteria
- [x] Administrator can manage services without database changes.
- [x] Slug must be unique.
- [x] Expired services are automatically unavailable publicly (scopeActive).
- [x] Services can be sorted based on configured order (sort_order + defaultSort).

---

## Milestone 2 — Dynamic Field Builder

### Field Management
- [x] Add field.
- [x] Edit field.
- [x] Delete field.
- [x] Arrange field order.

### Field Properties
- [x] Internal name.
- [x] Display label.
- [x] Data type.
- [x] Required flag.
- [x] Visible flag.
- [x] Display order (via reorderable + sort_order).

### Acceptance Criteria
- [x] Every service can have different field structures.
- [x] Field changes do not require database migration.
- [x] Fields are stored dynamically.

---

## Milestone 3 — Identifier Configuration

### Features
- [x] Select one or more fields as identifiers (IdentifiersRelationManager).
- [x] Configure search mode (OR / AND) — already in ServiceForm.

### Business Rules
- [x] Minimum identifier per service: 1 (validated in IdentifiersRelationManager).
- [x] Maximum identifier per service: 3 (validated in IdentifiersRelationManager).
- [x] Identifier must originate from existing fields (Select options from service fields).
- [x] Identifier values must be unique within the same service (unique constraint on DB + validation rules).

### Acceptance Criteria
- [x] System prevents selecting more than 3 identifiers.
- [x] Public search form adapts automatically based on configuration (OR → single input, AND → multi input).
- [x] Search logic follows selected mode.

---

## Milestone 4 — Data Import Engine

### Import
- [x] Upload Excel file (.xlsx, .xls, .csv) via Filament page.
- [x] Preview imported data (table with validation status per row).
- [x] Validate data before saving (required fields, types, identifiers, duplicates).
- [x] Execute import (store valid rows, show error details for invalid rows).

### Validation
- [x] File format validation (mimes:xlsx,xls,csv, max 50MB).
- [x] Column mapping validation (headers matched to field name or label).
- [x] Required fields validation.
- [x] Identifier availability validation.
- [x] Duplicate identifier detection (in-file + against existing DB records).
- [x] Data consistency validation (number type, date format).

### Data Storage
- [x] service_records table with identifier_1/2/3 + JSON data column (done in M3).

### Acceptance Criteria
- [x] Invalid imports are rejected (errors per row + column mapping errors).
- [x] Error messages include affected row numbers.
- [x] Successfully imported records are searchable (via search engine from M3).

---

## Milestone 5 — Public Service Directory

### Features
- [x] Service cards with icon, name, and description (static version on homepage).
- [x] Dynamic listing dari database dengan controller.

### Acceptance Criteria
- [x] Only active services are displayed.
- [x] Expired services are hidden.
- [x] Services are sorted correctly.
- [x] UI is responsive and easy to understand.

---

## Milestone 6 — Public Search & Result Engine

### Dynamic Search Form
- [x] OR mode search form.
- [x] AND mode search form.
- [x] Dynamic form generation based on identifier configuration.

### Result View
- [x] Display only fields marked as visible.
- [x] Result rendering based on field configuration.

### Acceptance Criteria
- [x] Search response is fast.
- [x] OR mode accepts any matching identifier.
- [x] AND mode requires all identifiers.
- [x] Hidden fields are never exposed publicly.

---

## Milestone 7 — Security & Search Limiting

### Features
- [x] Track every search request.
- [x] Limit search attempts.
- [x] Temporary lock after reaching limits.
- [x] IP-based protection.

### Search Logs
- [x] search_logs table (service_id, input_value, is_success, ip_address, user_agent, created_at).
- [x] Log semua aktivitas pencarian.

### Acceptance Criteria
- [x] Excessive searches are blocked.
- [x] Lock duration follows service configuration.
- [x] Search activity can be audited.

---

## Milestone 8 — Analytics Dashboard

### Metrics
- [x] Total searches.
- [x] Successful searches.
- [x] Failed searches.
- [x] Unique visitors.
- [x] Daily statistics.
- [x] Peak usage times.

### Filament Widgets
- [x] Statistics widgets di admin panel.
- [x] Filter by service.

### Acceptance Criteria
- [x] Statistics are generated from search logs.
- [x] Data can be filtered by service.
- [x] Dashboard updates correctly.

---

## Milestone 9 — Export Engine

### Supported Formats
- [x] Excel (.xlsx) export.
- [x] CSV export.

### Features
- [x] Export all records.
- [x] Export with filters.
- [x] Export by import period.

### Acceptance Criteria
- [x] Exported data follows service schema.
- [x] Hidden fields can be included or excluded.
- [x] Large exports are handled efficiently.

---

## Milestone 10 — QR Code & Sharing

### Features
- [x] Auto-generate QR Code untuk setiap Service.
- [x] QR Code points to correct service URL.

### Acceptance Criteria
- [x] Every service has a unique QR Code.
- [x] QR Code always points to the correct service URL.
- [x] QR Code updates automatically when slug changes.

---

## Milestone 11 — Performance & Production Readiness

### Performance
- [x] Optimize database indexes.
- [x] Test with large datasets.
- [x] Optimize search queries.

### Reliability
- [x] Implement proper error handling.
- [x] Configure backups.
- [x] Configure monitoring.
- [x] Improve logging.

### Acceptance Criteria
- [x] Lookup response remains under 1 second under normal conditions.
- [x] System remains stable during peak traffic.
- [x] No schema migration is required when creating new services.
