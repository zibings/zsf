<script setup>
import { useLayout } from '@/layout/composables/layout';

const { layoutConfig } = useLayout();
</script>

<template>
    <div class="layout-footer">
        <img :src="'/layout/images/logo/footer-' + (layoutConfig.colorScheme.value === 'light' ? 'ultima' : 'ultima-dark') + '.svg'" alt="ultima-footer-logo" />
        <div class="flex gap-2">
            <Button icon="pi pi-github" rounded text plain></Button>
            <Button icon="pi pi-facebook" rounded text plain></Button>
            <Button icon="pi pi-twitter" rounded text plain></Button>
        </div>
    </div>
</template>
