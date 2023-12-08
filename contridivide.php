<?php

require_once 'contridivide.civix.php';
require_once 'php/utils.php';
require_once 'php/arrays.php';
// phpcs:disable
use CRM_Contridivide_ExtensionUtil as E;
// phpcs:enable
global $condiv_arrays;
$condiv_arrays = $condivArrays;

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
  global $condiv_arrays;
  foreach ($condiv_arrays as $entity){
	  $check = condiv_CheckIfExists($entity['type'],  $entity['name']);
	  if ($check == false)  {
		  condiv_CreateEntity($entity['type'], $entity['params']);
	  } else {
		  continue;
	  }
  }
}

function contridivide_civicrm_postCommit(string $op, string $objectName, int $objectId, &$objectRef){
	if ($objectName == "Contribution" && $op == "create" ){
		
		//How the receiptID will be formatted (Example: TD_1)
		
		$idHead = "error"; //idhead will either hold "TD_" or "NT_"
		$idNum = 1; //idNum will hold the num end of the receipt ID "1"
		$whereArray = "";
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
		$idHead = $isFinDeductable[0]['is_deductible'] ? "TDR" : "NTDR";
		
		if ($idHead == "TDR"){
			$whereArray = [
				['contridiv_group.contridiv_receiptID', 'CONTAINS', $idHead],
				['contridiv_group.contridiv_receiptID', 'NOT CONTAINS', 'N'],
			];
		} else {
			$whereArray = [['contridiv_group.contridiv_receiptID', 'CONTAINS', $idHead]];
		}
		
		//Step 4: Get all contributions that have the heading of "TD_" or "NT_"
		$contributions = civicrm_api4('Contribution', 'get', [
		  'select' => [
			'contridiv_group.contridiv_receiptID',
		  ],
		  'where' => $whereArray,
		  'checkPermissions' => FALSE,
		]);
		
		//Step 5: Check if any contributions exist from the get search
		if ($contributions[0] > 0){
			//if there are contributions, go through all of them
			foreach($contributions as $con){
				//get the number part of the contribution name
				$id = (int)substr($con['contridiv_group.contridiv_receiptID'], strlen($idHead));
				//compare if the current id is more than the highest id num, if it is, saves the current id to the highest num
				if ($id > $idNum){
					$idNum = $id;
				}
			}
			$idNum += 1;
			condiv_CreateReceiptID($objectId, $idHead, $idNum);
		} else {
			//if there is not, that means no contributions were made, thus we set the idNum to 0
			condiv_CreateReceiptID($objectId, $idHead, 1);
		}
	}
}