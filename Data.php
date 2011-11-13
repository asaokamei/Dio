<?php
namespace CenaDta\Dio;

class Data
{
	var $save_id = 'DioData.save.ID';
	// +--------------------------------------------------------------- +
	function __construct( $save_id=NULL )
	{
		if( Util::isValue( $save_id ) ) $this->save_id = $save_id;
	}
	// +--------------------------------------------------------------- +
}





?>