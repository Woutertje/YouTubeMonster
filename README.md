# YouTubeMonster

YouTubeMonster is an opensource PHP project. I created this project for fun. If you want to make some changes, for the project and send a pull request, I'll seriously review them for deployment. Find some security issues? Please submit the issue on GitHub.
https://github.com/FuturePortal/YTM

YouTubeMonster is written from scratch. This to make everything so fast and efficient as possible. Got some improvements? Feel free to fork!

### Not live yet

I'm currenlty working on a projects setup for GitHub, this code is not deployed live yet! But it will be! Soon!

# Coding standards

All Pull Requests should follow the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md). This will improve the overal readability of the project.

# Setup YouTubeMonster

The project was created to run on windows and linux servers.

### Install web server
I used XAMPP for my server, make sure you have PHP 5.4 or higher. Also make sure the following modules are active:
`mod_rewrite.so
mod_ssl.so`

### Database
Make sure your webserver runs MySQL
* Create a (empty) database and a user to access it (Collation: `utf8_general_ci`)
* Copy `core/config.php.tmp` and rename the copy to `core/config.php`. Insert your database details.
* Open `dev/updatedatabase.php` in your browser to build the database OR to update the database with new changes from GitHub.

### Finishing touches
Change everything you have to change in the `core/config.php`, or at least the required fields.