import { usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { watch, ref } from "vue";

export function useFlashToast() {
    const page = usePage();
    const toast = useToast();

    watch(
        () => page.props.flash,
        (flash) => {
            if (!flash) return;

            // chỉ show success nếu chưa show
            if (flash.success) {
                toast.add({
                    severity: "success",
                    summary: "Thành công",
                    detail: flash.success,
                    life: 3000,
                });
                flash.success = null
            }

            // chỉ show error nếu chưa show
            if (flash.error) {
                toast.add({
                    severity: "error",
                    summary: "Lỗi",
                    detail: flash.error,
                    life: 3000,
                });
                flash.error = null
            }
        },
        { deep: true, immediate: true }
    );
}
