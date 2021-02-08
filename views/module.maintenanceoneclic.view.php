<?php

require_once './include/config.inc.php';
require_once './include/hosts.inc.php';
require_once './include/maintenances.inc.php';
require_once './include/forms.inc.php';
require_once './include/users.inc.php';

$page['title'] = _('Maintenance one clic');
$page['file'] = 'maintenance_oneclic.php';
$page['scripts'] = ['class.calendar.js'];
$page['type'] = detect_page_type();

function console($data) {
	# print_r($data);
	if(gettype($data) == 'object' || gettype($data) == 'array') {
		$o = date('Y-m-d H:i:s') . ' ' . print_r($data, true);
	} else {
		$o = date('Y-m-d H:i:s') . ' ' . $data;
	}
	file_put_contents('/usr/share/zabbix/modules/log.txt', $o . PHP_EOL, FILE_APPEND);
}

require_once './include/page_header.php';
$this->includeJsFile('nearley.js.php');
$this->includeJsFile('ipv4.js.php');
$this->includeJsFile('helper.js.php');
$this->includeJsFile('styles.js.php');


$connectionName = "enp0s3"; // you must change this value according to your server setting
$nmcli_out = [];
$net = [];
$dhcp_mode = "auto";

$ipv4 = "";
$gateway = "";
$dns1 = "";
$dns2 = "";
$messages = [];
$resCodeOnGet = 0;
$resCodeOnSet = 0;
//print_r($_POST);

if (sizeof($_POST) == 0) {
	$comm = '/usr/share/zabbix/modules/np_server_settings.sh --device="enp0s3" --command=get';
	exec($comm, $nmcli_out, $resCodeOnGet);

	foreach($nmcli_out as $n) {
		if(strpos($n, "ipv4.") !== false) {
			list($p, $v) = preg_split("/\s+/", $n);
			$net[substr($p, 0, -1)] = $v;
		} elseif (strpos($n, "Error:") !== false) {
			$errorMsgs[] = $n;
		} else {
			$messages[] = $n;
		}
	}

	if(array_key_exists('ipv4.method', $net)) {
		$dhcp_mode = $net['ipv4.method'];
	}
	if(array_key_exists('ipv4.addresses', $net)) {
		$ipv4 = $net['ipv4.addresses'];
	}
	if(array_key_exists('ipv4.gateway', $net)) {
		$gateway = $net['ipv4.gateway'];
	}

	if(array_key_exists('ipv4.dns', $net)) {
		$dns_arr = preg_split("/\,/", $net['ipv4.dns']);
		$dns1 = (isset($dns_arr[0])) ? $dns_arr[0] : "";
		$dns2 = (isset($dns_arr[1])) ? $dns_arr[1] : "";
	}
} elseif (sizeof($_POST) > 0) {
	$comm = '/usr/share/zabbix/modules/np_server_settings.sh --device="enp0s3" --command=set';

	if(isset($_POST['dhcp_mode']) && $dhcp_mode != $_POST['dhcp_mode']) {
		$comm = $comm . ' --dhcp=' . $_POST['dhcp_mode'];
	}
	if(isset($_POST['ipv4']) && $ipv4 != $_POST['ipv4']) {
		$comm = $comm .' --ipv4=' . $_POST['ipv4'];
	}
	if(isset($_POST['gateway']) && $gateway != $_POST['gateway']) {
		$comm = $comm . ' --gateway=' . $_POST['gateway'];
	}
	if(isset($_POST['dns1']) && $dns1 != $_POST['dns1']) {
		$comm = $comm . ' --dns1=' . $_POST['dns1'];
	}
	if(isset($_POST['dns2']) && $_POST['dns2'] != "" && $dns2 != $_POST['dns2']) {
		$comm = $comm . ' --dns2=' . $_POST['dns2'];		
	}
	
	exec($comm, $nmcli_out, $resCodeOnSet);

	
	$comm = '/usr/share/zabbix/modules/np_server_settings.sh --device="enp0s3" --command=get';
	exec($comm, $nmcli_out, $resCodeOnGet);

	foreach($nmcli_out as $n) {
		if(strpos($n, "ipv4.") !== false) {
			list($p, $v) = preg_split("/\s+/", $n);
			$net[substr($p, 0, -1)] = $v;
		} else {
			$messages[] = $n;
		}
	}

	if(array_key_exists('ipv4.method', $net)) {
		$dhcp_mode = $net['ipv4.method'];
	}
	if(array_key_exists('ipv4.addresses', $net)) {
		$ipv4 = $net['ipv4.addresses'];
	}
	if(array_key_exists('ipv4.gateway', $net)) {
		$gateway = $net['ipv4.gateway'];
	}

	if(array_key_exists('ipv4.dns', $net)) {
		$dns_arr = preg_split("/\,/", $net['ipv4.dns']);
		$dns1 = (isset($dns_arr[0])) ? $dns_arr[0] : "";
		$dns2 = (isset($dns_arr[1])) ? $dns_arr[1] : "";
	}

}

?>



