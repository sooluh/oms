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
- [ ] Add field.
- [ ] Edit field.
- [ ] Delete field.
- [ ] Arrange field order.

### Field Properties
- [ ] Internal name.
- [ ] Display label.
- [ ] Data type.
- [ ] Required flag.
- [ ] Visible flag.
- [ ] Display order.

### Acceptance Criteria
- [ ] Every service can have different field structures.
- [ ] Field changes do not require database migration.
- [ ] Fields are stored dynamically.

---

## Milestone 3 — Identifier Configuration

### Features
- [ ] Select one or more fields as identifiers.
- [ ] Configure search mode (OR / AND).

### Business Rules
- [ ] Minimum identifier per service: 1.
- [ ] Maximum identifier per service: 3.
- [ ] Identifier must originate from existing fields.
- [ ] Identifier values must be unique within the same service.

### Acceptance Criteria
- [ ] System prevents selecting more than 3 identifiers.
- [ ] Public search form adapts automatically based on configuration.
- [ ] Search logic follows selected mode.

---

## Milestone 4 — Data Import Engine

### Import
- [ ] Upload Excel file.
- [ ] Preview imported data.
- [ ] Validate data before saving.
- [ ] Execute import.

### Validation
- [ ] File format validation.
- [ ] Column mapping validation.
- [ ] Required fields validation.
- [ ] Identifier availability validation.
- [ ] Duplicate identifier detection.
- [ ] Data consistency validation.

### Data Storage
- [ ] service_records table with identifier_1/2/3 + JSON data column.

### Acceptance Criteria
- [ ] Invalid imports are rejected.
- [ ] Error messages include affected row numbers.
- [ ] Successfully imported records are searchable.

---

## Milestone 5 — Public Service Directory

### Features
- [x] Service cards with icon, name, and description (static version on homepage).
- [ ] Dynamic listing dari database dengan controller.

### Acceptance Criteria
- [ ] Only active services are displayed.
- [ ] Expired services are hidden.
- [ ] Services are sorted correctly.
- [ ] UI is responsive and easy to understand.

---

## Milestone 6 — Public Search & Result Engine

### Dynamic Search Form
- [ ] OR mode search form.
- [ ] AND mode search form.
- [ ] Dynamic form generation based on identifier configuration.

### Result View
- [ ] Display only fields marked as visible.
- [ ] Result rendering based on field configuration.

### Acceptance Criteria
- [ ] Search response is fast.
- [ ] OR mode accepts any matching identifier.
- [ ] AND mode requires all identifiers.
- [ ] Hidden fields are never exposed publicly.

---

## Milestone 7 — Security & Search Limiting

### Features
- [ ] Track every search request.
- [ ] Limit search attempts.
- [ ] Temporary lock after reaching limits.
- [ ] IP-based protection.

### Search Logs
- [ ] search_logs table (service_id, input, is_success, ip_address, user_agent, timestamp).
- [ ] Log semua aktivitas pencarian.

### Acceptance Criteria
- [ ] Excessive searches are blocked.
- [ ] Lock duration follows service configuration.
- [ ] Search activity can be audited.

---

## Milestone 8 — Analytics Dashboard

### Metrics
- [ ] Total searches.
- [ ] Successful searches.
- [ ] Failed searches.
- [ ] Unique visitors.
- [ ] Daily statistics.
- [ ] Peak usage times.

### Filament Widgets
- [ ] Statistics widgets di admin panel.
- [ ] Filter by service.

### Acceptance Criteria
- [ ] Statistics are generated from search logs.
- [ ] Data can be filtered by service.
- [ ] Dashboard updates correctly.

---

## Milestone 9 — Export Engine

### Supported Formats
- [ ] Excel (.xlsx) export.
- [ ] CSV export.

### Features
- [ ] Export all records.
- [ ] Export with filters.
- [ ] Export by import period.

### Acceptance Criteria
- [ ] Exported data follows service schema.
- [ ] Hidden fields can be included or excluded.
- [ ] Large exports are handled efficiently.

---

## Milestone 10 — QR Code & Sharing

### Features
- [ ] Auto-generate QR Code untuk setiap Service.
- [ ] QR Code points to correct service URL.

### Acceptance Criteria
- [ ] Every service has a unique QR Code.
- [ ] QR Code always points to the correct service URL.
- [ ] QR Code updates automatically when slug changes.

---

## Milestone 11 — Performance & Production Readiness

### Performance
- [ ] Optimize database indexes.
- [ ] Test with large datasets.
- [ ] Optimize search queries.

### Reliability
- [ ] Implement proper error handling.
- [ ] Configure backups.
- [ ] Configure monitoring.
- [ ] Improve logging.

### Acceptance Criteria
- [ ] Lookup response remains under 1 second under normal conditions.
- [ ] System remains stable during peak traffic.
- [ ] No schema migration is required when creating new services.
