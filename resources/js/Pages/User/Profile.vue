<script setup>
import { ref, computed, watch, defineAsyncComponent } from 'vue';

import { usePage } from '@inertiajs/vue3';
import { useDialog } from 'primevue/usedialog';

import Avatar from 'primevue/avatar';
import Button from 'primevue/button';

const dialog = useDialog();
const changePasswordDialog = defineAsyncComponent(() => import('../../Components/Dialog/User/ChangePassword.vue'));
const updateProfileDialog = defineAsyncComponent(() => import('../../Components/Dialog/User/UpdateProfile.vue'));

const showChangePasswordDialog = () => {
    dialog.open(changePasswordDialog, {
        props: {
            header: 'Đổi mật khẩu',
            style: {
                width: '30vw',
            },
            breakpoints: {
                '960px': '50vw',
                '640px': '40vw'
            },
            modal: true,
            position: 'right',
        },
        data: {
            username: user.value.user_name
        }
    });
}

const showUpdateProfileDialog = () => {
    dialog.open(updateProfileDialog, {
        props: {
            header: 'Cập nhật thông tin cá nhân',
            style: {
                width: '40vw',
            },
            breakpoints: {
                '960px': '60vw',
                '640px': '50vw'
            },
            modal: true,
            position: 'right',
        },
        data: {
            userInfo: JSON.parse(JSON.stringify(customerInfor.value)),
            username: user.value.user_name
        }
    });
}

// show username 
const page = usePage();

const user = computed(() => page.props.auth.user);
const customerInfor = ref(removeCustomerFields({ ...user.value.customer }));

watch(
    () => page.props.auth.user.customer,
    (newCustomer) => {
        customerInfor.value = removeCustomerFields({ ...newCustomer });
    },
    { deep: true, immediate: true }
);

// Danh sách các trường cần xóa
function removeCustomerFields(customer) {
    const removeFields = [
        "id",
        "user_id",
        "customer_type_id",
        "customer_group_id",
        "created_at",
        "updated_at",
        "note",
        "has_account"
    ];
    return Object.fromEntries(
        Object.entries(customer).filter(([key]) => !removeFields.includes(key))
    );
}

// format label
function formatLabel(str) {
    str = str.replace(/[-_]/g, " ");

    return str.split(" ").map(word =>
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(" ");
}

function getGender(val) {
    switch (val) {
        case 'male':
            return 'Nam';
        case 'female':
            return 'Nữ';
        default:
            return '-';
    }
}

</script>

<template>
    <div class="user-header | box flex flex-col p-20">
        <Avatar class="user-avatar" icon="pi pi-user" size="xlarge" shape="circle" />
        <h2 class="fs-normal-heading">{{ customerInfor.full_name }}</h2>
        <span class="text-gray-600">{{ customerInfor.email }}</span>
    </div>
    <div class="user-infor-section | box flow px-20 py-4" style="--flow-spacer:1rem">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-lg">Thông tin cá nhân</h3>
            <Button @click="showUpdateProfileDialog" label="Cập nhật" icon="pi pi-user-edit" severity="danger"
                variant="text" raised />
        </div>
        <ul class="columns-2 gap-12">
            <li class="grid grid-cols-[100px_1fr] md:grid-cols-[140px_1fr] gap-x-6 py-2 border-b border-dashed border-gray-300"
                v-for="(value, key) in customerInfor" :key="key">
                <span class="text-gray-500">{{ formatLabel(key) }}:</span>
                <template v-if="key === 'gender'">
                    <span class="text-right">{{ getGender(value) }}</span>
                </template>
                <template v-else>
                    <span class="text-right">{{ value || '-' }}</span>
                </template>
            </li>
        </ul>
    </div>
    <div class="password-section | box flow px-20 py-4" style="--flow-spacer:1rem">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-lg">Mật khẩu</h3>
            <Button @click="showChangePasswordDialog" label="Thay đổi mật khẩu" icon="pi pi-user-edit" severity="danger"
                variant="text" raised />
        </div>
        <div class="flex justify-betwen gap-x-6 py-2">
            <span class="text-gray-500">Cập nhật lần cuối lúc:</span>
            <!-- updated at -->
            <span>
                {{
                    user.password_changed_at
                        ? new Date(user.password_changed_at).toLocaleString('vi-VN', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        })
                        : '-'
                }}
            </span>
        </div>
    </div>
</template>
