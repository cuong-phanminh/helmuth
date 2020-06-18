-- Install Environment

1. Install Node.jd and NPM

2. Navigate to theme's root folder using a command prompt

3. Use command: "npm install" to install node modules

4. Use command: "gulp watch" to build scss to css


-- Functions

Name: seadev_get_theme_setting( $setting )
Usage: Get a specific setting of Advanced Theme Settings page
Located: inc/advanced_theme_settings.php
Params:
  $setting: acf field name in Seadev Advanced Theme Settings field group

Name: seadev_get_api_key ( $name )
Usage: Get a value from user defined name in API tab of Advanced Theme Settings page
Located: inc/advanced_theme_settings.php
Params:
  $name: key name to get the corresponding value 


-- Shortcodes

Name: [seadev-social-meida]
Usage: Listing social media channels from Social Media tab in Advanced Theme Settings page
Located: inc/shortcodes.php


-- Actions

Name: seadev_before_body_open_tag
Usage: Execute code block right before open <body> tag

Name: seadev_before_body_open_tag
Usage: Execute code block before closing </body> tag


-- Files

Customize Sass Variables: sass/theme/_variables.scss
Customize Style: sass/theme/_theme.scss
Customize Responsive Style: sass/theme/_responsive.scss
Customize Editor Style: sass/editor.scss
Customize Scripts: js/scripts.js
Customize Guttenberg Block: inc/blocks.php





