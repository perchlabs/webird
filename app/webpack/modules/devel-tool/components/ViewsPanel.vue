<template>
  <div class="component-config-panel">

    <div class="header">
      Views Info
      <div class="header-close" @click="setActivePanel(false)">&times;</div>
    </div>

    <div class="content">

      <h3 class="content-collapser">App Paths</h3>
      <table class="content__table">
        <tbody>
        <tr v-for="view in sections.viewsRendered" class="content__table-row">
          <td class="content__table-cell">{{view.path}}</td>
          <td class="content__table-cell">{{view.params}}</td>
        </tr>
        </tbody>
      </table>



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

    /**
     *
     */
    props: {
      sections: {
        type: Object,
        required: true,
      },
    },

    /**
     *
     */
    methods: {

      /**
       *
       */
      ...mapActions([
        'setActivePanel',
      ]),
    },

  }
</script>

<style scoped>
  @import './panels.css'
</style>
