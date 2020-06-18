=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.4
Tested up to: 4.9.8
Stable tag: 4.48
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

User Role Editor WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. Changes made to the roles will stay unchanged.
You will have to configure lost part of the settings at the User Role Editor Pro Settings page again after that.
Right decision in this case is to delete free version folder (user-role-editor) after deactivation via FTP, not via WordPress.

== Changelog ==
= [4.48] 25.09.2018 =
* Core version: 4.46
* Update: Edit posts/pages restrictions add-on: non-public taxonomies are not included into restricted database queries. URE will build now much shorter database queries for the sites which have a lot of such taxonomies at the database. For example sites with WP SEO Premium plugin, where 'yst_prominent_words' taxonomy may count thousands records at wp_terms and wp_term_taxonomy db tables.
* Update: Content view restrictions add-on: "Content View Restrictions" meta box markup was changed at post/page editor for better compatibility with Gutenberg. It will work as a usual meta box, located under main editor area.
* Update: Widgets admin access add-on: ure_widgets_edit_access_user custom filter was added. It allows to set final list of widgets blocked for editing by current user.
* Fix: Admin menu blocking add-on: URL processing were enhanced for the links with 'customize.php' inside, like "Appearance->Header" or "Appearance->Background". Those links did not work (redirection to dashboard took place) in case of the restricted admin menu. 
* Fix: wp-admin pages permissions viewer add-on: 
    - typo at word 'pemissions' was corrected at the "Settings->User Role Editor->Additional modules" tab.
    - source code .php file reading file(...) call was wrapped by 'try ...catch' to exclude PHP warning generation in case of a problem with access to a source code file.
* Core version was updated to 4.46:
* Update: "Users" page, "Without role" button: underlying SQL queries were replaced with more robust versions (about 10 times faster).
  It is critical for sites with large quant of users.New query does not take into account though some cases with incorrect users data (usually imported from the external sources).
  It's possible to use older (comprehensive but slower) query version defining a PHP constant: "define('URE_COUNT_USERS_WITHOUT_ROLE_THOROUGHLY', true);" or
  return false from a custom 'ure_count_users_without_role_quick' filter.
* Update: Error checking was enhanced after default role change for the WordPress multisite subsite.
* Update: URE settings page template: HTML helper checked() is used where applicable.
* Fix: 2 spelling mistakes were fixed in the text labels.

= [4.47.3] 18.08.2018 =
* Core version: 4.45
* Update: Admin menu access add-on: admin menu copy creation priority was increased from 9999 to 9999999997 in order to execute code after that FooGallery plugin added all its menu items.
* Update: Content view restrictions add-on: meta box is added with 'low' priority now instead of 'default'. It's done for compatibility with custom post types (like "LearnPress->Courses") for which this meta box was not shown.
* Fix: Content view restrictions add-on: try to access the restricted child page returned "page not found" error instead of redirect to URL. Redirection did not work in case global $post was not define to the moment. Find the page ID by its path from the URL for this case now.
* Fix: Syntax error was fixed which produced "PHP Warning: A non-numeric value encountered in .../wp-content/plugins/user-role-editor-pro/pro/includes/classes/meta-boxes.php on line 434".
* Core version was updated to 4.45:
* Fix: Capability checkbox was shown as turned ON incorrectly for not granted capability included into a role, JSON: "caps":{"sample_cap":"false"}. Bug took place after the changing a currently selected role.
* Fix: Custom capabilities groups "User Role Editor" and "WooCommerce" were registered at the wrong 3rd tree level - changed to 2. 

= [4.47.2] 05.07.2018 =
* Core version: 4.44
* Update: Widgets admin access add-on: It's possible now to block the access to sidebars (widgets areas) created by a user with help of Divi theme.
* Fix: Content view restrictions add-on: 'warning index not defined' (content-view-restrictions-posts-list.php, line #395) was fixed. 
* Core version was updated to 4.44:
* Update: URE had executed 'profile_update' action after update of user permissions from the user permissions editor page: Users->selected user->Capabilities. 
  It was replaced with 'ure_user_permissions_update' action now. It will allow to exclude conflicts with other plugins - "WP Members" [lost checkbox fields values](https://wordpress.org/support/topic/conflict-with-wp-members-2/), for example.
* Update: Additional options for role (like "Hide admin bar" at the bottom of URE page) did not applied to the user with 'ure_edit_roles' capability. This condition was removed.
* Update: fix PHP notice 'Undefined offset: 0 in ...' at includes/classes/protect-admin.php, not_edit_admin(), where the 1st element of $caps array not always has index 0.
* Update: PHP required version was increased up to 5.4.

= [4.47.1] 05.06.2018 =
* Core version: 4.43
* Core version was updated to 4.43:
* Update: references to non-existed roles are removed from the URE role additional options data storage after any role update.
* Fix: Additional options section view for the current role was not refreshed properly after other current role selection.

= [4.47] 22.05.2018 =
* Core version: 4.42
* New: support for new user capabilities introduced by WordPress 4.9.6 was added: manage_privacy_options (Settings->Privacy), export_others_personal_data (Tools->Export Personal Data), erase_others_personal_data (Tools->Erase Personal Data).
* Fix: Divi theme et_pb_layout (Divi Library) custom post type (CPT) was unavailable even for admin when "Force custom post types to use their own capabilities" URE option was turned ON.
et_pb_layout CPT is not available by default at User Role Editor pages (users.php), and we have to tell Divi to load et_pb_layout for URE pages via Divi's custom filter 'et_builder_should_load_framework'.

== Upgrade Notice ==
= [4.48] 25.09.2018 =
* Core version: 4.46
* Update: Edit posts/pages restrictions add-on: non-public taxonomies are not included into restricted database queries. URE will build now much shorter database queries for the sites which have a lot of such taxonomies at the database. For example sites with WP SEO Premium plugin, where 'yst_prominent_words' taxonomy may count thousands records at wp_terms and wp_term_taxonomy db tables.
* Update: Content view restrictions add-on: "Content View Restrictions" meta box markup was changed at post/page editor for better compatibility with Gutenberg. It will work as a usual meta box, located under main editor area.
* Update: Widgets admin access add-on: ure_widgets_edit_access_user custom filter was added. It allows to set final list of widgets blocked for editing by current user.
* Fix: Admin menu blocking add-on: URL processing were enhanced for the links with 'customize.php' inside, like "Appearance->Header" or "Appearance->Background". Those links did not work (redirection to dashboard took place) in case of the restricted admin menu. 
* Fix: wp-admin pages permissions viewer add-on: 
    - typo at word 'pemissions' was corrected at the "Settings->User Role Editor->Additional modules" tab.
    - source code .php file reading file(...) call was wrapped by 'try ...catch' to exclude PHP warning generation in case of a problem with access to a source code file.
* Core version was updated to 4.46:
* Update: "Users" page, "Without role" button: underlying SQL queries were replaced with more robust versions (about 10 times faster).
  It is critical for sites with large quant of users.New query does not take into account though some cases with incorrect users data (usually imported from the external sources).
  It's possible to use older (comprehensive but slower) query version defining a PHP constant: "define('URE_COUNT_USERS_WITHOUT_ROLE_THOROUGHLY', true);" or
  return false from a custom 'ure_count_users_without_role_quick' filter.
* Update: Error checking was enhanced after default role change for the WordPress multisite subsite.
* Update: URE settings page template: HTML helper checked() is used where applicable.
* Fix: 2 spelling mistakes were fixed in the text labels.
