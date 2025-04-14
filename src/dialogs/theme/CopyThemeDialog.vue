<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyTheme'"
		name="Thema kopiëren"
		:can-close="false">
		<div v-if="objectStore.getState('theme').success !== null || objectStore.getState('theme').error">
			<NcNoteCard v-if="objectStore.getState('theme').success" type="success">
				<p>Thema succesvol gekopieerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('theme').success" type="error">
				<p>Er is iets fout gegaan bij het kopiëren van thema</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('theme').error" type="error">
				<p>{{ objectStore.getState('theme').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('theme')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Thema wordt gekopieerd...</span>
		</div>
		<p v-if="objectStore.getState('theme').success === null && !objectStore.isLoading('theme')">
			Wil je <b>{{ objectStore.getActiveObject('theme')?.name }}</b> kopiëren? Dit zal een nieuw thema maken met dezelfde gegevens.
		</p>
		<template v-if="objectStore.getState('theme').success === null && !objectStore.isLoading('theme')" #actions>
			<NcButton 
				:disabled="objectStore.isLoading('theme')" 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('theme')"
				icon="Copy"
				type="primary"
				@click="copyTheme()">
				<template #icon>
					<Copy :size="20" />
				</template>
				Kopiëren
			</NcButton>
		</template>
		<template v-else #actions>
			<NcButton 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'

import Cancel from 'vue-material-design-icons/Cancel.vue'
import Copy from 'vue-material-design-icons/ContentCopy.vue'
import { Theme } from '../../entities/index.js'

/**
 * Copy theme dialog component
 *
 * @category Dialogs
 * @package
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'CopyThemeDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Copy,
	},
	methods: {
		/**
		 * Copy the active theme
		 *
		 * @return {void}
		 */
		copyTheme() {
			const activeTheme = objectStore.getActiveObject('theme')
			if (!activeTheme?.id) return

			const themeClone = { ...activeTheme }
			themeClone.name = 'KOPIE: ' + themeClone.name
			if (Object.keys(themeClone.data).length === 0) {
				delete themeClone.data
			}
			delete themeClone.id
			delete themeClone._id
			themeClone.status = 'Concept'

			const themeItem = new Theme({
				...themeClone,
			})

			objectStore.createObject('theme', themeItem)
				.then(() => {
					navigationStore.setSelected('theme')
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('theme', { success: null, error: null })
						navigationStore.setDialog(false)
					}, 2000)
				})
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
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
