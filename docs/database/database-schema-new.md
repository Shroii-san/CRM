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
| `id`               | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key             |
| `role_id`          | SMALLINT     | NOT NULL | -                 | FOREIGN KEY | FK to `roles.id`        |
| `name`             | VARCHAR(255) | NOT NULL | -                 | -           | User name               |
| `email`            | VARCHAR(255) | NULL     | NULL              | UNIQUE      | User email              |
| `phone`            | VARCHAR(20)  | NULL     | NULL              | UNIQUE      | User phone              |
| `password_hash`    | VARCHAR(255) | NOT NULL | -                 | -           | Password hash           |
| `is_active`        | BOOL         | NOT NULL | TRUE              | -           | User status             |
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
| `is_active`  | BOOL         | NOT NULL | TRUE              | -           | Menu status                 |
| `created_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp          |
| `updated_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp       |

## `menu_icons`

| Column       | Type Data   | NULLABLE | Default           | Constraints | Description           |
| ------------ | ----------- | -------- | ----------------- | ----------- | --------------------- |
| `id`         | SMALLINT    | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `name`       | VARCHAR(50) | NOT NULL | -                 | UNIQUE      | Icon name             |
| `class_name` | VARCHAR(50) | NOT NULL | -                 | -           | Class library name    |
| `created_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `role_menus`

Tabel penghubung untuk roles dan menus.

| Column    | Type Data | NULLABLE | Default | Constraints | Description      |
| --------- | --------- | -------- | ------- | ----------- | ---------------- |
| `role_id` | SMALLINT  | NOT NULL | -       | FOREIGN KEY | FK to `roles.id` |
| `menu_id` | SMALLINT  | NOT NULL | -       | FOREIGN KEY | FK to `menus.id` |

---

# 3. Client Management

## `persons`

| Column       | Type Data    | NULLABLE | Default           | Constraints | Description           |
| ------------ | ------------ | -------- | ----------------- | ----------- | --------------------- |
| `id`         | INT          | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `name`       | VARCHAR(255) | NOT NULL | -                 | -           | Full name             |
| `email`      | VARCHAR(255) | NULL     | NULL              | UNIQUE      | Email                 |
| `phone`      | VARCHAR(20)  | NULL     | NULL              | UNIQUE      | Phone number          |
| `created_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at` | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `industries`

Menyimpan data tipe industri

contoh :

- Manufacturing
- Software
- Education
- Healthcare
- Finance

| Column        | Type Data    | NULLABLE | Default           | Constraints | Description           |
| ------------- | ------------ | -------- | ----------------- | ----------- | --------------------- |
| `id`          | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `name`        | VARCHAR(100) | NOT NULL | -                 | UNIQUE      | Industry name         |
| `description` | VARCHAR(255) | NULL     | NULL              | -           | Industry description  |
| `is_active`   | BOOL         | NOT NULL | TRUE              | -           | Industry status       |
| `created_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `organizations`

| Column        | Type Data    | NULLABLE | Default | Constraints | Description           |
| ------------- | ------------ | -------- | ------- | ----------- | --------------------- |
| `id`          | INT          | NOT NULL | A_I     | PRIMARY KEY | Primary key           |
| `industry_id` | SMALLINT     | NOT NULL | -       | FOREIGN KEY | FK to `industries.id` |
| `name`        | VARCHAR(255) | NOT NULL | -       | UNIQUE      | Organization name     |
| `email`       | VARCHAR(255) | NULL     | NULL    | -           | Organization email    |
| `phone`       | VARCHAR(20)  | NULL     | NULL    | -           | Organization phone    |
| `website`     | VARCHAR(255) | NULL     | NULL    | -           | Organization website  |
| `address`     | TEXT         | NULL     | NULL    | -           | Organization address  |
| `province_id` | SMALLINT     | NULL     | NULL    | FOREIGN KEY | FK to `provinces.id`  |
| `regency_id`  | SMALLINT     | NULL     | NULL    | FOREIGN KEY | FK to `regencies.id`  |
| `district_id` | INT          | NULL     | NULL    | FOREIGN KEY | FK to `districts.id`  |
| `village_id`  | INT          | NULL     | NULL    | FOREIGN KEY | FK to `villages.id`   |
| `created_at`  | TIMESTAMPTZ  | NULL     | NULL    | -           | Creation timestamp    |
| `updated_at`  | TIMESTAMPTZ  | NULL     | NULL    | -           | Last update timestamp |

## `organization_contacts`

Tabel penghubung antara organizations dan persons.

