# Oh My School

## Product Requirement Document (PRD)

### Version

1.0

### Status

Draft

---

# 1. Product Overview

## Product Name

Oh My School

## Vision

Oh My School is a Dynamic Student Information Lookup Engine platform that allows schools to create various self-service lookup services flexibly without needing to build a separate application for every requirement.

The system is designed so that administrators can create new services, define data structures, import data, and provide public pages for students to look up information independently.

---

# 2. Background

Previously, the school had several static lookup applications.

Examples:

- Graduation Status Checker.
- Exam Score Checker.
- New Student Admission (PPDB) Result Checker.

Each application had limitations:

- Only supported one type of service.
- Field structure was hardcoded in the source code.
- Search identifier was fixed (e.g. only Student ID/NIS).
- Whenever a new service was needed, the application had to be forked and modified again.

This approach caused:

- Project duplication.
- Difficult maintenance.
- Slow development.
- Risk of inconsistency between systems.

---

# 3. Goals

Build a single platform that can support various lookup services such as:

- Graduation status.
- Exam results.
- Admission (PPDB) selection results.
- Grade promotion.
- Class assignment.
- Administrative information.
- Other services that require data lookup based on a unique identifier.

All services must be creatable through the administrator panel without any source code changes.

---

# 4. Core Concepts

## 4.1 Service

A Service is an information service that can be accessed by students through a public page.

Examples:

- Graduation Status 2026.
- Even Semester Exam Results.
- 2026 Admission (PPDB) Selection Results.

Each Service has the following properties:

| Property                | Description                        |
| ----------------------- | ---------------------------------- |
| Name                    | Service name                       |
| Slug                    | Unique service URL                 |
| Icon                    | Icon name from Phosphor Icons      |
| Description             | Short service description          |
| Is Active               | Active/inactive status             |
| Start Date              | Date the service becomes available |
| End Date                | Date the service ends              |
| Sort Order              | Display order on the public page   |
| Search Attempt Limit    | Maximum number of search attempts  |
| Search Attempt Duration | Attempt restriction period         |

---

## 4.2 Field

A Field defines the data structure of a Service.

Example Fields for the Graduation Service:

| Name       | Label             | Type      |
| ---------- | ----------------- | --------- |
| nis        | Student ID (NIS)  | Text      |
| nama       | Student Name      | Text      |
| kelas      | Class             | Text      |
| status     | Graduation Status | Text      |
| keterangan | Message           | Long Text |

Each Field has the following configuration:

| Property   | Description                          |
| ---------- | ------------------------------------ |
| Name       | Internal field name                  |
| Label      | Displayed label                      |
| Type       | Data type                            |
| Required   | Whether it is required during import |
| Visible    | Whether it is shown to public users  |
| Sort Order | Display order                        |

---

## 4.3 Identifier

An Identifier is a Field used for the data lookup process performed by students.

Examples:

- Student ID (NIS).
- National Student ID (NISN).
- Registration Number.
- Date of Birth.

### Identifier Rules

- Each Service must have at least 1 identifier.
- Each Service can have a maximum of 3 identifiers.
- Identifiers are selected from Fields that have already been created.
- An identifier must have a unique value within a single Service.
- Identifiers support two search modes:

### OR Mode

The search succeeds if at least one identifier matches.

Example:

Identifiers:

- NIS
- NISN

Public display:

```
Enter NIS or NISN

[________________]
```

---

### AND Mode

The search succeeds only if all identifiers match.

Example:

Identifiers:

- NIS
- Date of Birth

Public display:

```
NIS
[_______________]

Date of Birth
[_______________]
```

All inputs are required.

---

## 4.4 Record

A Record is imported data owned by a Service.

A Record stores dynamic data in JSON format.

Example:

```json
{
    "nis": "12345",
    "nama": "Ahmad Fauzi",
    "kelas": "XII IPA 1",
    "status": "LULUS"
}
```

For search performance purposes, each Record also has up to three separate identifier columns:

```
identifier_1
identifier_2
identifier_3
```

These columns are used for database indexing and do not replace the main JSON data.

---

# 5. Public User Flow

## Landing Page

The main page displays the list of available Services.

Each Service displays:

- Icon.
- Name.
- Short description.

When selected, the user is redirected to:

```
/services/{slug}
```

---

## Service Page

The search page adapts to the Service configuration.

OR Mode example:

```
Enter NIS or NISN

[____________]

[ Search ]
```

AND Mode example:

```
NIS

[____________]

Date of Birth

[____________]

[ Search ]
```

---

## Result Page

If data is found, the system only displays Fields with Visible status.

Example:

```
Name:
Ahmad Fauzi

Class:
XII IPA 1

Status:
PASSED
```

---

# 6. Data Import

Administrators can import Excel files.

The import process must perform the following validations:

- The file must be in a supported Excel format.
- Column headers must match the defined Fields.
- Required Fields must be present.
- Identifier data must be present.
- There must be no duplicate identifiers within a single Service.
- Data types must match the Field configuration.

If an error occurs, the system must provide a clear error report along with the affected row number.

---

# 7. QR Code

Each Service automatically has a QR Code generated from its Service URL.

Example:

```
https://domain.com/services/kelulusan-2026
```

The QR Code can be used for:

- School banners.
- Announcement letters.
- Parent communication groups.

---

# 8. Analytics

The system must log search activity.

Analytics data includes:

- Total searches.
- Number of successful searches.
- Number of failed searches.
- Number of unique visitors.
- Daily search statistics.
- Peak search times.

---

# 9. Security

The system must have protection against excessive searches.

Security features:

- Rate limiting based on IP address.
- Maximum search attempts configurable per Service.
- Temporary blocking once the search limit is reached.
- Logging of search activity for audit purposes.

---

# 10. Export Data

Administrators can export Records.

Supported formats:

- Excel (.xlsx).
- CSV.

Export can be performed based on:

- All data.
- Specific filters.
- Import date range.

---

# 11. Database Design

## services

Stores service configuration.

```
id
name
slug
icon
description
is_active
start_date
end_date
search_mode (OR / AND)
max_attempt
attempt_duration
sort_order
created_at
updated_at
```

---

## service_fields

Stores the Field schema.

```
id
service_id
name
label
type
required
visible
sort_order
created_at
updated_at
```

---

## service_identifiers

Mapping of identifiers used by a Service.

```
id
service_id
field_id
identifier_order
created_at
```

Maximum records: 3 per Service.

---

## service_records

Stores student data.

```
id
service_id

identifier_1
identifier_2
identifier_3

data JSON

created_at
updated_at
```

Recommended index:

```
INDEX(service_id, identifier_1)
INDEX(service_id, identifier_2)
INDEX(service_id, identifier_3)
```

---

## search_logs

Stores search history.

```
id
service_id
input_value
is_success
ip_address
user_agent
created_at
```

---

# 12. Non Functional Requirements

## Performance

- Searches must have a response time of less than 1 second under normal usage.
- Identifiers must use database indexing.
- The system must be able to handle hundreds of thousands to millions of Records.

## Scalability

The system must be able to add new Services without any source code changes.

## Maintainability

Changes to a Service's schema must not require a database migration.

---

# 13. Future Possibilities

Features that can be developed in future versions:

- Custom result page templates.
- API integration with academic systems.
- Automatic notifications or announcements.
- Scheduled data import from external systems.

---

# 14. Product Philosophy

One Platform. Unlimited School Checking Services.

Oh My School is not a graduation checker or a score checker application, but rather an engine that allows schools to dynamically build a variety of student information services.
