# AGENTS.md

Notes for future OpenCode sessions working in this repo. Read this before running commands.

## Stack

Symfony 7.1 app on PHP >= 8.2, Doctrine ORM 3, Twig, Symfony Security/Messenger/Mailer.
Frontend uses **Asset Mapper** (`assets/`, `importmap.php`) — there is no Node/Webpack/Encore toolchain. JS deps come from `importmap:install`.

## First-time setup (deps are not vendored in a fresh checkout)

```
composer install                                              # app deps; also runs cache:clear, assets:install, importmap:install
composer install --working-dir=tools/php-cs-fixer             # php-cs-fixer is a separate sub-project (see Lint below)
```

`bin/console` will throw `LogicException` until `composer install` has run.

## Database

Engine is **MariaDB 10.11**, consistent across `.env` (`DATABASE_URL=mysql://db:db@db:3306/db?serverVersion=mariadb-10.11.0`) and the MySQL-dialect migration in `migrations/`. The Docker dev stack runs it as the `db` service matching that URL. There is no Postgres involved anymore despite the original recipe scaffold.

## Running locally (Docker, dev only)

```
docker compose up -d                # builds the app image, starts db + app + Mailpit
```

- App at `http://localhost:${HTTP_PORT:-8000}` — set `HTTP_PORT` if 8000 is taken on the host.
- The `app` service auto-runs `composer install` + `doctrine:migrations:migrate` on every start (idempotent). The project is bind-mounted at `/var/www/html`, so edits are live; there is no Node/Encore build step.
- Seed sample crops once with `docker compose exec app bin/console doctrine:fixtures:load` (this **purges** the DB).
- MariaDB data persists in the `db_data` volume; Mailpit UI on `http://localhost:8025`, SMTP on `1025`.
- Tests can run inside the container too: first create the `_test` DB and load fixtures, then `docker compose exec app bin/phpunit`.

## Common commands

```
bin/console doctrine:migrations:migrate                       # apply schema migrations (migrations/ use namespace DoctrineMigrations)
bin/console doctrine:fixtures:load                            # dev fixtures (src/DataFixtures/CropFixtures.php)
```

## Tests

PHPUnit is invoked through Symfony's phpunit-bridge wrapper, not `vendor/bin/phpunit` directly:

```
bin/phpunit                                                   # full suite (phpunit.xml.dist forces APP_ENV=test)
bin/phpunit tests/Controller/HomeControllerTest.php           # one file
bin/phpunit --filter testHomePageContent                      # one test
```

Requirements an agent will miss:
- Tests hit a real database. The test env uses a separate db with `_test` suffix (see `config/packages/doctrine.yaml` `when@test` → `dbname_suffix`). Create + migrate it before running.
- **Fixtures must be loaded first** or tests fail (documented in `README.md`):
  ```
  bin/console --env=test doctrine:fixtures:load
  ```
- `HomeControllerTest` queries `Crop` rows matching the **current month**. It mocks `Symfony\Component\Clock\ClockInterface` to pin the date to 2024-09-01 (September) and asserts only September crops appear. If you change `CropFixtures` or the month-filter logic, update both together.

## Lint / formatting

php-cs-fixer is installed as its own composer project under `tools/php-cs-fixer/` (its `vendor/` is gitignored via `tools/*/vendor`). There is **no `.php-cs-fixer(.php)` config** — it runs with defaults.

```
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix
```

There is no PHPStan/Psalm, no CI workflow, and no Makefile/task runner — don't invent commands.

## Architecture map

- Routes are PHP attributes; `config/routes.yaml` only mounts `src/Controller/`. Add new routes with `#[Route]` on controllers.
- Service wiring is autowire + autoconfigure over `App\` (`config/services.yaml`), excluding `src/DependencyInjection/`, `src/Entity/`, `src/Kernel.php`.
- `App\CropsByMonth` is the core service: injects `ClockInterface` + `EntityManagerInterface`, returns `Crop` entities whose `plantingMonth` matches `$clock->now()->format('m')` (zero-padded month, e.g. `09`). `HomeController` renders their `commonName` into `templates/home.html.twig`.
- `src/Repository/` is currently empty; Doctrine falls back to the default `EntityRepository`.

## Env

Real env vars beat `.env`; `.env.local` and `.env.*.local` are gitignored. `.env.test` carries test-only overrides (`KERNEL_CLASS`, deprecation helper, Panther settings).
