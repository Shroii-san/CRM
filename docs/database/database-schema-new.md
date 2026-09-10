# CRM Database Schema

## 1. Overview

Dokumen ini menggambarkan tabel database yang digunakan aplikasi.

### Domains

1. Authentication & Authorization
2. Client Management
3. Sales Pipeline
4. Task & Communication
5. Integration & Files
6. Audit
7. Reference Data
8. Application Configuration
9. Framework / Infrastructure

> **Kalender adalah fitur halaman, bukan sebuah entitas database.** fitur ini mengambil data dari tasks dan interactions.

---

# 2. Authentication & Authorization

## `users`

| Column             | Type Data    | NULLABLE | Default           | Constraints | Description             |
| ------------------ | ------------ | -------- | ----------------- | ----------- | ----------------------- |
| `id`               | INT          | NOT NULL | A_I               | PRIMARY KEY | Primary key             |
| `role_id`          | SMALLINT     | NOT NULL | -                 | FOREIGN KEY | FK to `roles.id`        |
| `name`             | VARCHAR(255) | NOT NULL | -                 | -           | User name               |
| `email`            | VARCHAR(255) | NULL     | NULL              | UNIQUE      | User email              |
| `phone`            | VARCHAR(20)  | NULL     | NULL              | UNIQUE      | User phone              |
| `password_hash`    | VARCHAR(255) | NOT NULL | -                 | -           | Password hash           |
| `is_active`        | BOOL         | NOT NULL | FALSE             | -           | User status             |
| `remember_token`   | VARCHAR(100) | NULL     | NULL              | -           | User cookie token       |
| `last_activity_at` | TIMESTAMPTZ  | NULL     | NULL              | -           | Last activity timestamp |
| `created_at`       | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp      |
| `updated_at`       | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp   |

## `roles`

| Column        | Type Data    | NULLABLE | Default | Constraints       | Description           |
| ------------- | ------------ | -------- | ------- | ----------------- | --------------------- |
| `id`          | SMALLINT     | NOT NULL | A_I     | PRIMARY KEY       | Primary key           |
| `name`        | VARCHAR(50)  | NOT NULL | -       | UNIQUE            | Role name             |
| `description` | VARCHAR(255) | NULL     | NULLL   | -                 | Role description      |
| `created_at`  | TIMESTAMPTZ  | NULL     | NULL    | CURRENT_TIMESTAMP | Creation timestamp    |
| `updated_at`  | TIMESTAMPTZ  | NULL     | NULL    | CURRENT_TIMESTAMP | Last update timestamp |

## `role_permissions`

