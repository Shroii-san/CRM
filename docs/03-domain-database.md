# CRM Database Schema

## 1. Overview

Struktur database CRM dibagi menjadi beberapa domain utama:

- **Authentication & Authorization**
- **Client Management**
- **Sales Pipeline**
- **Task & Communication**
- **Supporting Data**
- **Audit & History**

Hubungan utama sistem:

```text
User
 │
 ├── Deal
 ├── Task
 ├── Interaction
 ├── Note
 └── Activity Log

Client
 │
 ├── Person
 │
 └── Organization
       └── Organization Contact

Pipeline
 │
 └── Pipeline Stage
       └── Stage Task Template

Deal
 │
 ├── Task
 ├── Interaction
 ├── Note
 ├── Attachment
 └── Deal Stage History
```

---

# 2. Authentication & Authorization

## users

Menyimpan user internal yang menggunakan CRM.

| Column             | Description                                |
| ------------------ | ------------------------------------------ |
| `id`               | Primary key                                |
| `role_id`          | FK ke `roles.id`                           |
| `name`             | Nama user                                  |
| `email`            | Email user                                 |
| `password_hash`    | Password terenkripsi                       |
| `status`           | Status user, misalnya `active`, `inactive` |
| `last_activity_at` | Aktivitas terakhir user                    |
| `created_at`       | Waktu data dibuat                          |
| `updated_at`       | Waktu data diperbarui                      |

### Relationships

```text
roles 1 --- N users
users 1 --- N deals
users 1 --- N tasks
users 1 --- N interactions
users 1 --- N notes
users 1 --- N activity_logs
```

---

## roles

Menyimpan role user.

| Column        | Description      |
| ------------- | ---------------- |
| `id`          | Primary key      |
| `name`        | Nama role        |
| `description` | Deskripsi role   |
| `created_at`  | Waktu dibuat     |
| `updated_at`  | Waktu diperbarui |

Contoh:

```text
Admin
Sales
Manager
```

---

## role_permissions

Mengatur permission setiap role.

| Column       | Description                  |
| ------------ | ---------------------------- |
| `id`         | Primary key                  |
| `role_id`    | FK ke `roles.id`             |
| `resource`   | Resource/module yang diakses |
| `can_view`   | Hak melihat                  |
| `can_create` | Hak membuat                  |
| `can_update` | Hak mengubah                 |
| `can_delete` | Hak menghapus                |
| `can_assign` | Hak melakukan assignment     |
| `created_at` | Waktu dibuat                 |
| `updated_at` | Waktu diperbarui             |

---

# 3. Client Management

## persons

Menyimpan data individual/person.

| Column       | Description      |
| ------------ | ---------------- |
| `id`         | Primary key      |
| `name`       | Nama lengkap     |
| `email`      | Email            |
| `phone`      | Nomor telepon    |
| `created_at` | Waktu dibuat     |
| `updated_at` | Waktu diperbarui |

> `job_title` tidak ditempatkan langsung pada `persons`, karena jabatan merupakan hubungan antara person dengan organization.

---

## organizations

Menyimpan data perusahaan atau organisasi.

| Column        | Description       |
| ------------- | ----------------- |
| `id`          | Primary key       |
| `name`        | Nama organization |
| `industry`    | Industri          |
| `phone`       | Nomor telepon     |
| `email`       | Email             |
| `website`     | Website           |
| `address`     | Alamat            |
| `province_id` | FK province       |
| `regency_id`  | FK regency        |
| `district_id` | FK district       |
| `village_id`  | FK village        |
| `created_at`  | Waktu dibuat      |
| `updated_at`  | Waktu diperbarui  |

---

## organization_contacts

Tabel penghubung antara `persons` dan `organizations`.

Digunakan untuk menentukan siapa yang menjadi contact person dari suatu organization.

| Column            | Description                    |
| ----------------- | ------------------------------ |
| `id`              | Primary key                    |
| `organization_id` | FK ke `organizations.id`       |
| `person_id`       | FK ke `persons.id`             |
| `job_title`       | Jabatan person di organization |
| `is_primary`      | Apakah contact utama           |
| `started_at`      | Mulai bekerja/berhubungan      |
| `ended_at`        | Akhir hubungan                 |
| `created_at`      | Waktu dibuat                   |
| `updated_at`      | Waktu diperbarui               |

### Relationship

