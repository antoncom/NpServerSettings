<?php declare(strict_types = 1);
 
namespace Modules\MaintenanceOneClic\Actions;
 
use CControllerResponseData;
use CControllerResponseFatal;
use CScript;
use API;
use CController as CAction;
 
/**
 * Example module action.
 */
class MaintenanceOneClicView extends CAction {
	/**
	 * Initialize action. Method called by Zabbix core.
	 *
	 * @return void
	 */
	public function init(): void {
		/**
		 * Disable SID (Sessoin ID) validation. Session ID validation should only be used for actions which involde data
		 * modification, such as update or delete actions. In such case Session ID must be presented in the URL, so that
		 * the URL would expire as soon as the session expired.
		 */
		$this->disableSIDvalidation();
	}
 
	/**
	 * Check and sanitize user input parameters. Method called by Zabbix core. Execution stops if false is returned.
	 *
	 * @return bool true on success, false on error.
	 */
	protected function checkInput(): bool {
		$fields = [
			'gate_address' 		=> 'string|not_empty',
			/*
			'ipv4_static'  		=> 'string',
			'gate_address' 		=> 'string|not_empty',
	        'dns_address_basic' => 'string|not_empty',
	        'dns_address_extra' => 'string'
	        */
		];
		/*
			'ipv4_type'			=> 'required|string|not_empty',
	        'ipv4_static'  		=> 'required|string|not_empty',
	        'gate_address' 		=> 'required|string|not_empty',
	        'dns_address_basic' => 'required|string|not_empty',
	        'dns_address_extra' => 'string'
        ];
        */

        // $result = CScript();

        /*

        if(sizeof($_POST) > 0) {
			$log = date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);;
        	file_put_contents(__DIR__ . '/log.txt', $result . PHP_EOL, FILE_APPEND);
        } else {
        	file_put_contents(__DIR__ . '/log.txt', "..no POST data.." . PHP_EOL, FILE_APPEND);
        }
        */

        //$log = date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);
        //file_put_contents(__DIR__ . '/log.txt', $result . PHP_EOL, FILE_APPEND);

        
        

        // Only validated data will further be available using $this->hasInput() and $this->getInput().
        $ret = $this->validateInput($fields);

        if (!$ret) {
            $this->setResponse(new CControllerResponseFatal());
        }
		return $ret;
	}
 
	/**
	 * Check if the user has permission to execute this action. Method called by Zabbix core.
	 * Execution stops if false is returned.
	 *
	 * @return bool
	 */
	protected function checkPermissions(): bool {
		$permit_user_types = [USER_TYPE_ZABBIX_ADMIN, USER_TYPE_SUPER_ADMIN];
 
		return in_array($this->getUserType(), $permit_user_types);
	}
 
	/**
	 * Prepare the response object for the view. Method called by Zabbix core.
	 *
	 * @return void
	 */
	protected function doAction(): void {

		$data = [];
 
		$response = new CControllerResponseData($data);
 
		$this->setResponse($response);
	} 
}