<header class="header-title"><nav class="sidebar-nav-toggle" role="navigation" aria-label="Sidebar control"><button type="button" id="sidebar-button-toggle" class="button-toggle" title="Show sidebar">Show sidebar</button></nav><div><h1 id="page-title-general">NetPing Server Settings</h1></div></header>


<?php if ($resCodeOnGet > 0) { ?>

		<output class="msg-bad" role="contentinfo" aria-label="Error message">
			<span>
				<h2>Error when reading the settings!</h2>
				<?php
					foreach($messages as $key=>$val) {
						echo "<p>#" . intval($key+1) . ": " . $val . "</p>";
					}
				?>
			</span>
			<button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-bad').remove();" title="Close"></button>
		</output>

<?php } elseif ($resCodeOnSet > 0) { ?>

		<output class="msg-bad" role="contentinfo" aria-label="Error message">
			<span>
				<h2>Error when apply the settings!</h2>
				<?php
					foreach($messages as $key=>$val) {
						echo "<p>#" . intval($key+1) . ": " . $val . "</p>";
					}
				?>
			</span>
			<button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-bad').remove();" title="Close"></button>
		</output>

<?php } elseif (sizeof($_POST) > 0) { ?>

		<output class="msg-good" role="contentinfo" aria-label="Success message">
			<span>
				<h2>Server settings were applied successfully!</h2>
				<dl class="attrs-line">
				<?php
					foreach($nmcli_out as $raw) {
						if(strpos($raw, "ipv4.") !== false) {
							list($key, $val) = preg_split("/:\s+/", $raw);
							echo "<dt>" . $key . " : </dt><dd>" . $val . "</dd>";
						}
					}
				?>
				</dl>
			</span>
			<button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-good	').remove();" title="Close"></button>
		</output>';

		<?php foreach($messages as $msg) { ?>
			<output class="msg-good" role="contentinfo" aria-label="Success message">
				<span><?php echo $msg; ?></span>
				<button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-good	').remove();" title="Close"></button>
			</output>';
		<?php } ?>

<?php } ?>



<br/>
<form method="post">
	<div id="tabs" class="table-forms-container ui-tabs ui-widget ui-widget-content ui-corner-all" style="visibility: visible;">
		<div id="maintenanceTab" aria-labelledby="tab_maintenanceTab" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false">
			<ul class="table-forms" id="maintenanceFormList">
				<li>
					<div class="table-forms-td-left">
						<label class="form-label-ipv4-type" for="time">IP v4 адрес </label>
					</div>
					<div class="table-forms-td-right">
						<select id="dhcp_mode" name="dhcp_mode" onchange="enableStatic(this);">
							<option value="auto" <?php if($dhcp_mode == 'auto') echo ' selected'; ?>>DHCP</option>
							<option value="manual" <?php if($dhcp_mode == 'manual') echo ' selected'; ?>>Статический</option>
						</select>
					</div>
				</li>
				<li>
					<div class="table-forms-td-left">
						<label for="IP v4 адрес">Статический </label>
					</div>
					<div class="table-forms-td-right">
						<input type="text" id="ipv4" name="ipv4" disabled="true" value="<?php echo $ipv4; ?>" maxlength="255" style="width: 270px;">
						<?php $exampleIpv4 = (isset($ipv4)) ? $ipv4 : "192.168.0.2/24"; ?>
						<div class="error" style="display: none; color: red;">Укажите корректный IP / маску, например: <a href="javascript: $('#ipv4').val('<?php echo $exampleIpv4; ?>'); validateIpV4('<?php echo $exampleIpv4; ?>');"><?php echo $exampleIpv4; ?></a></div>
					</div>
				</li>
				<li>
					<div class="table-forms-td-left">
						<label for="Адрес гейта">Адрес гейта </label>
					</div>
					<div class="table-forms-td-right">
						<input type="text" id="gateway" name="gateway" value="<?php echo $gateway; ?>" maxlength="255" style="width: 270px;">
					</div>
				</li>
				<li>
					<div class="table-forms-td-left">
						<label for="Адрес основного DNS сервера">Адрес основного DNS сервера </label>
					</div>
					<div class="table-forms-td-right">
						<input type="text" id="dns1" name="dns1" value="<?php echo $dns1; ?>" maxlength="255" style="width: 270px;">
					</div>
				</li>
				<li>
					<div class="table-forms-td-left">
						<label for="Адрес дополнительного DNS сервера">Адрес дополнительного DNS сервера </label>
					</div>
					<div class="table-forms-td-right">
						<input type="text" id="dns2" name="dns2" value="<?php echo $dns2; ?>" maxlength="255" style="width: 270px;">
					</div>
				</li>
			</ul>
			<ul class="table-forms">
				<li>
					<div class="table-forms-td-left"></div>
					<div class="table-forms-td-right tfoot-buttons">
						<button type="submit" id="save_and_apply" value="SaveAndApply"/>Save and Apply</button>
					</div>
				</li>
			</ul>
		</div>
	</div>
</form>
<?php

require_once './include/page_footer.php';

?>