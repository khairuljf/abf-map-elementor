<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Hip_Maps_Elementor_Widget;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class abf_map_Widget extends \Elementor\Widget_Base {

    /**
     * register scripts
     */
    public function get_script_depends() {

        wp_register_script( 'abf-leaflet-js', plugins_url( '/assets/js/leaflet.js', __FILE__ ) );
        wp_register_script( 'abf-leaflet-provider', plugins_url( '/assets/js/leaflet-provider.js', __FILE__ ), [ 'external-library' ] );
        wp_register_script( 'abf-custom-map', plugins_url( '/assets/js/custom-map.js', __FILE__ ) );


        return [
            'abf-leaflet-js',
            'abf-leaflet-provider',
            'abf-custom-map',
        ];

    }

    /**
     * @return string[]
     * register style
     */
    public function get_style_depends() {

        wp_register_style('abf-map-widget-css', plugins_url('/assets/css/leaflet.css', __FILE__));
        wp_register_style('abf-map-widget-custom-css', plugins_url('/assets/css/map-style.css', __FILE__));


        return [
            'abf-map-widget-css',
            'abf-map-widget-custom-css',
        ];

    }


    /**
     * Get widget name.
     *
     * Retrieve oEmbed widget name.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget name.
     */
    public function get_name() {
        return 'Abf Map';
    }

    /**
     * Get widget title.
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Abf Map', 'elementor-oembed-widget' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve oEmbed widget icon.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-google-maps';
    }

    /**
     * Get custom help URL.
     *
     * Retrieve a URL where the user can get more information about the widget.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget help URL.
     */
    public function get_custom_help_url() {
        return 'https://developers.elementor.com/docs/widgets/';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'general' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return [ 'map', 'leafjs', 'pin','marker' ];
    }

    /**
     * Register oEmbed widget controls.
     *
     * Add input fields to allow the user to customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {


        // Add Marker Repeter
        $this->map_marker_control();
        // Add Marker setttings
        $this->map_settings_controls();
        $this->style_content_register_controls();
        $this->style_close_icon_register_controls();
        $this->style_map_box_register_controls();

    }

    //map marker controls
    protected function map_marker_control(){


        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Abf Map Marker', 'elementor-oembed-widget' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );




        $repeater = new Repeater();

        $repeater->add_control(
            'hip_pin_icon',
            array(
                'label'   => __('Custom Pin Icon', 'abf_addons'),
                'type'    => Controls_Manager::MEDIA,
                'dynamic' => array('active' => true),
                'default' =>[
                    'url' => plugin_dir_url(dirname(__FILE__)).'abf-map-elementor/widgets/assets/image/pin-icon.svg',
                ]
            )
        );

        $repeater->add_control(
            'abf_map_latitude',
            array(
                'name'        => 'abf_map_latitude',
                'label'       => __('Latitude', 'hip'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => array('active' => true),
                'description' => 'Click <a href="https://www.latlong.net/" target="_blank">here</a> to get your location coordinates',
                'label_block' => true,
                'default'=>'23.810331'
            )
        );

        $repeater->add_control(
            'abf_map_longitude',
            array(
                'name'        => 'abf_map_longitude',
                'label'       => __('Longitude', 'hip'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => array('active' => true),
                'description' => 'Click <a href="https://www.latlong.net/" target="_blank">here</a> to get your location coordinates',
                'label_block' => true,
                'default' => '90.412521'
            )
        );

        $repeater->add_control(
            'hip_pin_title',
            array(
                'label'       => __('Pin Popup Info', 'hip'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => array('active' => true),
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'abf_map_link',
            [
                'label' => esc_html__( 'Single Map Link', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'plugin-name' ),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => true,
                ],
                'label_block' => true,
            ]
        );


        $repeater->add_control(
            'hip_desc_color',
            [
                'label' => esc_html__( 'Text Color', 'hip' ),
                'type' =>Controls_Manager::COLOR,
                'default'	=> '#000',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}  a' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]

        );

        $repeater->add_control(
            'hip_desc_hover_color',
            [
                'label' => esc_html__( 'Text hover Color', 'hip' ),
                'type' => Controls_Manager::COLOR,
                'default'	=> '#fff',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}  .leaflet-popup-content-wrapper:hover .leaflet-popup-content a' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'hip_box_bgclr',
            [
                'label' => esc_html__( 'Info Box BG', 'hip' ),
                'type' => Controls_Manager::COLOR,
                'default'	=> '#1EB2F1',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-content-wrapper' => 'background: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-tip' => 'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'hip_box_bgclr_hover',
            [
                'label' => esc_html__( 'Info Box BG Hover', 'hip' ),
                'type' => Controls_Manager::COLOR,
                'default'	=> '#000',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-content-wrapper:hover' => 'background: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-content-wrapper:hover + .leaflet-popup-tip-container .leaflet-popup-tip' => 'border-top-color: {{VALUE}}'
                ],
            ]
        );


        $this->add_control(
            'abf_map_pins',
            array(
                'label'       => __('Map Pins List', 'hip'),
                'type'        => Controls_Manager::REPEATER,
                'default'     => array(
                    'hip_map_latitude'  => '23.810331',
                    'hip_map_longitude' => '90.412521',
                    'hip_pin_title'     => __('Hip custom  Maps', 'hip'),
                ),
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ hip_pin_title }}}',
                'prevent_empty' => false,
                'separator' => 'before',
            )
        );

        $this->end_controls_section();
    }
    //map settings controls
    protected function map_settings_controls(){
        //controls
        $this->start_controls_section(
            'map-settings-section',
            [
                'label'=>__('Map Settings', 'hip'),
                'tab'=> \Elementor\Controls_Manager::TAB_CONTENT
            ]
        );

        $this->add_control(
            'hip_map_fit_bounds',
            [
                'label' => __('Auto Fit Map Bounds', 'hip'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'hip'),
                'label_off' => __('Off', 'hip'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'hip_map_zoom_control',
            [
                'label' => __('Zoom Control Option', 'hip'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'hip'),
                'label_off' => __('Hide', 'hip'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );





        $this->add_control(
            'hip_maps_zoom_desktop',
            array(
                'label'   => __('Zoom Level', 'hip'),
                'type'    => Controls_Manager::SLIDER,

                'range'   => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 22,
                    ),
                ),
                'default' => [
                    'unit' => '%',
                    'size' => 8,
                ],
                'separator' => 'before',

            )
        );


        $this->add_responsive_control(
            'hip_maps_map_height',
            array(
                'label'     => __('Height', 'hip'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => array(
                    'size' => 500,
                ),
                'range'     => array(
                    'px' => array(
                        'min' => 80,
                        'max' => 1400,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .hip-custom-map.leaflet-container' => 'height: {{SIZE}}px;',
                ),
            )
        );
        $this->add_control(
            'hip_map_popup_show_always',
            [
                'label' => __('Pin Popup open when load?', 'hip'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Load', 'hip'),
                'label_off' => __('Hide', 'hip'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        //Map style
        $this->add_control(
            'map_style',
            [
                'label' => __('Map Style', 'hip'),
                'type' => Controls_Manager::SELECT,
                'default' => 'CartoDB.Positron',
                'options' => [
                    'OpenStreetMap.Mapnik' => esc_html__('Open Street Map', 'hip'),
                    'Stamen.TonerLite' => esc_html__('Toner Lite', 'hip'),
                    'Esri.WorldStreetMap' => esc_html__('World Street Map', 'hip'),
                    'CartoDB.Positron' => esc_html__('Positron ', 'hip'),
                    'Stadia.AlidadeSmooth' => esc_html__('Stadia Alidade Smooth ', 'hip'),
                    'Esri.WorldGrayCanvas' => esc_html__('World Gray Canvas ', 'hip'),
                    'CartoDB.Voyager' => esc_html__('CartoDB Voyager ', 'hip'),
                ],
            ]
        );

        $this->end_controls_section();
    }
    //Style control section

    protected function style_content_register_controls() {

        $this->start_controls_section(
            'hip_info_popup_style',
            [
                'label' => esc_html__( 'Popup Info', 'hip' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .leaflet-popup-content a,{{WRAPPER}} .leaflet-popup-content a',
            ]
        );

        $this->add_responsive_control(
            'hip_map_info_box_radius',
            array(
                'label'      => __('Border Radius', 'hip'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', '%', 'em'),
                'default' => [
                    'unit' => 'px',
                    'size' => 5,
                ],
                'selectors'  => array(
                    '{{WRAPPER}} .leaflet-popup-content-wrapper ' => 'border-radius: {{SIZE}}{{UNIT}};overflow:hidden',
                ),
            )
        );
        $this->add_responsive_control(
            'hip_info_box_margin',
            array(
                'label'      => __('Margin', 'hip'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => [
                    'top'=>0, 'right'=>0, 'bottom'=>25, 'left'=>0, 'unit'=>'px'
                ],
                'selectors'  => array(
                    '{{WRAPPER}} .leaflet-popup.leaflet-zoom-animated' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );

        $this->add_responsive_control(
            'hip_info_box_padding',
            array(
                'label'      => __('Padding', 'hip'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => [
                    'top'=>14, 'right'=>28, 'bottom'=>14, 'left'=>28, 'unit'=>'px'
                ],
                'selectors'  => array(
                    '{{WRAPPER}} .leaflet-popup-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );


        $this->end_controls_section();

    }
    protected function style_close_icon_register_controls() {

        $this->start_controls_section(
            'hip_close_icon',
            [
                'label' => esc_html__( 'Close Icon', 'hip' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,

            ]
        );



        $this->add_control(
            'hip_close_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'hip' ),
                'type' =>Controls_Manager::COLOR,
                'default'	=> '#000',
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container a.leaflet-popup-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );



        $this->add_control(
            'hip_close_bg_color',
            [
                'label' => esc_html__( 'Icon Box Color', 'hip' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container a.leaflet-popup-close-button' => 'background: {{VALUE}}',
                ],
            ]
        );



        $this->add_responsive_control(
            'hip_hip_close_margin',
            array(
                'label'      => __('Margin', 'hip'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .leaflet-container a.leaflet-popup-close-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );



        $this->end_controls_section();

    }
    protected function style_map_box_register_controls() {

        $this->start_controls_section(
            'map-style-control',
            [
                'label' => esc_html__( 'Map ', 'hip' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__( 'Border', 'hip' ),
                'selector' => '{{WRAPPER}} .hip-map-container ',
            ]
        );

        $this->add_responsive_control(
            'hip_map_radius',
            array(
                'label'      => __('Border Radius', 'hip'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', '%', 'em'),
                'selectors'  => array(
                    '{{WRAPPER}} .hip-map-container ' => 'border-radius: {{SIZE}}{{UNIT}};overflow:hidden',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'label'    => __('Box Shadow', 'hip'),
                'name'     => 'hip_map_shadow',
                'selector' => '{{WRAPPER}} .hip-map-container',
            )
        );

        $this->add_responsive_control(
            'hip_map_box_margin',
            array(
                'label'      => __('Margin', 'hip'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .hip-map-container ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );

        $this->add_responsive_control(
            'hip_map_box_padding',
            array(
                'label'      => __('Padding', 'hip'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .hip-map-container ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );


        $this->end_controls_section();

    }




    /**
     * Render oEmbed widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();
        $get_pins = $settings['abf_map_pins'];




        $html = wp_oembed_get( $settings['url'] );

        echo '<div class="oembed-elementor-widget">';
        echo ( $html ) ? $html : $settings['url'];
        echo '</div>';

    }

}