| Column       | Type Data   | NULLABLE | Default           | Constraints | Description           |
| ------------ | ----------- | -------- | ----------------- | ----------- | --------------------- |
| `id`         | SMALLINT    | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `role_id`    | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `roles.id`      |
| `menu_id`    | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `menus.id`      |
| `can_view`   | BOOL        | NULL     | FALSE             | -           | View permission       |
| `can_create` | BOOL        | NULL     | FALSE             | -           | Create permission     |
| `can_update` | BOOL        | NULL     | FALSE             | -           | Update permission     |
| `can_delete` | BOOL        | NULL     | FALSE             | -           | Delete permission     |
| `can_assign` | BOOL        | NULL     | FALSE             | -           | Assignment permission |
| `created_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `menus`

| Column       | Type Data    | NULLABLE | Default           | Constraints | Description                 |
| ------------ | ------------ | -------- | ----------------- | ----------- | --------------------------- |
| `id`         | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key                 |
| `parent_id`  | SMALLINT     | NULL     | -                 | FOREIGN KEY | Parent menu, FK to menus.id |
| `icon_id`    | SMALLINT     | NULL     | -                 | FOREIGN KEY | FK to `menu_icons.id`       |
| `name`       | VARCHAR(50)  | NOT NULL | -                 | UNIQUE      | Menu name                   |
| `slug`       | VARCHAR(100) | NULL     | NULL              | UNIQUE      | Menu identifier             |
| `route`      | VARCHAR(50)  | NULL     | -                 | -           | Application route           |
| `position`   | SMALLINT     | NOT NULL | 0                 | -           | Display order               |
| `is_active`  | BOOL         | NOT NULL | FALSE             | -           | Menu status                 |
| `created_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp          |
| `updated_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp       |

## `menu_icons`

| Column       | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------ | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key           |
| `name`       | Icon name             |
| `class_name` | Class library name    |
| `created_at` | Creation timestamp    |
| `updated_at` | Last update timestamp |

## `role_menus`

Tabel penghubung untuk roles dan menus.

| Column    | Type Data        | NULLABLE | Default | Constraints | Description |
| --------- | ---------------- | -------- | ------- | ----------- | ----------- |
| `role_id` | FK to `roles.id` |
| `menu_id` | FK to `menus.id` |

---

# 3. Client Management

## `persons`

| Column       | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------ | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key           |
| `name`       | Full name             |
| `email`      | Email                 |
| `phone`      | Phone number          |
| `created_at` | Creation timestamp    |
| `updated_at` | Last update timestamp |

## `industries`

Menyimpan data tipe industri

contoh :

- Manufacturing
- Software
- Education
- Healthcare
- Finance

| Column        | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------- | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key           |
| `name`        | Industry name         |
| `description` | Industry description  |
| `status`      | Industry status       |
| `created_at`  | Creation timestamp    |
| `updated_at`  | Last update timestamp |

## `organizations`

| Column        | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------- | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key           |
| `industry_id` | FK to `industries.id` |
| `name`        | Organization name     |
| `phone`       | Organization phone    |
| `email`       | Organization email    |
| `website`     | Organization website  |
| `address`     | Organization address  |
| `province_id` | FK to `provinces.id`  |
| `regency_id`  | FK to `regencies.id`  |
| `district_id` | FK to `districts.id`  |
| `village_id`  | FK to `villages.id`   |
| `created_at`  | Creation timestamp    |
| `updated_at`  | Last update timestamp |

## `organization_contacts`

Tabel penghubung antara organizations dan persons.

| Column            | Type Data                           | NULLABLE | Default | Constraints | Description |
| ----------------- | ----------------------------------- | -------- | ------- | ----------- | ----------- |
| `id`              | Primary key                         |
| `organization_id` | FK to `organizations.id`            |
| `person_id`       | FK to `persons.id`                  |
| `job_title`       | Job title in the organization       |
| `is_primary`      | Whether this is the primary contact |
| `started_at`      | Relationship start date             |
| `ended_at`        | Relationship end date, nullable     |
| `created_at`      | Creation timestamp                  |
| `updated_at`      | Last update timestamp               |

## `organization_social_profiles`

| Column            | Type Data                | NULLABLE | Default | Constraints | Description |
| ----------------- | ------------------------ | -------- | ------- | ----------- | ----------- |
| `id`              | Primary key              |
| `organization_id` | FK to `organizations.id` |
| `platform`        | Social platform          |
| `username`        | Account username         |
| `url`             | Profile URL              |
| `created_at`      | Creation timestamp       |
| `updated_at`      | Last update timestamp    |

## `client_sources`

Sumber dari mana client datang, diatur oleh superadmin.

contoh :

- Website
- Referral
- Social Media
- Event
- Advertisement

| Column        | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------- | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key           |
| `name`        | Source name           |
| `description` | Source description    |
| `status`      | Source status         |
| `created_at`  | Creation timestamp    |
| `updated_at`  | Last update timestamp |

## `clients`

Merepresentasikan entah itu individu ataupun organisasi yang menjadi client/prospect

| Column            | Type Data                           | NULLABLE | Default | Constraints | Description |
| ----------------- | ----------------------------------- | -------- | ------- | ----------- | ----------- |
| `id`              | Primary key                         |
| `person_id`       | FK to `persons.id`, nullable        |
| `organization_id` | FK to `organizations.id`, nullable  |
| `source_id`       | FK to `client_sources.id`, nullable |
| `status`          | Client status                       |
| `created_at`      | Creation timestamp                  |
| `updated_at`      | Last update timestamp               |

Seorang client harus merepresentasikan hanya satu tipe, yaitu `person` atau `organization`.

---

# 4. Sales Pipeline

## `pipelines`

| Column        | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------- | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key           |
| `name`        | Pipeline name         |
| `description` | Pipeline description  |
| `status`      | Pipeline status       |
| `created_at`  | Creation timestamp    |
| `updated_at`  | Last update timestamp |

## `pipeline_stages`

| Column        | Type Data                        | NULLABLE | Default | Constraints | Description |
| ------------- | -------------------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key                      |
| `pipeline_id` | FK to `pipelines.id`             |
| `name`        | Stage name                       |
| `slug`        | Stage identifier                 |
| `description` | Stage description                |
| `position`    | Stage order                      |
| `is_terminal` | Whether this is a terminal stage |
| `status`      | Stage status                     |
| `created_at`  | Creation timestamp               |
| `updated_at`  | Last update timestamp            |

contoh stages :

```text
Pipeline Utama
Lead -> Contacted -> Negotiation -> Proposal -> Demo -> Payment -> Closed Won / Closed Lost
```

## `stage_task_templates`

| Column            | Type Data                        | NULLABLE | Default | Constraints | Description |
| ----------------- | -------------------------------- | -------- | ------- | ----------- | ----------- |
| `id`              | Primary key                      |
| `stage_id`        | FK to `pipeline_stages.id`       |
| `name`            | Task template name               |
| `description`     | Task template description        |
| `priority`        | Default priority                 |
| `due_offset_days` | Deadline offset from stage event |
| `is_required`     | Whether the task is required     |
| `is_active`       | Whether the template is active   |
| `created_at`      | Creation timestamp               |
| `updated_at`      | Last update timestamp            |

## `deals`

| Column              | Type Data                  | NULLABLE | Default | Constraints | Description |
| ------------------- | -------------------------- | -------- | ------- | ----------- | ----------- |
| `id`                | Primary key                |
| `name`              | Deal name                  |
| `client_id`         | FK to `clients.id`         |
| `pipeline_id`       | FK to `pipelines.id`       |
| `current_stage_id`  | FK to `pipeline_stages.id` |
| `currency`          | Deal currency              |
| `value`             | Deal value                 |
| `status`            | Deal status                |
| `priority`          | Deal priority              |
| `expected_close_at` | Expected closing date      |
| `actual_close_at`   | Actual closing date        |
| `description`       | Deal description           |
| `assigned_user_id`  | FK to `users.id`           |
| `created_at`        | Creation timestamp         |
| `updated_at`        | Last update timestamp      |

status yang akan dipakai :

```text
open
won
lost
cancelled
abandoned
```

## `deal_stage_histories`

| Column          | Type Data            | NULLABLE | Default | Constraints | Description |
| --------------- | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`            | Primary key          |
| `deal_id`       | FK to `deals.id`     |
| `from_stage_id` | Previous stage       |
| `to_stage_id`   | New stage            |
| `changed_by`    | FK to `users.id`     |
| `changed_at`    | Transition timestamp |

