<script setup lang="ts">
import { app } from '@/config/app'
import AppLogoMark from '@/Components/Core/AppLogoMark.vue'
</script>
<template>
  <div class="flex justify-center flex-col mx-auto items-center">
    <AppLogoMark :title="app.getAppName()" class="w-24 md:w-32 lg:w-40" />
    <h1
      id="title"
      class="text-lg md:text-5xl lg:text-6xl mt-3 font-bold whitespace-nowrap"
      v-text="app.getAppName()"
    />
  </div>
</template>
