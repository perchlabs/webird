<template>
  <div class="component-config-panel">

    <div class="header">
      Request Info
      <div class="header-close" @click="setActivePanel(false)">&times;</div>
    </div>

    <div class="content">

      <template v-for="sectionItem in sectionList">
        <h3 class="content-collapser">{{sectionItem.title}}</h3>
        <table class="content__table">
          <thead class="content__table-head">
            <tr class="content__table-head-row">
              <th class="content__table-head-cell">Key</th>
              <th class="content__table-head-cell">Value</th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="(value, key) in sections[sectionItem.name]" class="content__table-row">
            <td class="content__table-cell">{{key}}</td>
            <td class="content__table-cell"><pre><code class="php">{{value}}</code></pre></td>
          </tr>
          </tbody>
        </table>
      </template>

    </div>

  </div>

</template>

<script>
  // System
  import {mapGetters, mapActions} from 'vuex'

  /**
   *
   */
  export default {

    props: {
      sections: {
        type: Object,
        required: true,
      },
    },

    data() {
      return {
        sectionList: [
          {
            name: 'SESSION',
            title: '$_SESSION',
          },
          {
            name: 'COOKIE',
            title: '$_COOKIE',
          },
          {
            name: 'GET',
            title: '$_GET',
          },
          {
            name: 'POST',
            title: '$_POST',
          },
          {
            name: 'FILES',
            title: '$_FILES',
          },

        ],
      }
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
