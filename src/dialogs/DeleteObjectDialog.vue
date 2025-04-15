/**
 * DeleteObjectDialog.vue
 * Generic dialog for deleting objects
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
	 * Type of object to delete
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
	/**
	 * Whether this is a multiple delete operation
	 * @type {boolean}
	 */
	isMultiple: {
		type: Boolean,
		default: false,
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
 * Handle delete action
 * @return {Promise<void>}
 */
const handleDelete = async () => {
	loading.value = true
	try {
		if (props.isMultiple) {
			await objectStore.deleteMultipleObjects(props.objectType)
		} else {
			await objectStore.deleteObject(props.objectType, activeObject.value)
		}
		navigationStore.setDialog(false)
	} catch (error) {
		console.error(`Error deleting ${props.objectType}:`, error)
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
	<div class="delete-object-dialog">
		<p>
			{{ t('opencatalogi', 'Wil je') }}
			<b v-if="!isMultiple">{{ activeObject?.title || activeObject?.name }}</b>
			{{ t('opencatalogi', isMultiple ? 'de geselecteerde items' : '') }}
			{{ t('opencatalogi', 'verwijderen? Deze actie kan niet ongedaan worden gemaakt.') }}
		</p>
		<div class="delete-object-dialog__actions">
			<NcButton :disabled="loading" @click="handleCancel">
				{{ t('opencatalogi', 'Annuleren') }}
			</NcButton>
			<NcButton type="primary" :disabled="loading" @click="handleDelete">
				{{ t('opencatalogi', 'Verwijderen') }}
			</NcButton>
		</div>
	</div>
</template>

<style scoped>
.delete-object-dialog {
	padding: 20px;
}

.delete-object-dialog__actions {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}
</style>
