# Docker

## Create containers

```bash
# cd to the location where you cloned the project
$ cd ~/projects/prestashop/tests/E2E

# Because the container build command copy sources instead of mounting to prevent file editions.
$ docker-compose build
$ docker-compose up -d
```

This command starts the containers. The parameter -d makes them run in the background. If you omit the -d you'll see the log.

Docker should start building (if it's the first time for these images) and running with the containers in the background.
Wait some seconds

## Run tests

```bash
$ docker-compose exec tests bash /tmp/run-tests.sh
```

