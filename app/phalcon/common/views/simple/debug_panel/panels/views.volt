<div class='title'>
	<h2>View Info <a class='wbdebug-panel-close'>&times;</a></h2>
</div>

<div class='panel-content'>
{% for rendered in viewsRendered %}
	<h3 class='collapser'>{{rendered['path']}}</h3>
	<table class='pdw-data-table'>
		<tbody>
		{% for key, value in rendered %}
			{% set class = (key == 'params') ? 'php' : '' %}
			<tr>
				<td style="width: 125px">{{key}}</td>
				<td>
					<pre><code class="{{class}}">{{ print_r(value) }}</code></pre>
				</td>
			</tr>
		{% endfor %}
		</tbody>
	</table>
{% endfor %}
</div>
