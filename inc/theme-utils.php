<?php
if (!defined('ABSPATH')) {
  exit;
}

if (!class_exists('ThemeUtils')) {
  class ThemeUtils {

    public static $dark_brightness = -0.1;
    public static $light_brightness = 0.2;

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
          'phone'       => 'Phone',
          'share'       => 'Share',
          'calendar'    => 'Calendar',
          'reset'       => 'Reset',
          'play'        => 'Play',
          'plus'        => 'Plus',
          'minus'       => 'Minus',
          'dismiss'     => 'Dismiss',
          'top'         => 'Chevron top',
          'left'        => 'Chevron left',
          'right'       => 'Chevron right',
          'bottom'      => 'Chevron bottom'
        )
      );
    }

    /**
     * Default color palette.
     *
     * @return array
     */
    public static function get_color_palette() {
      $color_palette = array();
      $file_path = get_template_directory() . '/theme.json';
      $file_data = wp_json_file_decode($file_path, ['associative' => true]);
      if (is_array($file_data) && isset($file_data['settings']['color']['palette'])) {
        foreach ($file_data['settings']['color']['palette'] as $color) {
          $color_palette[$color['slug']] = array(
            'color' => $color['color'],
            'label' => $color['name']
          );
        }
      }
      return $color_palette;
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
      foreach ($hex as &$color) {
        $adjustable_limit = $percent < 0 ? $color : 255 - $color;
        $adjust_amount = ceil($adjustable_limit * $percent);
        $color = str_pad(dechex($color + $adjust_amount), 2, '0', STR_PAD_LEFT);
      }
      return '#' . implode($hex);
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
      $color_palette = self::get_color_palette();
      $bg_color = $color_palette['light']['color'];
      $btn_color = $color_palette['black']['color'];
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
        content: url('data:image/svg+xml;base64," . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.83 4.89l1.34.94-5.81 8.38H9.02L5.78 9.67l1.34-1.25 2.57 2.4z" fill="' . $btn_color . '" /></svg>') . "');
      }\n";
      if ($logo_id) {
        $logo_url = wp_get_attachment_url($logo_id);
        $login_styles .= "#login h1 a, .login h1 a {
          width: 200px;
          height: 48px;
          background-size: 100% auto;
          background-image: url('$logo_url');
        }";
      }
      return $login_styles;
    }

    /**
     * Create inline styles for theme utils & missing colors.
     *
     * @return string $theme_styles.
     */
    public static function create_theme_styles() {
      $variable_colors = ['primary', 'secondary', 'alt'];
      $theme_styles = 'body{';
      foreach (self::get_color_palette() as $name => $value) {
        if (in_array($name, $variable_colors)) {
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
      if (strpos($video_url, '.mp4') !== false && $poster_id) {
        $video_image = wp_get_attachment_image_url($poster_id, 'large');
      } else if (strpos($video_url, 'vimeo') !== false) {
        preg_match('%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im', $video_url, $matches);
        $video_image = "https://vumbnail.com/{$matches[3]}_large.jpg";
      } else if (strpos($video_url, 'youtu') !== false) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $matches);
        $video_image = "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
      }
      return $video_image;
    }

    /**
     * Create icons for Blocks' JavaScript usage.
     *
     * @return array $icons
     */
    public static function create_block_icons() {
      $icons = array();
      foreach (self::get_icons() as $name => $label) {
        $icons[$name] = array(
          'label' => $label,
          'svg'   => self::create_svg_icon($name)
        );
      }
      return $icons;
    }

    /**
     * Create a list of social networks' URLs from options.
     *
     * @return array
     */
    public static function create_social_networks() {
      $social_urls = array();
      foreach (self::get_social() as $key => $name) {
        // TODO: how to set options
        if ($url = get_theme_mod($key, false)) {
          $social_urls[$key] = array(
            'url'  => $url,
            'name' => $name
          );
        }
      }
      return $social_urls;
    }

    /**
     * Create a Bootstrap SVG icon by name.
     *
     * @link   https://icons.getbootstrap.com/
     * 
     * @param  string $name [icon slug].
     * @return string [SVG render].
     */
    public static function create_svg_icon($name) {
      $svg   = '';
      $name  = strtolower($name);
      $icons = self::get_icons();
      if (in_array($name, array_keys($icons))) {
        $svg .= '<svg class="icon icon-' . $name . '" xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 16 16" aria-hidden="true">';
        switch ($name) {
          case 'facebook':
            $svg .= '<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>';
            break;
          case 'instagram':
            $svg .= '<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>';
            break;
          case 'youtube':
            $svg .= '<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.007 2.007 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.007 2.007 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31.4 31.4 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.007 2.007 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A99.788 99.788 0 0 1 7.858 2h.193zM6.4 5.209v4.818l4.157-2.408L6.4 5.209z"/>';
            break;
          case 'linkedin':
            $svg .= '<path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>';
            break;
          case 'twitter':
            $svg .= '<path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>';
            break;
          case 'whatsapp':
            $svg .= '<path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>';
            break;
          case 'skype':
            $svg .= '<path d="M4.671 0c.88 0 1.733.247 2.468.702a7.423 7.423 0 0 1 6.02 2.118 7.372 7.372 0 0 1 2.167 5.215c0 .344-.024.687-.072 1.026a4.662 4.662 0 0 1 .6 2.281 4.645 4.645 0 0 1-1.37 3.294A4.673 4.673 0 0 1 11.18 16c-.84 0-1.658-.226-2.37-.644a7.423 7.423 0 0 1-6.114-2.107A7.374 7.374 0 0 1 .529 8.035c0-.363.026-.724.08-1.081a4.644 4.644 0 0 1 .76-5.59A4.68 4.68 0 0 1 4.67 0zm.447 7.01c.18.309.43.572.729.769a7.07 7.07 0 0 0 1.257.653c.492.205.873.38 1.145.523.229.112.437.264.615.448.135.142.21.331.21.528a.872.872 0 0 1-.335.723c-.291.196-.64.289-.99.264a2.618 2.618 0 0 1-1.048-.206 11.44 11.44 0 0 1-.532-.253 1.284 1.284 0 0 0-.587-.15.717.717 0 0 0-.501.176.63.63 0 0 0-.195.491.796.796 0 0 0 .148.482 1.2 1.2 0 0 0 .456.354 5.113 5.113 0 0 0 2.212.419 4.554 4.554 0 0 0 1.624-.265 2.296 2.296 0 0 0 1.08-.801c.267-.39.402-.855.386-1.327a2.09 2.09 0 0 0-.279-1.101 2.53 2.53 0 0 0-.772-.792A7.198 7.198 0 0 0 8.486 7.3a1.05 1.05 0 0 0-.145-.058 18.182 18.182 0 0 1-1.013-.447 1.827 1.827 0 0 1-.54-.387.727.727 0 0 1-.2-.508.805.805 0 0 1 .385-.723 1.76 1.76 0 0 1 .968-.247c.26-.003.52.03.772.096.274.079.542.177.802.293.105.049.22.075.336.076a.6.6 0 0 0 .453-.19.69.69 0 0 0 .18-.496.717.717 0 0 0-.17-.476 1.374 1.374 0 0 0-.556-.354 3.69 3.69 0 0 0-.708-.183 5.963 5.963 0 0 0-1.022-.078 4.53 4.53 0 0 0-1.536.258 2.71 2.71 0 0 0-1.174.784 1.91 1.91 0 0 0-.45 1.287c-.01.37.076.736.25 1.063z"/>';
            break;
          case 'globe':
            $svg .= '<path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855-.143.268-.276.56-.395.872.705.157 1.472.257 2.282.287V1.077zM4.249 3.539c.142-.384.304-.744.481-1.078a6.7 6.7 0 0 1 .597-.933A7.01 7.01 0 0 0 3.051 3.05c.362.184.763.349 1.198.49zM3.509 7.5c.036-1.07.188-2.087.436-3.008a9.124 9.124 0 0 1-1.565-.667A6.964 6.964 0 0 0 1.018 7.5h2.49zm1.4-2.741a12.344 12.344 0 0 0-.4 2.741H7.5V5.091c-.91-.03-1.783-.145-2.591-.332zM8.5 5.09V7.5h2.99a12.342 12.342 0 0 0-.399-2.741c-.808.187-1.681.301-2.591.332zM4.51 8.5c.035.987.176 1.914.399 2.741A13.612 13.612 0 0 1 7.5 10.91V8.5H4.51zm3.99 0v2.409c.91.03 1.783.145 2.591.332.223-.827.364-1.754.4-2.741H8.5zm-3.282 3.696c.12.312.252.604.395.872.552 1.035 1.218 1.65 1.887 1.855V11.91c-.81.03-1.577.13-2.282.287zm.11 2.276a6.696 6.696 0 0 1-.598-.933 8.853 8.853 0 0 1-.481-1.079 8.38 8.38 0 0 0-1.198.49 7.01 7.01 0 0 0 2.276 1.522zm-1.383-2.964A13.36 13.36 0 0 1 3.508 8.5h-2.49a6.963 6.963 0 0 0 1.362 3.675c.47-.258.995-.482 1.565-.667zm6.728 2.964a7.009 7.009 0 0 0 2.275-1.521 8.376 8.376 0 0 0-1.197-.49 8.853 8.853 0 0 1-.481 1.078 6.688 6.688 0 0 1-.597.933zM8.5 11.909v3.014c.67-.204 1.335-.82 1.887-1.855.143-.268.276-.56.395-.872A12.63 12.63 0 0 0 8.5 11.91zm3.555-.401c.57.185 1.095.409 1.565.667A6.963 6.963 0 0 0 14.982 8.5h-2.49a13.36 13.36 0 0 1-.437 3.008zM14.982 7.5a6.963 6.963 0 0 0-1.362-3.675c-.47.258-.995.482-1.565.667.248.92.4 1.938.437 3.008h2.49zM11.27 2.461c.177.334.339.694.482 1.078a8.368 8.368 0 0 0 1.196-.49 7.01 7.01 0 0 0-2.275-1.52c.218.283.418.597.597.932zm-.488 1.343a7.765 7.765 0 0 0-.395-.872C9.835 1.897 9.17 1.282 8.5 1.077V4.09c.81-.03 1.577-.13 2.282-.287z"/>';
            break;
          case 'location':
            $svg .= '<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>';
            break;
          case 'search':
            $svg .= '<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>';
            break;
          case 'email':
            $svg .= '<path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z"/>';
            break;
          case 'phone':
            $svg .= '<path d="M13.25 14c-1.355 0-2.703-.332-4.043-1a13.794 13.794 0 0 1-3.605-2.602A13.794 13.794 0 0 1 3 6.793c-.668-1.34-1-2.688-1-4.043 0-.21.07-.39.215-.535A.727.727 0 0 1 2.75 2h2.332c.156 0 .293.055.41.16a.717.717 0 0 1 .223.422l.453 2.102c.02.156.02.296-.008.425a.711.711 0 0 1-.176.325L4.316 7.117a13.78 13.78 0 0 0 2.094 2.7 11.41 11.41 0 0 0 2.625 1.949l1.582-1.633A.925.925 0 0 1 11 9.875a.748.748 0 0 1 .434-.023l1.984.433a.68.68 0 0 1 .414.25A.688.688 0 0 1 14 11v2.25c0 .21-.07.39-.215.535a.727.727 0 0 1-.535.215Zm0 0"/>';
            break;
          case 'share':
            $svg .= '<path d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.499 2.499 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5z"/>';
            break;
          case 'calendar':
            $svg .= '<path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>';
            break;
          case 'reset':
            $svg .= '<path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>';
            break;
          case 'play':
            $svg .= '<path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>';
            break;
          case 'plus':
            $svg .= '<path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>';
            break;
          case 'minus':
            $svg .= '<path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>';
            break;
          case 'dismiss':
            $svg .= '<path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>';
            break;
          case 'top':
            $svg .= '<path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/>';
            break;
          case 'left':
            $svg .= '<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>';
            break;
          case 'right':
            $svg .= '<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>';
            break;
          case 'bottom':
            $svg .= '<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>';
            break;
        }
        $svg .= '</svg>';
      }
      return $svg;
    }
  }
}
