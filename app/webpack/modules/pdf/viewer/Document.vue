<template>
  <div class="component-document">
    Document
    <page
      v-for="(page, i) in pagesCalc"
      :key="i"
      :src="page.src"
      :dim="page.dim"
      :top="page.top">
    </page>
  </div>
</template>

<script>
  // System
  import {mapGetters, mapActions} from 'vuex'
  // Local
  import Page from './Page'

  export default {
    components: {
      Page,
    },

    props: {
      pages: {
        type: Array,
        required: true,
      },
    },

    data() {
      return {
        spacerY: 30,
        scale: 1.0,
        pagesCalc: null,
      }
    },

    created() {
      let top = 0
      const pagesCalc = Array(this.pages.length)

      for (let i = 0; i < this.pages.length; i++) {
        const {src, dim} = this.pages[i]

        pagesCalc[i] = {
          src,
          dim,
          top,
        }

        top += this.spacerY + dim[1]
      }

      this.pagesCalc = pagesCalc
    },

    mounted() {
      console.log(this.pagesCalc)
    }
  }
</script>

<style scoped>
@import 'style/vars.css';

.component-document {

}

</style>
