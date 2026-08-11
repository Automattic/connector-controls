# AGENTS.md

Context for AI coding agents working in this repo. This is a fast index into the
human docs — not a second source of truth. Read this first, then the doc that
matches your task.

## What this repo is

**Connector Controls**: a WordPress VIP integration that will provide secure,
centralized at-rest storage and management for WordPress Core Connectors
credentials (Core stores them in the database in plaintext today). It is a
complete, runnable VIP integration, templated from the [VIP Integrations Starter
Kit](https://github.com/Automattic/vip-integrations-starter-kit) and destined to
be vendored into `vip-go-mu-plugins-ext`.

**Status: early development.** The credential storage and management features are
not built yet. The REST `/sum` endpoint, the footer signature, and the
`enabled`/`message` settings are placeholder scaffolding inherited from the kit —
expect to replace them, not extend them. Runtime config is read from a single
VIP-provided constant through `inc/class-config.php`.

## Map

| Path                                              | What lives here                                                                                            |
| ------------------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| `connector-controls.php`                          | Plugin entry file: header, guards, constants, autoloader.                                                  |
| `inc/`                                            | Runtime code (autoloaded via Composer classmap). `class-config.php`, `class-telemetry.php`, REST handlers. |
| `views/`                                          | Admin page templates.                                                                                      |
| `fixtures/`                                       | Mock runtime configs for local dev and tests — see `fixtures/README.md`.                                   |
| `tests/phpunit/`, `tests/e2e/`                    | PHPUnit and Playwright tests.                                                                              |
| `vip-manifest.yaml`                               | The handoff manifest VIP registers the integration from.                                                   |
| `vip-manifest.schema.json`                        | JSON Schema the manifest is validated against.                                                             |
| `bin/setup.php`                                   | One-shot prefix-rewrite scaffolder from the kit. Already applied; kept for reference.                      |
| `docs/`                                           | Human docs. Start with `docs/vip-integration.md`.                                                          |
| `.wpvip/`, `.devcontainer/`, `.github/workflows/` | VIP dev-env, Codespaces, and CI.                                                                           |

## Non-negotiables

- **Do not delete the app folders.** Every top-level directory is part of a
  complete VIP application (`docs/directories.md`). Removing them breaks the
  integration.
- **One config constant.** All runtime config comes from a single VIP-defined
  constant read through `inc/class-config.php`. Never read `$_ENV`, hardcode
  secrets, or add a second config source.
- **Degrade, never fatal.** Missing or invalid config must disable features and
  surface an admin notice — never a fatal. There are fixtures for this.
- **Tracks-only telemetry.** Telemetry goes through `inc/class-telemetry.php`
  (VIP Tracks API, `class_exists`-guarded). No Stats/Pixel. Never put secrets,
  raw content, emails, or credentials in event properties.
- **Keep the prefix set in sync.** Slug (`connector-controls`), namespace
  (`Automattic\ConnectorControls`), config constant (`VIP_CONNECTOR_CONTROLS_CONFIG`),
  and telemetry/option prefix (`connector_controls_`) all belong together. If you
  rename one, rename all — including `vip-manifest.yaml`.

## Conventions

Follow the code that is already here. When in doubt, copy the nearest existing
pattern rather than introducing a new one.

### PHP

- **Baseline:** PHP 8.2+, WordPress 6.9 / 7.0. Do not use syntax newer than 8.2.
- **Namespaces:** everything under the `Automattic\ConnectorControls` root
  namespace. One class per file, filenames `class-<name>.php`, autoloaded via the
  Composer classmap on `inc/`.
- **Coding standards:** WordPress VIP rules via PHPCS (`composer phpcs`). Escape
  output, sanitize input, use `wpdb->prepare`, add capability checks on admin
  and REST surfaces. `composer phpcbf` auto-fixes what it can.
- **Static analysis:** Psalm must stay green (`composer psalm`). Annotate types;
  the existing classes show the expected docblock style.
- **No new runtime dependencies** without a strong reason — the plugin ships with
  what VIP already provides.

### Config

Read config only through `inc/class-config.php`. It reads the single
VIP-provided constant, validates it, and exposes `is_ready()` /
`missing_fields()` style accessors. New settings are added by:

1. declaring the field in `vip-manifest.yaml` (and its schema), and
2. reading it through `Config` — never touching the constant directly elsewhere.

Whatever you add to `Config`, keep the graceful-degradation contract: with
missing or invalid config the plugin disables its features and shows an admin
notice; it never fatals. Fixtures in `fixtures/` cover the valid, incomplete,
and invalid states — wire new cases in there.

### Telemetry

Record events through `inc/class-telemetry.php` only. It wraps the VIP Tracks
API behind a `class_exists` guard so non-VIP environments no-op. Event names are
prefixed with `connector_controls_`. Properties carry usage metadata only —
never secrets, request payloads, emails, or credentials. Declare every event in
the `telemetry` section of `vip-manifest.yaml`.

### Tests

- Unit tests in `tests/phpunit/`, e2e in `tests/e2e/` (Playwright). Add tests
  with the code that needs them; mirror the structure of the existing suites.
- The graceful-degradation behavior above is behavioral — prove it with a test,
  not just a static guard.

### Manifest

`vip-manifest.yaml` must stay in sync with the code: the constant name, the
plugin folder/entry file/namespace, every config `key` the plugin reads, and the
telemetry events it records. The schema (`vip-manifest.schema.json`) rejects
unknown keys, so a typo fails validation — run `vip-integration validate` after
editing it. See `docs/manifest.md` for the field-by-field guide.

## Commands

Details live in `docs/vip-integration.md`.

### Set up

```sh
composer install        # PHP dependencies
npm ci                  # Node dependencies (Playwright)
```

### Local environment

The e2e suite needs a running VIP dev-env:

```sh
vip dev-env create
vip dev-env start
```

### Test, lint, analyze

```sh
composer test           # PHPUnit + Playwright e2e (e2e needs the dev-env above)
composer test:unit      # PHPUnit only
composer test:e2e       # Playwright only
composer phpcs          # WordPress VIP coding standards
composer phpcbf         # auto-fix what PHPCS can
composer psalm          # static analysis
```

### Validate the integration

Conformance is checked by the external `vip-integration` CLI (not a composer
script in this repo). Run it from the repo root:

```sh
npx @automattic/vip-integration validate            # human report
npx @automattic/vip-integration validate --format json   # for CI
```

It exits non-zero when the integration is not conformant, so it can gate CI.
Among other rules it validates `vip-manifest.yaml` against
`vip-manifest.schema.json` — see `docs/manifest.md`.