---

# 5. Task & Communication

## `tasks`

| Column                   | Type Data                                 | NULLABLE | Default | Constraints | Description |
| ------------------------ | ----------------------------------------- | -------- | ------- | ----------- | ----------- |
| `id`                     | Primary key                               |
| `name`                   | Task name                                 |
| `description`            | Task description                          |
| `client_id`              | FK to `clients.id`, nullable              |
| `deal_id`                | FK to `deals.id`, nullable                |
| `stage_task_template_id` | FK to `stage_task_templates.id`, nullable |
| `assigned_user_id`       | FK to `users.id`                          |
| `due_at`                 | Task deadline                             |
| `completed_at`           | Completion timestamp, nullable            |
| `status`                 | Task status                               |
| `priority`               | Task priority                             |
| `created_at`             | Creation timestamp                        |
| `updated_at`             | Last update timestamp                     |

status yang akan dipakai :

```text
planned
in_progress
completed
cancelled
```

## `task_reminders`

| Column       | Type Data                | NULLABLE | Default | Constraints | Description |
| ------------ | ------------------------ | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key              |
| `task_id`    | FK to `tasks.id`         |
| `remind_at`  | Reminder timestamp       |
| `status`     | Reminder status          |
| `sent_at`    | Sent timestamp, nullable |
| `created_at` | Creation timestamp       |
| `updated_at` | Last update timestamp    |

