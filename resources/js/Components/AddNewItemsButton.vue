<template>
    <Button severity="success" size="small" raised @click="handleClick">
        <div class="flex items-center gap-x-2 sub-menu">
            <i class="pi pi-plus" style="font-size: .875rem"></i>
            <span>{{ label }}</span>
        </div>
    </Button>
    <Menu v-if="hasMenu" class="toggle-menu" ref="addNewItemsMenu" :model="menuItems" :popup="true">
        <template #item="{ item, props }">
            <div class="toggle-menu-item">
                <span>{{ item.label }}</span>
            </div>
        </template>
    </Menu>
</template>

<script setup>
import { ref } from 'vue';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
const props = defineProps({
    label: {
        type: String,
        required: true
    },
    hasMenu: {
        type: Boolean,
        required: true
    },
    menuItems: Array
});

const emit = defineEmits(['parentClickEvent']);

// toggle menu if has menu
const addNewItemsMenu = ref(null);
const toggleMenu = (event) => {
    addNewItemsMenu.value?.toggle(event);
};

const handleClick = (event) => {
    if (props.hasMenu) {
        toggleMenu(event)
    }
    else
    {
        emit('parentClickEvent');
    }
};

</script>