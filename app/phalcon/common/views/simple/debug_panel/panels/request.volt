<div class='title'>
	<h2>Request Info <a class='wbdebug-panel-close'>&times;</a></h2>
</div>
<div class='panel-content'>
	<h3 class='collapser'>$_SESSION</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
		{% for key, value in SESSION %}
			<tr>
				<td>{{key}}</td>
				<td>
					<pre><code class="php">{{ print_r(value) }}</code></pre>
				</td>
			</tr>
		{% endfor %}
		</tbody>
	</table>

	<h3 class='collapser'>$_COOKIE</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
		{% for key, value in COOKIE %}
			<tr>
				<td>{{key}}</td>
				<td>
					<pre><code class="php">'{{ value }}'</code></pre>
				</td>
			</tr>
		{% endfor %}
		</tbody>
	</table>

	<h3 class='collapser'>$_GET</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			{% for key, value in GET %}
				<tr>
					<td>{{key}}</td>
					<td>
						<pre><code class="php">'{{ value }}'</code></pre>
					</td>
				</tr>
			{% endfor %}
		</tbody>
	</table>

	<h3 class='collapser'>$_POST</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
		{% for key, value in POST %}
			<tr>
				<td>{{key}}</td>
				<td>
					<pre class=""><code class="php">'{{ value }}'</code></pre>
				</td>
			</tr>
		{% endfor %}
		</tbody>
	</table>

	<h3 class='collapser'>$_FILES</h3>
	<table class='wbdebug-data-table'>
		<thead>
			<tr>
				<th>Key</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
		{% for key, value in FILES %}
			<tr>
				<td>{{key}}</td>
				<td>
					<pre class=""><code class="php">{{ value }}</code></pre>
				</td>
			</tr>
		{% endfor %}
		</tbody>
	</table>

</div>
