Crontab generator from JSON
========

Code for deployment
--------

```php

try {
    $generator = \Krolikoff\CronGen\Generator::init()->setJson('/path/to/config.json')->generate->install();
}
catch (\Exception $e) {
    echo $e->getMessage();
}

```

Sample JSON-config
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
	    "schedule": "every hour at 22 minute",
	    "command": "{PREFIX} admin upload photos"
	}
    ]
}
```