| Column            | Type Data    | NULLABLE | Default           | Constraints | Description                         |
| ----------------- | ------------ | -------- | ----------------- | ----------- | ----------------------------------- |
| `id`              | INT          | NOT NULL | A_I               | PRIMARY KEY | Primary key                         |
| `organization_id` | INT          | NOT NULL | -                 | FOREIGN KEY | FK to `organizations.id`            |
| `person_id`       | INT          | NOT NULL | -                 | FOREIGN KEY | FK to `persons.id`                  |
| `job_title`       | VARCHAR(255) | NULL     | NULL              | -           | Job title in the organization       |
| `is_primary`      | BOOL         | NOT NULL | TRUE              | -           | Whether this is the primary contact |
| `started_at`      | DATE         | NULL     | NULL              | -           | Relationship start date             |
| `ended_at`        | DATE         | NULL     | NULL              | -           | Relationship end date, nullable     |
| `created_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp                  |
| `updated_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp               |

## `organization_social_profiles`

| Column            | Type Data    | NULLABLE | Default           | Constraints | Description              |
| ----------------- | ------------ | -------- | ----------------- | ----------- | ------------------------ |
| `id`              | INT          | NOT NULL | A_I               | PRIMARY KEY | Primary key              |
| `organization_id` | INT          | NOT NULL | -                 | FOREIGN KEY | FK to `organizations.id` |
| `platform`        | VARCHAR(50)  | NOT NULL | -                 | -           | Social platform          |
| `username`        | VARCHAR(255) | NOT NULL | -                 | -           | Account username         |
| `url`             | VARCHAR(255) | NULL     | NULL              | UNIQUE      | Profile URL              |
| `created_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp       |
| `updated_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp    |

## `client_sources`

Sumber dari mana client datang, diatur oleh superadmin.

contoh :

- Website
- Referral
- Social Media
- Event
- Advertisement

| Column        | Type Data    | NULLABLE | Default           | Constraints | Description           |
| ------------- | ------------ | -------- | ----------------- | ----------- | --------------------- |
| `id`          | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `name`        | VARCHAR(50)  | NOT NULL | -                 | UNIQUE      | Source name           |
| `description` | VARCHAR(255) | NULL     | -                 | NULL        | Source description    |
| `is_active`   | BOOL         | NOT NULL | TRUE              | -           | Source status         |
| `created_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `clients`

Merepresentasikan entah itu individu ataupun organisasi yang menjadi client/prospect

| Column            | Type Data  | NULLABLE | Default           | Constraints | Description                         |
| ----------------- | ---------- | -------- | ----------------- | ----------- | ----------------------------------- |
| `id`              | INT        | NOT NULL | A_I               | PRIMARY KEY | Primary key                         |
| `person_id`       | INT        | NULL     | -                 | FOREIGN KEY | FK to `persons.id`, nullable        |
| `organization_id` | INT        | NULL     | -                 | FOREIGN KEY | FK to `organizations.id`, nullable  |
| `source_id`       | SMALLINT   | NOT NULL | -                 | FOREIGN KEY | FK to `client_sources.id`, nullable |
| `is_active`       | BOOL       | NOT NULL | TRUE              | -           | Client status                       |
| `created_at`      | TIMESTAMPZ | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp                  |
| `updated_at`      | TIMESTAMPZ | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp               |

Seorang client harus merepresentasikan hanya satu tipe, yaitu `person` atau `organization`.

---

# 4. Sales Pipeline

## `pipelines`

| Column        | Type Data    | NULLABLE | Default           | Constraints | Description           |
| ------------- | ------------ | -------- | ----------------- | ----------- | --------------------- |
| `id`          | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key           |
| `name`        | VARCHAR(50)  | NOT NULL | -                 | UNIQUE      | Pipeline name         |
| `description` | VARCHAR(255) | NULL     | NULL              | -           | Pipeline description  |
| `is_active`   | BOOL         | NOT NULL | TRUE              | -           | Pipeline status       |
| `created_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp    |
| `updated_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp |

## `pipeline_stages`

| Column        | Type Data    | NULLABLE | Default           | Constraints | Description                      |
| ------------- | ------------ | -------- | ----------------- | ----------- | -------------------------------- |
| `id`          | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY | Primary key                      |
| `pipeline_id` | SMALLINT     | NOT NULL | -                 | FOREIGN KEY | FK to `pipelines.id`             |
| `name`        | VARCHAR(50)  | NOT NULL | -                 | -           | Stage name                       |
| `slug`        | VARCHAR(255) | NULL     | NULL              | -           | Stage identifier                 |
| `description` | VARCHAR(255) | NULL     | NULL              | -           | Stage description                |
| `position`    | SMALLINT     | NOT NULL | 0                 | -           | Stage order                      |
| `is_terminal` | BOOL         | NOT NULL | FALSE             | -           | Whether this is a terminal stage |
| `is_active`   | BOOL         | NOT NULL | TRUE              | -           | Stage status                     |
| `created_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp               |
| `updated_at`  | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp            |

