Technical Support Boat
===============
This script is an example of technical implementation of technical support via bot telegrams. Web interfacing allows to receive and respond to user messages sent to the telegram by a bot.

### Installation
To run the script, you need a web server with support for PHP and the MySQL database. 

Just copy the script to your web server. Now you need to configure the configuration file.

```sh
/php.inc/config.php
```

Configuring database settings:

```php
$config['hostname'] = 'localhost';
$config['username'] = '';
$config['password'] = '';
$config['database'] = '';
$config['prefix']   = 'test_';
```

Specify the API key of the bot* telegram:

```php
$config['telegram_key'] = '';
```
*creation of new telegrams of the bot is described in detail on the Internet

The update gets through the getUpdates method, so you need to add the file to the cron file update.php.

Download the SQL dump (located at the root) in your created database.