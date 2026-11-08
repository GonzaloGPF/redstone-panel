<script setup lang="ts">
/**
 * The mark of RedstonePanel: a pixel art block with a screen, where a redstone line beats into a lamp.
 * Online, a signal runs along the line and the lamp glows when it arrives; offline, everything is off;
 * loading, the lamp blinks. Nothing moves for whoever asks for reduced motion.
 */
type Props = {
  state?: 'online' | 'offline' | 'loading'
  title?: string
}

withDefaults(defineProps<Props>(), {
  state: 'online',
  title: undefined,
})

// The pixels of the line, in the order the signal goes through them: [x, y]
const TRACE: [number, number][] = [
  [2, 9],
  [3, 9],
  [4, 9],
  [5, 8],
  [5, 7],
  [5, 6],
  [5, 5],
  [6, 4],
  [7, 5],
  [7, 6],
  [7, 7],
  [7, 8],
  [7, 9],
  [7, 10],
  [7, 11],
  [8, 12],
  [9, 11],
  [9, 10],
]
</script>
<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 16 16"
    shape-rendering="crispEdges"
    :role="title ? 'img' : 'presentation'"
    :aria-label="title"
    :aria-hidden="title ? undefined : 'true'"
    :class="['logo-mark', `logo-mark--${state}`]"
    :data-state="state"
  >
    <path fill="#52525b" d="M1 0h14v1h1v14h-1v1H1v-1H0V1h1z" />
    <path fill="#27272a" d="M1 15h14v-1h1v1h-1v1H1z M15 1h1v14h-1z" />
    <path fill="#18181b" d="M2 2h12v12H2z" />
    <g class="logo-mark__trace">
      <rect
        v-for="([x, y], step) in TRACE"
        :key="step"
        :x="x"
        :y="y"
        width="1"
        height="1"
        :style="{ '--step': step }"
      />
    </g>
    <g class="logo-mark__lamp">
      <path class="logo-mark__lamp-body" d="M10 8h3v3h-3z" />
      <path class="logo-mark__lamp-core" d="M11 9h1v1h-1z" />
    </g>
  </svg>
</template>
<style scoped>
.logo-mark__trace rect {
  fill: #dc2626;
}

.logo-mark__lamp-body {
  fill: #ef4444;
}

.logo-mark__lamp-core {
  fill: #fecaca;
}

/* Online: the signal runs along the line, and the lamp flashes when it arrives */
.logo-mark--online .logo-mark__trace rect {
  animation: logo-mark-signal 2.4s linear infinite;
  animation-delay: calc(var(--step) * 45ms);
}

.logo-mark--online .logo-mark__lamp {
  animation: logo-mark-glow 2.4s ease-out infinite;
  animation-delay: 0.8s;
}

/* Offline: no power */
.logo-mark--offline .logo-mark__trace rect {
  fill: #57534e;
}

.logo-mark--offline .logo-mark__lamp-body {
  fill: #44403c;
}

.logo-mark--offline .logo-mark__lamp-core {
  fill: #57534e;
}

/* Loading: the lamp blinks while the server is asked */
.logo-mark--loading .logo-mark__trace rect {
  fill: #7f1d1d;
}

.logo-mark--loading .logo-mark__lamp {
  animation: logo-mark-blink 1s steps(2, jump-none) infinite;
}

@keyframes logo-mark-signal {
  0%,
  6% {
    fill: #fecaca;
  }

  14%,
  100% {
    fill: #dc2626;
  }
}

@keyframes logo-mark-glow {
  0%,
  100% {
    filter: drop-shadow(0 0 0.4px #ef4444);
  }

  12% {
    filter: drop-shadow(0 0 1.6px #f87171) brightness(1.25);
  }
}

@keyframes logo-mark-blink {
  0% {
    opacity: 1;
  }

  100% {
    opacity: 0.35;
  }
}

@media (prefers-reduced-motion: reduce) {
  .logo-mark *,
  .logo-mark__trace rect {
    animation: none !important;
  }
}
</style>
