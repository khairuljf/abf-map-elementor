<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
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
        wp_register_script( 'abf-leaflet-provider', plugins_url( '/assets/js/leaflet-provider.js', __FILE__ ) );
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

        wp_register_style('abf-map-widget-css', plugins_url('/assets/css/leaflet.css', __FILE__), );
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
        return 'abf-map-id';
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
        return esc_html__( 'Abf Map ', 'abf_addon' );
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
        // Style for content
        $this->style_content_register_controls();
        // Style for close Icon
        $this->style_close_icon_register_controls();
        // Style for map box
        $this->style_map_box_register_controls();

    }

    //map marker controls
    protected function map_marker_control(){


        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Abf Map Marker', 'abf_addon' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );




        $repeater = new Repeater();

        $repeater->add_control(
            'abf_pin_icon',
            array(
                'label'   => __('Custom Pin Icon', 'abf_addon'),
                'type'    => Controls_Manager::MEDIA,
                'dynamic' => array('active' => true),
                'default' =>[
                    'url' => plugin_dir_url(dirname(__FILE__)).'/widgets/assets/image/pin-icon.svg',
                ]
            )
        );

        $repeater->add_control(
            'abf_map_latitude',
            array(
                'name'        => 'abf_map_latitude',
                'label'       => __('Latitude', 'abf_addon'),
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
                'label'       => __('Longitude', 'abf_addon'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => array('active' => true),
                'description' => 'Click <a href="https://www.latlong.net/" target="_blank">here</a> to get your location coordinates',
                'label_block' => true,
                'default' => '90.412521'
            )
        );

        $repeater->add_control(
            'abf_pin_title',
            array(
                'label'       => __('Pin Popup Title', 'abf_addon'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => array('active' => true),
                'default'     => 'Dhaka, Bangladesh',
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'abf_pin_desc',
            array(
                'label'       => __('Pin Popup Description', 'abf_addon'),
                'type'        => Controls_Manager::TEXTAREA,
                'dynamic'     => array('active' => true),
                'default'     => 'Location Description',
                'label_block' => true,
            )
        );

        $repeater->add_control(
            'abf_map_link',
            [
                'label' => esc_html__( 'Single Map Link (if available)', 'abf_addon' ),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'abf_addon' ),
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => true,
                ],
                'label_block' => true,
            ]
        );


        $repeater->add_control(
            'abf_desc_color',
            [
                'label' => esc_html__( 'Text Color', 'abf_addon' ),
                'type' =>Controls_Manager::COLOR,
                'default'	=> '#000',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}  a, {{WRAPPER}} {{CURRENT_ITEM}}  p' => 'color: {{VALUE}}',
                ],
                'separator' => 'before',
            ]

        );

        $repeater->add_control(
            'abf_desc_hover_color',
            [
                'label' => esc_html__( 'Text hover Color', 'abf_addon' ),
                'type' => Controls_Manager::COLOR,
                'default'	=> '#fff',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}  .leaflet-popup-content-wrapper:hover .leaflet-popup-content a,{{WRAPPER}} {{CURRENT_ITEM}}  .leaflet-popup-content-wrapper:hover .leaflet-popup-content p' => 'color: {{VALUE}}',
                ],
            ]
        );

        $repeater->add_control(
            'abf_box_bgclr',
            [
                'label' => esc_html__( 'Info Box Background', 'abf_addon' ),
                'type' => Controls_Manager::COLOR,
                'default'	=> '#1EB2F1',
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-content-wrapper' => 'background: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .leaflet-popup-tip' => 'border-top-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->add_control(
            'abf_box_bgclr_hover',
            [
                'label' => esc_html__( 'Info Box hover Background', 'abf_addon' ),
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
                'label'       => __('Map Pins List', 'abf_addon'),
                'type'        => Controls_Manager::REPEATER,
                'default'     => array(
                    'abf_map_latitude'  => '23.810331',
                    'abf_map_longitude' => '90.412521',
                    'abf_pin_title'     => __('Abf  Maps', 'abf_addon'),
                ),
                'fields'      => $repeater->get_controls(),
                'title_field' => '{{{ abf_pin_title }}}',
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
            'abf-map-settings-section',
            [
                'label'=>__('Map Settings', 'abf_addon'),
                'tab'=> \Elementor\Controls_Manager::TAB_CONTENT
            ]
        );

        $this->add_control(
            'abf_map_fit_bounds',
            [
                'label' => __('Auto Fit Map Bounds', 'abf_addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'abf_addon'),
                'label_off' => __('Off', 'abf_addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'abf_map_zoom_control',
            [
                'label' => __('Zoom Control Option', 'abf_addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'abf_addon'),
                'label_off' => __('Hide', 'abf_addon'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );




        $this->add_control(
            'abf_map_dragging_option',
            [
                'label' => __('Map Dragging', 'abf_addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'abf_addon'),
                'label_off' => __('Off', 'abf_addon'),
                'default' => 'yes',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'abf_maps_zoom_desktop',
            array(
                'label'   => __('Zoom Level', 'abf_addon'),
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
            'abf_maps_map_height',
            array(
                'label'     => __('Height', 'abf_addon'),
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
                    '{{WRAPPER}} .abf-map-container.leaflet-container' => 'height: {{SIZE}}px;',
                ),
            )
        );
        $this->add_control(
            'abf_map_popup_show_always',
            [
                'label' => __('Pin Popup open when load?', 'abf_addon'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Load', 'abf_addon'),
                'label_off' => __('Hide', 'abf_addon'),
                'default' => 'no',
                'separator' => 'before',
            ]
        );
        //Map style
        $this->add_control(
            'abf_map_style',
            [
                'label' => __('Map Style', 'abf_addon'),
                'type' => Controls_Manager::SELECT,
                'default' => 'CartoDB.Positron',
                'options' => [
                    'OpenStreetMap.Mapnik' => esc_html__('Open Street Map', 'abf_addon'),
                    'Stamen.TonerLite' => esc_html__('Toner Lite', 'abf_addon'),
                    'Esri.WorldStreetMap' => esc_html__('World Street Map', 'abf_addon'),
                    'CartoDB.Positron' => esc_html__('Positron ', 'abf_addon'),
                    'Stadia.AlidadeSmooth' => esc_html__('Stadia Alidade Smooth ', 'abf_addon'),
                    'Esri.WorldGrayCanvas' => esc_html__('World Gray Canvas ', 'abf_addon'),
                    'CartoDB.Voyager' => esc_html__('CartoDB Voyager ', 'abf_addon'),

                ],
            ]
        );

        $this->end_controls_section();
    }
    //Style control section

    protected function style_content_register_controls() {

        $this->start_controls_section(
            'abf_info_popup_style',
            [
                'label' => esc_html__( 'Popup Info', 'abf_addon' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__( 'Title Typography', 'plugin-name' ),
                'selector' => '{{WRAPPER}} .leaflet-popup-content a, {{WRAPPER}} .leaflet-popup-content p:first-child',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_details_typography',
                'label' => esc_html__( 'Details Typography', 'plugin-name' ),
                'selector' => '{{WRAPPER}} .leaflet-popup-content p:nth-child(2)',
            ]
        );

        $this->add_responsive_control(
            'abf_map_info_box_radius',
            array(
                'label'      => __('Border Radius', 'abf_addon'),
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
            'abf_info_box_margin',
            array(
                'label'      => __('Margin', 'abf_addon'),
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
            'abf_info_box_padding',
            array(
                'label'      => __('Padding', 'abf_addon'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => [
                    'top'=>15, 'right'=>30, 'bottom'=>15, 'left'=>30, 'unit'=>'px'
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
            'abf_close_icon',
            [
                'label' => esc_html__( 'Close Icon', 'abf_addon' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,

            ]
        );



        $this->add_control(
            'abf_close_icon_color',
            [
                'label' => esc_html__( 'Icon Color', 'abf_addon' ),
                'type' =>Controls_Manager::COLOR,
                'default'	=> '#000',
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container a.leaflet-popup-close-button' => 'color: {{VALUE}}',
                ],
            ]
        );



        $this->add_control(
            'abf_close_bg_color',
            [
                'label' => esc_html__( 'Icon Box Color', 'abf_addon' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .leaflet-container a.leaflet-popup-close-button' => 'background: {{VALUE}}',
                ],
            ]
        );



        $this->add_responsive_control(
            'abf_close_margin',
            array(
                'label'      => __('Margin', 'abf_addon'),
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
            'abf-map-style-control',
            [
                'label' => esc_html__( 'Map ', 'abf_addon' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'label' => esc_html__( 'Border', 'abf_addon' ),
                'selector' => '{{WRAPPER}} .abf-map-container ',
            ]
        );

        $this->add_responsive_control(
            'abf_map_radius',
            array(
                'label'      => __('Border Radius', 'abf_addon'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array('px', '%', 'em'),
                'selectors'  => array(
                    '{{WRAPPER}} .abf-map-container ' => 'border-radius: {{SIZE}}{{UNIT}};overflow:hidden',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'label'    => __('Box Shadow', 'abf_addon'),
                'name'     => 'abf_map_shadow',
                'selector' => '{{WRAPPER}} .abf-map-container',
            )
        );

        $this->add_responsive_control(
            'abf_map_box_margin',
            array(
                'label'      => __('Margin', 'abf_addon'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .abf-map-container ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ),
            )
        );

        $this->add_responsive_control(
            'abf_map_box_padding',
            array(
                'label'      => __('Padding', 'abf_addon'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors'  => array(
                    '{{WRAPPER}} .abf-map-container ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
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

        $map_auto_fit_bounds = !empty($settings['abf_map_fit_bounds'] ) ? $settings['abf_map_fit_bounds'] : 'no';
        $map_style = !empty($settings['abf_map_style']) ? $settings['abf_map_style'] : 'OpenStreetMap.Mapnik';



        $map_settings = array(
            'fitBounds'         => $map_auto_fit_bounds,
            'map_zoom_control'	=>$settings['abf_map_zoom_control'],
            'zoom_desktop'              => $settings['abf_maps_zoom_desktop']['size'],
            'map_dragging_option'	=>$settings['abf_map_dragging_option'],
            'mapstyle'           => $map_style,
            'automaticOpen'     =>  $settings['abf_map_popup_show_always'],

        );


        $this->add_render_attribute(
            'wrapper',
            [
                'class' => 'abf-map',
                'data-settings' => wp_json_encode($map_settings),
            ]
        );

        ?>

           <div class="abf-map-container">
                <div id='map' <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>> </div>


               <?php

               foreach ($get_pins as $index => $pin){

                   $pin_icon = $pin['abf_pin_icon']['url'];
                   $latitute = $pin['abf_map_latitude'];
                   $longitude = $pin['abf_map_longitude'];
                   $pin_popup_url = $pin['abf_map_link']['url'];
                   $pin_url_target = $pin['abf_map_link']['is_external'];
                   $pin_popup_title = $pin['abf_pin_title'];
                   $pin_popup_desc = $pin['abf_pin_desc'];
                   $key = 'abf_map_marker_' . $index;
                   $elemt_id = $pin['_id'];

                   $this->add_render_attribute(
                       $key,
                       array(
                           'class'          => 'abf-pin-icon',
                           'data-lat'       => $latitute,
                           'data-lng'       => $longitude,
                           'data-icon'      =>$pin_icon,
                           'data-url'      =>$pin_popup_url,
                           'data-target'     =>$pin_url_target,
                           'data-title'      =>$pin_popup_title,
                           'data-desc'      =>$pin_popup_desc,
                           'item_id'      =>$elemt_id,


                       )
                   );
                   ?>

                   <div <?php echo $this->get_render_attribute_string( $key ); ?>>  </div>

           </div>


        <?php

    }

    }

    protected function content_template() {
        ?>
        <#
        view.addRenderAttribute(
        'wrapper',
        {
        'class': [ 'abf-map']
        }
        );
        #>
        <div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>  </div>


        <?php
    }

}