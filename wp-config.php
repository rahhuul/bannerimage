<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'bannerplugin');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'ifV>k_,OL4k`kll:gp:e-#&W2VtK{bmR3i#D!^>4{+W<h2,riQz$B_L@UVDKhu3V');
define('SECURE_AUTH_KEY',  'I2!@5)Q)D7&L+vmu#4)[{8-# [YBN.ztS~lSGfu3B.777JxDY1|t[x|bN%hZD,TM');
define('LOGGED_IN_KEY',    'lGGBMJ~2wx2^STa!VG,gE&_zfurcEt]JY7L#4361@,^p~$Z3edhaW}LUbFYjQD_a');
define('NONCE_KEY',        '>e*cRF!^@Rl69D7>;mbi|D@qhe4P[VG@0e5@_3H:>3=8z-` :DUbin8B]zR0wC{:');
define('AUTH_SALT',        ')(sjzMDbr(jlDoa{u4=aYWQtG!<0}x5r2:1vC)?qf~qJ*pL Bw0O C{!++@OdesJ');
define('SECURE_AUTH_SALT', '62*VpC!qfYov^GRW?qui}<mCfb37C]fXf#C;)->Ranm%|XtPsR$FtPpL#+1Aq.v_');
define('LOGGED_IN_SALT',   '2o6BU,@D6m~KEqr;b}E{P%)E=cwxteq%w3))>c{.i+7A=100x$tY=yI.p*@8ZvCm');
define('NONCE_SALT',       '`$5Cpl>i,AO @I7Fm,vcGRi:xrL}wNqpv+opidwv}JvT4O|QNxjf&&GW9LFzy(r1');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */

define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
