# Connector Controls

Connector Controls is a WordPress VIP integration that provides secure, centralized at-rest storage and management for **WordPress Core Connectors** credentials.

WordPress Core stores Connector credentials (Settings → Connectors) in the site database in plain text, masked only in the UI, and any plugin can read them. Connector Controls gives VIP customers an encrypted, centrally managed layer to store those credentials and share them securely across their WordPress applications — the basis for centralized reporting and metering.

> **Status: early development.** This repository is a working WordPress VIP integration scaffold — templated from the [VIP Integrations Starter Kit](https://github.com/Automattic/vip-integrations-starter-kit) — with the credential storage and management features still to be built. The example REST endpoint and settings screen are placeholder scaffolding. Work is tracked in the Connector Controls project (VIPPROD).

Runtime config comes from a single VIP-provided constant read through a central `Config` class, with graceful degradation when config is missing or incomplete, Tracks-only telemetry, and the [handoff manifest](/docs/manifest.md) VIP registers the integration from. See [/docs/vip-integration.md](/docs/vip-integration.md) for the operational details.

## Technology

These are the tools used to ensure code quality on the WordPress VIP platform.

### Unit Tests

[PHPUnit 9](https://phpunit.de/index.html) for unit tests. See [/tests/phpunit](tests/phpunit/).

### End-to-end tests

[Playwright](https://playwright.dev/) for end-to-end tests. See [/tests/e2e](/tests/e2e).

### Static analysis

[Psalm](https://psalm.dev/) for static analysis. Psalm relies on type annotations — see [/inc](/inc) for the expected docblock style.

### Linting and coding standards

Linting and coding standards are powered by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) (PHPCS) with the WordPress VIP and WordPress core rulesets. See the [linting doc](/docs/linting.md).

### GitHub Actions

CI runs on every push and pull request to `main`:

| Workflow                               | What it does                                                                                                |
| -------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| `unit-tests.yml`                       | PHPUnit across the VIP platform baseline (PHP 8.2–8.5 × WordPress 6.9.x/latest, single site and multisite). |
| `e2e.yml`                              | Playwright end-to-end tests against a real `vip dev-env` (WordPress 6.9 and 7.0).                           |
| `lint.yml`                             | PHPCS with the WordPress VIP rulesets.                                                                      |
| `static-code-analysis.yml`             | Psalm static analysis.                                                                                      |
| `codeql.yml` / `dependency-review.yml` | Security scanning of code and dependency changes.                                                           |

## Repository structure

⚠️ The repository contains several folders that together constitute a complete WordPress VIP application; they should not be removed. A brief description is available in [/docs/directories.md](/docs/directories.md).

For more on how the codebase is structured, see https://docs.wpvip.com/technical-references/vip-codebase/.

## Local installation and development

You will need [Composer](https://getcomposer.org/), [Node.js](https://nodejs.org/en) and NPM, [Docker](https://www.docker.com/), and the [VIP-CLI](https://docs.wpvip.com/vip-cli/).

📝 Docker Desktop is recommended, but the environment is compatible with alternative container runtimes like Colima and Rancher Desktop — see [our documentation](https://docs.wpvip.com/vip-local-development-environment/requirements/#Alternatives-to-Docker-Desktop).

1. Clone the repository and change into its directory.
2. Install Composer dependencies:

```sh
composer install
```

3. Install Node.js dependencies:

```sh
npm i
```

4. Create and start a WPVIP local development instance:

```sh
vip dev-env create
vip dev-env start
```

5. Write code, write tests. Or the other way around! `composer test` runs both suites (the e2e half needs the dev-env from the previous step running — see [/docs/vip-integration.md](/docs/vip-integration.md)).

📝 For convenience, this repository contains a [vip-dev-env.yml](/.wpvip/vip-dev-env.yml) configuration file; tweak it to your needs. For an in-depth guide to VIP Local Development Environments, see [our documentation site](https://docs.wpvip.com/vip-local-development-environment/create/).

## Cloud-based development

GitHub Codespaces is supported with no set-up steps. The first start builds the environment (a few minutes); afterwards you can use either the web editor or local VS Code.

## Releasing

Conformance is checked with the external `vip-integration` CLI:

```sh
npx @automattic/vip-integration validate
```

Tagged versions of this integration are vendored into [`vip-go-mu-plugins-ext`](https://github.com/Automattic/vip-go-mu-plugins-ext/tree/trunk/vip-integrations), which is mounted onto VIP applications. Keep [`vip-manifest.yaml`](/vip-manifest.yaml) in sync with the code before cutting a release — see [/docs/manifest.md](/docs/manifest.md).
