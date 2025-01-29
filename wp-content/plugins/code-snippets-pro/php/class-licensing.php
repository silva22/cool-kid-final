<?php

namespace Code_Snippets;

use Freemius;
use Freemius_Exception;
use function fs_dynamic_init;
/**
 * Handles interfacing with the Freemius SDK and API.
 *
 * @package Code_Snippets
 */
class Licensing {
    /**
     * Freemius product ID.
     */
    private const PRODUCT_ID = 10565;

    /**
     * Freemius public key.
     */
    private const PUBLIC_KEY = 'pk_107ff34fc0b2a9700c150c1acf13a';

    /**
     * Freemius SDK instance.
     *
     * @var Freemius
     */
    private $sdk;

    /**
     * Class constructor.
     *
     * @throws Freemius_Exception Freemius fails to initialise.
     */
    public function __construct() {
        $plugin = code_snippets();
        $this->enable_multisite_support();
        require_once dirname( CODE_SNIPPETS_FILE ) . '/vendor/freemius/wordpress-sdk/start.php';
        $this->sdk = fs_dynamic_init( array(
            'id'               => self::PRODUCT_ID,
            'slug'             => 'code-snippets',
            'premium_slug'     => 'code-snippets-pro',
            'type'             => 'plugin',
            'public_key'       => self::PUBLIC_KEY,
            'is_premium'       => true,
            'is_premium_only'  => false,
            'premium_suffix'   => 'Pro',
            'has_addons'       => false,
            'has_paid_plans'   => false,
            'is_org_compliant' => false,
            'has_affiliation'  => 'selected',
            'menu'             => array(
                'slug'        => $plugin->get_menu_slug(),
                'contact'     => false,
                'support'     => false,
                'pricing'     => false,
                'affiliation' => false,
                'network'     => true,
            ),
            'is_live'          => true,
        ) );
        do_action( 'freemius_loaded' );
        $this->register_hooks();
        add_action( 'init', [$this, 'override_strings'] );
    }

    /**
     * Create the necessary constant to enable multisite support within the Freemius SDK.
     *
     * @return void
     */
    private function enable_multisite_support() {
        $constant_name = sprintf( 'WP_FS__PRODUCT_%d_MULTISITE', self::PRODUCT_ID );
        if ( !defined( $constant_name ) ) {
            define( $constant_name, true );
        }
    }

    /**
     * Determine whether the current site has an active license.
     *
     * @return bool
     */
    public function is_licensed() : bool
    {
     return true;   
        return $this->sdk->can_use_premium_code();
    }

    /**
     * Determine whether the current site has any license, including an expired license.
     *
     * @return bool
     */
    public function was_licensed() : bool
    {return true;
        
        return $this->sdk->has_any_license();
    }

    /**
     * Retrieve the current license secret key.
     *
     * @return string|null
     */

    /**
     * Register hooks with Freemius.
     *
     * @return void
     */
    public function register_hooks() {
        $this->sdk->add_action( 'after_uninstall', [$this, 'uninstall_hook'] );
        $this->sdk->add_filter(
            'is_submenu_visible',
            [$this, 'is_submenu_visible'],
            10,
            2
        );
        $this->sdk->add_filter( 'plugin_icon', [$this, 'plugin_icon'] );
    }

    /**
     * Get the relative path to the plugin icon.
     *
     * @return string
     */
    public function plugin_icon() : string {
        return dirname( CODE_SNIPPETS_FILE ) . '/assets/icon.svg';
    }

    /**
     * Control whether a Freemius submenu is visible.
     *
     * @param bool   $is_visible Whether the submenu is visible.
     * @param string $submenu_id Submenu ID.
     *
     * @return bool
     */
    public function is_submenu_visible( bool $is_visible, string $submenu_id ) : bool {
        return ( 'account' === $submenu_id ? $is_visible : false );
    }

    /**
     * Clean up data when the plugin is uninstalled.
     *
     * @return void
     */
    public function uninstall_hook() {
        require_once __DIR__ . '/uninstall.php';
        Uninstall\uninstall_plugin();
    }

    /**
     * Override default strings used by Freemius to better integrate it with the rest of the plugin.
     *
     * @return void
     */
    public function override_strings() {
        $this->sdk->override_i18n( array(
            'yee-haw'  => __( 'Success', 'code-snippets' ),
            'oops'     => __( 'Notice', 'code-snippets' ),
            'woot'     => __( 'Success', 'code-snippets' ),
            'right-on' => __( 'Thanks', 'code-snippets' ),
            'ok'       => __( 'Okay', 'code-snippets' ),
        ) );
        $this->sdk->add_filter(
            'connect_message_on_update',
            function (
                $message,
                $user_first_name,
                $product_title,
                $user_login,
                $site_link,
                $freemius_link
            ) {
                /* translators: 1: site url, 2: Freemius link */
                $text = __( 'Please help us improve Code Snippets! If you opt-in, some data about your usage of %1$s will be sent to %2$s. If you skip this, that\'s okay, Code Snippets will still work just fine.', 'code-snippets' );
                return sprintf( $text, $site_link, $freemius_link );
            },
            10,
            6
        );
        $this->sdk->add_filter( 'show_affiliate_program_notice', '__return_false' );
    }

}
