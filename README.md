Crontab generator from JSON
========

This generator allows you store your cron-tasks configs in your VCS as a simple JSON-file and install it on server after deployment. You can describe job execution time as an human friendly string:
* every 12 minutes
* every hour at 22 minute
* every day at 10:20

This is the concepts of JSON-format:
* Profile - cron enviroment, for example you can disable some tasks on test server
* Each profile has variables, which will be replaces in command line. It can be path to your framework console.php file or other cli utility.
* Jobs - it's your cron commands, described with following properties
** title - just task name
** is_enabled - you can temporaly disable your task
** profiles - on which profiles this job will run
** schedule - human friendly string, describing when job will be executed
** command - shell code, which will be executed

Code for deployment
--------

```php
try {
    $generator = \Krolikoff\CronGen\Generator::init()->setJson('/path/to/config.json')
        ->generate('YOUR_PROFILE_NAME')->install();
}
catch (\Exception $e) {
    echo $e->getMessage();
}
```

This config
--------

```json
{
    "profiles": {
        "local": {
            "variables": {
                "PREFIX": "/usr/bin/php /var/www/domain/console.php"
            }
        },
        "www": {
            "variables": {
                "PREFIX": "/usr/bin/php /var/www/vhosts/domain/public/console.php"
            }
        }
    },
    "jobs": [
        {
            "title": "Stat job",
            "is_enabled" : true,
            "profiles": ["local", "www"],
            "schedule": "every 10 minutes",
            "command": "{PREFIX} admin stats calculate"
        },
        {
            "title": "Download job",
            "is_enabled" : true,
            "profiles": ["www"],
            "schedule": "every day at 5:00",
            "command": "{PREFIX} api download stats"
        },
        {
            "title": "Upload job",
            "is_enabled" : false,
            "profiles": ["local"],
            "schedule": "every hour at 20 minute",
            "command": "{PREFIX} admin upload photos"
        }
    ]
}
```

will produce following crontab file for profile "www":

```php
#Stat job
*/10 * * * * /usr/bin/php /var/www/domain/console.php admin stats calculate
#Download job
0 5 * * * * /usr/bin/php /var/www/domain/console.php api download stats
```
