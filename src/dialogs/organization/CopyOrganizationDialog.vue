<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="navigationStore.dialog === 'copyOrganization'"
		name="Organisatie kopiëren"
		:can-close="false">
		<div v-if="objectStore.getState('organization').success !== null || objectStore.getState('organization').error">
			<NcNoteCard v-if="objectStore.getState('organization').success" type="success">
				<p>Organisatie succesvol gekopieerd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('organization').success" type="error">
				<p>Er is iets fout gegaan bij het kopiëren van organisatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('organization').error" type="error">
				<p>{{ objectStore.getState('organization').error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading('organization')" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>Organisatie wordt gekopieerd...</span>
		</div>
		<p v-if="objectStore.getState('organization').success === null && !objectStore.isLoading('organization')">
			Wil je <b>{{ objectStore.getActiveObject('organization')?.name }}</b> kopiëren? Dit zal een nieuwe organisatie maken met dezelfde gegevens.
		</p>
		<template v-if="objectStore.getState('organization').success === null && !objectStore.isLoading('organization')" #actions>
			<NcButton 
				:disabled="objectStore.isLoading('organization')" 
				icon="" 
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading('organization')"
				icon="Copy"
				type="primary"
				@click="copyOrganization()">
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
import { Organization } from '../../entities/index.js'

/**
 * Copy organization dialog component
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
	name: 'CopyOrganizationDialog',
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
		 * Copy the active organization
		 *
		 * @return {void}
		 */
		copyOrganization() {
			const activeOrganization = objectStore.getActiveObject('organization')
			if (!activeOrganization?.id) return

			const organizationClone = { ...activeOrganization }
			organizationClone.name = 'KOPIE: ' + organizationClone.name
			if (Object.keys(organizationClone.data).length === 0) {
				delete organizationClone.data
			}
			delete organizationClone.id
			delete organizationClone._id
			organizationClone.status = 'Concept'

			const organizationItem = new Organization({
				...organizationClone,
			})

			objectStore.createObject('organization', organizationItem)
				.then(() => {
					navigationStore.setSelected('organization')
					// Wait for the user to read the feedback then close the dialog
					setTimeout(() => {
						objectStore.setState('organization', { success: null, error: null })
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
