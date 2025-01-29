<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'web_final' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'Sn/+i&?%+a65IP<Vi|.{6?Hb 6pcX!-BKa-NCp[79)K=Kn*P57o$|>Zjm1fl4SC!' );
define( 'SECURE_AUTH_KEY',  ',zUX9*9p{_{V8h$xJ5}`8gp+uzlv,j| Hf,V0)Ow3B!.Btel@bbod%f(e5U]0K`%' );
define( 'LOGGED_IN_KEY',    '*h*)v-)oAXAf)HM-qQ5k1( iao^AEt:0_R(2O<h:HpPU~<zWLQ._*;o1Vc+[eVv/' );
define( 'NONCE_KEY',        '|O/Q]LI-g h){K|JQ19,MUt60GkN +[x3@[t!1,UqKg9Q[F!HG}ABJ! $6OkQ*,Y' );
define( 'AUTH_SALT',        'hUjB7jMpN>m^~JBl.NcVkkz<GmXQo3[0eYp3Vu+hU=L.!MqT-kX[8F@cRrlZq__d' );
define( 'SECURE_AUTH_SALT', 'Hwr&)N&dgz)%&XsoJb&cq&>75F)E.rl?LNl^Z2C0c$5wjx|8Z?10a~z5@g7|>1_<' );
define( 'LOGGED_IN_SALT',   'C1*E|TrmQl<T7jZkD,`$.PFZ)vfrgpv1:+_AYXL;UGh`;!}C;|F9qq*|O/-32;Z(' );
define( 'NONCE_SALT',       'po9_T8u$tY{K$GK~MlgU#cu,,>ZeQs5U*cQGi31T-o|l%qF[-_`!M>b,.WY.>~[j' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
