<?php

require_once 'contridivide.civix.php';
require_once 'php/utils.php';
require_once 'php/arrays.php';
// phpcs:disable
use CRM_Contridivide_ExtensionUtil as E;
// phpcs:enable
global $params;
$params = $civArrays;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function contridivide_civicrm_config(&$config): void {
  _contridivide_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function contridivide_civicrm_install(): void {
  _contridivide_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function contridivide_civicrm_enable(){
  global $params;
  foreach ($params as $entity){
	  $check = CheckIfExists($entity['type'],  $entity['name']);
	  if ($check == false)  {
		  CreateEntity($entity['type'], $entity['params']);
	  } else {
		  continue;
	  }
  }
}

function contridivide_civicrm_post(string $op, string $objectName, int $objectId, &$objectRef){
	if ($objectName == "Contribution" && $op == "create" ){
		
		//How the recieptID will be formatted (Example: TD_1)
		
		$idHead = "error"; //idhead will either hold "TD_" or "NT_"
		$idNum = 0; //idNum will hold the num end of the reciept ID "1"
		
		//Step 1: Get the financial type id that was inserted into the contribution
		$getContributionFinType = civicrm_api4('Contribution', 'get', [
		  'where' => [
			['id', '=', $objectId],
		  ],
		  'checkPermissions' => FALSE,
		]);
		
		//Step 2: Use financial type id to see the data 
		$isFinDeductable = civicrm_api4('FinancialType', 'get', [
		  'where' => [
			['id', '=', $getContributionFinType[0]['financial_type_id']],
		  ],
		  'checkPermissions' => FALSE,
		]);
		
		//Step 3: Use financial data to see if the financial type is deductable or not
		$idHead = $isFinDeductable[0]['is_deductible'] ? "TD_" : "NT_";
		
		//Step 4: Get all contributions that have the heading of "TD_" or "NT_"
		$contributions = civicrm_api4('Contribution', 'get', [
		  'select' => [
			'contridiv_group.contridiv_recieptID',
		  ],
		  'where' => [
			['contridiv_group.contridiv_recieptID', 'CONTAINS', $idHead],
		  ],
		  'checkPermissions' => FALSE,
		]);
		
		//Step 5: Check if any contributions exist from the get search
		if ($contributions[0] > 0){
			//if there are contributions, go through all of them
			foreach($contributions as $con){
				//get the number part of the contribution name
				$id = (int)substr($con['contridiv_group.contridiv_recieptID'], 3);
				//compare if the current id is more than the highest id num, if it is, saves the current id to the highest num
				if ($id > $idNum){
					$idNum = $id;
				}
			}
			$idNum += 1;
			CreateRecieptID($objectId, $idHead, $idNum);
		} else {
			//if there is not, that means no contributions were made, thus we set the idNum to 0
			CreateRecieptID($objectId, $idHead, 0);
		}
	}
}