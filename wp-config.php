<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wwwphilw_discoverfun');

/** MySQL database username */
define('DB_USER', 'wwwphilw_dfph');

/** MySQL database password */
define('DB_PASSWORD', '123$%dfph');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '3z*+p(W~u&gKukx,OH/bee`zJ[1ZkGod6MC|?PWi^7VLSxhqah3O+3{OSp@$9A%m');
define('SECURE_AUTH_KEY',  '#._}S^!(sVk4TQprfFRVIqOCu<Pgc{vO~e9;{IenjKP1d@|Fyu*%8-=0#}jTo1A2');
define('LOGGED_IN_KEY',    'nEVM$lmujX<ZQfm*/_1bQ2LG}K|o@OQiDX8y,![z%0!zZi,,cQ}4}T{xPpy~&g.%');
define('NONCE_KEY',        '!2@s;4ynEL]LIXY[X_I~%GsA^>4T589Nd<~sDzI_3g.~ja*g-,g.UuOB:)@3EW>[');
define('AUTH_SALT',        'X6AZM>-U$;N2Wl<9|->T3ke4a tI0Ofy*6C>Uk#6WdO;LP,$e#!e?AY3yQLue}Jd');
define('SECURE_AUTH_SALT', '3<!cPP}@#y$zKwocOv7FY:%BUWv{e77apPkQt7neV)a~RRl)wYg[<k3 >1Mj,9P5');
define('LOGGED_IN_SALT',   'BIe8g/L7W[2bR jB)uU_gw1hY> y[nw*$&lEgz idVtuTSI`gq0O;]aG?kz9@URK');
define('NONCE_SALT',       're/,Zk%e=gHpFF9|@yk(i-]<.[,&ZPQKxGj@#nv/$NJ~CWYsqEr69lC^U mUT=`X');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wwwdscvrfn_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
