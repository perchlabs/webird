<template>
  <document-viewer
    :pages="pages"
  />
</template>

<script>
  // Local
  import {DocumentViewer} from 'pdf'

  export default {
    components: {
      DocumentViewer,
    },

    data() {
      const pages = Array(4)
      for (let i = 0; i < pages.length; i++) {
        pages[i] = {
          src: `/static/pdf/pg_000${i + 1}.pdf`,

          // FIXME: Image Magick identify reporting wrong dimensions.
          // dim: [612, 792],
          dim: [630, 917],
        }
      }

      return {
        pages,
      }
    },
  }
</script>

<style scoped>

</style>