contoh stages :

```text
Pipeline Utama
Lead -> Contacted -> Negotiation -> Proposal -> Demo -> Payment -> Closed Won / Closed Lost
```

## `stage_task_templates`

| Column            | Type Data    | NULLABLE | Default           | Constraints               | Description                      |
| ----------------- | ------------ | -------- | ----------------- | ------------------------- | -------------------------------- |
| `id`              | SMALLINT     | NOT NULL | A_I               | PRIMARY KEY               | Primary key                      |
| `stage_id`        | SMALLINT     | NOT NULL | -                 | FOREIGN KEY               | FK to `pipeline_stages.id`       |
| `name`            | VARCHAR(255) | NOT NULL | -                 |                           | Task template name               |
| `description`     | VARCHAR(255) | NULL     | -                 | -                         | Task template description        |
| `priority`        | VARCHAR(10)  | NOT NULL | 'L'               | CHECK('L', 'M', 'H', 'U') | Default priority                 |
| `due_offset_days` | SMALLINT     | NULL     | -                 | -                         | Deadline offset from stage event |
| `is_required`     | BOOL         | NOT NULL | FALSE             | -                         | Whether the task is required     |
| `is_active`       | BOOL         | NOT NULL | TRUE              | -                         | Whether the template is active   |
| `created_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                         | Creation timestamp               |
| `updated_at`      | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                         | Last update timestamp            |

## `deals`

| Column              | Type Data             | NULLABLE | Default           | Constraints                    | Description                |
| ------------------- | --------------------- | -------- | ----------------- | ------------------------------ | -------------------------- |
| `id`                | INT                   | NOT NULL | A_I               | PRIMARY KEY                    | Primary key                |
| `name`              | VARCHAR(255)          | NOT NULL | -                 | -                              | Deal name                  |
| `client_id`         | INT                   | NOT NULL | -                 | FOREIGN KEY                    | FK to `clients.id`         |
| `pipeline_id`       | SMALLINT              | NOT NULL | -                 | FOREIGN KEY                    | FK to `pipelines.id`       |
| `current_stage_id`  | SMALLINT              | NOT NULL | -                 | FOREIGN KEY                    | FK to `pipeline_stages.id` |
| `currency`          | CHAR(3)               | NOT NULL | 'IDR'             | -                              | Deal currency              |
| `value`             | DECIMAL/NUMERIC(15,2) | NOT NULL | -                 | -                              | Deal value                 |
| `is_active`         | BOOL                  | NOT NULL | TRUE              | -                              | Deal status                |
| `status`            | VARCHAR(20)           | NOT NULL | 'open'            | CHECK('O', 'W', 'L', 'C', 'A') | Deal priority              |
| `priority`          | VARCHAR(10)           | NOT NULL | 'L'               | CHECK('L', 'M', 'H', 'U')      | Deal priority              |
| `expected_close_at` | DATE                  | NOT NULL | -                 | -                              | Expected closing date      |
| `actual_close_at`   | DATE                  | NULL     | -                 | -                              | Actual closing date        |
| `description`       | VARCHAR(255)          | NULL     | -                 | -                              | Deal description           |
| `assigned_user_id`  | SMALLINT              | NOT NULL | -                 | FOREIGN KEY                    | FK to `users.id`           |
| `created_at`        | TIMESTAMPTZ           | NULL     | CURRENT_TIMESTAMP | -                              | Creation timestamp         |
| `updated_at`        | TIMESTAMPTZ           | NULL     | CURRENT_TIMESTAMP | -                              | Last update timestamp      |

status yang akan dipakai :

```text
open
won
lost
cancelled
abandoned
```

## `deal_stage_histories`

| Column          | Type Data   | NULLABLE | Default           | Constraints | Description                |
| --------------- | ----------- | -------- | ----------------- | ----------- | -------------------------- |
| `id`            | INT         | NOT NULL | A_I               | PRIMARY KEY | Primary key                |
| `deal_id`       | INT         | NOT NULL | -                 | FOREIGN KEY | FK to `deals.id`           |
| `from_stage_id` | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `pipeline_stages.id` |
| `to_stage_id`   | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `pipeline_stages.id` |
| `changed_by`    | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `users.id`           |
| `changed_at`    | TIMESTAMPTZ | NOT NULL | CURRENT_TIMESTAMP | -           | Transition timestamp       |

---

# 5. Task & Communication

## `tasks`

| Column                   | Type Data    | NULLABLE | Default           | Constraints                  | Description                               |
| ------------------------ | ------------ | -------- | ----------------- | ---------------------------- | ----------------------------------------- |
| `id`                     | INT          | NOT NULL | A_I               | PRIMARY KEY                  | Primary key                               |
| `name`                   | VARCHAR(255) | NOT NULL | -                 | -                            | Task name                                 |
| `description`            | VARCHAR(255) | NULL     | -                 | -                            | Task description                          |
| `client_id`              | INT          | NULL     | -                 | FOREIGN KEY                  | FK to `clients.id`, nullable              |
| `deal_id`                | INT          | NULL     | -                 | FOREIGN KEY                  | FK to `deals.id`, nullable                |
| `stage_task_template_id` | SMALLINT     | NULL     | -                 | FOREIGN KEY                  | FK to `stage_task_templates.id`, nullable |
| `assigned_user_id`       | SMALLINT     | NOT NULL | -                 | FOREIGN KEY                  | FK to `users.id`                          |
| `due_at`                 | DATE         | NOT NULL | -                 | -                            | Task deadline                             |
| `completed_at`           | TIMESTAMPTZ  | NULL     | NULL              | -                            | Completion timestamp, nullable            |
| `status`                 | VARCHAR(20)  | NOT NULL | 'planned'         | CHECK('p', 'ip', 'cp', 'cl') | Task status                               |
| `priority`               | VARCHAR(10)  | NOT NULL | 'L'               | CHECK('L', 'M', 'H', 'U')    | Task priority                             |
| `created_at`             | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                            | Creation timestamp                        |
| `updated_at`             | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                            | Last update timestamp                     |

status yang akan dipakai :

```text
planned
in_progress
completed
cancelled
```

## `task_reminders`

| Column       | Type Data   | NULLABLE | Default           | Constraints | Description              |
| ------------ | ----------- | -------- | ----------------- | ----------- | ------------------------ |
| `id`         | INT         | NOT NULL | A_I               | PRIMARY KEY | Primary key              |
| `task_id`    | INT         | NOT NULL | -                 | FOREIGN KEY | FK to `tasks.id`         |
| `remind_at`  | TIMESTAMPTZ | NOT NULL | -                 | -           | Reminder timestamp       |
| `is_active`  | BOOL        | NOT NULL | TRUE              | -           | Reminder status          |
| `sent_at`    | TIMESTAMPTZ | NULL     | NULL              | -           | Sent timestamp, nullable |
| `created_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp       |
| `updated_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp    |

## `notifications`

| Column            | Type Data    | NULLABLE | Default | Constraints | Description                             |
| ----------------- | ------------ | -------- | ------- | ----------- | --------------------------------------- |
| `id`              | INT          | NOT NULL | A_I     | PRIMARY KEY | Primary key                             |
| `user_id`         | SMALLINT     | NOT NULL | -       | FOREIGN KEY | FK to `users.id`                        |
| `type`            | VARCHAR(100) | NOT NULL | -       | -           | Notification type                       |
| `title`           | VARCHAR(255) | NOT NULL | -       | -           | Notification title                      |
| `message`         | VARCHAR(255) | NOT NULL | -       | -           | Notification message                    |
| `notifiable_type` | VARCHAR(255) | NULL     | -       | -           | Referenced entity type                  |
| `notifiable_id`   | INT          | NULL     | -       | -           | Referenced entity ID                    |
| `metadata`        | JSONB        | NULL     | -       | -           | Data/payload tambahan dalam format JSON |
| `read_at`         | TIMESTAMPTZ  | NULL     | -       | -           | First-read timestamp, nullable          |
| `created_at`      | TIMESTAMPTZ  | NULL     | -       | -           | Creation timestamp                      |

Read state:

```text
read_at = NULL
-> unread

