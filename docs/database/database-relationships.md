# CRM Database Relationships

## 1. Authentication & Authorization

### Role -> Users

```text
roles 1 ─── N users
```

### Role -> Permissions

```text
roles 1 ─── N role_permissions
```

### Role <--> Menu

```text
roles N ─── N menus
    penghubung
     role_menus
```

### Menu -> Child Menus

```text
menus 1 ─── N menus
```

Self-referencing melalui `menus.parent_id`.

### Icon <--> Menu

```text
menu_icons 1 ─── 1 menus
```

---

# 2. Client Management

### Industry -> Organizations

```text
industries 1 ─── N organizations
```

### Organization <--> Person

```text
organizations N ─── N persons
             penghubung
       organization_contacts
```

### Organization -> Social Profiles

```text
organizations 1 ─── N organization_social_profiles
```

### Person -> Client

```text
persons 1 ─── 0..1 clients
```

### Organization -> Client

```text
organizations 1 ─── 0..1 clients
```

### Client Source -> Clients

```text
client_sources 1 ─── N clients
```

---

# 3. Sales Pipeline

### Pipeline -> Stages

```text
pipelines 1 ─── N pipeline_stages
```

### Pipeline -> Deals

```text
pipelines 1 ─── N deals
```

### Pipeline Stage -> Deals

```text
pipeline_stages 1 ─── N deals
```

### Pipeline Stage -> Task Templates

```text
pipeline_stages 1 ─── N stage_task_templates
```

### Client -> Deals

```text
clients 1 ─── N deals
```

### User -> Deals

```text
users 1 ─── N deals
```

### Deal -> Stage History

```text
deals 1 ─── N deal_stage_histories
```

### Stage -> Stage History

```text
pipeline_stages 1 ─── N deal_stage_histories
```

### User -> Stage History

```text
users 1 ─── N deal_stage_histories
```

---

# 4. Task & Communication

### Client -> Tasks

```text
clients 1 ─── N tasks
```

### Deal -> Tasks

```text
deals 1 ─── N tasks
```

### Stage Task Template -> Tasks

```text
stage_task_templates 1 ─── N tasks
```

### User -> Tasks

```text
users 1 ─── N tasks
```

### Task -> Reminders

```text
tasks 1 ─── N task_reminders
```

### User -> Notifications

```text
users 1 ─── N notifications
```

### Notification -> Target

```text
notifications
├── notifiable_type
└── notifiable_id
```

### Client -> Interactions

```text
clients 1 ─── N interactions
```

### Deal -> Interactions

```text
deals 1 ─── N interactions
```

### Organization Contact -> Interactions

```text
organization_contacts 1 ─── N interactions
```

### User -> Interactions

```text
users 1 ─── N interactions
```

### Client -> Notes

```text
clients 1 ─── N notes
```

### Deal -> Notes

```text
deals 1 ─── N notes
```

---

# 5. Integration & Files

### Client -> External Conversations

```text
clients 1 ─── N external_conversations
```

### Attachment -> Related Entity

```text
attachments
├── related_type
└── related_id
```

---

# 6. Audit

### User -> Activity Logs

```text
users 1 ─── N activity_logs
```

### Activity Log -> Target

```text
activity_logs
├── target_type
└── target_id
```

---

# 7. Reference Data

### Country -> Province

```text
countries 1 ─── N provinces
```

### Province -> Regency

```text
provinces 1 ─── N regencies
```

### Regency -> District

```text
regencies 1 ─── N districts
```

### District -> Village

```text
districts 1 ─── N villages
```

### District -> Postal Codes

```text
districts 1 ─── N postal_codes
```

### Organization -> Region Data

```text
organizations
├── province_id
├── regency_id
├── district_id
└── village_id
```

---

# 8. Application Configuration

### `app_profiles`

### `app_settings`

---

# 9. Framework / Infrastructure

### Users -> Sessions

```text
users 1 ─── N sessions
```

### Password Reset Tokens

```text
password_reset_tokens
```

### Migrations

```text
migrations
```

---

# 10. Relationship Overview

```text
roles
  │
  ├── users
  │    ├── deals
  │    ├── tasks
  │    ├── interactions
  │    ├── notifications
  │    ├── notes
  │    ├── activity_logs
  │    └── sessions
  │
  └── role_menus ─── menus ─── menu_icons


industries
    │
    └── organizations
          │
          ├── organization_contacts ─── persons
          │                              │
          │                              └── clients
          │
          └── organization_social_profiles


client_sources
    │
    └── clients
          │
          ├── deals
          │     ├── tasks
          │     ├── interactions
          │     ├── notes
          │     └── deal_stage_histories
          │
          ├── tasks
          ├── interactions
          ├── notes
          └── external_conversations


pipelines
    │
    └── pipeline_stages
          ├── stage_task_templates ─── tasks
          └── deals
                └── deal_stage_histories


notifications
    └── polymorphic target

attachments
    └── polymorphic target

activity_logs
    └── polymorphic target
```

---

# 11. Cardinality Summary

| Parent                  | Relationship | Child                                 |
| ----------------------- | ------------ | ------------------------------------- |
| `roles`                 | 1:N          | `users`                               |
| `roles`                 | 1:N          | `role_permissions`                    |
| `roles`                 | N:N          | `menus` via `role_menus`              |
| `menus`                 | 1:N          | `menus`                               |
| `industries`            | 1:N          | `organizations`                       |
| `organizations`         | N:N          | `persons` via `organization_contacts` |
| `organizations`         | 1:N          | `organization_social_profiles`        |
| `persons`               | 1:0..1       | `clients`                             |
| `organizations`         | 1:0..1       | `clients`                             |
| `client_sources`        | 1:N          | `clients`                             |
| `pipelines`             | 1:N          | `pipeline_stages`                     |
| `pipelines`             | 1:N          | `deals`                               |
| `pipeline_stages`       | 1:N          | `deals`                               |
| `pipeline_stages`       | 1:N          | `stage_task_templates`                |
| `clients`               | 1:N          | `deals`                               |
| `users`                 | 1:N          | `deals`                               |
| `deals`                 | 1:N          | `deal_stage_histories`                |
| `users`                 | 1:N          | `deal_stage_histories`                |
| `clients`               | 1:N          | `tasks`                               |
| `deals`                 | 1:N          | `tasks`                               |
| `stage_task_templates`  | 1:N          | `tasks`                               |
| `users`                 | 1:N          | `tasks`                               |
| `tasks`                 | 1:N          | `task_reminders`                      |
| `users`                 | 1:N          | `notifications`                       |
| `clients`               | 1:N          | `interactions`                        |
| `deals`                 | 1:N          | `interactions`                        |
| `organization_contacts` | 1:N          | `interactions`                        |
| `users`                 | 1:N          | `interactions`                        |
| `clients`               | 1:N          | `notes`                               |
| `deals`                 | 1:N          | `notes`                               |
| `clients`               | 1:N          | `external_conversations`              |
| `users`                 | 1:N          | `activity_logs`                       |
| `countries`             | 1:N          | `provinces`                           |
| `provinces`             | 1:N          | `regencies`                           |
| `regencies`             | 1:N          | `districts`                           |
| `districts`             | 1:N          | `villages`                            |
| `districts`             | 1:N          | `postal_codes`                        |
| `users`                 | 1:N          | `sessions`                            |
