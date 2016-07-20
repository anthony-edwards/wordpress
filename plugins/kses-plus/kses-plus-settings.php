<?php
class KSESPLUS
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'GE KSES',
            'manage_options',
            'kses-plus-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'kses_plus' );
        ?>
        <div class="wrap">
            <h2>GE KSES Tag Whitelist</h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'kses_plus_group' );
                do_settings_sections( 'kses-plus-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'kses_plus_group', // Option group
            'kses_plus', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'kses_plus', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'kses-plus-setting-admin' // Page
        );

        add_settings_field(
            'allowed_tags', // ID
            'Allowed Tags', // Title
            array( $this, 'kses_plus_allowed_tags_callback' ), // Callback
            'kses-plus-setting-admin', // Page
            'kses_plus' // Section
        );

        add_settings_field(
            'kses_plus_roles', // ID
            'KSES Role', // Title
            array( $this, 'kses_plus_roles_callback' ), // Callback
            'kses-plus-setting-admin', // Page
            'kses_plus' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['allowed_tags'] ) )
          $new_input['allowed_tags'] = sanitize_text_field( $input['allowed_tags'] );
        if(isset( $input['kses_plus_roles']))
          $new_input['kses_plus_roles'] = $input['kses_plus_roles'];

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter comma delimited list of tags you would like to allow:';
    }

    public function kses_plus_roles_callback(){
      $editable_roles = get_editable_roles();
      print '<select multiple name="kses_plus[kses_plus_roles][]>';
      foreach ( $editable_roles as $role => $details ) {
        $name = translate_user_role($details['name'] );
        $ge_role = isset( $this->options['kses_plus_roles'] ) ? esc_attr( $this->options['kses_plus_roles']) : $name;
        $selected = in_array($role, $this->options['kses_plus_roles']) ? 'selected="selected"': '';
        print '<option id="kses_plus_roles" value="'.$role.'"' .$selected.'>'.$name.'</option>';
      }
      print '</select>';
    }
    /**
     * Get the settings option array and print one of its values
     */
    public function kses_plus_allowed_tags_callback()
    {
        printf(
            '<input type="text" id="allowed_tags" name="kses_plus[allowed_tags]" value="%s" />',
            isset( $this->options['allowed_tags'] ) ? esc_attr( $this->options['allowed_tags']) : ''
        );
    }
}

if( is_admin() )
    $kses_plus = new KSESPLUS();
