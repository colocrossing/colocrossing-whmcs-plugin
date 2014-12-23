<div id="colocrossing-module">
	<h4>Device:</h4>
	<a href="{$device_url}">{$device_name}</a>
</div>

{literal}
<script type="text/javascript">
	$(document).ready(function() {
		//Move Module Content To Top of Page and Delete Container
		var module = $('#colocrossing-module'),
		moduleContainer = module.parent(),
		infoContainer = moduleContainer.parent();

		infoContainer.prepend(module);
		moduleContainer.remove();
	});
</script>
{/literal}
