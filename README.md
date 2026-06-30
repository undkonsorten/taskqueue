taskqueue
===============

Manages tasks given from other extensions

The goal is to manage every time-consuming task asynchronously in the background and not in the user response.
This leads to faster response for the user.
This extension makes heavy usage of the TYPO3 scheduler.

Further documentation is located in the Documentation folder.

## Running tests

Tests are executed inside Docker containers via `Build/Scripts/runTests.sh`. Docker must be installed and running.

### Prerequisites

Install the Composer dependencies first:

```bash
./Build/Scripts/runTests.sh -s composerUpdateMax
```

To target a specific TYPO3 version:

```bash
./Build/Scripts/runTests.sh -t 13.4 -s composerUpdateMax
./Build/Scripts/runTests.sh -t 14.3 -s composerUpdateMax
```

### PHP linting

Check all PHP files for syntax errors:

```bash
./Build/Scripts/runTests.sh -s lintPhp
```

### Unit tests

```bash
./Build/Scripts/runTests.sh -s unit
```

### Functional tests

Run with the default SQLite backend:

```bash
./Build/Scripts/runTests.sh -s functional
```

Run against MySQL or PostgreSQL:

```bash
./Build/Scripts/runTests.sh -s functional -d mysql
./Build/Scripts/runTests.sh -s functional -d postgres
```

### Options

| Option | Description | Default |
|--------|-------------|---------|
| `-s <suite>` | Test suite: `lintPhp`, `unit`, `functional`, `composerUpdateMin`, `composerUpdateMax` | `unit` |
| `-p <version>` | PHP version: `8.3`, `8.4` | `8.4` |
| `-t <version>` | TYPO3 version (for composer steps): `13.4`, `14.3` | `14.3` |
| `-d <dbms>` | Database for functional tests: `sqlite`, `mysql`, `mariadb`, `postgres` | `sqlite` |
| `-b <runtime>` | Container runtime: `docker`, `podman` | auto-detected |
| `-x` | Enable Xdebug (PhpStorm on port 9003) | off |

Full option reference:

```bash
./Build/Scripts/runTests.sh -h
```
