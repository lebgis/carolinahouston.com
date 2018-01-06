<?php
    if ( !function_exists('zoner_register_social_connect_user')) {
        function zoner_register_social_connect_user($args) {
            global $prefix, $zoner, $zoner_config;

            $user_id = 0;
            $defaults = array(
                'user_email' 	=> '',
                'user_name' 	=> '',
                'ident_code' 	=> '',
                'first_name'	=> '',
                'last_name'		=> ''
            );

            $args = wp_parse_args( $args, $defaults );

            if (email_exists($args['user_email'])) {
                $user_id = email_exists($args['user_email']);
            } else {
                $user_id = wp_create_user( $args['user_email'], $args['ident_code'], $args['user_email'] );
                if (!is_wp_error($user_id)) {
                    wp_update_user(
                        array(
                            'ID' 		 => $user_id,
                            'first_name' => $args['first_name'],
                            'last_name'  => $args['last_name'],
                            'role'		 => 'agent'
                        ) );
                }
            }
            return $user_id;
        }
    }

    if ( !function_exists('zoner_signin_facebook_connect')) {
        function zoner_signin_facebook_connect()
        {
            global $zoner, $zoner_config, $prefix;
            $fapi = $fsecret = $loginUrl = $user_id = '';

            if ((!empty($_REQUEST['code']) && !empty($_REQUEST['state']))) {

                if (!empty($zoner_config['facebook-api-key']))
                    $fapi = esc_html($zoner_config['facebook-api-key']);
                if (!empty($zoner_config['facebook-secret-code']))
                    $fsecret = esc_html($zoner_config['facebook-secret-code']);

                require_once ZONER_SHORTCODE_DIR.'/vc_social_connect/Facebook/autoload.php';

                $facebook = null;
                $facebook = new Facebook\Facebook([
                    'app_id'        => $fapi,
                    'app_secret'    => $fsecret,
                    'default_graph_version' => 'v2.8',
                ]);

                $accessToken = null;
                $helper = $facebook->getRedirectLoginHelper();

                try {
                    $accessToken = $helper->getAccessToken();
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    exit;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }

                if (! isset($accessToken)) {
                    if ($helper->getError()) {
                        header('HTTP/1.0 401 Unauthorized');
                        echo "Error: " . $helper->getError() . "\n";
                        echo "Error Code: " . $helper->getErrorCode() . "\n";
                        echo "Error Reason: " . $helper->getErrorReason() . "\n";
                        echo "Error Description: " . $helper->getErrorDescription() . "\n";
                    } else {
                        header('HTTP/1.0 400 Bad Request');
                        echo 'Bad request';
                    }
                    exit;
                }

                $oAuth2Client  = $facebook->getOAuth2Client();
                $tokenMetadata = $oAuth2Client->debugToken($accessToken);
                $user_id       = $tokenMetadata->getField('user_id');

                $_SESSION['fb_access_token'] = (string) $accessToken;


                if (!empty($user_id)) {
                    $wp_user_id = -1;
                    $facebook->setDefaultAccessToken($accessToken);

                    $request = $facebook->request('GET', '/me?fields=id,email,first_name,last_name,picture.width(800)');
                    try {
                        $response = $facebook->getClient()->sendRequest($request);
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }

                    $graphNode  = $response->getGraphNode();
                    $ufname     = $graphNode->getField('first_name');
                    $ulname     = $graphNode->getField('last_name');
                    $uemail     = $graphNode->getField('email');
                    $uid        = $graphNode->getField('id');
                    $ident_code = $fsecret . $uid;

                    $user_avatar = null;
                    $user_avatar  = $graphNode->getField('picture');
                    $user_avatar  = $user_avatar ['url'];

                    /*Facebook user data*/
                    $fb_args = array();

                    $fb_args['user_email'] = $uemail;
                    $fb_args['user_name']  = $ufname . ' ' . $ulname;
                    $fb_args['ident_code'] = $ident_code;
                    $fb_args['first_name'] = $ufname;
                    $fb_args['last_name']  = $ulname;

                    $wp_user_id = zoner_register_social_connect_user($fb_args);
                    wp_set_password($ident_code, $wp_user_id);

                    if ($wp_user_id != -1) {
                        $user = get_user_by('email', $uemail);
                        $wp_user_id = $user->ID;

                        if (!empty($user)) {
                            /*Sign on data for wordpress*/
                            $user_info = array();
                            $user_info['user_login']    = $user->user_login;
                            $user_info['user_password'] = $ident_code;
                            $user_info['remember'] = true;
                            $user_signon = wp_signon($user_info, false);
                        }
                    }

                    if (is_wp_error($user_signon)) {
                        wp_redirect(site_url());
                        exit;
                    } else {
                        $avatar_id = -1;
                        $avatar_id = get_user_meta($wp_user_id, $prefix . 'avatar_id', true);
                        if (!empty($user_avatar) && (isset($user_avatar))) {
                            $avatar_id = zoner_import_avatar($user_avatar, sanitize_user($ufname . ' ' . $ulname, true));
                            update_user_meta($wp_user_id, $prefix . 'avatar',    wp_get_attachment_url($avatar_id));
                            update_user_meta($wp_user_id, $prefix . 'avatar_id', $avatar_id);
                        }

                        $profile_link = add_query_arg(array('profile-page' => 'my_profile'), get_author_posts_url($wp_user_id));
                        wp_redirect($profile_link);
                        exit;
                    }
                } else {
                    wp_redirect(home_url('/'));
                    exit;
                }
            }
        }
    }

    if ( !function_exists('zoner_signin_google_connect')) {
        function zoner_signin_google_connect()
        {
            global $zoner, $zoner_config, $prefix;
            $gclient_id = $gclient_secret = $gdev_key = $picture = '';
            $wp_user_id = -1;

            if (!empty($_REQUEST['code']) && !isset($_REQUEST['state'])) {
                if (!empty($zoner_config['google-oauth-client-id'])) {
                    $gclient_id = esc_html($zoner_config['google-oauth-client-id']);
                }
                if (!empty($zoner_config['google-client-secret'])) {
                    $gclient_secret = esc_html($zoner_config['google-client-secret']);
                }
                if (!empty($zoner_config['google-api-key'])) {
                    $gdev_key = esc_html($zoner_config['google-api-key']);
                }

                if (!empty($gclient_id) && !empty($gclient_secret) && !empty($gdev_key)) {
                    $client = null;
                    require_once get_template_directory() . '/includes/admin/libs/theme-shortcodes/zoner-shortcodes/vc_social_connect/googleoauth/autoload.php';

                    $client = new Google_Client();
                    $client->setApplicationName(sprintf(__('Login to 1%s', 'zoner'), get_bloginfo('name')));
                    $client->setClientId($gclient_id);
                    $client->setClientSecret($gclient_secret);
                    $client->setDeveloperKey($gdev_key);
                    $client->setRedirectUri(site_url());
                    $client->setIncludeGrantedScopes(true);
                    $client->setAccessType('online');
                    $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));

                    $plus = new Google_Service_Oauth2($client);

                    if (isset($_REQUEST['code'])) {
                        $ident_code = $_REQUEST['code'];
                        $client->authenticate($ident_code);
                    }
                    $accessToken = $client->getAccessToken();

                    if (empty($accessToken)) {
                        $loginUrl = $client->createAuthUrl();
                        wp_redirect(site_url());
                        exit;
                    } else {
                        $user = '';
                        $user = $plus->userinfo->get();

                        $first_name = $user->given_name;
                        $last_name = $user->family_name;
                        $user_email = $user->email;
                        $picture = $user->picture;
                        $user_id = $user->id;


                        /*Facebook user data*/
                        $gg_args = array();
                        $display_name = $first_name . ' ' . $last_name;

                        $gg_args['user_email'] = $user_email;
                        $gg_args['user_name']  = $display_name;
                        $gg_args['ident_code'] = $ident_code;
                        $gg_args['first_name'] = $first_name;
                        $gg_args['last_name']  = $last_name;

                        $wp_user_id = zoner_register_social_connect_user($gg_args);
                        wp_set_password($ident_code, $wp_user_id);

                        if ($wp_user_id != -1) {
                            $user = get_user_by('email', $user_email);
                            $wp_user_id = $user->ID;

                            if (!empty($user)) {
                                /*Sign on data for wordpress*/
                                $user_info = array();
                                $user_info['user_login'] = $user->user_login;
                                $user_info['user_password'] = $ident_code;
                                $user_info['remember'] = true;
                                $user_signon = wp_signon($user_info, false);
                            }
                        }

                        if (is_wp_error($user_signon)) {
                            wp_redirect(site_url());
                            exit;
                        } else {
                            $avatar_id = -1;
                            $avatar_id = get_user_meta($wp_user_id, $prefix . 'avatar_id', true);
                            if (!empty($picture) && (isset($picture)) && (!$avatar_id)) {
                                $avatar_id = zoner_import_avatar($picture, sanitize_user($display_name, true));
                                update_user_meta($wp_user_id, $prefix . 'avatar', wp_get_attachment_url($avatar_id));
                                update_user_meta($wp_user_id, $prefix . 'avatar_id', $avatar_id);
                            }

                            $profile_link = add_query_arg(array('profile-page' => 'my_profile'), get_author_posts_url($wp_user_id));
                            wp_redirect($profile_link);
                            exit;
                        }
                    }
                }
            }
        }
    }

    if (!function_exists('zoner_import_avatar')) {
        function zoner_import_avatar($file_url, $user_name)
        {
            $attach_id = -1;

            $file_info = wp_check_filetype_and_ext($file_url, basename($file_url));
            if ($file_info['ext']) {
                $ext = $file_info['ext'];
            } else {
                $info = pathinfo($file_url);
                $ext = $info['extension'];
                $ext = substr($ext, 0, strpos($ext, "?"));
            }

            $binary_file = file_get_contents($file_url);
            $upload = wp_upload_bits(strtolower(sanitize_file_name($user_name . '-avatar.' . $ext)), null, $binary_file);

            if (!empty($upload['file'])) {
                $wp_upload_dir = wp_upload_dir();

                $filename = $upload['file'];
                $filetype = wp_check_filetype($filename);

                $attachment = array(
                    'guid' => $filename,
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_id    = wp_insert_attachment($attachment, $filename);
                $attach_data  = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
            }

            return $attach_id;
        }
    }