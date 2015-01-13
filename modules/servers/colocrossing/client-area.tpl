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
	});
</script>
{/literal}

<h3 style="text-align: left; margin-bottom: 10px;">Ports</h3>
<table class="table table-bordered table-striped" style="width: 100%;">
  <tr>
		<td style="width: 25%;">Power Status</td>
		<td style="width: 75%;">
			<strong style="color: green;">On</strong>
		</td>
	</tr>
	<tr>
		<td style="width: 25%;">Power Control</td>
		<td style="width: 75%;">
			<a type="button" class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=start">Start</a>
        	<a type="button" class="btn btn-danger" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=shutdown">Shutdown</a>
        	<a type="button" class="btn btn-primary" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=reboot">Reboot</a>
        </td>
	</tr>
	<tr>
		<td style="width: 25%;">Network Status</td>
		<td style="width: 75%;">
			<strong style="color: green;">On</strong>
		</td>
	</tr>
	<tr>
		<td style="width: 25%;">Network Control</td>
		<td style="width: 75%;">
			<a type="button" class="btn btn-success" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=start">Turn On</a>
    		<a type="button" class="btn btn-danger" href="clientarea.php?action=productdetails&id={$serviceid}&modop=custom&a=shutdown">Turn Off</a>
    	</td>
	</tr>
</table>

<h3 style="text-align: left; margin-bottom: 10px;">Graphs</h3>
<img src="/index.php?m=colocrossing&controller=devices&action=bandwidth-graph&id=6649&switch_id=6610&port_id=15&duration=current" style="width: 100%;">

<style type="text/css">
	.moduleoutput { border: none; padding: : 0px; }
</style>
