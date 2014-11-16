<div class='title'>
	<h2>Database Info <a class='wbdebug-panel-close'>&times;</a></h2>
</div>
<div class='panel-content'>
	<h3 class='collapser'>SQL Queries</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Query</th>
				<th>Params</th>
				<th style="width: 100px">Time (s)</th>
			</tr>
		</thead>
		<tbody>
		{% set total = 0 %}
		{% for profile in profiles %}
			{% set time = profile['time'] %}
			{% set sql = profile['sql'] %}
			{% set vars = profile['vars'] %}
			{% set total = total + time %}
			<tr>
				<td><pre><code class='sql'>{{sql}}</code></pre></td>
				<td><pre><code class='php'>{{ print_r(vars) }}</code></pre></td>
				<td>{{ number_format(time, 6) }}</td>
			</tr>
		{% endfor %}
		<tr>
			<td></td>
			<td></td>
			<td><strong>{{ number_format(total, 6) }}</strong></td>
		</tr>
		</tbody>
	</table>

	{% for db in dbs %}
		<h3 class='collapser'>DB Server</h3>
		<table class='wbdebug-data-table'>
			<thead>
				<tr>
					<td>Type</td>
					<td>{{ db.getType() }}</td>
				</tr>
			</thead>
			<tbody>
			{% for key, value in db.getDescriptor() %}
				<tr>
					<td>{{key}}</td>
					<td>{{value}}</td>
				</tr>
			{% endfor %}
			</tbody>
		</table>
	{% endfor %}

</div>
