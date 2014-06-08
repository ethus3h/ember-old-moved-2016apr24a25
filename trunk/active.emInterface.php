<?php
class emInterface
{
    function __construct()
    {
    }

    function store($data,$csum) {
		$status = 0;
		$csumn = new Csum($data);
		if(!$csum->matches($csumn)) {
			return null;
		}
    	return store($data,$csum);
    }
}
?>