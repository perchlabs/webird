<div class='title'>
	<h2>Server Info <a class='wbdebug-panel-close'>&times;</a></h2>
</div>
<div class='panel-content'>
	<h3 class='collapser'>Response Headers</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			{% for key, value in headersList %}
				<tr>
					<td>{{key}}</td>
					<td>{{value}}</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>

	<h3 class='collapser'>$_SERVER</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			{% for key, value in SERVER %}
				<tr>
					<td>{{key}}</td>
					<td>{{value}}</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>
</div>
