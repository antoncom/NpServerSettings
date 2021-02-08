
<script type="text/javascript">


	function enableStatic(el) {
		var isEnabled = (el.value == "manual") || false;
		if(isEnabled) {
			$("#ipv4").prop('disabled', false);
			$("#ipv4").val('<?php echo (isset($_POST['ipv4'])) ? $_POST['ipv4'] : ""; ?>');
		} else {
			$("#ipv4").prop('disabled', true);
			$("#ipv4").val("");
		}
	}

	$(document).ready(function() {
    	var isStatic = ($("#dhcp_mode").val() == "manual");
    	$("#ipv4").prop('disabled', !isStatic);	
    	$("#ipv4").keyup(function() {
    		validateIpV4($("#ipv4").val());
    	});

	});

	function validateIpV4(inp_string) {
		if($("#ipv4").prop('disabled') == false) {
			const parser = new nearley.Parser(nearley.Grammar.fromCompiled(grammar));

			try {
				parser.feed(inp_string);
				// console.log(parser.results);
				if(parser.results.length > 0) {
					$("#ipv4 + .error").css("display", "none");	
				} else {
					$("#ipv4 + .error").css("display", "block");
				}
				
			}
			catch (e) {
				$("#ipv4 + .error").css("display", "block");
			}
		} else {
			$("#ipv4 + .error").css("display", "none");	
		}

		
	}
</script>