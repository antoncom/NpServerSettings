<?php

require_once './include/js.inc.php';
require_once './include/page_header_netping.php';
?>

<header class="header-title"><nav class="sidebar-nav-toggle" role="navigation" aria-label="Sidebar control"><button type="button" id="sidebar-button-toggle" class="button-toggle" title="Show sidebar">Show sidebar</button></nav><div><h1 id="page-title-general">Maintenance one clic</h1></div></header>

<style>
  iframe {
    width: 1px;
    min-width: 100%;
  }
</style>

<iframe id="myIframe" src="http://mediapublish.ru"></iframe>

<script type="text/javascript">
	(function($) {
		//$('iframe').iFrameResize([{}])
		//iFrameResize({ log: true }, '#myIframe')
	}
</script>

<?php

require_once './include/page_footer.php';
