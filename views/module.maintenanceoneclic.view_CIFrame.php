<?php

//require_once './include/page_header_netping.php';
require_once './include/page_header.php';
?>

<header class="header-title"><nav class="sidebar-nav-toggle" role="navigation" aria-label="Sidebar control"><button type="button" id="sidebar-button-toggle" class="button-toggle" title="Show sidebar">Show sidebar</button></nav><div><h1 id="page-title-general">Maintenance one clic</h1></div></header>


	<?= (new CIFrame(''))
			->setSrc('http://192.168.0.2:81/index.html')
			->setScrolling('no')
		->toString()
		
	?>


<?php

require_once './include/page_footer.php';