## `notifications`

| Column            | Type Data                      | NULLABLE | Default | Constraints | Description |
| ----------------- | ------------------------------ | -------- | ------- | ----------- | ----------- |
| `id`              | Primary key                    |
| `user_id`         | FK to `users.id`               |
| `type`            | Notification type              |
| `title`           | Notification title             |
| `message`         | Notification message           |
| `notifiable_type` | Referenced entity type         |
| `notifiable_id`   | Referenced entity ID           |
| `read_at`         | First-read timestamp, nullable |
| `created_at`      | Creation timestamp             |

Read state:

```text
read_at = NULL
-> unread

read_at != NULL
-> read
```

## `interactions`

| Column                    | Type Data                                  | NULLABLE | Default | Constraints | Description |
| ------------------------- | ------------------------------------------ | -------- | ------- | ----------- | ----------- |
| `id`                      | Primary key                                |
| `client_id`               | FK to `clients.id`                         |
| `deal_id`                 | FK to `deals.id`, nullable                 |
| `organization_contact_id` | FK to `organization_contacts.id`, nullable |
| `type`                    | Interaction type                           |
| `subject`                 | Interaction subject                        |
| `description`             | Planned/detail information                 |
| `summary`                 | Result summary, nullable                   |
| `status`                  | Interaction status                         |
| `start_at`                | Start timestamp                            |
| `end_at`                  | End timestamp                              |
| `performed_by`            | FK to `users.id`                           |
| `external_reference`      | External system reference, nullable        |
| `created_at`              | Creation timestamp                         |
| `updated_at`              | Last update timestamp                      |

type yang akan dipakai :

```text
call
meeting
email
chat
```

status yang akan dipakai :

```text
scheduled
completed
cancelled
```

Untuk client organization, `organization_contact_id` menjadi contact person.

## `notes`

| Column       | Type Data                    | NULLABLE | Default | Constraints | Description |
| ------------ | ---------------------------- | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key                  |
| `client_id`  | FK to `clients.id`, nullable |
| `deal_id`    | FK to `deals.id`, nullable   |
| `content`    | Note content                 |
| `note_type`  | Note category                |
| `created_by` | FK to `users.id`             |
| `created_at` | Creation timestamp           |
| `updated_at` | Last update timestamp        |

---

# 6. Integration & Files

## `external_conversations`

| Column                     | Type Data                  | NULLABLE | Default | Constraints | Description |
| -------------------------- | -------------------------- | -------- | ------- | ----------- | ----------- |
| `id`                       | Primary key                |
| `platform`                 | External platform          |
| `external_conversation_id` | External conversation ID   |
| `client_id`                | FK to `clients.id`         |
| `status`                   | Conversation status        |
| `last_interaction_at`      | Last interaction timestamp |
| `metadata`                 | Additional metadata        |
| `created_at`               | Creation timestamp         |
| `updated_at`               | Last update timestamp      |

## `attachments`

| Column              | Type Data                | NULLABLE | Default | Constraints | Description |
| ------------------- | ------------------------ | -------- | ------- | ----------- | ----------- |
| `id`                | Primary key              |
| `file_name`         | File name                |
| `description`       | File description         |
| `mime_type`         | MIME type                |
| `file_size`         | File size                |
| `storage_reference` | Storage/object reference |
| `uploaded_by`       | FK to `users.id`         |
| `uploaded_at`       | Upload timestamp         |
| `related_type`      | Related entity type      |
| `related_id`        | Related entity ID        |

