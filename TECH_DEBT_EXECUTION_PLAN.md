# Technical Debt Execution Plan

Last updated: 2026-03-15

## Phase 0 - Stabilize Production Surface

- [x] P0.1 Route-controller contract alignment
  - [x] Implement missing methods or remove invalid routes for frontend newsletter/download flows.
  - [x] Implement missing methods or remove invalid routes for admin customer/project/download flows.
  - [x] Add smoke tests for affected route groups.
- [x] P0.2 Contact flow reliability bugfix
  - [x] Replace invalid mail success branching with exception-safe mail orchestration.
  - [x] Add feature test for successful contact submission flow.
  - [x] Add feature test for mail failure behavior.
- [x] P0.3 Frontend typing/compile stabilization
  - [x] Add missing shared domain types (Project, Customer, media DTOs) to TypeScript declarations.
  - [x] Remove direct template usage of browser globals where it breaks TS template type checking.
  - [x] Validate with vue-tsc and production build.
- [ ] P0.4 Baseline health checks
  - [x] Run targeted feature tests for touched backend files.
  - [x] Run linting/type checks for touched frontend files.

## Phase 1 - Restore Domain Integrity

- [x] P1.1 Download domain alignment
  - [x] Align download migration schema to service/model behavior.
  - [x] Implement Download model fields, casts, scopes, and media methods.
  - [x] Add feature tests for public/admin download listing and file processing.
- [x] P1.2 Subscriber domain alignment
  - [x] Align subscribers schema and model to dashboard/newsletter usage.
  - [x] Add tests for subscription, confirmation, and unsubscribe flows.
- [x] P1.3 Validation consistency
  - [x] Add FormRequest for quote submission.
  - [x] Add FormRequest for project reactions API.
  - [x] Update controllers to rely on FormRequest classes.

## Phase 2 - Boundary Refactor

- [x] P2.1 Service API decoupling
  - [x] Replace Request-driven service APIs with typed filter DTOs.
  - [x] Keep HTTP concerns in controllers only.
- [x] P2.2 Action-oriented orchestration
  - [x] Move complex mail/notification orchestration into dedicated actions/jobs.
  - [x] Add unit tests for orchestration behavior.

## Phase 3 - Performance Hardening

- [x] P3.1 Dashboard query optimization
  - [x] Replace daily loop query fan-out with grouped aggregate queries.
  - [x] Fix monthly comparison year scoping.
  - [x] Fix timeline sorting to use timestamps, not diffForHumans strings.
  - [x] Guard against division by zero in efficiency calculations.
- [x] P3.2 Search correctness and indexing
  - [x] Correct JSON technology search strategy in Project model.
  - [x] Add/verify supporting indexes for dominant filters.

## Phase 4 - Test and CI Guardrails

- [x] P4.1 Coverage expansion
  - [x] Add feature tests for quote/contact/reaction/download/admin CRUD flows.
- [x] P4.2 Quality gates
  - [x] Add frontend typecheck command to CI.
  - [x] Add route smoke tests to CI.
  - [x] Keep lint and formatter checks green.

## Working Rules

- Every code change must be paired with programmatic verification.
- Keep refactors incremental and deployable.
- Prefer preserving public API contracts unless explicitly changing behavior.
