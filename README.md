Tasker
======

Tasker is a simple PHP job scheduler that allows you to specify when your application's jobs should run by using a familiar cron syntax. The benefit is that your app's jobs and the times they run are managed inside your application instead of them living in a crontab (or equivalent) which lies outside of your application.

Requirements
------------
* *nix
* PHP 5.4

Installation
------------
The bundle should be installed using [composer](https://getcomposer.org).

**Add the lib to your composer.json file**

```json
{
    "require": {
        "shideon/tasker": "~0.1",
    }
}
```

Usage
-----
Tasker is simple. Create a cron job (or equivalent) that runs the tasker command every minute. The command requires 2 options. `--config_file` which is a path to your file that contains the jobs to run and `--log_file` which is where notices get sent to.

**Config file - /path/to/config.yml**

```yaml
tasker:
    tasks:
       - name: "My midnight job"
         time: "0 0 * * *"
         command: "sleep 10"
       - name: "My every other minute job"
         time: "*/2 * * * *"
         class: "My\Fkn\Class"
```

**Crontab**
```cron
* * * * * /path/to/tasker/bin/console shideon:tasker --config_file=/path/to/config.yml --log_file=/path/to/log
```

When tasker runs it will loop the jobs and determine if they're due to run and if they are then tasker will execute them in the background.

Configuration & Jobs
--------------------
The config section begins with "tasker", then "tasks" where we define our jobs.

Each job may contain the following values:

* **name** (required) - A unique name for the job.
* **time** (required) - The time the job should run. Any legal cron expression is accepted.
* *Either...*
  * **command** - A command to execute on the sysetm. There is also the special keyword `$console` which translates to the same console that the tasker command was executed from. So the following string `$console my:command --my-option` would be translated to `/path/to/tasker/bin/console my:command --my-option`. *The console in the lib is only aware of the 2 internal lib commands. For more on customizing this, see Symfony's docs for the [console component](http://symfony.com/doc/current/components/console/introduction.html).*
  * **class** - A class to run. The class must implement the `Shideon\Tasker\TaskInterface` interface. If the lib isn't aware of your class' namespace you can use the `file` config value below.
* **file** - Used to specify the file that the above `class` resides in. The file will be `require`d before the class is called on.
  
Logging
-------
Tasker uses [monolog](https://github.com/Seldaek/monolog) to handle logging and it logs to the file that you specify with the `--log_file` option.

The lib creares a `StreamHandler` handler at a log level of `INFO (200)`. You can also pass the `--log_level` option to define the level of logging you want. It must be a number and be equal to one of the class' [log level constants](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Logger.php#L29).

There is currently no way to define different or additional handlers for the logging as it would be extra work to handle from CLI. You can see the `Extending` section though.

Extending
---------
The commands `TaskerCommand` (shideon:tasker) and `RunTaskCommand` (shideon:tasker:run_task) are easily extensible. The most common reason to do this will be to instantiate monolog using custom functionality (which can be done by extending the command and defining your own `buildLogger()` method).

If doing this, remember that `bin/console` will not be aware of your new commands. You will probably want to create a new console that loads your commands then have your system's scheduler call on that instead of the default console. For more on this, see Symfony's docs for the [console component](http://symfony.com/doc/current/components/console/introduction.html).

Copyright
---------
Copyright (c) 2014 John Pancoast <shideon@gmail.com>

License
-------
The MIT License (MIT)
