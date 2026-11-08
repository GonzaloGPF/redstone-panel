import Aura from '@primevue/themes/aura'
import { definePreset } from '@primevue/themes'

/**
 * Aura with a redstone red as the primary color, and zinc surfaces
 */
const Redstone = definePreset(Aura, {
  semantic: {
    primary: {
      50: '{red.50}',
      100: '{red.100}',
      200: '{red.200}',
      300: '{red.300}',
      400: '{red.400}',
      500: '{red.500}',
      600: '{red.600}',
      700: '{red.700}',
      800: '{red.800}',
      900: '{red.900}',
      950: '{red.950}',
    },
  },
})

export const primeVueConfig = {
  theme: {
    preset: Redstone,
    options: {
      darkModeSelector: '.app-dark',
      cssLayer: {
        name: 'primevue',
        order: 'theme, base, primevue',
      },
    },
  },
}
