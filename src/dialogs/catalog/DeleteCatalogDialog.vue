<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'deleteCatalog'"
		name="Catalogus verwijderen"
		:can-close="false">
		<div v-if="objectStore.getState('catalog').success !== null || objectStore.getState('catalog').error">
			<NcNoteCard v-if="objectStore.getState('catalog').success" type="success">
				<p>Catalogus succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('catalog').success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van catalogus</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('catalog').error" type="error">
				<p>{{ objectStore.getState('catalog').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('catalog')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Catalogus wordt verwijderd...</span>
		</div>
		<p v-if="objectStore.getState('catalog').success === null && !objectStore.isLoading('catalog')">
			Wil je <b>{{ objectStore.getActiveObject('catalog')?.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<template v-if="objectStore.getState('catalog').success === null && !objectStore.isLoading('catalog')" #actions>
			<NcButton
				:disabled="objectStore.isLoading('catalog')"
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('catalog')"
				icon="Delete"
				type="error"
				@click="deleteCatalog()">
				<template #icon>
					<Delete :size="20" />
				</template>
				Verwijderen
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
import Delete from 'vue-material-design-icons/Delete.vue'

/**
 * Delete catalog dialog component
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
	name: 'DeleteCatalogDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	methods: {
		/**
		 * Delete the active catalog
		 *
		 * @return {void}
		 */
		deleteCatalog() {
			const activeCatalog = objectStore.getActiveObject('catalog')
			if (!activeCatalog?.id) return

			objectStore.deleteObject('catalog', activeCatalog.id)
				.then(() => {
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('catalog', { success: null, error: null })
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
