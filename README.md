# Setup

First, make sure you are running the latest version of Docker and Docker Compose.

**Windows is not supported, period.**

On **macOS**, add those lines in your `/etc/hosts` file:

```
127.0.0.1   web.localhost
127.0.0.1   phpmyadmin.web.localhost
```

Clone the project, move to its root directory and run:

```bash
$ cp .env.template .env
$ make up
```

This command will start the stack and also run the following commands in the `web` service:

* `composer install`: installs PHP dependencies
* `yarn install`: installs frontend dependencies
* `php bin/console doctrine:migrations:migrate --no-interaction`: runs the migration patches on the database

Once the `web` service is ready, you may now run bash inside it with:

```bash
$ make bash
```

Here you have to run the following commands:

```bash
$ yarn dev
# builds the frontend application...
$ php bin/console doctrine:fixtures:load --append
# adds fake data in the database...
```

**Note:** a watcher is also available with `yarn watch`.

# Available commands

```bash
$ make up
```

Starts the stack.

---

```bash
$ make down
```

Stops the stack

---

```bash
$ make bash
```

Runs bash inside the `web` service.

# Workflow before each push

Run the following command in the `web` service:

```bash
$ composer run csfix && composer run cscheck && composer run phpstan
```

# Workflow for database tables generation

Go the `web` service:

```bash
$ make bash
```

Create your PHP entity:
```bash
$ php bin/console make:entity
```

Generate the migration script:

```bash
$ php bin/console doctrine:migrations:diff
```

Execute the migration script to generate your db:

```bash
$ php bin/console doctrine:migrations:migrate
```