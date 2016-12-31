<?php
return $architecture = array(
	'post_type' => array(
		'books',
	),
	'taxonomy' => array(
		'authors',
	),
	'custom' => array(
		// this will add support for Metadata API
		'libraries' => array(
			'has_meta' => true,
		),
		'lending' => array(),
	),
);
