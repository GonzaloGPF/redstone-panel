<script setup lang="ts">
import Skeleton from 'primevue/skeleton'
import Tag from 'primevue/tag'
import { computed } from 'vue'
import AppCard from '@/Components/Core/AppCard.vue'
import AppLogoMark from '@/Components/Core/AppLogoMark.vue'
import { Formatter } from '@/objects/Formatter'
import { Translator } from '@/objects/Translator'
import type { ServerStatus } from '@/types/server'

type Props = {
  /** A skeleton takes its place while it is undefined (the server is still being asked). */
  server?: ServerStatus
}

const props = defineProps<Props>()

const state = computed(() => {
  if (!props.server) return 'loading'

  return props.server.online ? 'online' : 'offline'
})

const fields = computed(() => {
  const server = props.server
  if (!server?.online) return []

  return [
    {
      label: Translator.tl('players'),
      value: `${server.players ?? 0} / ${server.maxPlayers ?? 0}`,
    },
    { label: Translator.tl('version'), value: server.version ?? '-' },
    { label: Translator.tl('latency'), value: `${server.latency ?? '-'} ms` },
    {
      label: Translator.tl('world'),
      value: [server.world, server.gameMode].filter(Boolean).join(' · '),
    },
  ]
})
</script>
<template>
  <AppCard
    :title="Translator.tl('server_status')"
    icon="dns"
    data-server-status
    :data-state="state"
  >
    <div class="flex items-center gap-4">
      <AppLogoMark :state="state" class="size-16 shrink-0" />
      <div class="flex min-w-0 flex-col gap-2">
        <template v-if="server">
          <div class="flex flex-wrap items-center gap-2">
            <Tag
              :severity="server.online ? 'success' : 'danger'"
              :value="Translator.tl(server.online ? 'online' : 'offline')"
            />
            <span
              v-if="server.motd"
              class="truncate text-lg font-semibold"
              v-text="server.motd"
            />
          </div>
          <span class="text-sm text-muted-color" v-text="server.address" />
        </template>
        <template v-else>
          <Skeleton width="10rem" height="1.75rem" />
          <Skeleton width="8rem" height="1rem" />
        </template>
      </div>
    </div>
    <dl v-if="fields.length" class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
      <div v-for="field in fields" :key="field.label" data-server-field>
        <dt class="text-sm text-muted-color" v-text="field.label" />
        <dd class="text-xl font-semibold" v-text="field.value" />
      </div>
    </dl>
    <p
      v-else-if="server"
      class="mt-4 text-muted-color"
      v-text="Translator.tl('server_unreachable')"
    />
    <template v-if="server" #footer>
      <span
        class="text-xs text-muted-color"
        v-text="
          Translator.tl('checked_at', {
            time: Formatter.timeFormat(server.checkedAt),
          })
        "
      />
    </template>
  </AppCard>
</template>
