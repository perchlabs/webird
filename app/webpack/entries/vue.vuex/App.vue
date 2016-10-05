
<template>
  <div>
    <div>Counter: {{counter}}</div>
    <button @click="decrease">Decrease</button>
    <button @click="increase">Increase</button>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        counter: 0
      }
    },
    methods: {
      decrease: function() {
        this.counter--
      },
      increase: function() {
        this.counter++
      }
    }
  }
</script>

<style>

</style>