---

# 7. Audit

## `activity_logs`

| Column        | Type Data                  | NULLABLE | Default | Constraints | Description |
| ------------- | -------------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key                |
| `user_id`     | FK to `users.id`, nullable |
| `action`      | Action performed           |
| `target_type` | Target entity type         |
| `target_id`   | Target entity ID           |
| `metadata`    | Additional event data      |
| `created_at`  | Activity timestamp         |

---

# 8. Reference Data

## `countries`

| Column | Type Data    | NULLABLE | Default | Constraints | Description |
| ------ | ------------ | -------- | ------- | ----------- | ----------- |
| `id`   | Primary key  |
| `name` | Country name |

## `provinces`

| Column       | Type Data            | NULLABLE | Default | Constraints | Description |
| ------------ | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key          |
| `country_id` | FK to `countries.id` |
| `name`       | Province name        |

## `regencies`

| Column        | Type Data            | NULLABLE | Default | Constraints | Description |
| ------------- | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key          |
| `province_id` | FK to `provinces.id` |
| `name`        | Regency/city name    |

## `districts`

| Column       | Type Data            | NULLABLE | Default | Constraints | Description |
| ------------ | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`         | Primary key          |
| `regency_id` | FK to `regencies.id` |
| `name`       | District name        |

## `villages`

| Column        | Type Data            | NULLABLE | Default | Constraints | Description |
| ------------- | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key          |
| `district_id` | FK to `districts.id` |
| `name`        | Village name         |

## `postal_codes`

| Column        | Type Data            | NULLABLE | Default | Constraints | Description |
| ------------- | -------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key          |
| `district_id` | FK to `districts.id` |
| `postal_code` | Postal code          |

---

# 9. Application Configuration

## `app_profiles`

| Column         | Type Data                   | NULLABLE | Default | Constraints | Description |
| -------------- | --------------------------- | -------- | ------- | ----------- | ----------- |
| `id`           | Primary key                 |
| `app_name`     | Application name            |
| `company_name` | Company name                |
| `logo`         | Logo reference              |
| `favicon`      | Favicon reference           |
| `email`        | Application/company email   |
| `phone`        | Application/company phone   |
| `address`      | Application/company address |
| `website`      | Website                     |
| `description`  | Application description     |
| `created_at`   | Creation timestamp          |
| `updated_at`   | Last update timestamp       |

## `app_settings`

| Column        | Type Data             | NULLABLE | Default | Constraints | Description |
| ------------- | --------------------- | -------- | ------- | ----------- | ----------- |
| `id`          | Primary key           |
| `key`         | Setting key           |
| `value`       | Setting value         |
| `type`        | Value type            |
| `description` | Setting description   |
| `created_at`  | Creation timestamp    |
| `updated_at`  | Last update timestamp |

---

# 10. Framework / Infrastructure

Tabel-tabel berikut akan ada di database tapi bukan entitas kebutuhan untuk CRMs.

## `migrations`

| Column      | Type Data              | NULLABLE | Default | Constraints | Description |
| ----------- | ---------------------- | -------- | ------- | ----------- | ----------- |
| `id`        | Primary key            |
| `migration` | Migration name         |
| `batch`     | Migration batch number |

## `password_reset_tokens`

| Column       | Type Data                | NULLABLE | Default | Constraints | Description |
| ------------ | ------------------------ | -------- | ------- | ----------- | ----------- |
| `email`      | User email               |
| `token`      | Password reset token     |
| `created_at` | Token creation timestamp |

## `sessions`

| Column          | Type Data               | NULLABLE | Default | Constraints | Description |
| --------------- | ----------------------- | -------- | ------- | ----------- | ----------- |
| `id`            | Session identifier      |
| `user_id`       | Related user, nullable  |
| `ip_address`    | Client IP address       |
| `user_agent`    | Client user agent       |
| `payload`       | Session payload         |
| `last_activity` | Last activity timestamp |

---
