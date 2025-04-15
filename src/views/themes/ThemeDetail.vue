/**
 * ThemeDetail.vue
 * Component for displaying theme details
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
	<div class="theme-details-container">
		<div v-if="objectStore.isLoading('theme')" class="loading-container">
			<NcLoadingIcon :size="32" />
		</div>

		<div v-else-if="!objectStore.getActiveObject('theme')" class="empty-state">
			<p>Selecteer een thema om de details te bekijken.</p>
		</div>

		<div v-else class="theme-details">
			<div class="theme-details-header">
				<h2>{{ objectStore.getActiveObject('theme').title }}</h2>
				<div class="theme-details-actions">
					<NcButton type="tertiary" @click="navigationStore.setModal('theme')">
						<template #icon>
							<Pencil :size="20" />
						</template>
						Bewerken
					</NcButton>
					<NcActionButton @click="navigationStore.setDialog('deleteObject', { objectType: 'theme', dialogName: 'deleteObject', displayName: 'Thema' })">
						<template #icon>
							<Delete :size="20" />
						</template>
						{{ t('opencatalogi', 'Verwijderen') }}
					</NcActionButton>
				</div>
			</div>

			<div class="theme-details-content">
				<div class="theme-details-section">
					<h3>Beschrijving</h3>
					<p>{{ objectStore.getActiveObject('theme').description }}</p>
				</div>

				<div class="theme-details-section">
					<h3>Stijl</h3>
					<p>{{ objectStore.getActiveObject('theme').style }}</p>
				</div>

				<div class="theme-details-section">
					<h3>Kleuren</h3>
					<div v-if="objectStore.getActiveObject('theme').colors?.length" class="theme-colors">
						<div v-for="color in objectStore.getActiveObject('theme').colors"
							:key="color.id"
							class="color-block"
							:style="{ backgroundColor: color.value }"
							:title="color.name">
							<span class="color-name">{{ color.name }}</span>
						</div>
					</div>
					<p v-else>
						Geen kleuren gedefinieerd
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

export default {
	name: 'ThemeDetail',
	components: {
		NcButton,
		NcLoadingIcon,
		NcActionButton,
		Pencil,
		Delete,
	},
}
</script>

<style scoped>
.theme-details-container {
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

.theme-details-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: var(--OC-margin-20);
}

.theme-details-actions {
	display: flex;
	gap: var(--OC-margin-10);
}

.theme-details-content {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-20);
}

.theme-details-section {
	display: flex;
	flex-direction: column;
	gap: var(--OC-margin-10);
}

.theme-colors {
	display: flex;
	flex-wrap: wrap;
	gap: var(--OC-margin-10);
}

.color-block {
	width: 100px;
	height: 100px;
	border-radius: 8px;
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
	overflow: hidden;
}

.color-name {
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	background: rgba(0, 0, 0, 0.5);
	color: white;
	padding: 4px;
	text-align: center;
	font-size: 12px;
}
</style>
