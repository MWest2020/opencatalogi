/**
 * MenuModal.vue
 * Modal for creating and editing menus
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
	<NcModal v-if="navigationStore.modal === 'menu'"
		ref="modalRef"
		:label-id="isEdit ? 'editMenuModal' : 'addMenuModal'"
		@close="closeModal">
		<div class="modal__content">
			<h2>Menu {{ isEdit ? 'bewerken' : 'toevoegen' }}</h2>
			<div v-if="objectStore.getState('menu').success !== null || objectStore.getState('menu').error">
				<NcNoteCard v-if="objectStore.getState('menu').success" type="success">
					<p>{{ isEdit ? 'Menu succesvol bewerkt' : 'Menu succesvol toegevoegd' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="!objectStore.getState('menu').success" type="error">
					<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van het menu' : 'Er is iets fout gegaan bij het toevoegen van het menu' }}</p>
				</NcNoteCard>
				<NcNoteCard v-if="objectStore.getState('menu').error" type="error">
					<p>{{ objectStore.getState('menu').error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="objectStore.getState('menu').success === null && !objectStore.isLoading('menu')" class="form-group">
				<NcTextField
					v-model="menu.title"
					label="Titel*"
					:disabled="objectStore.isLoading('menu')"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					v-model="menu.description"
					label="Beschrijving"
					:disabled="objectStore.isLoading('menu')"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
				<NcCheckboxRadioSwitch
					v-model="menu.published"
					:disabled="objectStore.isLoading('menu')">
					Gepubliceerd
				</NcCheckboxRadioSwitch>
			</div>
			<div v-if="objectStore.isLoading('menu')" class="loading-status">
				<NcLoadingIcon :size="20" />
				<span>{{ isEdit ? 'Menu wordt bewerkt...' : 'Menu wordt toegevoegd...' }}</span>
			</div>
			<NcButton v-if="objectStore.getState('menu').success === null && !objectStore.isLoading('menu')"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('menu')"
				type="primary"
				class="menu-submit-button"
				@click="saveMenu">
				<template #icon>
					<ContentSaveOutline :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import {
	NcButton,
	NcModal,
	NcTextField,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcLoadingIcon,
} from '@nextcloud/vue'

// icons
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import { Menu } from '../../entities/index.js'

export default {
	name: 'MenuModal',
	components: {
		NcModal,
		NcTextField,
		NcCheckboxRadioSwitch,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		ContentSaveOutline,
	},
	data() {
		return {
			menu: {
				title: '',
				description: '',
				published: false,
			},
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('menu')
		},
		inputValidation() {
			const menuItem = new Menu(this.menu)
			const result = menuItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'menu' && !this.hasUpdated) {
			if (this.isEdit) {
				const activeMenu = objectStore.getActiveObject('menu')
				this.menu = { ...activeMenu }
			}
			this.hasUpdated = true
		}
	},
	methods: {
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.menu = {
				title: '',
				description: '',
				published: false,
			}
			objectStore.setState('menu', { success: null, error: null })
		},
		saveMenu() {
			const menuItem = new Menu(this.menu)

			if (this.isEdit) {
				objectStore.updateObject('menu', menuItem.id, menuItem)
					.then(() => {
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('menu', menuItem)
					.then(() => {
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			}
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.menu-submit-button {
    margin-block-start: 1rem;
}

.loading-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin: 1rem 0;
    color: var(--color-text-lighter);
}
</style>

<style scoped>
.form-group {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}
</style>
