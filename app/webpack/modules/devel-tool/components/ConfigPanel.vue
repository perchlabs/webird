<template>
  <div class="component-config-panel">

    <div class="header">
      Config Info
      <div class="header-close" @click="setActivePanel(false)">&times;</div>
    </div>

    <div class="content">

      <h3 class="content-collapser">App Paths</h3>
      <table class="content__table">
        <tbody>
        <tr v-for="(value, key) in sections.path" class="content__table-row">
          <td class="content__table-cell">{{key}}</td>
          <td class="content__table-cell">{{value}}</td>
        </tr>
        </tbody>
      </table>

      <h3 class="content-collapser">Services</h3>
      <table class="content__table">
        <thead class="content__table-head">
          <tr class="content__table-head-row">
            <th class="content__table-head-cell">Vendor</th>
            <th class="content__table-head-cell">client Id</th>
            <th class="content__table-head-cell">client Secret</th>
          </tr>
        </thead>
        <tbody>
          <!--
          {% if config.org.services is defined %}
            {% for vendor, codes in config.org.services %}
              <tr class="debug-tool__panel_tbody_tr">
                <td class="debug-tool__panel_cell">{{vendor}}</td>
                <td class="debug-tool__panel_cell"><pre>{{codes["clientId"]}}</pre></td>
                <td class="debug-tool__panel_cell"><pre>{{codes["clientSecret"]}}</pre></td>
              </tr>
            {% endfor %}
          {% endif %}
          -->
        </tbody>
      </table>

    </div>

  </div>

</template>

<script>
  // System
  import {mapGetters, mapActions} from 'vuex'

  export default {
    props: {
      sections: {
        type: Object,
        required: true,
      },
    },

    methods: {

      ...mapActions([
        'setActivePanel',
      ]),
    },

  }
</script>

<style scoped>
  @import './panels.css'
</style>
