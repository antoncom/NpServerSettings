
<script type="text/javascript">
	function enableStatic(el) {
		var isEnabled = (el.value == "static") || false;
		$("#ipv4").prop('disabled', !isEnabled);
	}

	$(document).ready(function() {
    	var isStatic = ($("#dhcp_mode").val() == "static");
    	$("#ipv4").prop('disabled', !isStatic);	
	});
</script>