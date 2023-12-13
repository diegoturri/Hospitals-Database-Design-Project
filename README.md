# Hospitals-Database-Design-Project
Design and Implement a Database for a hospital chain

## Manual (MacOS)

### XAMPP 8.2.4 (Apache + MariaDb + Php + Perl)

	1 - download it at this link: https://www.apachefriends.org/download.html
	2 - install it
	3 - in the XAMPP application file etc/php.ini remove the ';' to:
		- extension=php_pdo_pgsql.dll
		- extension=php_pgsql.dll
		- extension="pgsql.so"
This will enable the extension to connect with our PostgreSQL database.


### PostgreSQL 16.1

	1 - download it at this link: https://www.postgresql.org/download/
	2 - Install it 
	- default port: 5432
	- password: diegoturri.com


### Prepare the PostgreSQL database

	- open PostgreSQL
	- on the left of the screen open Servers
	- right click on Databases and click Create -> Database
	- database name: hospitals, owner: postgres
	- Save it
	- now let's right click on our new database 'hospitals' (you can find under 'Databases' on the left on the screen)
	- click on Query Tool
	- copy and paste inside this screen the content of the DDL file in the project folder
	- execute this query
Now the database is created and ready to connect with the project.


### Prepare the Execution

	1 - download the project
	2 - place the project folder inside XAMPP htdocs folder
	3 - start XAMPP and click on "Manage Servers" and then on "Restart All" to restart all the servers
	4 - open the browser and type the url "localhost/project_folder_name"
	5 - now navigate the project folder files in the browser and open main.php

### Execution

	1 - insert the credentials 
		- username: diegoturri
		- password: diegoturri.com
	2 - select the table you want manage and click the select button
	3 - use the interface to insert, delete or update a value in the selected table
