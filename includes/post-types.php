<?php

// Register the Client post type
function fcd_register_client_post_type() {
    $labels = array(
        'name'               => 'Clients',
        'singular_name'      => 'Client',
        'menu_name'          => 'Clients',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Client',
        'edit_item'          => 'Edit Client',
        'new_item'           => 'New Client',
        'view_item'          => 'View Client',
        'search_items'       => 'Search Clients',
        'not_found'          => 'No Clients Found',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'supports'           => array('title', 'editor'),
        'menu_icon'          => 'dashicons-businessperson',
        'show_in_menu'       => true,
    );

    register_post_type('client', $args);
}
add_action('init', 'fcd_register_client_post_type');

// Customize title field placeholder
function fcd_change_client_title_placeholder( $title ) {
    $screen = get_current_screen();
    if ( 'client' === $screen->post_type ) {
        $title = 'Enter client’s name';
    }
    return $title;
}
add_filter( 'enter_title_here', 'fcd_change_client_title_placeholder' );

// Label the content editor as "Notes"
function fcd_label_editor_notes($post) {
    if ($post->post_type === 'client') {
        echo '<h1>Client Notes</h1>';
    }
}
add_action('edit_form_after_title', 'fcd_label_editor_notes');

// Add Client Info Meta Box
function fcd_add_client_meta_boxes() {
    add_meta_box(
        'fcd_client_info',
        'Client Info',
        'fcd_render_client_meta_box',
        'client',
        'advanced',
        'high'
    );
}
add_action('add_meta_boxes', 'fcd_add_client_meta_boxes');

// Render the fields in the meta box
function fcd_render_client_meta_box($post) {
    $fields = [
        'email'        => 'Email',
        'company'      => 'Company name',
        'address'      => 'Physical address',
        'city'         => 'City',
        'state'        => 'State',
        'zip'          => 'Zip Code',
        'phone'        => 'Phone number',
        'website'      => 'Website',
        'instagram'    => 'Instagram',
        'x'            => 'X',
        'facebook'     => 'Facebook',
        'pinterest'    => 'Pinterest',
        'tiktok'       => 'TikTok',
        'substack'     => 'Substack',
        'referred_by'  => 'Referred by',
    ];

    // Load saved values
    foreach ($fields as $key => $label) {
        $meta[$key] = get_post_meta($post->ID, '_fcd_client_' . $key, true);
    }

    $lead_type = get_post_meta($post->ID, '_fcd_client_lead_type', true);

    wp_nonce_field('fcd_save_client_meta', 'fcd_client_meta_nonce');

    echo '<style>
        .fcd-field-group { margin-bottom: 1rem; }
        .fcd-label { font-weight: 600; display: block; margin-bottom: .25rem; }
    </style>';

    foreach ($fields as $key => $label) {
        echo '<div class="fcd-field-group">';
        echo '<label class="fcd-label" for="fcd_client_' . esc_attr($key) . '">' . esc_html($label) . '</label>';
        echo '<input type="text" id="fcd_client_' . esc_attr($key) . '" name="fcd_client_' . esc_attr($key) . '" value="' . esc_attr($meta[$key]) . '" style="width: 100%;" />';
        echo '</div>';
    }

    echo '<div class="fcd-field-group">';
    echo '<span class="fcd-label">Lead Type</span>';
    echo '<label><input type="radio" name="fcd_client_lead_type" value="cold" ' . checked($lead_type, 'cold', false) . '> Cold Lead</label><br />';
    echo '<label><input type="radio" name="fcd_client_lead_type" value="warm" ' . checked($lead_type, 'warm', false) . '> Warm Lead</label><br />';
    echo '<label><input type="radio" name="fcd_client_lead_type" value="client" ' . checked($lead_type, 'client', false) . '> Client</label>';
    echo '</div>';
}

// Save the meta box data
function fcd_save_client_meta($post_id) {
    if (!isset($_POST['fcd_client_meta_nonce']) || !wp_verify_nonce($_POST['fcd_client_meta_nonce'], 'fcd_save_client_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = [
        'email', 'company', 'address', 'city', 'state', 'zip', 'phone',
        'website', 'instagram', 'x', 'facebook', 'pinterest', 'tiktok',
        'substack', 'referred_by'
    ];

    foreach ($fields as $field) {
        $meta_key = '_fcd_client_' . $field;
        $value = isset($_POST['fcd_client_' . $field]) ? sanitize_text_field($_POST['fcd_client_' . $field]) : '';
        update_post_meta($post_id, $meta_key, $value);
    }

    // Save lead type separately
    if (isset($_POST['fcd_client_lead_type'])) {
        update_post_meta($post_id, '_fcd_client_lead_type', sanitize_text_field($_POST['fcd_client_lead_type']));
    }
}
add_action('save_post_client', 'fcd_save_client_meta');
