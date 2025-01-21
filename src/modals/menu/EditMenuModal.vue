<script setup>
import { navigationStore, menuStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
</script>

<template>
	<NcDialog v-if="navigationStore.modal === 'editMenu'"
		:name="menuStore.menuItem?.id ? 'Edit Menu' : 'Add Menu'"
		size="normal"
		:can-close="false">
		<NcNoteCard v-if="success" type="success">
			<p>Menu succesvol bewerkt</p>
		</NcNoteCard>
		<NcNoteCard v-if="error" type="error">
			<p>{{ error }}</p>
		</NcNoteCard>

		<template #actions>
			<NcButton
				@click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success ? 'Sluiten' : 'Annuleren' }}
			</NcButton>
			<NcButton v-if="!success"
				:disabled="loading"
				type="primary"
				@click="editMenu()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading && menuStore.menuItem?.id" :size="20" />
					<Plus v-if="!loading && !menuStore.menuItem?.id" :size="20" />
				</template>
				{{ menuStore.menuItem?.id ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</template>

		<div v-if="!success" class="formContainer">
			<NcTextField
				:disabled="loading"
				label="Naam"
				:value.sync="menuItem.name"
				:error="!!inputValidation.fieldErrors?.['name']"
				:helper-text="inputValidation.fieldErrors?.['name']?.[0]" />
			<NcInputField
				:disabled="loading"
				label="Positie"
				type="number"
				:value.sync="menuItem.position"
				:error="!!inputValidation.fieldErrors?.['position']"
				:helper-text="inputValidation.fieldErrors?.['position']?.[0]" />
			<div :class="`codeMirrorContainer ${getTheme()}`">
				<CodeMirror
					v-model="menuItem.items"
					:basic="true"
					placeholder="[{ &quot;key&quot;: &quot;value&quot; }]"
					:dark="getTheme() === 'dark'"
					:tab="true"
					:gutter="true"
					:linter="jsonParseLinter()"
					:lang="json()" />
				<NcButton class="prettifyButton" :disabled="!menuItem.items || !verifyJsonValidity(menuItem.items)" @click="prettifyJson">
					<template #icon>
						<AutoFix :size="20" />
					</template>
					Prettify
				</NcButton>
			</div>
		</div>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcLoadingIcon,
	NcNoteCard,
	NcInputField,
	NcTextField,
} from '@nextcloud/vue'
import CodeMirror from 'vue-codemirror6'
import { json, jsonParseLinter } from '@codemirror/lang-json'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import AutoFix from 'vue-material-design-icons/AutoFix.vue'

import { Menu } from '../../entities/index.js'

/**
 * Component for editing menu items
 */
export default {
	name: 'EditMenuModal',
	components: {
		NcDialog,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcTextField,
		NcInputField,
		// Icons
		ContentSaveOutline,
		Cancel,
		Plus,
	},
	data() {
		return {
			menuItem: {
				name: '',
				position: 0,
				items: '',
			},
			success: null,
			loading: false,
			error: false,
			hasUpdated: false,
			closeDialogTimeout: null,
		}
	},
	computed: {
		inputValidation() {
			const menuItem = new Menu({
				...this.menuItem,
			})

			const result = menuItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		this.initializeMenuItem()
	},
	updated() {
		if (navigationStore.modal === 'editMenu' && !this.hasUpdated) {
			this.initializeMenuItem()
			this.hasUpdated = true
		}
	},
	methods: {
		/**
		 * Initialize menu item data from store
		 */
		initializeMenuItem() {
			if (menuStore.menuItem?.id) {
				this.menuItem = {
					...menuStore.menuItem,
					items: typeof menuStore.menuItem.items === 'string' ? menuStore.menuItem.items : JSON.stringify(menuStore.menuItem.items, null, 2),
				}
			}
		},
		/**
		 * Close the dialog and reset state
		 */
		closeModal() {
			navigationStore.setModal(false)
			clearTimeout(this.closeModalTimeout)
			this.success = null
			this.loading = false
			this.error = false
			this.hasUpdated = false
			this.menuItem = {
				name: '',
				position: 0,
				items: '',
			}
		},
		/**
		 * Save menu item changes
		 */
		async editMenu() {
			this.loading = true

			const menuItem = new Menu({
				...this.menuItem,
				items: this.menuItem.items ? JSON.parse(this.menuItem.items) : [],
			})

			menuStore.saveMenu(menuItem).then(({ response }) => {
				this.success = response.ok
				this.error = false
				response.ok && (this.closeModalTimeout = setTimeout(this.closeModal, 2000))
			}).catch((error) => {
				this.success = false
				this.error = error.message || 'An error occurred while saving the menu'
			}).finally(() => {
				this.loading = false
			})
		},

		prettifyJson() {
			this.menuItem.items = JSON.stringify(JSON.parse(this.menuItem.items), null, 2)
		},
		verifyJsonValidity(jsonInput) {
			if (jsonInput === '') return true
			try {
				JSON.parse(jsonInput)
				return true
			} catch (e) {
				return false
			}
		},
	},
}
</script>
