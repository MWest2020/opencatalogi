/**
 * ThemeDetail.vue
 * Component for displaying theme details
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { ref, computed } from 'vue'
import { objectStore, navigationStore } from '../../store/store.js'
import { NcButton } from '@nextcloud/vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Get the active theme from the store
 * @return {object | null}
 */
const theme = computed(() => objectStore.getActiveObject('theme'))

/**
 * Handle save action
 * @return {Promise<void>}
 */
const handleSave = async () => {
	loading.value = true
	try {
		await objectStore.updateObject('theme', theme.value)
		navigationStore.setDialog(false)
	} catch (error) {
		console.error('Error saving theme:', error)
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @return {void}
 */
const handleCancel = () => {
	navigationStore.setDialog(false)
}
</script>

<template>
	<div class="theme-detail">
		<p>{{ t('opencatalogi', 'Weet je zeker dat je dit thema wilt opslaan?') }}</p>
		<div class="theme-detail__actions">
			<NcButton :disabled="loading" @click="handleCancel">
				{{ t('opencatalogi', 'Annuleren') }}
			</NcButton>
			<NcButton type="primary" :disabled="loading" @click="handleSave">
				{{ t('opencatalogi', 'Opslaan') }}
			</NcButton>
		</div>
	</div>
</template>

<style scoped>
.theme-detail {
	padding: 20px;
}

.theme-detail__actions {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}
</style>
