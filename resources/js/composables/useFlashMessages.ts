import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { toast } from 'vue-sonner'

export function useFlashMessages() {
  const page = usePage()

  // Watch for flash messages and display them as toasts
  watch(
    () => page.props.flash,
    (flash) => {
      if (flash?.success) {
        toast.success(flash.success as string)
      }

      if (flash?.error) {
        toast.error(flash.error as string)
      }

      if (flash?.warning) {
        toast.warning(flash.warning as string)
      }

      if (flash?.info) {
        toast.info(flash.info as string)
      }

      if (flash?.rate_limit) {
        toast.warning(flash.rate_limit as string, {
          duration: 6000,
        })
      }
    },
    { deep: true, immediate: true }
  )
}
