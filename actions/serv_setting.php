<?php declare(strict_types = 1);
 
namespace Modules\NetPing\Actions;
 
use CControllerResponseData;
use CControllerResponseFatal;
use CController as CAction;
 
/**
 * Example module action.
 */
class SomethingView extends CAction {
 
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
			'ipv4_type' => 'required|string', /* DHCP or Static*/
			'ipv4'  => 'string',
			'gate_ip' => 'required|string',
			'dns_basic' => 'required|string',
			'dns_extra' => 'string'
		];
 
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
		$data = [
			'ipv4_type' => $this->getInput('ipv4_type'),
			'gate_ip' => $this->getInput('gate_ip'),
			'dns_basic' => $this->getInput('dns_basic'),
			'dns_extra' => $this->getInput('dns_extra'),
		];

		if($this->getInput('ipv4_type') = "static") {
			'ipv4' => $this->getInput('ipv4'),
		}
 
		$response = new CControllerResponseData($data);
 
		$this->setResponse($response);
	}
}