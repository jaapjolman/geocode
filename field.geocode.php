<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroStreams geocode Field Type
 *
 * Generate longitude and latitude from a specified location
 *
 * @package		PyroStreams
 * @author		David Lewis
 * @copyright	Copyright (c) 2012, David Lewis
 */

class Field_Geocode
{
	public $field_type_slug			= 'geocode';

	public $db_col_type				= 'varchar';

	public $version					= '1.0.0';

	public $author					= array('name' => 'Ryan Thompson', 'url' => 'http://aiwebsystems.com');

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @access	public
	 * @param $data	array
	 * @return	string
	 */
	public function form_output($data)
	{
		$data['value'] = unserialize($data['value']);

		$options = array(
			'name'	=> $data['form_slug'],
			'id'	=> $data['form_slug'],
			'value'	=> isset($data['value']['input']) ? $data['value']['input'] : $data['value'],
		);

		$l_failed = $this->CI->lang->line('streams:geocode:geocode_error');

		$html = '<span id="'.$data['form_slug'].'_msg" class="geocode_map_msg"></span>';
		$html .= '<div id="'.$data['form_slug'].'_map" class="geocode_map"></div>';

		$options_input = array(
			'name'	=> $data['form_slug'].'_geocode',
			'id'	=> $data['form_slug'].'_geocode',
			'value'	=> isset($data['value']['geocode']) ? $data['value']['geocode'] : null,
		);
		return form_input($options).form_input($options_input).$html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Before saving
	 *
	 * @access	public
	 * @param $data	array
	 * @return	string
	 */
	public function pre_save($field_value, $field_params, $stream, $row_id = FALSE, $field_values)
	{
		// $location
		return serialize(array('input' => $this->CI->input->post($field_params->field_slug), 'geocode' => $this->CI->input->post($field_params->field_slug.'_geocode')));
	}

	// --------------------------------------------------------------------------

	/**
	 * Tag output variables
	 *
	 * Outputs 'latitude' & 'longitude' variables
	 *
	 * @access 	public
	 * @param	string
	 * @param	array
	 * @return	array
	 */
	public function pre_output_plugin($input)
	{
		if ( ! $input) return null;

		$pieces = explode(',', $input);

		if (count($pieces) != 2) return null;

		return array('latitude' => trim($pieces[0]), 'longitude' => $pieces[1]);
	}

    /**
     * Event when field shown
     *
     * Load assets
     *
     * @access public
     * @param $field object
     * @return void
     */
	public function event($field)
	{
		$this->CI->type->add_misc('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>');
		$this->CI->type->add_js('geocode', 'geocode.js');
		$this->CI->type->add_css('geocode', 'geocode.css');
		$this->CI->type->add_misc('<script type="text/javascript">$(document).ready(function() { initialize("'.$field->field_slug.'"); });</script>');
	}
}
