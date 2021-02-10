
<script type="text/javascript">

	var gateway_valid = true,
		ipv4_valid = true,
		dns1_valid = true,
		dns2_valid = true;

	function enableStatic(el) {
		var isEnabled = (el.value == "manual") || false;
		if(isEnabled) {
			$("#ipv4").prop('disabled', false);
			$("#ipv4").val('<?php echo (isset($_POST['ipv4'])) ? $_POST['ipv4'] : ""; ?>');

			$("#gateway").prop('disabled', false);
			$("#gateway").val('<?php echo (isset($_POST['gateway'])) ? $_POST['gateway'] : ""; ?>');
		} else {
			$("#ipv4").prop('disabled', true);
			$("#ipv4").val("");

			$("#gateway").prop('disabled', true);
			$("#gateway").val("");
		}
		validateIpV4($("#ipv4").val());
		validateIpGateway($("#gateway").val());
	}

	$(document).ready(function() {
    	var isStatic = ($("#dhcp_mode").val() == "manual");

    	$("#ipv4").prop('disabled', !isStatic);	
    	$("#gateway").prop('disabled', !isStatic);	

    	$("#ipv4").keyup(function() {
    		validateIpV4($("#ipv4").val());
    	});
    	$("#gateway").keyup(function() {
    		validateIpGateway($("#gateway").val());
    	});
    	$("#dns1").keyup(function() {
    		validateIpDns1($("#dns1").val());
    	});
    	$("#dns2").keyup(function() {
    		validateIpDns2($("#dns2").val());
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
					ipv4_valid = true;
				} else {
					$("#ipv4 + .error").css("display", "block");
					ipv4_valid = false;
				}
				
			}
			catch (e) {
				$("#ipv4 + .error").css("display", "block");
				ipv4_valid = false;
			}
		} else {
			$("#ipv4 + .error").css("display", "none");	
			ipv4_valid = true;
		}
		validateForm();
	}

	function validateIpGateway(ip) {
		if($("#gateway").prop('disabled') == false) {
			const parser = new nearley.Parser(nearley.Grammar.fromCompiled(grammarIpSingle));

			try {
				parser.feed(ip);
				console.log(parser.results);
				if(parser.results.length > 0) {
					$("#gateway + .error").css("display", "none");
					gateway_valid = true;
				} else {
					$("#gateway + .error").css("display", "block");
					gateway_valid = false;
				}
				
			}
			catch (e) {
				$("#gateway + div.error").css("display", "block");
				gateway_valid = false;
			}
		} else {
			$("#gateway + .error").css("display", "none");	
			gateway_valid = true;
		}
		validateForm();
	}

	function validateIpDns1(ip) {
		if(ip == "") {
			dns1_valid = true;
		} else {
			const parser = new nearley.Parser(nearley.Grammar.fromCompiled(grammarIpSingle));
			try {
				parser.feed(ip);
				if(parser.results.length > 0) {
					$("#dns1 + .error").css("display", "none");
					dns1_valid = true;
				} else {
					$("#dns1 + .error").css("display", "block");
					dns1_valid = false;
				}
				
			}
			catch (e) {
				$("#dns1 + .error").css("display", "block");
				dns1_valid = false;
			}
		}
		validateForm();
	}

	function validateIpDns2(ip) {
		if(ip == "") {
			dns2_valid = true;
		} else {
			const parser = new nearley.Parser(nearley.Grammar.fromCompiled(grammarIpSingle));

			try {
				parser.feed(ip);
				if(parser.results.length > 0) {
					$("#dns2 + .error").css("display", "none");	
					dns2_valid = true;
				} else {
					$("#dns2 + .error").css("display", "block");
					dns2_valid = false;
				}
				
			}
			catch (e) {
				$("#dns2 + .error").css("display", "block");
				dns2_valid = false;
			}
		}
		
		validateForm();
	}

	function validateForm() {
		if(ipv4_valid && gateway_valid && dns1_valid && dns2_valid) {
			$("#save_and_apply").attr("disabled", false);
		} else {
			$("#save_and_apply").attr("disabled", true);
		}
	}
</script>