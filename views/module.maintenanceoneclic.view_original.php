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

require_once './include/page_header.php';
?>


<header class="header-title"><nav class="sidebar-nav-toggle" role="navigation" aria-label="Sidebar control"><button type="button" id="sidebar-button-toggle" class="button-toggle" title="Show sidebar">Show sidebar</button></nav><div><h1 id="page-title-general">Maintenance one clic</h1></div></header>

<?php
if (!empty($_POST["host"]) && !empty($_POST["time"]) && empty($_POST['Filter'])){
	
	$period=$_POST["time"];
	$description=$_POST["description"];
	$day=0;
	
	$names=$_POST["host"];
	$hosts = API::Host()->get(array(
			'filter' => array('host' => $names,'name' => $names),
			'output' => array('hostid'),
			'searchByAny' => 1,
		));

	$now=time();
	$tomorrow=time() + $period;
	$hostids=array_column($hosts, 'hostid');
	$user=CWebUser::$data['alias'];
	if ($_POST["time"] == 86400 ){
		$strDuration="1d";
		$day=1;
	}
	else
	{
		$strDuration=gmdate("G:i",$_POST["time"])."h";
	}
	if (is_array($names)) $names="Multiple hosts";
	$maintenance=array(
			'name' => "OneClic - $names by $user for $strDuration (".date('Y-m-d H:i:s',$now).")",
			'active_since' => "$now",
			'active_till' => "$tomorrow",
			'maintenance_type' => 0,
			'hostids' => $hostids,
			'groupids' => array(),
			'description' => "$description",
			'timeperiods' => array(array(
					'timeperiod_type' =>"0",
					'period' =>"$period",
					'every' => "1",
					'day' => "$day",
					'dayofweek' => "0"
					))

			);
	$result = API::Maintenance()->create($maintenance);
	if (!empty($result['maintenanceids'][0])){
		?><output class="msg-good" role="contentinfo" aria-label="Success message"><span>Maintenance succefully added : <a href='maintenance.php?form=update&maintenanceid=<?php echo $result['maintenanceids'][0] ?>'>Maintenance <?php echo $result['maintenanceids'][0] ?></a></span><button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-good').remove();" title="Close"></button></output>
<?php
	}else
	{
		?><output class="msg-bad" role="contentinfo" aria-label="Error message"><span>Error on maintenance creation</span><button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-bad').remove();" title="Close"></button></output><?php
	}
}
if (isset($_POST['host'])){
	if (empty($_POST['host'])){
		?><output class="msg-bad" role="contentinfo" aria-label="Error message"><span>Error on maintenance creation - No host selected</span><button type="button" class="overlay-close-btn" onclick="jQuery(this).closest('.msg-bad').remove();" title="Close"></button></output><?php
	}
}
?>
	

<br/>
<form method="post">
<div id="tabs" class="table-forms-container ui-tabs ui-widget ui-widget-content ui-corner-all" style="visibility: visible;">
<div id="maintenanceTab" aria-labelledby="tab_maintenanceTab" class="ui-tabs-panel ui-widget-content ui-corner-bottom" role="tabpanel" aria-expanded="true" aria-hidden="false">
<ul class="table-forms" id="maintenanceFormList">
<li><div class="table-forms-td-left">
<label class="form-label-asterisk" for="host">Host </label>
</div>
<div class="table-forms-td-right">
<?php
if (!empty($_GET["hostids"])) {

		$hosts = API::Host()->get(array(
                                'output' => array('host'),
				'filter' => array('hostid' => $_GET["hostids"][0]),
                                ));
}
else
{
	//filter if exist
	if (!empty($_POST["Filter"])){

		$hosts = API::Host()->get(array(
                                'output' => array('host','name'),
				'search' => array('name','host' => $_POST["host"]),
                                ));

	}
	else 
	{
		$hosts = API::Host()->get(array(
				'output' => array('host','name'),
				));
	}
}
	$arr_hosts=array();
	if (empty($_POST["Filter"])){
		foreach($hosts as $host){
			array_push($arr_hosts,$host['host']);
			if (!empty($host['name'])) array_push($arr_hosts,$host['name']);
		}
		$arr_hosts=array_unique($arr_hosts);
	}
	else 
	{	
		$arr_hosts=array_column($hosts,'name');
	}
	sort($arr_hosts);
if (count($arr_hosts) == 0 ) {
	?>Search with pattern "<?php echo $_POST["host"]; ?>" has no result. Please check your filter.
	 <input type="hidden" id="host" name="host" value=""> 
	<?php

}
elseif (count($arr_hosts) == 1 ) 
{
	?>
	<select type="text" name="host">
	<option value="<?php echo $arr_hosts[0]; ?>" selected/><?php echo $arr_hosts[0]; ?></option></select><?php
}
else {
	if (empty($_POST["Filter"])) {
		?>
		<input list="host" type="text" name="host" size="50" autocomplete="off">
		<datalist id="host"><?php
	}
	else
	{
		?><select type="text" name="host[]" multiple><?php

	}
	foreach($arr_hosts as $name){
		?><option value="<?php echo $name; ?>"><?php echo $name; ?></option><?php
	}
	
	 if (empty($_POST["Filter"])) {

		?></datalist><?php
	 }
}

$atEndOfWorkingDay=strtotime('today 18:00:00');
$nowTime = new DateTime('now');
$EndOfWorkingDayMin = intval (($atEndOfWorkingDay - $nowTime->getTimestamp()));

?>
</select>
<?php
	if (empty($_POST["Filter"])){ ?>
<button type="submit" name="Filter" value="Filter">Filter</button>
	<?php
	}?>	
<li>
	<div class="table-forms-td-left">
		<label class="form-label-asterisk" for="time">Time </label>
	</div>
	<div class="table-forms-td-right">
		<select id="time" name="time">
			<option value="1800">30m</option>
			<option value="3600">1h</option>
			<option value="7200">2h</option>
			<option value="14400">4h</option>
			<option value="86400">24h</option>
			<option value="<?php echo $EndOfWorkingDayMin ?>">Until 18h00</option>
		</select>
	</div>
</li>
<li>
	<div class="table-forms-td-left">
		<label for="description">Description </label>
	</div>
	<div class="table-forms-td-right">
		<textarea id="description" name="description" rows="7" style="width: 480px;" value="<?php echo CWebUser::$data['alias']; ?>"></textarea>
	</div>
</li>
</ul>
<ul class="table-forms">
	<li>
		<div class="table-forms-td-left"></div>
		<div class="table-forms-td-right tfoot-buttons">
			<button type="submit" value="Add"/>Add</button>
		</div>
	</li>
	</ul>
</div>
</div>
</form>
<?php

require_once './include/page_footer.php';
