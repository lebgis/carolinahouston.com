<?php
/**
 *
 * @property int $views_count
 * @property int $conversions_count
 * @property int $conversion_rate
 * @property Opt_In_Model_Stats $slide_in
 * @property Opt_In_Model_Stats $popup
 * @property Opt_In_Model_Stats $after_content
 *
 * Class Opt_In_Model_Stats
 */
class Opt_In_Model_Stats extends Hustle_Data
{
    /**
     * @var Opt_In_Model $_optin
     */
    private $_optin;

    /**
     * Type of optin we are getting stats for
     *
     * @var string $_optin_type
     */
    public $_optin_type;

    /**
     * Inits class
     *
     * Opt_In_Model_Stats constructor.
     * @param Opt_In_Model $optin
     * @param $optin_type
     */
    function __construct( Opt_In_Model $optin, $optin_type ){
        parent::__construct();
        $this->_optin = $optin;
        $this->_optin_type = $optin_type;

    }

    /**
     * Returns stat key
     *
     * @param $suffix
     * @return string
     */
    private function _get_key( $suffix ){
        return $this->_optin_type . "_" . $suffix;
    }

    /**
     * Fetches views count from db
     *
     * @return int
     */
    function get_views_count(){
        return (int) $this->_wpdb->get_var( $this->_wpdb->prepare( "SELECT COUNT(meta_id) FROM " . $this->get_meta_table() . " WHERE `optin_id`=%d AND `meta_key`=%s ", $this->_optin->id,  $this->_get_key( self::KEY_VIEW ) ) );
    }

    /**
     * Fetches conversions count from db
     *
     * @return int
     */
    function get_conversions_count(){
        return (int) $this->_wpdb->get_var( $this->_wpdb->prepare( "SELECT COUNT(meta_id) FROM " . $this->get_meta_table() . " WHERE `optin_id`=%d AND `meta_key`=%s ", $this->_optin->id,  $this->_get_key( self::KEY_CONVERSION )  ) );
    }

    /**
     * Calculates and Returns conversion rate
     *
     * @return float|int
     */
    function get_conversion_rate(){
        return (int) $this->views_count > 0 ?  round( ( $this->conversions_count / $this->views_count )  * 100, 2 ) : 0;
    }

    /**
     * Fetches conversion data from db
     *
     * @return array
     */
    function get_conversion_data(){
        return (object) $this->_wpdb->get_results( $this->_wpdb->prepare( "SELECT * FROM " . $this->get_meta_table() . " WHERE `optin_id`=%d AND `meta_key`=%s ", $this->_optin->id,  $this->_get_key( self::KEY_CONVERSION )  ) );
    }
}