```text
persons N --- N organizations

melalui:

organization_contacts
```

---

## organization_social_profiles

Menyimpan akun media sosial suatu organization.

| Column            | Description                 |
| ----------------- | --------------------------- |
| `id`              | Primary key                 |
| `organization_id` | FK ke `organizations.id`    |
| `platform`        | Platform, misalnya LinkedIn |
| `username`        | Username                    |
| `url`             | URL profil                  |
| `created_at`      | Waktu dibuat                |
| `updated_at`      | Waktu diperbarui            |

---

## clients

Merepresentasikan pihak yang menjadi client/prospect dalam CRM.

Client dapat berupa:

- Person
- Organization

| Column            | Description                        |
| ----------------- | ---------------------------------- |
| `id`              | Primary key                        |
| `person_id`       | FK ke `persons.id`, nullable       |
| `organization_id` | FK ke `organizations.id`, nullable |
| `status`          | Status hubungan client             |
| `source`          | Sumber client                      |
| `created_at`      | Waktu dibuat                       |
| `updated_at`      | Waktu diperbarui                   |

### Business Rule

Satu client hanya boleh merepresentasikan salah satu:

```text
person_id IS NOT NULL
organization_id IS NULL
```

atau:

```text
person_id IS NULL
organization_id IS NOT NULL
```

Tidak boleh:

```text
person_id IS NULL
organization_id IS NULL
```

dan tidak boleh keduanya terisi.

### Relationship

```text
Person       1 --- 0..1 Client

Organization 1 --- 0..1 Client
```

---

# 4. Sales Pipeline

## pipelines

Menyimpan definisi pipeline.

Contoh:

```text
Enterprise Sales
SMB Sales
Partner Sales
```

| Column        | Description          |
| ------------- | -------------------- |
| `id`          | Primary key          |
| `name`        | Nama pipeline        |
| `description` | Deskripsi            |
| `status`      | `active`, `inactive` |
| `created_at`  | Waktu dibuat         |
| `updated_at`  | Waktu diperbarui     |

### Relationship

```text
pipelines 1 --- N pipeline_stages
pipelines 1 --- N deals
```

---

## pipeline_stages

Menyimpan stage dari pipeline.

Contoh:

```text
Lead
Contacted
Negotiation
Proposal
Demo
Payment
Closed Won
Closed Lost
```

| Column        | Description            |
| ------------- | ---------------------- |
| `id`          | Primary key            |
| `pipeline_id` | FK ke `pipelines.id`   |
| `name`        | Nama stage             |
| `slug`        | Identifier stage       |
| `description` | Deskripsi              |
| `position`    | Urutan stage           |
| `is_terminal` | Menandakan stage akhir |
| `status`      | `active`, `inactive`   |
| `created_at`  | Waktu dibuat           |
| `updated_at`  | Waktu diperbarui       |

### Constraints

```text
UNIQUE (pipeline_id, slug)
UNIQUE (pipeline_id, position)
```

### Relationship

```text
pipelines 1 --- N pipeline_stages
pipeline_stages 1 --- N deals
```

---

## stage_task_templates

Mendefinisikan task/action otomatis atau standar yang perlu dilakukan pada suatu stage.

Contoh:

```text
Stage: Proposal

Task:
Send proposal to client
Due offset: 2 days
Priority: high
```

| Column            | Description                          |
| ----------------- | ------------------------------------ |
| `id`              | Primary key                          |
| `stage_id`        | FK ke `pipeline_stages.id`           |
| `name`            | Nama task                            |
| `description`     | Deskripsi                            |
| `priority`        | Default priority                     |
| `due_offset_days` | Deadline relatif setelah masuk stage |
| `is_required`     | Apakah task wajib                    |
| `is_active`       | Status template                      |
| `created_at`      | Waktu dibuat                         |
| `updated_at`      | Waktu diperbarui                     |

### Relationship

```text
pipeline_stages 1 --- N stage_task_templates
```

---

# 5. Deal Management

## deals

Menyimpan opportunity/deal antara perusahaan dengan client.

