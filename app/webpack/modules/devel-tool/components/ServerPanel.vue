<template>
  <div class="component-config-panel">

    <div class="header">
      Server Info
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
            <td class="content__table-cell">{{value}}</td>
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
            name: 'headersList',
            title: 'Response Headers',
          },
          {
            name: 'SERVER',
            title: '$_SERVER',
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
