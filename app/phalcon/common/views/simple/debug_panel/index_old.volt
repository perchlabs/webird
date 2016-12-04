<!--DEBUG_PANEL_START-->
<div id="wbdebug-container" style="display: none">
    <a id="wbdebug-icon"></a>
    <div id="wbdebug-toolbar">

        <ul id="wbdebug-main-nav">
            {% for panelName, panel in panels %}
            <li>
                <a class="wbdebug-open" href="#" data-open="{{panelName}}">
                    {{ panelName|capitalize }}<br />
                </a>
            </li>
            {% endfor %}
        </ul>

        {% for panelName, content in panels %}
            <div id="wbdebug-panel-{{panelName}}" class="wbdebug-panel">
                {{ content }}
            </div>
        {% endfor %}

        <div id="wbdebug-status">
            <h3 class="title">Resource Usage</h3>
            <table>
                <tbody>
                    <tr>
                        <td>load time</td>
                        <td>{{loadTime}} s</td>
                    </tr>
                    <tr>
                        <td>elapsed time</td>
                        <td>{{elapsedTime}} s</td>
                    </tr>
                    <tr>
                        <td>mem</td>
                        <td>{{mem}} KB</td>
                    </tr>
                    <tr>
                        <td>mem peak</td>
                        <td>{{memPeak}} KB</td>
                    </tr>
                    <tr>
              <td>session size</td>
              <td>{{'%0.3F KB'|format(sessionSize)}}</td>
                    </tr>
                </tbody>
            </table>

            <h3 class="title">Access</h3>
            <table>
                <tbody>
                    <tr>
                        <td style="padding-bottom: 4px;">Webpack</td>
                        <td style="padding-bottom: 4px;"><a href="{{url('webpack-dev-server')}}" target="_blank" class="ctrl">webpack-dev-server</a></td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 4px;">Script</td>
                        <td style="padding-bottom: 4px;"><code style="" class="php">wbdebug.toggle()</code></td>
                    </tr>
                    <tr>
                        <td>Hotkey</td>
                        <td><kbd>Ctrl + Shift + D</kbd></td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
</div>
<!--DEBUG_PANEL_END-->
