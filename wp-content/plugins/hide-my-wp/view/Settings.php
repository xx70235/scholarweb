<div id="hmw_settings"><?php
    if (defined('HMW_DISABLE') && HMW_DISABLE) {
    } else {
        if (get_option('permalink_structure') && !is_multisite() && !HMW_Classes_Tools::isNginx()) { ?>
            <div class="col-md-8 no-p">
                <?php if (HMW_Classes_Tools::$default['hmw_login_url'] <> HMW_Classes_Tools::getOption('hmw_login_url') &&
                    !HMW_Classes_Tools::getOption('error') && !HMW_Classes_Tools::getOption('logout')
                ) { ?>
                    <div class="panel panel-white">
                        <div class="panel-body f-gray-dark ">
                            <p style="text-align: center; font-size: 16px; ">
                                <a href="https://wordpress.org/support/plugin/hide-my-wp/reviews/?rate=5#new-post" target="_blank"><img src="<?php echo _HMW_THEME_URL_ ?>img/5-stars.jpg" style="width: 50%; margin: 10px auto; display: block;"/>

                                    <h3 style="text-align: center; line-height: 28px">Help us support this plugin in the future. Please rate us 5 stars.</h3>
                                </a>
                            </p>
                            <p style="text-align: center; font-size: 16px; ">
                            <h4 style="text-align: center; line-height: 28px">For help and support
                                <a href="http://wpplugins.tips/contact-us" target="_blank">click here </a></h4>
                            </p>
                        </div>
                    </div>
                <?php } ?>

                <form method="POST">
                    <input type="hidden" name="action" value="hmw_settings"/>
                    <input type="hidden" name="hmw_nonce" value="<?php echo wp_create_nonce(_HMW_NONCE_ID_); ?>"/>

                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php _e('Default article/product sorting: ', _HMW_PLUGIN_NAME_); ?></h3>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-7 m-b-lg">
                                <select name="hmw_mode" class="form-control m-b-sm">
                                    <option value="default" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'default') ? 'selected="selected"' : '') ?>><?php _e("Default (don't hide any URL)", _HMW_PLUGIN_NAME_) ?></option>
                                    <option value="lite" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'lite') ? 'selected="selected"' : '') ?>><?php _e("Lite mode (recomended for non-experts)", _HMW_PLUGIN_NAME_) ?></option>
                                    <option value="custom" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'custom') ? 'selected="selected"' : '') ?>><?php _e("Custom mode (custom wp-admin and wp-login URLs)", _HMW_PLUGIN_NAME_) ?></option>
                                    <option value="ninja" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'ninja') ? 'selected="selected"' : '') ?>><?php _e("Ninja mode (Premium Feature)", _HMW_PLUGIN_NAME_) ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-white tab-panel hmw_lite" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'lite') ? '' : 'style="display: none;"') ?>>
                        <div class="panel-body">
                            <div class="col-md-12 m-b-sm ">
                                <?php echo __("The Lite option hides the /wp-admin URL for visitors and changes the /wp-login.php with /login", _HMW_PLUGIN_NAME_); ?>
                            </div>
                            <div class="col-md-12 m-b-lg admin_warning" style="color:red;">
                                <?php echo sprintf(__("To login to your dashboard you will have to access:  %s/login", _HMW_PLUGIN_NAME_), get_bloginfo('url')); ?>
                            </div>
                            <div class="col-md-12 m-b-sm">
                                <?php echo sprintf(__("Admin URL: %s", _HMW_PLUGIN_NAME_), '<strong>' . get_bloginfo('url') . '/wp-admin') . '</strong>' ?>
                            </div>
                            <div class="col-md-12 m-b-sm">
                                <?php echo sprintf(__("Login URL: %s", _HMW_PLUGIN_NAME_), '<strong>' . get_bloginfo('url') . '/login') . '</strong>' ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-white tab-panel hmw_ninja" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'ninja') ? '' : 'style="display: none;"') ?>>
                        <div class="panel-body">
                            <div class="col-md-12 m-b-sm no-p-h text-center">
                                <?php echo sprintf(__("For ninja mode you need %sHide My Wordpress PRO%s.", _HMW_PLUGIN_NAME_), '<strong><a href="http://wpplugins.tips/wordpress" target="_blank">', '</a></strong>') ?>
                                <a href="http://wpplugins.tips/wordpress" target="_blank"><img src="<?php echo _HMW_THEME_URL_ ?>img/get_it_now.png" style="width: 100%; margin: 10px auto; display: block;"/></a>

                            </div>
                        </div>
                    </div>
                    <div class="panel panel-white tab-panel hmw_custom" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'custom') ? '' : 'style="display: none;"') ?>>
                        <div class="panel-body">

                            <div class="col-md-12  m-t-lg m-b-md admin_warning" style="color:green; font-size: 16px">
                                <?php echo sprintf(__("Use this option only if you know what you're doing. Write us to <a href='mailto:%s'>%s</a> if you need help.", _HMW_PLUGIN_NAME_), _HMW_SUPPORT_EMAIL_, _HMW_SUPPORT_EMAIL_); ?>
                            </div>
                            <div class="admin_warning col-md-12 m-b-lg p-v-md b-b">
                                <?php
                                if (file_exists(ABSPATH . 'wp-config.php')) {
                                    $global_config_file = ABSPATH . 'wp-config.php';
                                } else {
                                    $global_config_file = dirname(ABSPATH) . '/wp-config.php';
                                }
                                if (!HMW_Classes_ObjController::getClass('HMW_Models_Rewrite')->is_writeable_ACLSafe($global_config_file)) {
                                    echo __("wp-config.php is write-protected. You will have to update wp-config.php manually", _HMW_PLUGIN_NAME_) . '<br />';
                                }
                                echo sprintf(__("%sWARNING:%s If you want to change the wp-admin path, first copy the safe link: %s", _HMW_PLUGIN_NAME_), '<span style="color:red;">', '</span>', '<br /><strong>' . site_url() . '/wp-login.php?hmw_disable=' . HMW_Classes_Tools::getOption('hmw_disable') . '</strong>');
                                ?>
                            </div>
                            <div class="col-md-12 m-b-sm">
                                <div class="checker col-md-12 ios-switch switch-md m-b-xxs p-v-xxs">
                                    <input type="hidden" value="0" name="hmw_hide_admin">

                                    <div class="col-md-3 no-p"><?php _e('Hide "wp-admin"', _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="col-md-1 no-p">
                                        <input type="checkbox" name="hmw_hide_admin" class="js-switch pull-right fixed-sidebar-check" data-switchery="true" style="display: none;" <?php echo(HMW_Classes_Tools::getOption('hmw_hide_admin') ? 'checked="checked"' : '') ?> value="1"/>
                                    </div>
                                    <div class="col-md-7 no-p"><?php echo __('Show 404 Not Found Error for /wp-admin', _HMW_PLUGIN_NAME_) ?></div>
                                </div>
                            </div>
                            <div class="col-md-12 m-b-sm">
                                <div class="checker col-md-12 ios-switch switch-md m-b-xxs p-v-xxs">
                                    <input type="hidden" value="0" name="hmw_hide_newadmin">

                                    <div class="col-md-3 no-p"><?php _e('Hide the new admin', _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="col-md-1 no-p">
                                        <input type="checkbox" name="hmw_hide_newadmin" class="js-switch pull-right fixed-sidebar-check" data-switchery="true" style="display: none;" <?php echo(HMW_Classes_Tools::getOption('hmw_hide_newadmin') ? 'checked="checked"' : '') ?> value="1"/>
                                    </div>
                                    <div class="col-md-7 no-p"><?php echo __('Let only the new login to be accesible and redirect me to admin after I\'m logged in.', _HMW_PLUGIN_NAME_) . " " . sprintf(__("To login you have to access: %s", _HMW_PLUGIN_NAME_), '<br /><strong>' . site_url('wp-login.php') . '</strong>'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-12 m-b-xxs">
                                <div class="col-md-3 p-v-xxs"><?php _e('Custom admin URL', _HMW_PLUGIN_NAME_); ?>:</div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="hmw_admin_url" value="<?php echo HMW_Classes_Tools::$options['hmw_admin_url'] ?>" placeholder="<?php echo HMW_Classes_Tools::getOption('hmw_admin_url') ?>"/>
                                </div>
                                <div class="col-md-5 p-v-xxs f-gray"><?php _e('eg. adm', _HMW_PLUGIN_NAME_); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-white tab-panel  hmw_custom" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'custom') ? '' : 'style="display: none;"') ?>>
                        <div class="panel-body">
                            <div class="col-md-12 m-b-sm">
                                <div class="checker col-md-12 ios-switch switch-md m-b-xxs p-v-xxs">
                                    <input type="hidden" value="0" name="hmw_hide_login">

                                    <div class="col-md-3 no-p"><?php _e('Hide "wp-login.php"', _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="col-md-1 no-p">
                                        <input type="checkbox" name="hmw_hide_login" class="js-switch pull-right fixed-sidebar-check" data-switchery="true" style="display: none;" <?php echo(HMW_Classes_Tools::getOption('hmw_hide_login') ? 'checked="checked"' : '') ?> value="1"/>
                                    </div>
                                    <div class="col-md-7 no-p"><?php _e('Show 404 Not Found Error for /wp-login.php', _HMW_PLUGIN_NAME_); ?></div>

                                </div>
                            </div>

                            <div class="col-md-12 m-b-xxs" style="  display: block;">
                                <div class="col-md-3 p-v-xxs"><?php _e('Custom login URL', _HMW_PLUGIN_NAME_); ?>:</div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" name="hmw_login_url" value="<?php echo HMW_Classes_Tools::$options['hmw_login_url'] ?>" placeholder="<?php echo HMW_Classes_Tools::getOption('hmw_login_url') ?>"/>
                                </div>
                                <div class="col-md-5 p-v-xxs f-gray"><?php _e('eg. login or signin', _HMW_PLUGIN_NAME_); ?></div>

                            </div>

                        </div>
                    </div>

                    <div class="panel panel-white tab-panel  hmw_custom" <?php echo((HMW_Classes_Tools::getOption('hmw_mode') == 'custom') ? '' : 'style="display: none;"') ?>>
                        <div class="panel-body">
                            <div class="col-md-12 m-t-md m-b-sm">
                                <div class="checker col-md-12 ios-switch switch-md m-b-xxs p-v-xxs">
                                    <div class="col-md-4 no-p"><?php _e('Redirect user when 404 to custom page:', _HMW_PLUGIN_NAME_); ?></div>
                                    <div class="col-md-5">
                                        <select name="hmw_url_redirect" class="form-control m-b-sm">
                                            <option value="." <?php echo('.' == HMW_Classes_Tools::getOption('hmw_url_redirect') ? 'selected' : '') ?>><?php _e("Front page", _HMW_PLUGIN_NAME_) ?></option>
                                            <option value="404" <?php echo('404' == HMW_Classes_Tools::getOption('hmw_url_redirect') ? 'selected' : '') ?> ><?php _e("404 page", _HMW_PLUGIN_NAME_) ?></option>
                                            <?php
                                            $pages = get_pages();
                                            foreach ($pages as $page) {
                                                $option = '<option value="' . $page->post_name . '" ' . ($page->post_name == HMW_Classes_Tools::getOption('hmw_url_redirect') ? 'selected' : '') . '>';
                                                $option .= $page->post_title;
                                                $option .= '</option>';
                                                echo $option;
                                            } ?>
                                        </select></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-t-lg tab-panel hmw_default hmw_lite hmw_custom">
                        <div class="col-md-12 no-p-h m-b-sm">
                            <div class="checker col-md-12 ios-switch switch-md m-b-xxs p-v-xxs">
                                <input type="hidden" value="0" name="hmw_send_email">

                                <div class="col-md-1 no-p">
                                    <input type="checkbox" name="hmw_send_email" class="js-switch pull-right fixed-sidebar-check" data-switchery="true" style="display: none;" <?php echo(HMW_Classes_Tools::getOption('hmw_send_email') ? 'checked="checked"' : '') ?> value="1"/>
                                </div>
                                <div class="col-md-10 no-p"><?php _e('Send me an email with the new URLs and the secure parameter', _HMW_PLUGIN_NAME_); ?></div>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-success save"><?php _e('Save', _HMW_PLUGIN_NAME_); ?></button>
                    </div>

                </form>
            </div>
            <div class="col-md-4">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('Need Help?', _HMW_PLUGIN_NAME_); ?></h3>
                    </div>
                    <div class="panel-body f-gray-dark">
                        <form action="<?php echo admin_url('admin-ajax.php') ?>" nonce="<?php echo wp_create_nonce(_HMW_NONCE_ID_) ?>" id="hmw_support">
                            <div class="m-b-md"><?php echo __("We're happy to answer any questions you may have and aim to respond within 24 hours", _HMW_PLUGIN_NAME_) ?></div>
                            <h4 id="hmw_success" style="display: none"><?php _e('Question sent.', _HMW_PLUGIN_NAME_); ?></h4>
                            <h4 id="hmw_error" style="display: none; color: red; line-height: 20px"><?php _e('Error sending the email. Make sure that you can send emails from your website.', _HMW_PLUGIN_NAME_); ?></h4>
                            <div class="form-group">
                                <label for="hmw_email"><?php _e('Your Email (for reply)', _HMW_PLUGIN_NAME_); ?></label>
                                <input type="text" class="form-control" id="hmw_email" placeholder="<?php _e('Email', _HMW_PLUGIN_NAME_); ?> ..." value="<?php
                                $current_user = wp_get_current_user();
                                echo $current_user->user_email;
                                ?>">
                            </div>
                            <div class="form-group">
                                <label for="hmw_question"><?php _e('Question', _HMW_PLUGIN_NAME_); ?></label>
                                <textarea class="form-control" id="hmw_question" rows="3" placeholder="<?php _e('Your question ...', _HMW_PLUGIN_NAME_); ?>" style="height: 80px; position: relative; line-height: 20px; font-size: 16px; "></textarea>
                            </div>
                            <button type="button" class="btn btn-primary"><?php _e('Send', _HMW_PLUGIN_NAME_); ?></button>
                        </form>
                    </div>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('Secure your Wordpress ', _HMW_PLUGIN_NAME_); ?></h3>
                    </div>
                    <div class="panel-body f-gray-dark">
                        <p>You can hide the admin URLs and login to increases your security against hackers and spammers.</p>

                        <p>Note! After you change your admin URL, in case you can't login to Wordpress, just go to
                            <br/><br/><strong><?php echo site_url() ?>/wp-login.php?hmw_disable=<?php echo HMW_Classes_Tools::getOption('hmw_disable') ?></strong><br/><br/> and all the default options will be back.
                        </p>
                    </div>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('Recommended Plugins', _HMW_PLUGIN_NAME_); ?></h3>
                        <p>We only recommend these plugins because they are professionals. These plugins are not ours and we don't guarantee for them</p>
                    </div>
                    <div class="panel-body f-gray-dark text-left b-b">
                        <h4>Squirrly - SEO Plugin</h4>
                        <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px;">SEO By Squirrly helps you write content that is SEO friendly and ALSO Human friendly.
                        You’ll get to improve your rankings and significantly more organic traffic.
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Over 150.000 active users
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Over 10.000.000 optimized articles
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Top Rated on Wordpress
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Best Keyword Research Tool
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Free Site Audits Every Week
                        </span>
                            <span style="display: block; line-height: 20px; font-size: 14px; margin-bottom: 10px; margin-left: 5px; font-weight: bold;">
                             - Premium Support
                        </span>
                        <a href="https://plugin.squirrly.co" title="Wordpress SEO plugin" target="_blank"><img src="<?php echo _HMW_THEME_URL_ . 'img/squirrly_seo.png' ?>" border="0" alt="" width="320"/></a><img height="0" width="0" src="//sucuri.7eer.net/i/326051/216898/3713" style="position:absolute;visibility:hidden;" border="0"/>
                    </div>
                    <div class="panel-body f-gray-dark text-center b-b">
                        <h4>Instapage - Landing Page Plugin</h4>
                        <p>
                            <a href="http://instapg.es/fLmqw" target="_blank" style="outline:none;border:none;"><img src="https://ambassador-api.s3.amazonaws.com/uploads/marketing/3795/2017_02_06_15_45_54.png" alt="Instapage" border="0"/></a>
                        </p>
                    </div>
                    <div class="panel-body f-gray-dark text-center">
                        <h4>Wp Rocket - Cache Plugin</h4>
                        <p>
                            <a href="https://wp-rocket.me/" title="WP Rocket" target="_blank"><img src="<?php echo _HMW_THEME_URL_ . 'img/cache.png' ?>" title="Wp Rocket" width="320"></a>
                        </p>

                    </div>
                </div>

            </div>
            <?php
        }
    }
    ?>
</div>
