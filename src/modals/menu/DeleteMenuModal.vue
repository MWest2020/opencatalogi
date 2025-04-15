/**
 * DeleteMenuModal.vue
 * Modal for deleting menus
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
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'deleteMenu'"
		ref="modalRef"
		class="deleteMenuModal"
		label-id="deleteMenuModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Menu verwijderen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Menu succesvol verwijderd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het verwijderen van menu</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<p>Weet je zeker dat je het menu '{{ menu.title }}' wilt verwijderen?</p>
			</div>

			<span class="buttonContainer">
				<NcButton
					@click="navigationStore.setModal(false)">
					{{ success ? 'Sluiten' : 'Annuleer' }}
				</NcButton>
				<NcButton v-if="success === null"
					:disabled="loading"
					type="error"
					@click="handleDelete">
					<template #icon>
						<span>
							<NcLoadingIcon v-if="loading" :size="20" />
							<Delete v-if="!loading" :size="20" />
						</span>
					</template>
					Verwijderen
				</NcButton>
			</span>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Loading state for the component
 * @type {import('vue').Ref<boolean>}
 */
const loading = ref(false)

/**
 * Success state for the component
 * @type {import('vue').Ref<boolean|null>}
 */
const success = ref(null)

/**
 * Error state for the component
 * @type {import('vue').Ref<string|null>}
 */
const error = ref(null)

/**
 * Get the active menu from the store
 * @return {object | null}
 */
const menu = computed(() => objectStore.getActiveObject('menu'))

/**
 * Handle delete action
 * @return {Promise<void>}
 */
const handleDelete = async () => {
	loading.value = true
	try {
		await objectStore.deleteObject('menu', menu.value.id)
		success.value = true
	} catch (error) {
		console.error('Error deleting menu:', error)
		success.value = false
		error.value = error.message
	} finally {
		loading.value = false
	}
}

/**
 * Handle cancel action
 * @return {void}
 */
const handleCancel = () => {
	navigationStore.setModal(false)
}

export default {
	name: 'DeleteMenuModal',
	components: {
		NcModal,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
	},
	data() {
		return {
			loading: false,
			success: null,
			error: null,
		}
	},
	methods: {
		closeModal() {
			this.navigationStore.setModal(false)
		},
	},
}
</script>

<style scoped>
.modal__content {
	padding: 20px;
}

.buttonContainer {
	display: flex;
	justify-content: flex-end;
	gap: 10px;
	margin-top: 20px;
}

.form-group {
	display: flex;
	flex-direction: column;
	gap: 10px;
	margin-top: 20px;
}
</style>