| Column              | Description                 |
| ------------------- | --------------------------- |
| `id`                | Primary key                 |
| `name`              | Nama deal                   |
| `client_id`         | FK ke `clients.id`          |
| `pipeline_id`       | FK ke `pipelines.id`        |
| `current_stage_id`  | FK ke `pipeline_stages.id`  |
| `currency`          | Mata uang                   |
| `value`             | Nilai deal                  |
| `status`            | Status deal                 |
| `priority`          | Prioritas                   |
| `expected_close_at` | Target closing              |
| `actual_close_at`   | Closing aktual              |
| `description`       | Deskripsi deal              |
| `assigned_user_id`  | User yang bertanggung jawab |
| `created_at`        | Waktu dibuat                |
| `updated_at`        | Waktu diperbarui            |

### Deal Status

Disarankan:

```text
open
won
lost
cancelled
abandoned
```

Stage menjawab:

```text
Deal sedang berada di tahap mana?
```

Status menjawab:

```text
Apakah deal masih berlangsung atau sudah berakhir?
```

### Relationship

```text
clients 1 --- N deals

pipelines 1 --- N deals

pipeline_stages 1 --- N deals

users 1 --- N deals
```

---

## deal_stage_histories

Mencatat perpindahan stage setiap deal.

| Column          | Description                      |
| --------------- | -------------------------------- |
| `id`            | Primary key                      |
| `deal_id`       | FK ke `deals.id`                 |
| `from_stage_id` | Stage sebelumnya                 |
| `to_stage_id`   | Stage baru                       |
| `changed_by`    | FK user yang melakukan perubahan |
| `changed_at`    | Waktu perubahan                  |

### Relationship

```text
deals 1 --- N deal_stage_histories

pipeline_stages 1 --- N deal_stage_histories
```

Contoh:

```text
Deal #101

Lead
↓
Contacted
↓
Negotiation
↓
Proposal
```

History:

```text
Lead        → Contacted
Contacted   → Negotiation
Negotiation → Proposal
```

---

# 6. Task Management

## tasks

Menyimpan task/action yang perlu dilakukan user.

| Column                   | Description                    |
| ------------------------ | ------------------------------ |
| `id`                     | Primary key                    |
| `name`                   | Nama task                      |
| `description`            | Deskripsi                      |
| `client_id`              | FK ke client, nullable         |
| `deal_id`                | FK ke deal, nullable           |
| `stage_task_template_id` | Template sumber task, nullable |
| `assigned_user_id`       | User yang bertanggung jawab    |
| `due_at`                 | Deadline                       |
| `completed_at`           | Waktu selesai                  |
| `status`                 | Status task                    |
| `priority`               | Prioritas                      |
| `created_at`             | Waktu dibuat                   |
| `updated_at`             | Waktu diperbarui               |

### Task Status

```text
planned
in_progress
completed
cancelled
```

### Priority

```text
low
medium
high
urgent
```

### Relationship

```text
clients 1 --- N tasks

deals 1 --- N tasks

users 1 --- N tasks

stage_task_templates 1 --- N tasks
```

---

## task_reminders

Menyimpan reminder suatu task.

Satu task dapat memiliki lebih dari satu reminder.

| Column       | Description             |
| ------------ | ----------------------- |
| `id`         | Primary key             |
| `task_id`    | FK ke `tasks.id`        |
| `remind_at`  | Waktu reminder          |
| `status`     | Status reminder         |
| `sent_at`    | Waktu reminder terkirim |
| `created_at` | Waktu dibuat            |
| `updated_at` | Waktu diperbarui        |

### Reminder Status

```text
pending
sent
cancelled
```

### Relationship

```text
tasks 1 --- N task_reminders
```

---

# 7. Communication

## interactions

Menyimpan komunikasi/interaksi dengan client.

| Column               | Description                           |
| -------------------- | ------------------------------------- |
| `id`                 | Primary key                           |
| `client_id`          | FK ke `clients.id`                    |
| `deal_id`            | FK ke `deals.id`, nullable            |
| `type`               | Jenis interaction                     |
| `subject`            | Judul interaction                     |
| `description`        | Detail/rencana                        |
| `summary`            | Ringkasan setelah interaction selesai |
| `status`             | Status interaction                    |
| `start_at`           | Waktu mulai                           |
| `end_at`             | Waktu selesai                         |
| `performed_by`       | FK ke `users.id`                      |
| `external_reference` | ID/resource eksternal                 |
| `created_at`         | Waktu dibuat                          |
| `updated_at`         | Waktu diperbarui                      |

### Interaction Type

```text
call
meeting
email
chat
```

### Interaction Status

```text
scheduled
completed
cancelled
```

