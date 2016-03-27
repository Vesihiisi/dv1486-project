# MVC Project

This repository is part of an assignment DV1486 Databased Web Applications with PHP and MVC framework, at Blekinge Tekniska Högskola, Sweden, spring term 2016. [Assignment specification](http://dbwebb.se/phpmvc/kmom10). [Detailed report](http://www.student.bth.se/~alkw15/dbwebb-kurser/mvc/kmom05/Anax-MVC/webroot/index.php/redovisning).

As per requirement 1-3, now follows an installation description.

## Installation description

1. Download the contents of this repository into a directory on an Apache server.

```https://github.com/Vesihiisi/dv1486-project.git```

2. The project has a number of external dependencies that are needed for it to function. Those are listed in the composer.json file and also in composer.lock which contains the exact version of the dependencies used. These files are in the main directory and will be used to download the dependencies with composer. Composer will check the composer.lock file and if it exists it will install those specific versions.

```composer install```

3. The project uses an sqlite database. The database configuration file is app/config/database_sqlite.php. There you will find the path to the database file. You will also have to create the database file according to the path in that file.

4. Finally, initialize the database with all the tables necessary and example data, as per requirement 1-3. Open the webroot directory in a web browser and access the route index.php/setup. That will create the database tables and populate them with example data.
