<?php

if(!function_exists('timezones')) {
	function timezones() {
		$tzs = \DateTimeZone::listIdentifiers();
		$items = array();
		
		foreach ($tzs as $key => $value) {
			$items[$value] = $value;
		}
		
		return $items;
	}
}