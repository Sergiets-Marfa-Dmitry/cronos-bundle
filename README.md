# Cronos Bundle

A bundle for Symfony 4/5/6/7 that allows you to use `@Cron` annotations or `#[Cron]` attribute to configure when cron should run your console commands.

Uses the [Cronos](https://github.com/mybuilder/cronos) library to do the actual output and updating.

## Installation

### Install with composer

Run the composer require command:

```bash
$ composer require mybuilder/cronos-bundle
```

### Enable the bundle

If you do not use Symfony Flex, enable the bundle in the `config/bundles.php` for Symfony 4/5/6/7:

```php
return [
    MyBuilder\Bundle\CronosBundle\MyBuilderCronosBundle::class => ['all' => true],
];
```

### Configure the bundle

You can add the following to your `config/packages/my_builder_cronos.yaml` (Symfony 4/5/6) to configure the package.

```yaml
my_builder_cronos:
    exporter:
        key: unique-key
        mailto: cron@example.com
        path: /bin:/usr/local/bin
        executor: php
        console: bin/console
        shell: /bin/bash
```

| option   | description                                                                                                                                                                            |
|----------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| key      | Unique key that wraps all the cron configured for the current application.                                                                                                             |
| mailto   | Sets the default email address for all cron output to go to.                                                                                                                           |
| path     | Sets the path for all commands in the crontab it works just like the shell PATH, but it does not inherit from your environment. That means you cannot use ~ or other shell expansions. |
| executor | Allows you to specify a program that all commands should be passed to such as `/usr/local/bin/php`.                                                                                    |
| console  | Allows you to specify the console that all commands should be passed to such as `bin/console`.                                                                                         |
| shell    | Allows you to specify which shell each program should be run with.                                                                                                                     |

## Usage

The first step is to add the use case for the annotation to the top of the command you want to use the @Cron annotations in.

```php
use MyBuilder\Bundle\CronosBundle\Annotation\Cron;
```

Then add to the phpdoc for the command class the '@Cron' annotation which tells cron when you want it to run.
This example says it should be run on the web server, every 5 minutes, and we don't want to log any output.

```php
/**
 * Command for sending our email messages from the database.
 * 
 * @Cron(minute="/5", noLogs=true, server="web")
 */
#[Cron(minute: "/5", noLogs: true, server: "web")
class SendQueuedEmailsCommand extends Command {}
```

### Specifying when to run

The whole point of cron is being able to specify when a script is run therefore there are a lot of options.

You should read the [general cron info](https://en.wikipedia.org/wiki/Cron) for a general idea of cron and what you can use in these time fields.

**Please note** You CANNOT use `*/` in the annotations, if you want `*/5` just put `/5` and [Cronos](https://github.com/mybuilder/cronos) will automatically change it to `*/5`.

### Attributes examples

| attribute                                                      | description                               |
|----------------------------------------------------------------|-------------------------------------------|
| `#[Cron(minute: "/5")]`                                        | Every 5 minutes                           |
| `#[Cron(minute: "5")]`                                         | At the 5th minute of each hour            |
| `#[Cron(minute: "5", hour: "8")]`                              | 5 minutes past 8am every day              |
| `#[Cron(minute: "5", hour: "8", dayOfWeek: "0")]`              | 5 minutes past 8am every Sunday           |
| `#[Cron(minute: "5", hour: "8", dayOfMonth: "1")]`             | 5 minutes past 8am on first of each month |
| `#[Cron(minute: "5", hour: "8", dayOfMonth: "1", month: "1")]` | 5 minutes past 8am on first of of January |
| `#[Cron(minute: "/5", params: "--user=barman")]`               | Every 5 minutes, with a custom param      |

## Building the cron

You should run `bin/console cronos:dump` and review what the cron file would look after it has been updated.
If everything looks ok you can replace your crontab by running the command below.

`bin/console cronos:replace`

You can also limit which commands are included in the cron file by specifying a server, and it will then only show commands which are specified for that server.

### Exporting the cron

    bin/console cronos:dump --server=web
    bin/console cronos:replace --server=web

### Environment

You can choose which environment you want to run the commands in cron under like this.

`bin/console cronos:replace --server=web --env=prod`

## Troubleshooting

* When a cron line is executed it is executed with the user that owns the crontab, but it will not execute any of the users default shell files so all paths etc need to be specified in the command called from the cron line.
* Your crontab will not be executed if you do not have usable shell in `/etc/passwd`
* If your jobs don't seem to be running check the cron daemon is running, also check your username is in `/etc/cron.allow` and not in `/etc/cron.deny`.
* Environmental substitutions do not work, you cannot use things like `$PATH`, `$HOME`, or `~/sbin`.

---

Created by [MyBuilder](https://www.mybuilder.com/) - Check out our [blog](https://tech.mybuilder.com/) for more insight into this and other open-source projects we release.