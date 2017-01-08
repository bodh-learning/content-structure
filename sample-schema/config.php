<?php

return $architecture = array(
	'post_type' => array(
		'books',
	),
	'taxonomy' => array(
		'authors' => array( 'books' ), // will add it to books
	),
	'custom' => array(
		'libraries' => array(
			'has_meta' => true, // this will add support for Metadata API
		),
		'lending' => array(),
	),
);
