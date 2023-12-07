<?php 

$civArrays = array(
	//Reciept Details Custom Group'
	array(
		'type' => 'CustomGroup',
		'name' => 'contridiv_group',
		'params' => array(
			'name' => 'contridiv_group',
			'title' => 'Reciept Details',
			'extends' => 'Contribution',
			'style' => 'Inline',
			'is_active' => TRUE,
		),
	),
	//Reciept ID Custom Field
	array(
		'type' => 'CustomField',
		'name' => 'contridiv_recieptID',
		'params' => array(
			'custom_group_id.name' => 'contridiv_group',
			'name' => 'contridiv_recieptID',
			'label' => 'Reciept ID',
			'data_type' => 'String',
			'html_type' => 'Text',
			'is_searchable' => TRUE,
			'is_view' => TRUE,
		),
	),
);