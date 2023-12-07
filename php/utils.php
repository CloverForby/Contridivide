<?php

function CreateEntity(string $EntityType, array $Params){
	$results = civicrm_api4(strval($EntityType), 'create', ['values' => $Params,]);
}

function CheckIfExists(string $EntityType, string $EntityName) : bool{
	$result = civicrm_api4(strval($EntityType), 'get', [
		  'where' => [
			['name', '=', strval($EntityName)],
		  ],
		  'checkPermissions' => FALSE,
	]);
	
	if (isset($result[0]) && $result[0] > 0) {
		return TRUE;
	} else {
		return FALSE;
	};
}

function CreateRecieptID($ContID, $Head, $Num){
	$results = civicrm_api4('Contribution', 'update', [
		  'values' => [
			'contridiv_group.contridiv_recieptID' => strval($Head . $Num),
		  ],
		  'where' => [
			['id', '=', $ContID],
		  ],
		  'checkPermissions' => TRUE,
		]);
}