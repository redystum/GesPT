# GesPT

Modern academic equivalence management platform built with **Laravel 11** (backend) and **Vue 3** (frontend).

## Quick Access

| Service       | URL                                             |
|---------------|-------------------------------------------------|
| Frontend      | http://staging-gespt.172.22.21.149.sslip.io     |
| Backend (API) | http://staging-gespt.172.22.21.149.sslip.io/api |

## Demo Credentials

### Seeded by default

| Role | Email | Password |
| --- | --- | --- |
| Administrator | `admin@gespt.local` | `admin123` |

### Example users

Use these as demo examples for role-based testing.

| Role | Email                                | Password     |
| --- |--------------------------------------|--------------|
| Coordinator | `coordinator@gespt.local`            | `coord123`   |
| Teacher | `teacher@gespt.local`                | `teacher123` |
| Teacher | all `@ipleiria.pt` terminated emails | SSO          |


## Environment Notes

- Frontend needs (on [staging.yaml](.gitea/workflows/staging.yaml)):
  - `VITE_API_SERVER_URL=...`
  - `VITE_API_PATH_URL=...`
- Backend needs:
  - `IPLEIRIA_AUTH_URL=...`
  - `FRONTEND_URL=...`
  - `DB_*=...` (all db configurations)
  - `MAIL_*=...` (all mail configurations)
