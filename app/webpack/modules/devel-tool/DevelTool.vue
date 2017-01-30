<template>
  <div
    v-if="isOpen"
    class="component-devel-tool">

    <div class="nav">
      <template v-for="panelName in panels">
        <div class="panel-open" @click="setActivePanel(panelName)">{{panelName}}</div>
      </template>

      <div class="status">
        <h3 class="status__section-title">Resource Usage</h3>
        <table class="status__section-table">
          <tbody>
            <tr>
              <td class="status__section-table-cell">load time</td>
              <td class="status__section-table-cell">{{measurement.loadTime}} s</td>
            </tr>
            <tr>
              <td class="status__section-table-cell">elapsed time</td>
              <td class="status__section-table-cell">{{measurement.elapsedTime}} s</td>
            </tr>
            <tr>
              <td class="status__section-table-cell">mem</td>
              <td class="status__section-table-cell">{{measurement.mem}} KB</td>
            </tr>
            <tr>
              <td class="status__section-table-cell">mem peak</td>
              <td class="status__section-table-cell">{{measurement.memPeak}} KB</td>
            </tr>
            <tr>
              <td class="status__section-table-cell">session size</td>
              <td class="status__section-table-cell">{{measurement.sessionSize}}</td>
            </tr>
          </tbody>
        </table>

        <h3 class="status__section-title">Access</h3>
        <table class="status__section-table">
          <tbody>
            <tr>
              <td class="status__section-table-cell">Webpack</td>
              <td class="status__section-table-cell"><a href="/webpack-dev-server" target="_blank" class="ctrl">webpack-dev-server</a></td>
            </tr>
            <tr>
              <td class="status__section-table-cell">Hotkey</td>
              <td class="status__section-table-cell"><kbd>{{shortcut}}</kbd></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <div class="panels" v-show="isPanelOpen">
      <config-panel
        v-show="panelName == 'config'"
        :sections="panelData.config">
      </config-panel>
      <database-panel
        v-show="panelName == 'database'"
        :sections="panelData.database">
      </database-panel>
      <views-panel
        v-show="panelName == 'views'"
        :sections="panelData.views">
      </views-panel>
      <request-panel
        v-show="panelName == 'request'"
        :sections="panelData.request">
      </request-panel>
      <server-panel
        v-show="panelName == 'server'"
        :sections="panelData.server">
      </server-panel>
    </div>

  </div>
</template>

<script>
  // System
  import {mapGetters, mapActions} from 'vuex'
  import mousetrap from 'mousetrap'
  // Local
  import ConfigPanel from './components/ConfigPanel'
  import DatabasePanel from './components/DatabasePanel'
  import ViewsPanel from './components/ViewsPanel'
  import RequestPanel from './components/RequestPanel'
  import ServerPanel from './components/ServerPanel'

  /**
   *
   */
  export default {

    /**
     *
     */
    components: {
      ConfigPanel,
      DatabasePanel,
      ViewsPanel,
      RequestPanel,
      ServerPanel,
    },

    /**
     *
     */
    data() {
      return {
        panels: ['config', 'database', 'views', 'request', 'server',],
        shortcut: 'ctrl+y'
      }
    },

    /**
     *
     */
    computed: {

      /**
       *
       */
      ...mapGetters([
        'wasOpened',
        'isOpen',
        'isPanelOpen',
        'panelName',
        'panelData',
        'measurement',
      ]),
    },

    /**
     *
     */
    methods: {

      /**
       *
       */
      ...mapActions([
        'openTool',
        'setActivePanel',
        'toggleTool',
        'openTool',
      ]),
    },

    /**
     *
     */
    mounted() {
      mousetrap.bind([this.shortcut], () => {
        this.toggleTool()
      })
    },

  }
</script>

<style scoped>
@import 'style/vars.css';

.component-devel-tool {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background: transparent;
  z-index: 10000;
  pointer-events: none;
  overflow: hidden;
}

.nav {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  padding: 0;
  width: 190px;
  background: var(--block__background--color--medium);
  pointer-events: auto;
}
.panels {
  position: absolute;
  top: 0;
  right: 191px;
  bottom: 0;
  left: 0;
  width: calc(100% - 190px);
  pointer-events: auto;
  overflow: hidden;
}

.panel-open {
  display: inline-block;
  text-decoration: none;
  font-weight: bold;
  padding: 5px 7px 5px 7px;
  border-style: solid;
  border-color: transparent;
  border-width: 1px 0px 1px 0px;
  width: 100%;
  color: var(--action__text--color);
}
.panel-open:hover {
  color: var(--action__text--color--hover);
  cursor: pointer;
}
.panel-open--active {
  color: var(--html__color);
  background-color: white;
  border-color: var(--block__background--color--light);
}




.status {
  box-sizing: border-box;
  width: 100%;
  padding: 10px;
  position: absolute;
  bottom: 5px;
  right: 0;
  border-top: 2px solid var(--block__border--color--medium);
  background: var(--block__background--color--medium);
}

.status__section-title {
  font-size: 11px;
  font-weight: bold;
  line-height: 12px;
  margin: 0 0 5px 0;
  padding: 8px 0 0 0;
  color: var(--html__color);
}

.status__section-table {
  font-weight: normal;
  width: 100%;
  font-size: 11px;
  color: var(--html__color);
  border: none;
}

.status__section-table-cell {
  font-size: 11px;
  color: var(--html__color);
  border: none;

  text-align: left;
  padding: 0;
  margin: 0;
  line-height: 16px;
  vertical-align: middle;
  border: none;
  text-align: right;
}

</style>