### Relationship

```text
clients 1 --- N interactions

deals 1 --- N interactions

users 1 --- N interactions
```

---

## external_conversations

Menyimpan conversation/thread dari platform eksternal.

Contoh:

```text
WhatsApp
Email
Instagram
Telegram
```

| Column                     | Description                   |
| -------------------------- | ----------------------------- |
| `id`                       | Primary key                   |
| `platform`                 | Platform                      |
| `external_conversation_id` | ID conversation dari platform |
| `client_id`                | FK ke `clients.id`            |
| `status`                   | Status conversation           |
| `last_interaction_at`      | Interaction terakhir          |
| `metadata`                 | Metadata tambahan             |
| `created_at`               | Waktu dibuat                  |
| `updated_at`               | Waktu diperbarui              |

### Constraint

```text
UNIQUE (
    platform,
    external_conversation_id
)
```

---

# 8. Notes

## notes

Menyimpan informasi penting mengenai client atau deal.

| Column       | Description            |
| ------------ | ---------------------- |
| `id`         | Primary key            |
| `client_id`  | FK ke client, nullable |
| `deal_id`    | FK ke deal, nullable   |
| `content`    | Isi note               |
| `note_type`  | Jenis note             |
| `created_by` | FK ke user             |
| `created_at` | Waktu dibuat           |
| `updated_at` | Waktu diperbarui       |

### Contoh Note Type

```text
general
requirement
preference
issue
important
```

### Business Rule

Minimal salah satu harus tersedia:

```text
client_id
deal_id
```

---

# 9. Attachment

## attachments

Menyimpan metadata file yang di-upload.

| Column              | Description            |
| ------------------- | ---------------------- |
| `id`                | Primary key            |
| `file_name`         | Nama file              |
| `description`       | Deskripsi              |
| `mime_type`         | MIME type              |
| `file_size`         | Ukuran file            |
| `storage_reference` | Lokasi/path/object key |
| `uploaded_by`       | FK user                |
| `uploaded_at`       | Waktu upload           |
| `related_type`      | Tipe entity terkait    |
| `related_id`        | ID entity terkait      |

Contoh:

```text
related_type = deal
related_id   = 10
```

atau:

```text
related_type = interaction
related_id   = 25
```

Attachment menggunakan polymorphic association karena file dapat berkaitan dengan berbagai entity.

---

# 10. Audit

## activity_logs

Mencatat tindakan user di dalam aplikasi.

Berbeda dengan `interactions`.

```text
Interaction
= komunikasi user dengan client

Activity Log
= tindakan user di dalam aplikasi
```

| Column        | Description           |
| ------------- | --------------------- |
| `id`          | Primary key           |
| `user_id`     | FK user               |
| `action`      | Action yang dilakukan |
| `target_type` | Jenis entity          |
| `target_id`   | ID entity             |
| `metadata`    | Informasi tambahan    |
| `created_at`  | Timestamp activity    |

Contoh:

```text
User changed deal stage
User created client
User updated task
User deleted note
```

Contoh metadata:

```json
{
    "from_stage_id": 2,
    "to_stage_id": 3
}
```

---

# 11. Reference Data

Data wilayah tetap dipertahankan sebagai reference/supporting data.

## countries

```text
id
name
```

Relationship:

```text
countries 1 --- N provinces
```

---

## provinces

```text
id
country_id
name
```

Relationship:

```text
provinces 1 --- N regencies
```

---

## regencies

```text
id
province_id
name
```

Relationship:

```text
regencies 1 --- N districts
```

---

## districts

```text
id
regency_id
name
```

Relationship:

```text
districts 1 --- N villages
```

---

## villages

```text
id
district_id
name
```

---

## postal_codes

```text
id
district_id
postal_code
```

---

# 12. Entity Classification

## Core

```text
users
roles

persons
organizations
organization_contacts
clients

pipelines
pipeline_stages
deals
```

---

## Action & Communication

```text
tasks
task_reminders
interactions
notes
stage_task_templates
```

---

## Supporting

```text
organization_social_profiles
external_conversations
attachments
```

---

## Audit & History

```text
activity_logs
deal_stage_histories
```

---

## Reference

```text
countries
provinces
regencies
districts
villages
postal_codes
```

---

# 13. Relationship Summary

