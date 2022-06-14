<?php
if ( !defined('ABSPATH') ) {
  exit;
}

if ( !class_exists('ThemeUtils') ) {
  class ThemeUtils {

    public static $dark_brightness = -0.208;
    public static $light_brightness = 0.416;

    /**
     * Default social networks key-value.
     *
     * @return array
     */
    public static function get_social() {
      return array(
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin'  => 'LinkedIn',
        'twitter'   => 'Twitter',
        'youtube'   => 'YouTube',
        'whatsapp'  => 'WhatsApp',
        'skype'     => 'Skype'
      );
    }

    /**
     * Default icons key-value.
     *
     * @return array
     */
    public static function get_icons() {
      return array_merge(
        self::get_social(),
        array(
          'globe'       => 'World',
          'location'    => 'Location',
          'search'      => 'Search',
          'email'       => 'Email',
          'share'       => 'Share',
          'calendar'    => 'Calendar',
          'reset'       => 'Reset',
          'play'        => 'Play',
          'plus'        => 'Plus',
          'minus'       => 'Minus',
          'dismiss'     => 'Dismiss',
          'left'        => 'Chevron left',
          'right'       => 'Chevron right',
          'arrow-up'    => 'Arrow up',
          'arrow-left'  => 'Arrow left',
          'arrow-right' => 'Arrow right',
          'arrow-down'  => 'Arrow down'
        )
      );
    }

    /**
     * Default color scheme.
     *
     * @return array
     */
    public static function get_color_scheme() {
      $color_scheme = array();
      $file_path = get_template_directory() .'/theme.json';
      $file_data = wp_json_file_decode($file_path, ['associative' => true]);
      if ( is_array($file_data) && isset($file_data['settings']['color']['palette']) ) {
        foreach($file_data['settings']['color']['palette'] as $color) {
          $color_scheme[$color['slug']] = array(
            'color' => $color['color'],
            'label' => $color['name']
          );
        }
      }
      return $color_scheme;
    }

    /**
     * Helper method to get brightness by HEX.
     *
     * @param  string $hex
     * @param  float  $percent
     * @return string
     */
    public static function get_color_brightness($hex, $percent) {
      $hex = ltrim($hex, '#');
      if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
      }
      $hex = array_map('hexdec', str_split($hex, 2));
      foreach ($hex as & $color) {
        $adjustable_limit = $percent < 0 ? $color : 255 - $color;
        $adjust_amount = ceil($adjustable_limit * $percent);
        $color = str_pad(dechex($color + $adjust_amount), 2, '0', STR_PAD_LEFT);
      }
      return '#'.implode($hex);
    }

    /**
     * Create inline style for theme admin/bar styles.
     *
     * @return string $admin_styles.
     */
    public static function create_admin_styles() {
      $admin_styles = 'html {
        margin-bottom: var(--wp-admin--admin-bar--height) !important;
      }
      @media (max-width: 782px) {
        html {
          margin-bottom: var(--wp-admin--admin-bar--height) !important;
        }
        #wpadminbar {
          position: fixed !important;
        }
      }
      #wpadminbar {
        top: auto !important;
        bottom: 0 !important;
      }
      #wpadminbar .menupop .ab-sub-wrapper,
      #wpadminbar .shortlink-input {
        bottom: var(--wp-admin--admin-bar--height) !important;
      }';
      return $admin_styles;
    }

    /**
     * Create inline style for theme admin/login styles.
     *
     * @param  int    $logo_id [current logo ID].
     * @return string $login_styles [HTML output].
     */
    public static function create_login_styles($logo_id) {
      $color_scheme = self::get_color_scheme();
      $bg_color = $color_scheme['light']['color'];
      $btn_color = $color_scheme['black']['color'];
      $btn_light_color = self::get_color_brightness($btn_color, self::$light_brightness);
      $login_styles = "body {
        background: $bg_color;
      }
      .wp-core-ui .button {
        background: $btn_color;
        border-color: $btn_color;
      }
      .wp-core-ui .button-primary.focus,
      .wp-core-ui .button-primary.hover,
      .wp-core-ui .button-primary:focus,
      .wp-core-ui .button-primary:hover {
        background: $btn_light_color;
        border-color: $btn_light_color;
      }
      .wp-core-ui .button-primary.focus,
      .wp-core-ui .button-primary:focus {
        box-shadow: 0 0 0 1px #fff, 0 0 0 3px $btn_light_color;
      }
      .wp-core-ui .button-secondary {
      color: $btn_color;
        border-color: transparent !important;
        box-shadow: none !important;
      }
      .wp-core-ui .button-secondary:hover,
      .wp-core-ui .button-secondary:focus {
        color: $btn_light_color;
      }
      input[type=checkbox]:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime-local]:focus, input[type=datetime]:focus, input[type=email]:focus, input[type=month]:focus, input[type=number]:focus, input[type=password]:focus, input[type=radio]:focus, input[type=search]:focus, input[type=tel]:focus, input[type=text]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, select:focus, textarea:focus {
        border-color: $btn_light_color;
        box-shadow: 0 0 0 1px $btn_light_color;
      }
      input[type=checkbox]:checked::before {
        content: url('data:image/svg+xml;base64,". base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.83 4.89l1.34.94-5.81 8.38H9.02L5.78 9.67l1.34-1.25 2.57 2.4z" fill="'.$btn_color.'" /></svg>') ."');
      }";
      if ($logo_id) {
        $logo_url = wp_get_attachment_url($logo_id);
        $logo_path = wp_get_original_image_path($logo_id);
        list($width, $height) = getimagesize($logo_path);
        $login_styles .= "#login h1 a, .login h1 a {
          width: ${width}px;
          height: ${height}px;
          background-image: url('$logo_url');
          background-size: ${width}px ${height}px;
        }";
      }
      return $login_styles;
    }

    /**
     * Create inline styles for theme partials & missing colors.
     *
     * @return string $theme_styles.
     */
    public static function create_theme_styles() {
      $theme_styles = '';
      $variable_colors = ['primary', 'secondary', 'alt'];
      $partials_dir = get_stylesheet_directory() .'/assets/css/partials';
      $partials = array_diff( scandir($partials_dir), ['.', '..'] );
      // TODO: reorder files (?)
      foreach($partials as $file) {
        $partial_css = file_get_contents("{$partials_dir}/{$file}");
        $theme_styles .= preg_replace(
          ['/\s*(\w)\s*{\s*/','/\s*(\S*:)(\s*)([^;]*)(\s|\n)*;(\n|\s)*/','/\n/','/\s*}\s*/'],
          ['$1{ ','$1$3;',"",'} '],
          $partial_css
        );
      }
      $theme_styles .= 'body{';
      foreach(self::get_color_scheme() as $name => $value) {
        if ( in_array($name, $variable_colors) ) {
          $color_dark = self::get_color_brightness($value['color'], self::$dark_brightness);
          $color_light = self::get_color_brightness($value['color'], self::$light_brightness);
          $theme_styles .= "--wp--preset--color--{$name}-dark: $color_dark;";
          $theme_styles .= "--wp--preset--color--{$name}-light: $color_light;";
        }
      }
      $theme_styles .= '}';
      return $theme_styles;
    }

    /**
     * Create video preview image by source URL.
     *
     * @param  string $video_url [provided video URL].
     * @param  string $poster_id [provided poster image ID].
     * @return string $video_image [preview image URL].
     */
    public static function create_video_preview($video_url, $poster_id = 0) {
      if ( strpos($video_url, '.mp4') !== false && $poster_id ) {
        $video_image = wp_get_attachment_image_url($poster_id, 'large');
      } else if( strpos($video_url, 'vimeo') !== false ) {
        preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video_url, $matches);
        $video_image = "https://vumbnail.com/{$matches[3]}_large.jpg";
      } else if( strpos($video_url, 'youtu') !== false ) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $matches);
        $video_image = "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
      }
      return $video_image;
    }

    /**
     * Create icons for Blocks' JavaScript usage.
     *
     * @return array
     */
    public static function create_block_icons() {
      return array(
        'prev'  => self::create_svg_icon('arrow-left'),
        'next'  => self::create_svg_icon('arrow-right'),
        'plus'  => self::create_svg_icon('plus'),
        'minus' => self::create_svg_icon('minus'),
        'reset' => self::create_svg_icon('reset'),
        'location' => self::create_svg_icon('location')
      );
    }

    /**
     * Create a list of social networks' URLs from options.
     *
     * @return array
     */
    public static function create_social_networks() {
      $social_urls = array();
      foreach(self::get_social() as $key => $name) {
        // TODO: how to set options
        if ( $url = get_theme_mod($key, false) ) {
          $social_urls[$key] = array(
            'url'  => $url,
            'name' => $name
          );
        }
      }
      return $social_urls;
    }

    /**
     * Create SVG icon by name.
     *
     * @param  string $name [icon slug].
     * @return string [SVG render].
     */
    public static function create_svg_icon($name) {
      $svg   = '';
      $name  = strtolower($name);
      $icons = self::get_icons();
      if ( in_array($name, array_keys($icons)) ) {
        $svg .= '<svg class="icon icon-'.$name.'" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" aria-hidden="true">';
        switch($name) {
          case 'facebook':
            $svg .= '<path d="M12 2C6.5 2 2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12c0-5.5-4.5-10-10-10z"/>';
            break;
          case 'instagram':
            $svg .= '<path d="M12,4.622c2.403,0,2.688,0.009,3.637,0.052c0.877,0.04,1.354,0.187,1.671,0.31c0.42,0.163,0.72,0.358,1.035,0.673 c0.315,0.315,0.51,0.615,0.673,1.035c0.123,0.317,0.27,0.794,0.31,1.671c0.043,0.949,0.052,1.234,0.052,3.637 s-0.009,2.688-0.052,3.637c-0.04,0.877-0.187,1.354-0.31,1.671c-0.163,0.42-0.358,0.72-0.673,1.035 c-0.315,0.315-0.615,0.51-1.035,0.673c-0.317,0.123-0.794,0.27-1.671,0.31c-0.949,0.043-1.233,0.052-3.637,0.052 s-2.688-0.009-3.637-0.052c-0.877-0.04-1.354-0.187-1.671-0.31c-0.42-0.163-0.72-0.358-1.035-0.673 c-0.315-0.315-0.51-0.615-0.673-1.035c-0.123-0.317-0.27-0.794-0.31-1.671C4.631,14.688,4.622,14.403,4.622,12 s0.009-2.688,0.052-3.637c0.04-0.877,0.187-1.354,0.31-1.671c0.163-0.42,0.358-0.72,0.673-1.035 c0.315-0.315,0.615-0.51,1.035-0.673c0.317-0.123,0.794-0.27,1.671-0.31C9.312,4.631,9.597,4.622,12,4.622 M12,3 C9.556,3,9.249,3.01,8.289,3.054C7.331,3.098,6.677,3.25,6.105,3.472C5.513,3.702,5.011,4.01,4.511,4.511 c-0.5,0.5-0.808,1.002-1.038,1.594C3.25,6.677,3.098,7.331,3.054,8.289C3.01,9.249,3,9.556,3,12c0,2.444,0.01,2.751,0.054,3.711 c0.044,0.958,0.196,1.612,0.418,2.185c0.23,0.592,0.538,1.094,1.038,1.594c0.5,0.5,1.002,0.808,1.594,1.038 c0.572,0.222,1.227,0.375,2.185,0.418C9.249,20.99,9.556,21,12,21s2.751-0.01,3.711-0.054c0.958-0.044,1.612-0.196,2.185-0.418 c0.592-0.23,1.094-0.538,1.594-1.038c0.5-0.5,0.808-1.002,1.038-1.594c0.222-0.572,0.375-1.227,0.418-2.185 C20.99,14.751,21,14.444,21,12s-0.01-2.751-0.054-3.711c-0.044-0.958-0.196-1.612-0.418-2.185c-0.23-0.592-0.538-1.094-1.038-1.594 c-0.5-0.5-1.002-0.808-1.594-1.038c-0.572-0.222-1.227-0.375-2.185-0.418C14.751,3.01,14.444,3,12,3L12,3z M12,7.378 c-2.552,0-4.622,2.069-4.622,4.622S9.448,16.622,12,16.622s4.622-2.069,4.622-4.622S14.552,7.378,12,7.378z M12,15 c-1.657,0-3-1.343-3-3s1.343-3,3-3s3,1.343,3,3S13.657,15,12,15z M16.804,6.116c-0.596,0-1.08,0.484-1.08,1.08 s0.484,1.08,1.08,1.08c0.596,0,1.08-0.484,1.08-1.08S17.401,6.116,16.804,6.116z"/>';
            break;
          case 'youtube':
            $svg .= '<path d="M21.8,8.001c0,0-0.195-1.378-0.795-1.985c-0.76-0.797-1.613-0.801-2.004-0.847c-2.799-0.202-6.997-0.202-6.997-0.202 h-0.009c0,0-4.198,0-6.997,0.202C4.608,5.216,3.756,5.22,2.995,6.016C2.395,6.623,2.2,8.001,2.2,8.001S2,9.62,2,11.238v1.517 c0,1.618,0.2,3.237,0.2,3.237s0.195,1.378,0.795,1.985c0.761,0.797,1.76,0.771,2.205,0.855c1.6,0.153,6.8,0.201,6.8,0.201 s4.203-0.006,7.001-0.209c0.391-0.047,1.243-0.051,2.004-0.847c0.6-0.607,0.795-1.985,0.795-1.985s0.2-1.618,0.2-3.237v-1.517 C22,9.62,21.8,8.001,21.8,8.001z M9.935,14.594l-0.001-5.62l5.404,2.82L9.935,14.594z" />';
            break;
          case 'linkedin':
            $svg .= '<path d="M19.7,3H4.3C3.582,3,3,3.582,3,4.3v15.4C3,20.418,3.582,21,4.3,21h15.4c0.718,0,1.3-0.582,1.3-1.3V4.3 C21,3.582,20.418,3,19.7,3z M8.339,18.338H5.667v-8.59h2.672V18.338z M7.004,8.574c-0.857,0-1.549-0.694-1.549-1.548 c0-0.855,0.691-1.548,1.549-1.548c0.854,0,1.547,0.694,1.547,1.548C8.551,7.881,7.858,8.574,7.004,8.574z M18.339,18.338h-2.669 v-4.177c0-0.996-0.017-2.278-1.387-2.278c-1.389,0-1.601,1.086-1.601,2.206v4.249h-2.667v-8.59h2.559v1.174h0.037 c0.356-0.675,1.227-1.387,2.526-1.387c2.703,0,3.203,1.779,3.203,4.092V18.338z"/>';
            break;
          case 'twitter':
            $svg .= '<path d="M22.23,5.924c-0.736,0.326-1.527,0.547-2.357,0.646c0.847-0.508,1.498-1.312,1.804-2.27 c-0.793,0.47-1.671,0.812-2.606,0.996C18.324,4.498,17.257,4,16.077,4c-2.266,0-4.103,1.837-4.103,4.103 c0,0.322,0.036,0.635,0.106,0.935C8.67,8.867,5.647,7.234,3.623,4.751C3.27,5.357,3.067,6.062,3.067,6.814 c0,1.424,0.724,2.679,1.825,3.415c-0.673-0.021-1.305-0.206-1.859-0.513c0,0.017,0,0.034,0,0.052c0,1.988,1.414,3.647,3.292,4.023 c-0.344,0.094-0.707,0.144-1.081,0.144c-0.264,0-0.521-0.026-0.772-0.074c0.522,1.63,2.038,2.816,3.833,2.85 c-1.404,1.1-3.174,1.756-5.096,1.756c-0.331,0-0.658-0.019-0.979-0.057c1.816,1.164,3.973,1.843,6.29,1.843 c7.547,0,11.675-6.252,11.675-11.675c0-0.178-0.004-0.355-0.012-0.531C20.985,7.47,21.68,6.747,22.23,5.924z"/>';
            break;
          case 'whatsapp':
            $svg .= '<path d="M14.906 13.043c.117 0 .551.2 1.305.59.754.394 1.156.629 1.2.71.019.044.026.114.026.2 0 .297-.074.637-.226 1.02-.145.347-.461.64-.953.878-.488.235-.945.352-1.363.352-.512 0-1.36-.273-2.547-.828a7.672 7.672 0 01-2.278-1.582c-.64-.653-1.3-1.477-1.98-2.477-.645-.957-.961-1.82-.953-2.597v-.11c.027-.812.36-1.515.992-2.113.215-.2.445-.297.695-.297.055 0 .137.008.242.02.11.015.192.023.254.023.172 0 .29.027.356.086s.136.18.207.367c.074.18.219.57.441 1.18.227.605.336.941.336 1.004 0 .187-.152.445-.46.77-.31.327-.462.534-.462.624 0 .063.02.13.067.2.3.652.758 1.261 1.363 1.835.5.473 1.176.922 2.023 1.352.11.063.207.094.297.094.133 0 .375-.215.723-.649.348-.433.578-.652.695-.652zm-2.719 7.102a8.209 8.209 0 003.262-.672 8.464 8.464 0 002.684-1.793 8.513 8.513 0 001.797-2.688 8.191 8.191 0 00.668-3.262 8.186 8.186 0 00-.668-3.257 8.513 8.513 0 00-1.797-2.688 8.464 8.464 0 00-2.684-1.793 8.142 8.142 0 00-3.261-.672 8.142 8.142 0 00-3.262.672 8.464 8.464 0 00-2.684 1.793 8.513 8.513 0 00-1.797 2.688 8.186 8.186 0 00-.668 3.257 8.19 8.19 0 001.606 4.93l-1.059 3.121 3.242-1.031a8.233 8.233 0 004.622 1.395zm0-18.512c1.368 0 2.672.27 3.918.804 1.247.536 2.32 1.254 3.22 2.157.902.902 1.62 1.976 2.155 3.222.54 1.243.805 2.551.805 3.914a9.757 9.757 0 01-.805 3.918c-.535 1.247-1.253 2.32-2.156 3.223-.898.902-1.972 1.621-3.219 2.156a9.824 9.824 0 01-3.918.805 9.925 9.925 0 01-4.886-1.262l-5.586 1.797 1.82-5.426c-.965-1.59-1.445-3.324-1.445-5.21 0-1.364.265-2.672.805-3.915.535-1.246 1.253-2.32 2.156-3.222.898-.903 1.972-1.621 3.219-2.157a9.824 9.824 0 013.918-.804zm0 0"/>';
            break;
          case 'skype':
            $svg .= '<path d="M17.426 14.238a2.944 2.944 0 00-.914-2.145 4.016 4.016 0 00-.977-.655 7.749 7.749 0 00-1.105-.458 15.647 15.647 0 00-1.172-.304l-1.39-.324c-.27-.063-.466-.11-.59-.141a4.491 4.491 0 01-.47-.152 1.373 1.373 0 01-.402-.215 1.123 1.123 0 01-.222-.281.797.797 0 01-.098-.403c0-.687.64-1.031 1.926-1.031.386 0 .73.055 1.031.16.305.11.547.234.727.383.175.148.347.297.507.45.16.151.34.28.536.386.195.11.41.16.644.16.418 0 .754-.14 1.008-.426a1.48 1.48 0 00.383-1.031c0-.492-.25-.938-.75-1.332-.5-.399-1.133-.7-1.903-.906a9.47 9.47 0 00-2.437-.309c-.606 0-1.195.07-1.766.207a6.227 6.227 0 00-1.601.633 3.212 3.212 0 00-1.192 1.164c-.3.496-.449 1.07-.449 1.719 0 .547.086 1.023.254 1.43.172.406.422.742.75 1.007.332.27.687.485 1.07.653.387.164.844.308 1.383.433l1.953.485c.805.195 1.305.355 1.5.48.285.18.43.445.43.805 0 .347-.18.636-.535.863-.36.227-.828.34-1.407.34-.457 0-.863-.07-1.226-.215a2.692 2.692 0 01-.871-.516 9.883 9.883 0 01-.61-.601 3.23 3.23 0 00-.617-.516 1.326 1.326 0 00-.722-.215c-.446 0-.782.137-1.012.403-.227.27-.34.601-.34 1.004 0 .824.543 1.527 1.633 2.109 1.09.586 2.39.879 3.898.879.653 0 1.278-.082 1.875-.25a6.075 6.075 0 001.64-.715c.497-.313.892-.73 1.184-1.254a3.49 3.49 0 00.446-1.758zm4.86 2.907c0 1.418-.5 2.628-1.509 3.632-1.004 1.008-2.215 1.508-3.632 1.508a5.009 5.009 0 01-3.137-1.07A10.01 10.01 0 0112 21.43a9.27 9.27 0 01-3.664-.746 9.374 9.374 0 01-3.012-2.008 9.374 9.374 0 01-2.008-3.012A9.27 9.27 0 012.57 12c0-.652.075-1.32.215-2.008a5.009 5.009 0 01-1.07-3.137c0-1.418.5-2.628 1.508-3.632 1.004-1.008 2.215-1.508 3.632-1.508 1.165 0 2.207.355 3.137 1.07A10.01 10.01 0 0112 2.57a9.27 9.27 0 013.664.746 9.374 9.374 0 013.012 2.008 9.374 9.374 0 012.008 3.012A9.27 9.27 0 0121.43 12c0 .652-.075 1.32-.215 2.008a5.009 5.009 0 011.07 3.137zm0 0"/>';
            break;
          case 'globe':
            $svg .= '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95a15.65 15.65 0 0 0-1.38-3.56A8.03 8.03 0 0 1 18.92 8zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56A7.987 7.987 0 0 1 5.08 16zm2.95-8H5.08a7.987 7.987 0 0 1 4.33-3.56A15.65 15.65 0 0 0 8.03 8zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2s.07-1.35.16-2h4.68c.09.65.16 1.32.16 2s-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95a8.03 8.03 0 0 1-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2s-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z"/>';
            break;
          case 'location':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0 0 13 3.06V1h-2v2.06A8.994 8.994 0 0 0 3.06 11H1v2h2.06A8.994 8.994 0 0 0 11 20.94V23h2v-2.06A8.994 8.994 0 0 0 20.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>';
            break;
          case 'search':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>';
            break;
          case 'email':
            $svg .= '<path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/><path d="M0 0h24v24H0z" fill="none"/>';
            break;
          case 'share':
            $svg .= '<path d="M17.9 15.7c-.9 0-1.7.4-2.2 1.1L8.9 13c0-.3.1-.7.1-1 0-.3-.1-.7-.2-.9l6.8-3.8c.5.6 1.3 1.1 2.2 1.1 1.6 0 2.9-1.3 2.9-2.9 0-1.6-1.3-2.9-2.9-2.9S15 3.8 15 5.4c0 .3.1.7.2.9l-6.8 3.8c-.6-.6-1.4-1-2.3-1-1.6 0-2.9 1.3-2.9 2.9s1.3 2.9 2.9 2.9c.9 0 1.7-.4 2.2-1.1l6.8 3.8c0 .4-.1.7-.1 1 0 1.6 1.3 2.9 2.9 2.9s2.9-1.3 2.9-2.9-1.3-2.9-2.9-2.9zm0-12.2c1 0 1.9.8 1.9 1.9s-.8 1.9-1.9 1.9S16 6.4 16 5.4s.8-1.9 1.9-1.9zM6.1 13.9c-1 0-1.9-.8-1.9-1.9s.8-1.9 1.9-1.9S8 11 8 12s-.8 1.9-1.9 1.9zm11.8 6.6c-1 0-1.9-.8-1.9-1.9 0-1 .8-1.9 1.9-1.9s1.9.8 1.9 1.9c0 1.1-.9 1.9-1.9 1.9z"/>';
            break;
          case 'calendar':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M21 3.5h-4.2V2c0-.3-.3-.6-.6-.6s-.6.3-.6.6v1.5H8.4V2c0-.3-.3-.6-.6-.6s-.6.3-.6.6v1.5H3c-.3 0-.6.3-.6.6v16.6c0 .3.3.6.6.6h18c.3 0 .6-.3.6-.6V4.1c0-.3-.3-.6-.6-.6zm-.6 16.6H3.6V9h16.9v11.1zm0-12.2H3.6V4.7h3.7v1.4c0 .3.3.6.6.6s.6-.3.6-.6V4.7h7.2v1.4c0 .3.3.6.6.6s.6-.3.6-.6V4.7h3.7v3.2z"/><path d="M15.5 14.4c0 .3-.3.6-.6.6h-2.4v2.4c0 .3-.3.6-.6.6s-.6-.3-.6-.6V15H9c-.3 0-.6-.3-.6-.6s.3-.6.6-.6h2.4v-2.4c0-.3.3-.6.6-.6s.6.3.6.6v2.4H15c.3 0 .5.3.5.6z"/>';
            break;
          case 'reset':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>';
            break;
          case 'play':
            $svg .= '<path d="M0 0h24v24H0z" fill="none"/><path d="M8 5v14l11-7z"/>';
            break;
          case 'plus':
            $svg .= '<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/>';
            break;
          case 'minus':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 13H5v-2h14v2z"/>';
            break;
          case 'dismiss':
            $svg .= '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/>';
            break;
          case 'left':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12l4.58-4.59z"/>';
            break;
          case 'right':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M10 6 8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6-6-6z"/>';
            break;
          case 'arrow-up':
            $svg .= '<path fill="none" d="M0 0h24v24H0z"/><path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"/>';
            break;
          case 'arrow-left':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>';
            break;
          case 'arrow-right':
            $svg .= '<path d="M0 0h24v24H0V0z" fill="none"/><path d="m12 4-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z"/>';
            break;
          case 'arrow-down':
            $svg .= '<path fill="none" d="M0 0h24v24H0z"/><path d="m19 15-1.41-1.41L13 18.17V2h-2v16.17l-4.59-4.59L5 15l7 7 7-7z"/>';
            break;
        }
        $svg .= '</svg>';
      }
      return $svg;
    }

  }
}
