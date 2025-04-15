/**
 * CopyObjectDialog.vue
 * Generic dialog for copying objects
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
import { objectStore, navigationStore } from '../store/store.js'
import { NcButton } from '@nextcloud/vue'

/**
 * Props for the component
 * @type {object}
 */
const props = defineProps({
	/**
	 * Type of object to copy
	 * @type {string}
	 */
	objectType: {
		type: String,
		required: true,
	},
	/**
	 * Name of the dialog
	 * @type {string}
	 */
	dialogName: {
		type: String,
		required: true,
	},
	/**
	 * Display name for the object type
	 * @type {string}
	 */
	displayName: {
		type: String,
		required: true,
	},
})

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Get the active object from the store
 * @return {object | null}
 */
const activeObject = computed(() => objectStore.getActiveObject(props.objectType))

/**
 * Handle copy action
 * @return {Promise<void>}
 */
const handleCopy = async () => {
	loading.value = true
	try {
		await objectStore.copyObject(props.objectType, activeObject.value)
		navigationStore.setDialog(false)
	} catch (error) {
		console.error(`Error copying ${props.objectType}:`, error)
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
	<div class="copy-object-dialog">
		<p>
			{{ t('opencatalogi', 'Wil je') }}
			<b>{{ activeObject?.title || activeObject?.name }}</b>
			{{ t('opencatalogi', 'kopiëren?') }}
		</p>
		<div class="copy-object-dialog__actions">
			<NcButton :disabled="loading" @click="handleCancel">
				{{ t('opencatalogi', 'Annuleren') }}
			</NcButton>
			<NcButton type="primary" :disabled="loading" @click="handleCopy">
				{{ t('opencatalogi', 'Kopiëren') }}
			</NcButton>
		</div>
	</div>
</template>

<style scoped>
.copy-object-dialog {
	padding: 20px;
}

.copy-object-dialog__actions {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}
</style>