```text
roles
  1
  │
  N
users
  │
  ├───────────────┬─────────────────┬────────────────┐
  │               │                 │                │
  N               N                 N                N
deals           tasks          interactions      activity_logs


persons
   │
   ├──────── N organization_contacts N ─────── organizations
   │                                             │
   │                                             │
   └────────── client ◄──────────────────────────┘
                  │
                  │ 1
                  │
                  N
                deals
                  │
     ┌────────────┼───────────────┐
     │            │               │
     N            N               N
   tasks     interactions        notes


pipelines
    │
    │ 1
    ▼
pipeline_stages
    │
    ├─────────────► stage_task_templates
    │
    │
    └─────────────► deals


deals
   │
   ├── N tasks
   │
   ├── N interactions
   │
   ├── N notes
   │
   └── N deal_stage_histories


tasks
   │
   └── N task_reminders
```

---

# 14. Cardinality Summary

| Parent         | Relationship | Child                |
| -------------- | ------------ | -------------------- |
| Role           | 1:N          | User                 |
| Person         | 1:N          | Organization Contact |
| Organization   | 1:N          | Organization Contact |
| Person         | 1:0..1       | Client               |
| Organization   | 1:0..1       | Client               |
| Client         | 1:N          | Deal                 |
| Client         | 1:N          | Task                 |
| Client         | 1:N          | Interaction          |
| Client         | 1:N          | Note                 |
| Pipeline       | 1:N          | Pipeline Stage       |
| Pipeline       | 1:N          | Deal                 |
| Pipeline Stage | 1:N          | Deal                 |
| Pipeline Stage | 1:N          | Stage Task Template  |
| Deal           | 1:N          | Task                 |
| Deal           | 1:N          | Interaction          |
| Deal           | 1:N          | Note                 |
| Deal           | 1:N          | Deal Stage History   |
| Task           | 1:N          | Task Reminder        |
| User           | 1:N          | Deal                 |
| User           | 1:N          | Task                 |
| User           | 1:N          | Interaction          |
| User           | 1:N          | Note                 |
| User           | 1:N          | Activity Log         |

---

# 15. Main CRM Flow

```text
Person / Organization
        │
        ▼
      Client
        │
        ▼
       Deal
        │
        ▼
     Pipeline
        │
        ▼
       Stage
        │
        ├─────────────┐
        │             │
        ▼             ▼
      Task       Interaction
        │             │
        ├──────┬──────┘
        │      │
        ▼      ▼
     Reminder Notes
        │
        ▼
    Follow Up
        │
        ▼
Stage Transition
        │
        ▼
Deal Stage History
        │
        ▼
       ...
        │
        ▼
       Won
        or
       Lost
```

---

# 16. Default Sales Pipeline Example

```text
Lead
 │
 ▼
Contacted
 │
 ▼
Negotiation
 │
 ▼
Proposal
 │
 ▼
Demo
 │
 ▼
Payment
 │
 ├──────────► Closed Won
 │
 └──────────► Closed Lost
```

Stage tetap bersifat dinamis dan harus berasal dari:

```text
pipeline_stages
```

bukan hard-coded pada application logic.

---

# 17. Important Business Rules

1. Setiap `pipeline_stage` harus dimiliki tepat satu `pipeline`.

2. `position` stage harus unik dalam satu pipeline.

3. `current_stage_id` suatu deal harus berasal dari pipeline yang sama dengan `deal.pipeline_id`.

4. Perubahan `current_stage_id` harus menghasilkan record baru pada `deal_stage_histories`.

5. Deal harus mempunyai satu client.

6. Deal harus mempunyai satu pipeline dan current stage.

7. Satu client dapat mempunyai banyak deal.

8. Client hanya boleh mewakili `person` atau `organization`, bukan keduanya.

9. Task dapat berkaitan dengan client dan/atau deal.

10. Interaction wajib berkaitan dengan client, sementara deal bersifat opsional.

11. Note minimal berkaitan dengan client atau deal.

12. Activity log tidak boleh digunakan sebagai pengganti business history seperti `deal_stage_histories`.

13. Stage task template merupakan definisi action, sedangkan `tasks` merupakan instance pekerjaan aktual.

14. Stage tidak boleh di-hard-code seperti `lead`, `proposal`, atau `payment` pada source code karena stage bersifat configurable.

15. `won`, `lost`, dan status akhir deal harus mempunyai aturan konsisten dengan terminal stage pada pipeline.
