/**
 * GlossaryDetails.vue
 * Component for displaying glossary details
 * @category Views
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="glossary-details-container">
		<div v-if="objectStore.isLoading('glossary')" class="loading-container">
			<NcLoadingIcon :size="32" />
		</div>

		<div v-else-if="!objectStore.getActiveObject('glossary')" class="empty-state">
			<p>Selecteer een term om de details te bekijken.</p>
		</div>

		<div v-else class="glossary-details">
			<div class="glossary-details-header">
				<h2>{{ objectStore.getActiveObject('glossary').title }}</h2>
				<div class="glossary-details-actions">
					<NcButton type="tertiary" @click="navigationStore.setModal('glossary')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcButton>
					<NcActionButton @click="navigationStore.setDialog('copyObject', { objectType: 'glossary', dialogTitle: 'Term' })">
						<template #icon>
							<ContentCopy :size="20" />
						</template>
						Kopiëren
					</NcActionButton>
					<NcActionButton @click="navigationStore.setDialog('deleteObject', { objectType: 'glossary', dialogTitle: 'Term' })">
						<template #icon>
							<Delete :size="20" />
						</template>
						Verwijderen
					</NcActionButton>
				</div>
			</div>

			<div class="glossary-details-content">
				<div class="glossary-details-section">
					<h3>Beschrijving</h3>
					<p>{{ objectStore.getActiveObject('glossary').description }}</p>
				</div>

				<div class="glossary-details-section">
					<h3>Definitie</h3>
					<p>{{ objectStore.getActiveObject('glossary').definition }}</p>
				</div>

				<div class="glossary-details-section">
					<h3>Gerelateerde termen</h3>
					<div v-if="objectStore.getActiveObject('glossary').relatedTerms?.length" class="related-terms">
						<NcButton v-for="term in objectStore.getActiveObject('glossary').relatedTerms"
							:key="term.id"
							type="secondary"
							@click="selectTerm(term)">
							{{ term.title }}
						</NcButton>
					</div>
					<p v-else>
						Geen gerelateerde termen
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NcButton, NcLoadingIcon, NcActionButton } from '@nextcloud/vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'

/**
 * Glossary details component
 *
 * @category Views
 * @package
 * @author Your Name
 * @copyright 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/your-repo
 */
export default {
	name: 'GlossaryDetails',
	components: {
		NcButton,
		NcLoadingIcon,
		NcActionButton,
		Pencil,
		Delete,
		ContentCopy,
	},
	methods: {
		/**
		 * Select a related term
		 *
		 * @param {object} term - The term to select
		 * @return {void}
		 */
		selectTerm(term) {
			objectStore.setActiveObject('glossary', term)
		},
	},
}
</script>

<style scoped>
.glossary-details-container {
	padding: var(--OC-margin-20);
}

.loading-container {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 200px;
}

.empty-state {
	text-align: center;
	padding: var(--OC-margin-20);
	color: var(--color-text-lighter);
}

.glossary-details-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: var(--OC-margin-20);
}

.glossary-details-actions {
	display: flex;
	gap: var(--OC-margin-10);
}

.glossary-details-content {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-20);
}

.glossary-details-section {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}

.related-terms {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
}
</style>
