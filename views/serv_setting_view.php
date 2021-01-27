<?php declare(strict_types = 1);
 
/**
 * @var CView $this
 */
 
$this->includeJsFile('serv_setting.view.js.php');
 
(new CWidget())
	->setTitle(_('Server settins'))
	->addItem(new CDiv($data['ipv4_type']))
	->addItem(new CPartial('module.serv_setting.reusable', [
	    'gate_ip' => $data['gate_ip']
	])
	->show();