read_at != NULL
-> read
```

## `interactions`

| Column                    | Type Data    | NULLABLE | Default           | Constraints                 | Description                                |
| ------------------------- | ------------ | -------- | ----------------- | --------------------------- | ------------------------------------------ |
| `id`                      | INT          | NOT NULL | A_I               | PRIMARY KEY                 | Primary key                                |
| `client_id`               | INT          | NOT NULL | -                 | FOREIGN KEY                 | FK to `clients.id`                         |
| `deal_id`                 | INT          | NULL     | -                 | FOREIGN KEY                 | FK to `deals.id`, nullable                 |
| `organization_contact_id` | INT          | NULL     | -                 | FOREIGN KEY                 | FK to `organization_contacts.id`, nullable |
| `type`                    | VARCHAR(20)  | NOT NULL | 'chat'            | CHECK('cl', 'm', 'e', 'ct') | Interaction type                           |
| `subject`                 | VARCHAR(255) | NOT NULL | -                 | -                           | Interaction subject                        |
| `description`             | VARCHAR(255) | NULL     | -                 | -                           | Planned/detail information                 |
| `summary`                 | TEXT         | NULL     | -                 | -                           | Result summary, nullable                   |
| `status`                  | VARCHAR(20)  | NOT NULL | 'scheduled'       | CHECK('sc', 'cp', 'cl')     | Interaction status                         |
| `start_at`                | TIMESTAMPTZ  | NULL     | -                 | -                           | Start timestamp                            |
| `end_at`                  | TIMESTAMPTZ  | NULL     | -                 | -                           | End timestamp                              |
| `performed_by`            | SMALLINT     | NOT NULL | -                 | FOREIGN KEY                 | FK to `users.id`                           |
| `external_reference`      | VARCHAR(255) | NULL     | -                 | -                           | External system reference, nullable        |
| `created_at`              | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                           | Creation timestamp                         |
| `updated_at`              | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -                           | Last update timestamp                      |

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

| Column       | Type Data   | NULLABLE | Default           | Constraints | Description                  |
| ------------ | ----------- | -------- | ----------------- | ----------- | ---------------------------- |
| `id`         | INT         | NOT NULL | A_I               | PRIMARY KEY | Primary key                  |
| `client_id`  | INT         | NULL     | -                 | FOREIGN KEY | FK to `clients.id`, nullable |
| `deal_id`    | INT         | NULL     | -                 | FOREIGN KEY | FK to `deals.id`, nullable   |
| `content`    | TEXT        | NOT NULL | -                 | -           | Note content                 |
| `note_type`  | VARCHAR(50) | NOT NULL | -                 | -           | Note category                |
| `created_by` | SMALLINT    | NOT NULL | -                 | FOREIGN KEY | FK to `users.id`             |
| `created_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp           |
| `updated_at` | TIMESTAMPTZ | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp        |

