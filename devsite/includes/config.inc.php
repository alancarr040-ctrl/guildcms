<?php

$DB_HOST = "localhost";	// Database hostname.
$DB_NAME = "your_db";	// Database name.
$DB_USER = "your_db_user";	// Database username - should have read/write access to the table defined in $DB_TABLE.
$DB_PASS = "your_db_pass";	// Database password.

 // Login Database Information.
 $LOGIN_DB_HOST		= $DB_HOST;		// Login database hostname.
 $LOGIN_DB_NAME		= $DB_NAME;		// Login database name.
 $LOGIN_DB_USER		= $DB_USER;		// Login database username - only needs read-only access here.
 $LOGIN_DB_PASS		= $DB_PASS;		// Login database password.

 // GuildCMS log file. If you want a log file, then this file must be writable by
 // the user the HTTP daemon runs as.  This is not required, but can be useful for debugging.
 $LOGFILE		= 'site.log';

 // Email Configuration
 $ADMIN_EMAIL	= 'webmaster@yoursite.com';
 $MAIL_FROM		= 'site@yoursite.com';

 // Page Title
 $TITLE			= "GuildCMS :: A Multi-Gaming Guild";
 $PNAME			= ":: News";

 // Base URLs and Paths
 $baseurl = "http://www.yoursite.com";		// Required: The external URL used to access the guildcms directory.
 $guildcms = "index.php";			// Required: You should not need to ever change this.
 $GUILDCMS = $baseurl . "/" . $reportsdb;
 $SITE = "http://www.yoursite.com/";
 $server_path = '/home/theregs/public_html';		// Required: Full path to the reportsdb directory.

 // RSS Options
 $RSS_FILE = $server_path . '/rss.xml';

 // Misc.
 $cookie_timeout = 0;	// Session cookie timeout in seconds (Default: 0).

///////////////////////////////////////////////////////////////////////////////////////////////
// Internal Variables - Do Not Edit.
///////////////////////////////////////////////////////////////////////////////////////////////
 // Misc. Global Variables.
 // Arbitrarily editing these values will break things. Changing values will often
 // require a change to the database as well.
 $db_connection			= "";
 $login_db_connection		= "";
 $ERR_MSG			= "";
 $PRINTABLE_WIDTH		= 800;	// Width for the printable.
 $USER_MANAGEMENT		= 1;

 // Constants
 DEFINE( "GUILDCMS_NAME", "GuildCMS :: A Multi-Gaming Guild" );
 DEFINE( "REPORTS_DB_NAME_LINK", "<a href=\"http://www.yoursite.com/\" style=\"text-decoration:none\">GuildCMS :: A Multi-Gaming Guild</a>" );
 DEFINE( "GUILDCMS", "1.0" );
 DEFINE("IMAGE_DISPLAY", 5);
 DEFINE( "DEV_NAME", "GuildCMS" );
 DEFINE( "DEV_EMAIL", "webmaster@yoursite.com" );
 DEFINE( "ERR_UNDEF", -1 );
 DEFINE( "ERR_PERM", -2 );
 DEFINE( "ERR_FRETR", -4 );
 DEFINE( "ERR_FOPEN", -8 );
 DEFINE( "ERR_DB_NODATA", -16 );
 DEFINE( "ERR_UNDEF_EXIT", -32 );
 DEFINE( "DEBUG", 0 );

?>
