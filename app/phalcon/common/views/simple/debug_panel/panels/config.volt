<div class='title'>
    <h2>Config Info <a class='wbdebug-panel-close'>&times;</a></h2>
</div>
<div class='panel-content'>
    <h3 class='collapser'>App Paths</h3>
    <table class='wbdebug-data-table'>
        <tbody>
        {% for key, value in config['path'] %}
            <tr>
                <td>{{key}}</td>
                <td>{{value}}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <h3 class='collapser'>Services</h3>
    <table class='wbdebug-data-table'>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>client Id</th>
                <th>client Secret</th>
            </tr>
        </thead>
        <tbody>
        {% for vendor, codes in config['services'] %}
            <tr>
                <td>{{vendor}}</td>
                <td><pre>{{codes['clientId']}}</pre></td>
                <td><pre>{{codes['clientSecret']}}</pre></td>
            </tr>
        {% endfor %}
        </tbody>
    </table>

    <h3 class='collapser'>Config</h3>
    <table class='wbdebug-data-table'>
        <tbody>
            <tr><td>
                <pre><code class='php'>{{ print_r(config) }}</code></pre>
            </td></tr>
        </tbody>
    </table>
</div>
