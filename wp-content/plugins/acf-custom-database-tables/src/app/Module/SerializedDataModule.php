<?php


namespace ACFCustomDatabaseTables\Module;


use ACFCustomDatabaseTables\Contract\ModuleInterface;


/**
 * Class SerializedDataModule
 * @package ACFCustomDatabaseTables\Module
 *
 * This module, when enabled, will serialize eligible data for storage in the database, overriding the default
 * encoding.
 */
class SerializedDataModule implements ModuleInterface {


	/** @return string The module name */
	public function name() {
		return 'serialized_data';
	}


	public function init() {
		add_filter( 'acfcdt/filter_value_before_encode', [ $this, 'maybe_serialize_data' ] );
		add_filter( 'acfcdt/filter_value_before_decode', [ $this, 'maybe_unserialize_data' ] );
	}


	public function maybe_serialize_data( $data ) {
		return maybe_serialize( $data );
	}


	public function maybe_unserialize_data( $data ) {
		return maybe_unserialize( $data );
	}


}