# Oh My School - Development Milestones

## Project Goal

Build a dynamic student information lookup platform that allows administrators to create unlimited checking services without changing application source code.

---

# Milestone 0 — Project Foundation

## Objective

Prepare the project foundation, architecture, and core development environment.

## Deliverables

- Project repository initialized.
- Application architecture defined.
- Database connection configured.
- Authentication and authorization implemented.
- Admin panel prepared.
- Basic application layout implemented.
- Public website layout implemented.

## Acceptance Criteria

- Administrator can login securely.
- Admin and public routes are separated.
- Base navigation and layout are ready.
- Application is ready for feature development.

---

# Milestone 1 — Service Management

## Objective

Enable administrators to create and manage dynamic services.

## Features

### Service CRUD

Administrator can:

- Create service.
- Update service.
- Delete service.
- Enable or disable service.

### Service Properties

A service contains:

- Name.
- Slug.
- Icon (Phosphor Icon).
- Short description.
- Display order.
- Active status.
- Availability start date.
- Availability end date.
- Search mode:

    - OR mode.
    - AND mode.

- Maximum search attempts.
- Search lock duration.

## Acceptance Criteria

- Administrator can manage services without database changes.
- Slug must be unique.
- Expired services are automatically unavailable publicly.
- Services can be sorted based on configured order.

---

# Milestone 2 — Dynamic Field Builder

## Objective

Allow administrators to define the schema of each service.

## Features

### Field Management

Administrator can:

- Add field.
- Edit field.
- Delete field.
- Arrange field order.

### Field Properties

Each field has:

- Internal name.
- Display label.
- Data type.
- Required flag.
- Visible flag.
- Display order.

## Acceptance Criteria

- Every service can have different field structures.
- Field changes do not require database migration.
- Fields are stored dynamically.

---

# Milestone 3 — Identifier Configuration

## Objective

Configure how students search their data.

## Features

Administrator can:

- Select one or more fields as identifiers.
- Configure search mode:

    - OR: one identifier is enough.
    - AND: all identifiers must match.

## Business Rules

- Minimum identifier per service: 1.
- Maximum identifier per service: 3.
- Identifier must originate from existing fields.
- Identifier values must be unique within the same service.

## Technical Notes

Record storage uses dedicated indexed columns:

```
identifier_1
identifier_2
identifier_3
```

for high-performance lookup.

## Acceptance Criteria

- System prevents selecting more than 3 identifiers.
- Public search form adapts automatically based on configuration.
- Search logic follows selected mode.

---

# Milestone 4 — Data Import Engine

## Objective

Allow administrators to upload service records from Excel files.

## Features

### Import

Administrator can:

- Upload Excel file.
- Preview imported data.
- Validate data before saving.
- Execute import.

### Validation

System validates:

- File format.
- Column mapping.
- Required fields.
- Identifier availability.
- Duplicate identifiers.
- Data consistency.

### Data Storage

Records are stored as:

```
service_records
- service_id
- identifier_1
- identifier_2
- identifier_3
- data JSON
```

## Acceptance Criteria

- Invalid imports are rejected.
- Error messages include affected row numbers.
- Successfully imported records are searchable.

---

# Milestone 5 — Public Service Directory

## Objective

Provide a simple and modern homepage where students can access available services.

## Features

Display service cards containing:

- Icon.
- Service name.
- Description.

URL pattern:

```
/services/{slug}
```

## Acceptance Criteria

- Only active services are displayed.
- Expired services are hidden.
- Services are sorted correctly.
- UI is responsive and easy to understand.

---

# Milestone 6 — Public Search & Result Engine

## Objective

Allow students to search information based on configured identifiers.

## Features

### Dynamic Search Form

OR mode:

```
Enter NIS or NISN
[_______________]
```

AND mode:

```
NIS
[_______________]

Date of Birth
[_______________]
```

### Result View

Display only fields marked as visible.

## Acceptance Criteria

- Search response is fast.
- OR mode accepts any matching identifier.
- AND mode requires all identifiers.
- Hidden fields are never exposed publicly.

---

# Milestone 7 — Security & Search Limiting

## Objective

Protect public services from abuse and brute-force searching.

## Features

- Track every search request.
- Limit search attempts.
- Temporary lock after reaching limits.
- IP-based protection.

## Search Logs

Store:

- Service.
- Search input.
- Success or failure status.
- IP address.
- User agent.
- Timestamp.

## Acceptance Criteria

- Excessive searches are blocked.
- Lock duration follows service configuration.
- Search activity can be audited.

---

# Milestone 8 — Analytics Dashboard

## Objective

Provide insights about public usage.

## Metrics

Display:

- Total searches.
- Successful searches.
- Failed searches.
- Unique visitors.
- Daily statistics.
- Peak usage times.

## Acceptance Criteria

- Statistics are generated from search logs.
- Data can be filtered by service.
- Dashboard updates correctly.

---

# Milestone 9 — Export Engine

## Objective

Allow administrators to export service records.

## Supported Formats

- Excel (.xlsx)
- CSV

## Features

Export by:

- All records.
- Specific filters.
- Import period.

## Acceptance Criteria

- Exported data follows service schema.
- Hidden fields can be included or excluded based on configuration.
- Large exports are handled efficiently.

---

# Milestone 10 — QR Code & Sharing

## Objective

Make service access easier through shareable links.

## Features

Generate QR Code automatically for every service.

Example:

```
https://domain.com/services/kelulusan-2026
```

Usage:

- School banners.
- Official letters.
- Parent communication channels.

## Acceptance Criteria

- Every service has a unique QR Code.
- QR Code always points to the correct service URL.
- QR Code updates automatically when slug changes.

---

# Milestone 11 — Performance & Production Readiness

## Objective

Ensure the system is reliable for real-world usage.

## Performance

- Optimize database indexes.
- Test with large datasets.
- Optimize search queries.

## Reliability

- Implement proper error handling.
- Configure backups.
- Configure monitoring.
- Improve logging.

## Scalability

The system should handle:

- Hundreds of services.
- Millions of records.
- High traffic during announcement periods.

## Acceptance Criteria

- Lookup response remains under 1 second under normal conditions.
- System remains stable during peak traffic.
- No schema migration is required when creating new services.

---

# Definition of Done

The project is considered complete when:

- Administrators can create any type of checking service.
- Services have custom schemas.
- Services can define up to three identifiers.
- Students can search data through dynamic public forms.
- Data can be imported and exported safely.
- Search activity is protected, logged, and analyzed.
- The system performs efficiently with large datasets.

---

# Product Philosophy

One Platform. Unlimited School Checking Services.