---

# 6. Integration & Files

## `external_conversations`

| Column                     | Type Data    | NULLABLE | Default           | Constraints | Description                |
| -------------------------- | ------------ | -------- | ----------------- | ----------- | -------------------------- |
| `id`                       | INT          | NOT NULL | A_I               | PRIMARY KEY | Primary key                |
| `platform`                 | VARCHAR(50)  | NOT NULL | -                 | -           | External platform          |
| `external_conversation_id` | VARCHAR(255) | NOT NULL | -                 | -           | External conversation ID   |
| `client_id`                | INT          | NOT NULL | -                 | FOREIGN KEY | FK to `clients.id`         |
| `last_interaction_at`      | TIMESTAMPTZ  | NULL     | NULL              | -           | Last interaction timestamp |
| `metadata`                 | JSONB        | NULL     | NULL              | -           | Additional metadata        |
| `created_at`               | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Creation timestamp         |
| `updated_at`               | TIMESTAMPTZ  | NULL     | CURRENT_TIMESTAMP | -           | Last update timestamp      |

## `attachments`

| Column              | Type Data    | NULLABLE | Default | Constraints            | Description              |
| ------------------- | ------------ | -------- | ------- | ---------------------- | ------------------------ |
| `id`                | INT          | NOT NULL | A_I     | PRIMARY KEY            | Primary key              |
| `file_name`         | VARCHAR(255) | NOT NULL | -       | -                      | File name                |
| `description`       | TEXT         | NULL     | -       | -                      | File description         |
| `mime_type`         | VARCHAR(100) | NOT NULL | -       | -                      | MIME type                |
| `file_size`         | BIGINT       | NOT NULL | -       | CHECK (file_size >= 0) | File size                |
| `storage_reference` | VARCHAR(255) | NOT NULL | -       | -                      | Storage/object reference |
| `uploaded_by`       | SMALLINT     | NOT NULL | -       | FOREIGN KEY            | FK to `users.id`         |
| `created_at`        | TIMESTAMPTZ  | NULL     | -       | -                      | Upload timestamp         |
| `attachable_type`   | VARCHAR(50)  | NOT NULL | -       | -                      | Related entity type      |
| `attachable_id`     | INT          | NOT NULL | -       | -                      | Related entity ID        |

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
