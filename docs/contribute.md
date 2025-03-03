# Contribute to this plugin

> First, be sure to [get the stack installed using the docker-compose guide](install-with-docker-compose.md).
# Play with crowdsec state

```bash

# Add captcha your own IP for 15m:
docker-compose exec crowdsec cscli decisions add --ip ${DOCKER_HOST_IP} --duration 15m --type captcha

# Ban your own IP for 15 sec:
docker-compose exec crowdsec cscli decisions add --ip ${DOCKER_HOST_IP} --duration 15s --type ban

# Remove all decisions:
docker-compose exec crowdsec cscli decisions delete --all

# View CrowdSec logs:
docker-compose logs crowdsec
```

> Note: The `DOCKER_HOST_IP` environnment variable is initialized via `source ./load-env-vars.sh`.

# WP Scan pass

```bash
docker-compose run --rm wpscan --url http://wordpress5-6/
```

## Reinstall `composer` dependencies

```bash
docker-compose exec wordpress5-6 composer install --working-dir /var/www/html/wp-content/plugins/cs-wordpress-bouncer --prefer-source
```

> In this dev environment, we use `--prefer-source` to be able to develop the bouncer library at the same time. Composer may ask you for your own Github token to download sources instead of using dist packages.


### Quick `docker-compose` cheet sheet

```bash
docker-compose run wordpress sh # run sh on wordpress container
docker-compose ps # list running containers
docker-compose stop # stop
docker-compose rm # destroy
```

### Try the plugin with another PHP version

```bash
docker-compose down
docker images | grep crowdsec-bouncer_wordpress # to get the container id
docker rmi <container-id>
```

Then, in the `.env` file, replace:

```bash
CS_WORDPRESS_BOUNCER_PHP_VERSION=7.2
```

with :

```bash
CS_WORDPRESS_BOUNCER_PHP_VERSION=<the-new-php-version>
```

Then re-run the stack.

### Try the plugin with another WordPress version


The plugin is tested under each of these WordPress versions: `5.8`,`5.7`,`5.6`, `5.5`, `5.4`, `5.3`, `5.2`, `5.1`,
`5.0`, `4.9`.
(Representing [more than 90% of the wordpress websites](https://wordpress.org/about/stats/))

#### Add support for a new WordPress version

This is a cheat sheet to help testing briefly the support:

```bash

# To install a specific version
docker-compose up -d wordpress<X.X> crowdsec mysql redis memcached && docker-compose exec crowdsec cscli bouncers add wordpress-bouncer

# To display the captcha wall

docker-compose exec crowdsec cscli decisions add --ip ${DOCKER_HOST_IP} --duration 15m --type captcha

# To delete the image in order to rebuild it

docker-compose down && docker rmi wordpress-bouncer_wordpress<X.X>

# To debug inside the container

docker-compose run wordpress<X.X> bash
```

> Note: The `DOCKER_HOST_IP` environnement variable is initialized via `source ./load-env-vars.sh`.

### Plugin debug mode VS production mode

The debug mode throws verbose errors. The production mode hides every error to let users navigate in every edge cases.

This plugin goes in debug mode when Wordpress debug mode is enabled.

To try the production mode of this plugin, just disable the wordpress debug mode: in `docker-compose.yml`, comment the line:
```yml
    WORDPRESS_DEBUG: 1 # Comment this line the simulate the production mode
```

### Display the plugin logs

```bash
tail -f logs/debug-*
```
