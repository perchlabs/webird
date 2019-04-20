<template>
  <div class="component-database-panel">

    <div class="header">
      Database Info
      <div class="header-close" @click="setActivePanel(false)">&times;</div>
    </div>

    <div class="content">

      <h3 class="content-collapser">SQL Queries</h3>
      <table class="content__table">
        <thead class="content__table-head">
          <tr class="content__table-head-row">
            <th class="content__table-head-cell">Time</th>
            <th class="content__table-head-cell">SQL</th>
            <th class="content__table-head-cell">Params</th>
          </tr>
        </thead>

        <tbody>
        <tr v-for="profile in sections.profiles" class="content__table-row">
          <td class="content__table-cell">{{profile.time}}</td>
          <td class="content__table-cell"><pre><code class="sql">{{profile.sql}}</code></pre></td>
          <td class="content__table-cell"><pre><code class="php">{{profile.vars}}</code></pre></td>
        </tr>
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
