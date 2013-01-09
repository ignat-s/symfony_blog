Symfony Blog
========================

This blog made as example of simple application based on [Symfony 2][5], [Doctring ODM][6] and [MongoDB][7].

Installation
------------

Step 1: Install [MongoDB][7] and corresponding PHP extension

If you still don't have MongoDB installed it's time to get it. Follow this [installation guide][2].
You also have to enabled PHP extension for [MongoDB][7]. You can use this [installation guide][3].

Step 2: Set configuration

Create file app/config/parameters.yml. You can copy it from app/config/parameters.yml.dist. Make sure that
mongodb_server and mongodb_database database_paths are correct.

Also change secret parameter with other unique value.

Step 2: Get [Composer][1]

Composer is a tool for dependency management in PHP. It allows you to declare the dependent libraries your project needs
and it will install them in your project for you.

To install it globally on linux use these commands:


   ```
   $ curl -s https://getcomposer.org/installer | php
   $ sudo mv composer.phar /usr/local/bin/composer
   ```

Step 3: Install vendors

Go to project root directory and run following command:

   ```
   composer install
   ```

Configuring web server
----------------------

For Apache virtual host configuration might look like that:

    ```
    <VirtualHost *:80>
        DocumentRoot "/var/www/symfony_blog/web/" # in your environment path might differs
        ServerAdmin admin@localhost
        ServerName symfony-blog.local # in your environment server name might differs

        <Directory "/var/www/symfony_blog/web/"> # in your environment path might differs
            Options FollowSymLinks
            Options all
            AllowOverride All
        </Directory>
    </VirtualHost>
    ```

Open URL http://symfony-blog.local/app_dev.php/ (your URL might be different). You should see blog start page.


Running tests
-------------

You need installed [PHPUnit][4].

Create app/phpunit.xml file from app/phpunit.xml.dist. You can run all tests using command.

    ```
    phpunit -c app/phpunit.xml
    ```

Enjoy!

[1]: http://getcomposer.org/
[2]: http://docs.mongodb.org/manual/installation/
[3]: http://php.net/manual/en/mongo.installation.php
[4]: http://www.phpunit.de/
[5]: http://symfony.com/
[6]: http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/index.html
[7]: http://www.mongodb.org/