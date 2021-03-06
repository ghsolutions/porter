# Porter

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/konsulting/porter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/konsulting/porter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/konsulting/porter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/konsulting/porter/build-status/master)

A [Docker](https://www.docker.com) based multi-site setup for local development. Inspired by [Laravel Valet](https://github.com/laravel/valet) & [Homestead](https://github.com/laravel/homestead) and [Shipping Docker's Vessel](https://github.com/shipping-docker/vessel), [Shipping Docker](https://serversforhackers.com/shipping-docker) and [Docker For Developers](https://bitpress.io/docker-for-php-developers/).

We're still learning Docker, and open to improvements to this set up and we're 'dog-fooding' it as we go. **Porter is currently in Alpha state**, we're refining as we move along.

Our aim is to use this for day-to-day development with simple, portable usage. We use Macs for our development, but given the portable nature of Docker we'd like to enable this offering to allow usage across each of MacOS, Linux and Windows.

Porter is developed using [Laravel-Zero](https://laravel-zero.com/).

Contributions are welcome.  We are a small company, so please be patient if your question or pull request need to wait a little.

## Requirements

 - Docker 18.06+
 - Docker Compose (1.22+)
 - PHP 7.1+ on host machine

## Installation

 - Install [Docker](https://www.docker.com/community-edition)
 
 - Login to docker (this will allow Porter to pull the images it needs). `docker login`
 
 - Clone Porter to a directory and install it's dependencies.
 
    ```
    git clone git@github.com:konsulting/porter.git
    cd porter
    composer install
    ```
 
 - Add Porter to your $PATH (e.g. in .bash_profile)
 
    ```
    export PATH="[path to porter]:$PATH" 
    source .bash_profile
    ```
 
 - Set up routing... you have some options.
   
   1. Use the DNS container shipped with Porter.  Update your machine's network DNS settings to point to 127.0.0.1 before other name servers. The container will resolve the domain for Porter. You will need to turn off locally any installed DNSmasq since the DNS container opens to port 53 on localhost. (e.g. `brew services stop dnsmasq`)
   
   2. Use your existing Laravel Valet domain - which uses DNSmasq installed locally on a Mac.
   
   3. Manually edit your `/etc/hosts` file for each domain.
   
   4. Roll your own solution.

 - Porter binds to ports 80 and 443, so you need to turn Valet off (`valet stop`) or any other services that are bound to them before using it.
 
 - In your terminal `cd` to the directory where your sites are located, and run `porter begin`
 
 - Finally run `porter start`
 
## Usage

Porter uses a simple set of commands for interaction (see below).

Sites are added manually. This allows us to set up each one up with its own NGiNX config. To add your first site, move to its directory and run `porter unsecure`.

Porter adds two simple environment variables to the PHP containers. 

1. `RUNNING_ON_PORTER=true` allowing you to identify when a site is running on Porter. 
2. `HOST_MACHINE_NAME=host.docker.internal` allowing you to resolve to services running directly on the host machine. The value for this changes every now and then, so this means you have less to remember.

Access them in PHP using:
```php
getenv('RUNNING_ON_PORTER')
getenv('HOST_MACHINE_NAME')
```

## Commands:

 - `porter begin {--home?} {--force?}` - Migrate and seed the sqlite database, and publish config files to `~/.porter/config`. It will set Porter home to the working directory when you run the command (or you can specify with the `--home` option).  It will also download the required docker images.
 - `porter start`
 - `porter status` - show the status of containers
 - `porter stop`
 - `porter restart` - Restart existing containers (e.g. pick up config changes for PHP FPM)
 - `porter logs {service}` - Show container logs, optionally pass in the service
  
### Basic settings

 - `porter domain {domain}` - Set TLD ('test' is the default for domains such as sample.test)
 - `porter home {dir?} {--show}` - Set the home dir for sites, run in the dir to use it directly - or set it specifically. Use `--show` to see the current setting.
 
### Site settings

Site commands will pick up the current working directory automatically.  They also allow you to specify the site by the directory name.

 - `porter site:list` 
 - `porter site:unsecure {site?}` - Set up a site to use http
 - `porter site:secure {site?}` - Set up a site to use https.
 - `porter site:remove {site?}` - Remove a site 
 - `porter site:php {site?}` - Choose the PHP version for site
 - `porter site:nginx-config {site?}` - Choose NGiNX config template for a site, ships with default (/public such as Laravel) and project_root
 - `porter site:renew-certs {--clear-ca}` - Renew the certificates for all secured sites, optionally rebuild CA.

Site NGiNX config files are created programmatically using the templates in `resources/views/nginx`. The config files are stored in `~/.porter/config/nginx/conf.d`.

NGiNX logs are visible from the `porter logs nginx`.

Porter will try to set your Mac up to trust the SSL certificates it generates by adding the generated CA to the keychain (it will request sudo permission). This works for Safari and Chrome, but not for Firefox.  In FireFox, you will need to manually add the certificate, which is located in `~/.porter/ssl/KleverPorterSelfSigned.pem`.

### PHP

 - `porter php:default` - Set default PHP version
 - `porter php:list` - List the available PHP versions
 - `porter php:open {run?} {--p|php-version?}` - Open the PHP cli for the project, if run from a project directory, it will select the associated version. Otherwise, you can select a version or use the default. Optionally run a command, such as `vendor/bin/phpunit` (if you need to pass arguments, wrap in quotes). 
 - `porter php:tinker` - Run Artisan Tinker in the project directory
  
`php.ini` files are stored in `~/.porter/config` by PHP version. If you change one, you'll need to run `porter php:restart` for changes to be picked up. 

We currently ship with containers for PHP 5.6, 7.0, 7.1 and 7.2.

### Node (npm/yarn)
 - `porter node:open {run?}` - Open Node cli, run in project dir. Optionally run a command, such as `npm run production` (if you need to pass arguments, wrap in quotes). 

### MySQL
Enabled by default. Available on the host machine on port 13306. The user is `root` and the password `secret`. You can connect with your favourite GUI if you want to.

 - `porter mysql:on`
 - `porter mysql:off`
 - `porter mysql:open` - Open MySQL cli

MySQL data is stored in `~/.porter/data/mysql`.

### Redis

Enabled by default. Available on the host machine on port 16379`.

 - `porter redis:on`
 - `porter redis:off`
 - `porter redis:open` - Open Redis cli

Redis data is stored in `~/.porter/data/redis`.

## DNS

 - `porter dns:flush` - flush your local machine's DNS in cases where it's getting a bit confused, saves you looking up the command we hope.

## Email

We have a [MailHog](https://github.com/mailhog/MailHog) container, all emails are routed to this container from PHP when using the `mail()` function. 

You can review received emails in MailHog's UI at [http://localhost:8025](http://localhost:8025/). Or, you can use the MailHog API to inspect received emails.

## PHP Extensions

We have added a number of PHP extensions to the containers that we use frequently. Notable ones are Imagick and Xdebug. 

### Xdebug

Xdebug is available on each PHP container. `xdebug.ini` files are stored in `storage/config` by PHP version.

It is set up for use with PHPSTORM, and on demand - you can use an extension such as Xdebug helper in Chrome to send the Cookie required to activate a debugging session ([Jetbrains article](https://confluence.jetbrains.com/display/PhpStorm/Configure+Xdebug+Helper+for+Chrome+to+be+used+with+PhpStorm)).

Xdebug is set up to communicate with the host machine on port 9001 to avoid clashes with any locally installed PHP-fpm.

## Browser Testing

We like [Laravel Dusk](https://laravel.com/docs/5.6/dusk), and also help with [Orchestra Testbench Dusk](https://github.com/orchestral/testbench-dusk) for package development. Porter provides a browser container with Chrome and Chromedriver for browser testing.

The browser container can be turned on and off (default on), in case it is not required.

- `porter browser:on`
- `porter browser:off`

Notes for your test setup...

 - Dusk and Testbench Dusk use a PHP based server running from the command line. With Porter, the server must be run at `0.0.0.0:8000` for it to be available to the browser container
 - The remote web-driver must point to the browser container at `http://browser:9515`
 - The url for testing needs to be the hostname of the PHP CLI container (where the tests are running) - which can be retrieved through `getenv('HOSTNAME')`
 - Finally, we need to add `--no-sandbox` to the options for Chrome and it should run '--headless'.

## SSH Keys

Porter include a `~/.porter/config/user/ssh` directory which is linked to the root user `.ssh` dir in the PHP cli containers and the Node container.

This means you can add the ssh keys you want to use in your dev environment specifically (if any).
 
## Tweaking things

As Porter is based on Docker, it is easy to add new containers as required or to adjust the way the existing containers are built. 

The docker-compose.yaml file is built using the views in `resources/views/docker-compose`.

The NGiNX config templates are in `resources/views/nginx`.

The following commands will be useful if you change these items.

 - `porter build` - (Re)build the containers.
 - `porter images:build` - Build the current container images.
 - `porter images:pull` - Pull the current images - which will then be used by docker-compose where it can.
 - `porter images:set` - Change the image set used for Porter. The default is `konsulting/porter-ubuntu`.
 - `porter make-files` - (Re)make the docker-compose.yaml, and the NGiNX config files.

We store personal config in the `.porter` directory in your home directory - keeping config and data separate from the main application. It includes:

 - `composer` - a composer cache dir, allowing the containers to avoid pulling as much info when using composer
 - `config` - containing the specific files for customisation of the containers/services
 - `data` - containing data for the MySQL and Redis containers by default
 - `ssl` - the generated SSL certificates used by Porter
 - `views` - allows the override and addition of views for building NGiNX configurations for example, and the `docker-compose.yaml` views for use with alternative images
 - a `docker` directory can be added to include alternative docker machine scripts similar to the original `konsulting/porter-ubuntu` and `konsulting/porter-alpine` in the project's `docker` directory
