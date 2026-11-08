<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppButton from '@/Components/Core/AppButton.vue'
import AppLogo from '@/Components/Core/AppLogo.vue'
import { Translator } from '@/objects/Translator'
</script>
<template>
  <div
    class="flex flex-col justify-center items-center gap-6 mx-auto py-8 text-center"
  >
    <Head :title="Translator.tl('home')" />
    <AppLogo class="mx-auto" />
    <p
      class="max-w-xl text-lg text-muted-color"
      data-tagline
      v-text="Translator.tl('tagline')"
    />
    <AppButton
      :label="Translator.tl('open_panel')"
      icon="monitor_heart"
      :href="route('dashboard.show')"
    />
  </div>
</template>
