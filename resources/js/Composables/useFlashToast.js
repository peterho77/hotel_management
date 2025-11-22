import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { watch, ref } from "vue";

export function useFlashToast() {
    const page = usePage();
    const toast = useToast();

    const shownFlash = ref({ success: null, error: null });

    watch(
        () => page.props.flash,
        (flash) => {
            if (!flash) return;

            // chỉ show success nếu chưa show
            if (flash.success && flash.success !== shownFlash.value.success) {
                toast.add({
                    severity: "success",
                    summary: "Thành công",
                    detail: flash.success,
                    life: 3000,
                });
                shownFlash.value.success = flash.success;
            }

            // chỉ show error nếu chưa show
            if (flash.error && flash.error !== shownFlash.value.error) {
                toast.add({
                    severity: "error",
                    summary: "Lỗi",
                    detail: flash.error,
                    life: 3000,
                });
                shownFlash.value.error = flash.error;
            }
        },
        { deep: true, immediate: true }
    );
}
