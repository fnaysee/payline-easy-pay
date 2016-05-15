<?php
### Database Information

# Database host name
define( 'DBTYPE', 'mysql' );

# Database host name
define( 'DBHOST', 'localhost' );

# This site database name __database_name__
define( 'DBNAME', 'easy-pay' );

# Database user __database_user_name__
define( 'DBUSER', 'root' );

# Database user password __database_user_password__
define( 'DBUSERPASS', '' );


### Script paths and urls

# Website path ( Don't edit )
define( 'ROOTPATH', dirname( __FILE__ ) . '/' );

# Website domain, subdomain or directory url ( Edit )
define( 'ROOTURL', 'http://localhost/easy-pay/' );

define( 'ADMINPATH', ROOTPATH . 'admin/' );
define( 'ADMINURL',  ROOTURL . 'admin/' );

define( 'INCLUDESPATH', ROOTPATH . 'includes/' );
define( 'INCLUDESURL', ROOTURL . 'includes/' );

define( 'TEMPLATESPATH', ROOTPATH . 'templates/' );
define( 'TEMPLATESURL', ROOTURL . 'templates/' );

define( 'PLUGINSPATH', ROOTPATH . 'plugins/' );
define( 'PLUGINSURL', ROOTURL . 'plugins/' );

### Debug mode

# Change to true only in development
define( 'DEBUG', true );


### Visitors access control

# Change to false to prevent checking tables on each run
define( 'ENABLEADMIN', true );

# Change to false to prevent checking tables on each run
define( 'ISFIRSTRUN', false );
