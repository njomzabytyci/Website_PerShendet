<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '%HPX53tjYzMyTE=,Vc]{zx~8B%h~18EDZf=I__px:vTs$+syg~C!+^;vN46CR2/X' );
define( 'SECURE_AUTH_KEY',   'K&!,g38BC[X%(,[cLyKx?V{^0}d^0pVaf@$([~0WJ)TW)c~n~Apkf-o8Z|*x$7.v' );
define( 'LOGGED_IN_KEY',     '0CoX)PN<5o83zopFCO%}LLmct5j]hrqRVPKj8.74,,>?U&Pw0E6Wfg;yo|p&,:Yn' );
define( 'NONCE_KEY',         's2[N^|`,$*}[(EpA)Chd6K]X( )J;$ 38}ijp9=cKY_E6vy?H3ly-iZfx|[H__W0' );
define( 'AUTH_SALT',         'YoM,$u]fHLt4 7{BE<V-b9,Jq9{D?fmxnr7TR/eY-K+T%~J+P+0ym7d cD,Ru4},' );
define( 'SECURE_AUTH_SALT',  '6x1~(V[fGr|ud=_TZoK}D7(!d)&vX#^Fi,4F~,aKtq7*wrt-(%Ua*}bUrbf(<JoP' );
define( 'LOGGED_IN_SALT',    '-Bdn#DA,<yk6?rL#8_9DrU_W+Xn=0H3%ana^ox:a^8%Q&~ @P*I}TTb?FlK|{D#*' );
define( 'NONCE_SALT',        '&!]>/%J]t@wNIa&Z`;q&GOeSrh({?H>n|1 88C2m!5SNX`%X9kIsjZ2ag9$f#~-q' );
define( 'WP_CACHE_KEY_SALT', '9VQcS}mhuHJJ/Uv%)?do7m-9S.N!*suCndjGj,|wvgT)I)-f?ysJtjucx@W7vgM/' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
