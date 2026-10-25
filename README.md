# OpenPeer

Peer-to-peer expert marketplace built with Laravel 13 and Inertia/React, backed by PostgreSQL 18 running in Laravel Sail.

## Requirements

- Docker Desktop
- PHP 8.3 (CLI, used by `composer test` and other tooling)
- Node.js 22
- Composer 2

## Local setup (first time)

```bash
composer install
cp .env.example .env
vendor/bin/sail up -d --wait
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate
npm install
npm run dev
```

The application is served at http://localhost (Vite on port 5173, PostgreSQL on port 5432).

> Do not run `composer setup` locally: its `artisan migrate` step runs on the host, which cannot reach the `pgsql` container hostname. It is intended for CI. Use the Sail-based steps above for local database setup.

## Daily workflow

| Task | Command |
|---|---|
| Start containers | `vendor/bin/sail up -d --wait` |
| Stop containers | `vendor/bin/sail down` |
| Run migrations | `vendor/bin/sail artisan migrate` |
| Database shell | `vendor/bin/sail psql` |
| Vite dev server | `npm run dev` |
| Production assets | `npm run build` |
| Test suite | `composer test` (requires Docker running) |
| Lint / format | `npm run check:fix` · `vendor/bin/pint` |
| Static analysis | `composer types:check` |

If `vendor/bin` is on your `PATH`, the `vendor/bin/sail` prefix can be shortened to `sail`.

## Ports and overrides

The compose stack publishes port 80 (app), 5173 (Vite) and 5432 (PostgreSQL). If a port is already taken, set the corresponding variable in `.env`:

| Variable | Default | Purpose |
|---|---|---|
| `APP_PORT` | `80` | Host port for the application |
| `VITE_PORT` | `5173` | Host port for the Vite dev server |
| `FORWARD_DB_PORT` | `5432` | Host port for PostgreSQL |

## Testing notes

- `phpunit.xml` runs the suite against the `testing` database on PostgreSQL 18.
- Sail provisions the `testing` database on first boot through its init script; `RefreshDatabase` manages its schema.
- `composer test` runs Pint, PHPStan (1 GB memory limit) and Pest from the host, connecting to PostgreSQL at `127.0.0.1:5432` — keep Docker running.
- Do not run the suite through `sail test`: `phpunit.xml` pins `DB_HOST=127.0.0.1`, which is only reachable from the host.

## Troubleshooting

- `vendor/bin/sail ps` — service status (the app health check hits `/up`, PostgreSQL uses `pg_isready`).
- `vendor/bin/sail logs` — container logs.
- `vendor/bin/sail down -v` — drops the PostgreSQL volume (destructive). The `openpeer` and `testing` databases are recreated on the next start.

## Documentation

Phase documents live in `docs/`: architecture, decisions and planning for phases 0 and 1.
