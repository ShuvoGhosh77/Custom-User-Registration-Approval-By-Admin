<?php
function custom_registration_form()
{
    ob_start();
    ?>
    <div class="role_selector">
        <form id="user-role-selection">
            <label>
                <input class="user-role-selection_btn1" type="radio" name="user_role" value="business" checked> Agenzia
            </label>
            <label>
                <input class="user-role-selection_btn2" type="radio" name="user_role" value="private"> Private
            </label>
        </form>
    </div>

    <div id="business-form" class="registration-form">
        <form id="custom-registration-form" method="post">
            <p>
                <label for="username">Nome Agenzia <span class="required">*</span></label>
                <input type="text" name="username" required placeholder="Inserisci il tuo Nome Agenzia">
            </p>
            <p>
                <label for="email">E-mail <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="Inserisci il tuo indirizzo email">
            </p>
            <p>
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" name="password" required placeholder="Inserisci la password">
            </p>
            <p>
                <label for="phone">Numero di telefono <span class="required">*</span></label>
                <input type="text" name="phone" required placeholder="Inserisci il tuo numero di telefono">
            </p>
            <p>
                <label for="sdi_code">Codice SDI</label>
                <input type="text" name="sdi_code" placeholder="Enter Your SDI Code">
            </p>
            <p>
                <input type="hidden" name="user_role" value="business">
            </p>
            <div class="form-condition">
                Acconsento al trattamento dei miei dati personali ai sensi del Regolamento (UE) 2016/679 (GDPR) e della
                normativa nazionale vigente, per le finalità indicate nell'informativa privacy. Ho letto e compreso
                l'informativa.
            </div>
            <div class="condition_button">
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="agree"> Sono d'accordo
                </label>
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="disagree"> Non sono d'accordo
                </label>
            </div>
            <?php wp_nonce_field('custom_registration_action', 'custom_registration_nonce'); ?>
            <p>
                <button id="user_register" type="submit" name="submit_registration" disabled>Register</button>
            </p>
        </form>
    </div>
    <div id="private-form" class="registration-form" style="display: none;">
        <input type="hidden" name="user_role" value="private">
        <form id="custom-registration-form" method="post">
            <p>
                <label for="username">Nome Agenzia <span class="required">*</span></label>
                <input type="text" name="username" required placeholder="Inserisci il tuo Nome Agenzia">
            </p>
            <p>
                <label for="email">E-mail <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="Inserisci il tuo indirizzo email">
            </p>
            <p>
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" name="password" required placeholder="Inserisci la password">
            </p>
            <p>
                <label for="phone">Numero di telefono <span class="required">*</span></label>
                <input type="text" name="phone" required placeholder="Inserisci il tuo numero di telefono">
            </p>

            <p>
                <label for="codice_code">codice fiscale alunn</label>
                <input type="text" name="codice_code" placeholder="Enter Your codice fiscale alunno">
            </p>

            <p>
                <label for="identificativo">Codice identificativo fiscale</label>
                <input type="text" name="identificativo" placeholder="Enter Your Codice identificativo fiscale">
            </p>

            <div class="form-condition">
                Acconsento al trattamento dei miei dati personali ai sensi del Regolamento (UE) 2016/679 (GDPR) e della
                normativa nazionale vigente, per le finalità indicate nell'informativa privacy. Ho letto e compreso
                l'informativa.
            </div>
            <div class="condition_button">
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="agree2"> Sono d'accordo
                </label>
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="disagree2"> Non sono d'accordo
                </label>
            </div>
            <?php wp_nonce_field('custom_registration_action', 'custom_registration_nonce'); ?>
            <p>
                <button id="user_register2" type="submit" name="submit_registration" disabled>Register</button>
            </p>
        </form>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('custom_registration', 'custom_registration_form');

function handle_custom_registration_form()
{
    if (isset($_POST['submit_registration']) && wp_verify_nonce($_POST['custom_registration_nonce'], 'custom_registration_action')) {
        $username = sanitize_text_field($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $phone = sanitize_text_field($_POST['phone']);
        $user_role = sanitize_text_field($_POST['user_role']);
        $role = ($user_role === 'business') ? 'business' : 'private';
        $sdi_code = ($role === 'business') ? sanitize_text_field($_POST['sdi_code']) : '';
        $codice_code = ($user_role === 'private') ? sanitize_text_field($_POST['codice_code']) : '';
        $identificativo = ($user_role === 'private') ? sanitize_text_field($_POST['identificativo']) : '';

        $user_id = wp_create_user($username, $password, $email);
        $random_number = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $unique_code = 'nrstudy-' . $random_number;

        if (!is_wp_error($user_id)) {
            // Assign the role to the user
            $user = new WP_User($user_id);
            $user->set_role($role);

            update_user_meta($user_id, 'unique_code', $unique_code);
            // Add custom fields and set user as inactive
            update_user_meta($user_id, 'phone', $phone);
            if ($user_role === 'business') {
                update_user_meta($user_id, 'sdi_code', $sdi_code);
            }
            if ($user_role === 'private') {
                update_user_meta($user_id, 'codice_code', $codice_code);
            }
            if ($user_role === 'private') {
                update_user_meta($user_id, 'codice_code', $identificativo);
            }
            update_user_meta($user_id, 'user_role', $user_role);
            update_user_meta($user_id, 'is_active', 0); // Mark user as inactive

            // Notify admin about the new registration
            wp_mail(get_option('admin_email'), 'Registrazione nuovo utente', "Un nuovo utente si è registrato:\n\nUsername: $username\nEmail: $email");

            wp_redirect(home_url('/success'));
            exit;
        } else {
            wp_die('Error: ' . $user_id->get_error_message());
        }
    }
}
add_action('init', 'handle_custom_registration_form');


function custom_add_roles()
{
    if (!get_role('business')) {
        add_role('business', 'Business', [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ]);
    }
    if (!get_role('private')) {
        add_role('private', 'Private', [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ]);
    }
    if (!get_role('teacher')) {
        add_role('teacher', 'Teacher', [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ]);
    }
}
add_action('init', 'custom_add_roles');


function custom_teacher_registration_form()
{
    ob_start();
    ?>
    <div id="teacher-form" class="registration-form">
        <form id="teacher-registration-form" method="post">
            <p>
                <label for="username">Nome e cognome<span class="required">*</span></label>
                <input type="text" name="username" required placeholder="Inserisci il tuo Nome Insegnante">
            </p>
            <p>
                <label for="address">Indirizzo<span class="required">*</span></label>
                <input type="text" name="address" required placeholder="Indirizzo">
            </p>
            <p>
                <label for="email">E-mail <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="Inserisci il tuo indirizzo email">
            </p>
            <p>
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" name="password" required placeholder="Inserisci la password">
            </p>
            <p>
                <label for="teacher_Date_of_birth">Data di nascita <span class="required">*</span></label>
                <input type="date" name="teacher_Date_of_birth" required placeholder="Data di nascita">
            </p>
            <p>
                <label for="taxcode">Codice fiscale <span class="required">*</span></label>
                <input type="text" name="taxcode" required placeholder="Codice fiscale">
            </p>
            <p>
                <label for="tcity">Città <span class="required">*</span></label>
                <input type="text" name="tcity" required placeholder="Città">
            </p>
            <p>
                <label for="subjecttaught">Materia insegnata<span class="required">*</span></label>
                <input type="text" name="subjecttaught" required placeholder="Materia insegnata">
            </p>
            <p>
                <label for="teacherschool">Nome scuola<span class="required">*</span></label>
                <input type="text" name="teacherschool" required placeholder="Nome scuola">
            </p>
            <p>
                <input type="hidden" name="user_role" value="teacher">
            </p>
            <div class="form-condition">
                Acconsento al trattamento dei miei dati personali ai sensi del Regolamento (UE) 2016/679 (GDPR) e della
                normativa nazionale vigente, per le finalità indicate nell'informativa privacy. Ho letto e compreso
                l'informativa.
            </div>
            <div class="condition_button">
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="agree3"> Sono d'accordo
                </label>
                <label>
                    <input id="radio_btn_1" type="radio" name="agreement" value="disagree3"> Non sono d'accordo
                </label>
            </div>
            <?php wp_nonce_field('teacher_registration_action', 'teacher_registration_nonce'); ?>
            <p>
                <button id="teacher_register" type="submit" name="submit_teacher_registration" disabled>Register</button>
            </p>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('teacher_registration', 'custom_teacher_registration_form');

function handle_teacher_registration_form()
{
    if (isset($_POST['submit_teacher_registration']) && wp_verify_nonce($_POST['teacher_registration_nonce'], 'teacher_registration_action')) {
        $username = sanitize_text_field($_POST['username']);
        $address = sanitize_text_field($_POST['address']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $teacher_Date_of_birth = sanitize_text_field($_POST['teacher_Date_of_birth']);
        $taxcode = sanitize_text_field($_POST['taxcode']);
        $tcity = sanitize_text_field($_POST['tcity']);
        $subjecttaught = sanitize_text_field($_POST['subjecttaught']);
        $teacherschool = sanitize_text_field($_POST['teacherschool']);
        $user_role = sanitize_text_field($_POST['user_role']);
        $random_number = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $unique_code = 'nrstudy-' . $random_number;

        if ($user_role === 'teacher') {
            $user_id = wp_create_user($username, $password, $email);

            if (!is_wp_error($user_id)) {
                // Assign the role to the user
                $user = new WP_User($user_id);
                $user->set_role('teacher');

                // Add custom fields and set user as inactive
                update_user_meta($user_id, 'user_role', $user_role);
                update_user_meta($user_id, 'unique_code', $unique_code);
                update_user_meta($user_id, 'is_active', 0); // Mark user as inactive
                update_user_meta($user_id, 'address', $address);
                update_user_meta($user_id, 'teacher_Date_of_birth', $teacher_Date_of_birth);
                update_user_meta($user_id, 'taxcode', $taxcode);
                update_user_meta($user_id, 'tcity', $tcity);
                update_user_meta($user_id, 'subjecttaught', $subjecttaught);
                update_user_meta($user_id, 'teacherschool', $teacherschool);

                // Notify admin about the new registration
                wp_mail(get_option('admin_email'), 'Registrazione nuovo insegnante', "Un nuovo insegnante si è registrato:\n\nUsername: $username\nEmail: $email");

                wp_redirect(home_url('/success'));
                exit;
            } else {
                wp_die('Error: ' . $user_id->get_error_message());
            }
        }
    }
}
add_action('init', 'handle_teacher_registration_form');






function restrict_login_for_inactive_users($user, $username, $password)
{
    // Check if the user exists
    if (is_wp_error($user)) {
        return $user;
    }

    // Check if the user is marked as inactive
    $is_active = get_user_meta($user->ID, 'is_active', true);
    if ($is_active == 0) {
        return new WP_Error('account inattivo', 'Il tuo account è in attesa di approvazione da parte dell amministratore. Attendi l approvazione prima di effettuare l accesso.');
    }

    return $user;
}
add_filter('authenticate', 'restrict_login_for_inactive_users', 30, 3);

function add_approval_column($columns)
{
    $columns['approval'] = 'Approval Status';
    return $columns;
}
add_filter('manage_users_columns', 'add_approval_column');

function show_approval_column_content($value, $column_name, $user_id)
{
    if ('approval' === $column_name) {
        $is_active = get_user_meta($user_id, 'is_active', true);

        if ($is_active) {
            return '<span style="color: green;">Approved</span>';
        } else {
            return '<a href="' . esc_url(admin_url("users.php?approve_user=$user_id")) . '" style="color: red;">Approve</a>';
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_approval_column_content', 10, 3);

function approve_user()
{
    if (isset($_GET['approve_user'])) {
        $user_id = intval($_GET['approve_user']);

        // Update the user meta to mark the user as active
        update_user_meta($user_id, 'is_active', 1);

        // Notify the user about account approval
        $user = get_userdata($user_id);
        wp_mail($user->user_email, 'Il tuo account è approvato', 'Congratulazioni! Il tuo account è stato approvato. Ora puoi effettuare il login.');

        // Redirect back to the Users page
        wp_redirect(admin_url('users.php'));
        exit;
    }
}
add_action('admin_init', 'approve_user');



function add_phone_column($columns)
{
    $columns['phone'] = 'Numero di telefono';
    return $columns;
}
add_filter('manage_users_columns', 'add_phone_column');

function show_phone_column_content($value, $column_name, $user_id)
{
    if ('phone' === $column_name) {
        $phone = get_user_meta($user_id, 'phone', true);
        return $phone ? esc_html($phone) : 'Non disponibile';
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_phone_column_content', 10, 3);

function add_unique_code_column($columns)
{
    $columns['unique_code'] = 'Codice Unico';
    return $columns;
}
add_filter('manage_users_columns', 'add_unique_code_column');



function show_unique_code_column_content($value, $column_name, $user_id)
{
    if ('unique_code' === $column_name) {
        $is_active = get_user_meta($user_id, 'is_active', true);
        if ($is_active == 1) { // Show only if approved
            $unique_code = get_user_meta($user_id, 'unique_code', true);
            return $unique_code ? esc_html($unique_code) : 'Non disponibile';
        } else {
            return '<span style="color: red;">Need Admin Approve</span>';
        }
    }
    return $value;
}
add_action('manage_users_custom_column', 'show_unique_code_column_content', 10, 3);