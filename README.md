# YouTubeMonster

YouTubeMonster is an open source PHP project. If you want to make some changes, fork and send a pull request. I'll seriously review all pull requests for deployment.

Find some security issues? Please submit the issue on GitHub.
https://github.com/FuturePortal/YouTubeMonster

YouTubeMonster is written from scratch. This to make everything as fast and efficient as possible. Got some improvements? Feel free to fork!

### Not live yet

This code is not deployed live yet! I'm still working on upgrading my server and making the project work with `vagrant` and `puppet`.

# Coding standards

All Pull Requests should follow the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). This will improve the overal readability of the project. I'm sorry not all code is following this standard at the moment, everything I update/add will.

# Setup YouTubeMonster

For now, there is no vagrant added to the project YET! You'll need to run a web sever locally, with MySQL.

### Install web server
I used XAMPP for my server, make sure you have PHP 5.4 or higher. Also make sure the following modules are active:
```
mod_rewrite.so
mod_ssl.so
```

### Database
Make sure your web server runs MySQL
* Create a (empty) database and a user to access it (Collation: `utf8_general_ci`)
* Copy `core/config.php.tmp` and rename the copy to `core/config.php`. Insert your database details.
* Open `dev/updatedatabase.php` in your browser to build the database OR to update the database with new changes from GitHub.

### Finishing touches
Change everything you have to change in the `core/config.php`, or at least the required fields.