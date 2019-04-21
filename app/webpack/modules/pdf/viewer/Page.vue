<template>
  <div
    :style="{
      top: `${top}px`,
      width: `${dim[0]}px`,
      height: `${dim[1]}px`,
    }"
    class="component-page">
    <canvas
      ref="pdfCanvas"
      :width="dim[0]"
      :height="dim[1]"
      class="image-layer">
    </canvas>
    <div
      ref="textLayer"
      class="text-layer textLayer">
    </div>
  </div>
</template>

<script>
  // System
  import {mapGetters, mapActions} from 'vuex'
  import {getDocument, renderTextLayer} from 'pdfjs-dist'
  import {TextLayerBuilder} from 'pdfjs-dist/lib/web/text_layer_builder'

  export default {
    props: {
      src: {
        type: String,
        required: true,
      },
      dim: {
        type: Array,
        required: true,
      },
      top: {
        type: Number,
        required: true,
      },
    },

    /**
     *
     */
    async mounted() {
      const loadingTask = await getDocument(this.src).promise

      const page = await loadingTask.getPage(1)

      const {pdfCanvas: canvas, textLayer: textLayerDiv} = this.$refs

      const scale = this.dim[0] / page.getViewport({scale: 1.0}).width

      // Get viewport (dimensions)
      const viewport = page.getViewport({scale})
      const {width, height} = viewport

      // Fetch canvas' 2d context
      const context = canvas.getContext('2d')

      // Prepare object needed by render method
      const renderContext = {
        canvasContext: context,
        viewport: viewport
      }

      // Render PDF page
      await page.render(renderContext).promise

      // Display text layer
      const textContent = await page.getTextContent()

      const textLayer = new TextLayerBuilder({
        textLayerDiv,
        viewport,
        pageIndex: page.pageIndex,
      })

      // Set text-fragments
      textLayer.setTextContent(textContent)

      // Render text-fragments
      textLayer.render()
    },
  }
</script>

<!-- Scoped CSS -->
<style scoped>
  @import 'style/vars.css';

  .component-page {
    position: absolute;
  }

  .image-layer {
    position: absolute;
    top: 0px;
    right: 0px;
    bottom: 0px;
    left: 0px;
    z-index: 1;
  }
  .text-layer {
    z-index: 2;
  }

</style>

<!-- Normal CSS -->
<style>
  .textLayer {
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    overflow: hidden;
    opacity: 0.2;
    line-height: 1.0;
  }

  .textLayer > span {
    color: transparent;
    position: absolute;
    white-space: pre;
    cursor: text;
    transform-origin: 0% 0%;
  }

  .textLayer .highlight {
    margin: -1px;
    padding: 1px;

    background-color: rgb(180, 0, 170);
    border-radius: 4px;
  }

  .textLayer .highlight.begin {
    border-radius: 4px 0px 0px 4px;
  }

  .textLayer .highlight.end {
    border-radius: 0px 4px 4px 0px;
  }

  .textLayer .highlight.middle {
    border-radius: 0px;
  }

  .textLayer .highlight.selected {
    background-color: rgb(0, 100, 0);
  }

  .textLayer ::selection {
    background: rgb(0,0,255);
  }

  .textLayer .endOfContent {
    display: block;
    position: absolute;
    left: 0px;
    top: 100%;
    right: 0px;
    bottom: 0px;
    z-index: -1;
    cursor: default;
    user-select: none;
  }

  .textLayer .endOfContent.active {
    top: 0px;
  }

</style>
