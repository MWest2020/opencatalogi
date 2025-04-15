<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="searchSideBar">
		<div class="searchSideBar-header">
			<h2>Zoeken</h2>
		</div>
		<div class="searchSideBar-content">
			<div class="searchSideBar-content-item">
				<h3>Zoekterm</h3>
				<NcTextField
					:value="objectStore.getSearchTerm('publication')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('publication') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('publication', value)"
					@trailing-button-click="objectStore.clearSearch('publication')">
					<Magnify :size="20" />
				</NcTextField>
			</div>
			<div class="searchSideBar-content-item">
				<h3>Filters</h3>
				<div class="searchSideBar-content-item-filters">
					<div class="searchSideBar-content-item-filters-item">
						<h4>Publicatie type</h4>
						<NcSelect
							:value="objectStore.getFilter('publication', 'publicationType')"
							:options="objectStore.getCollection('publicationType').results.map((type) => ({ label: type.name, value: type.id }))"
							label="Selecteer een publicatie type"
							@update:value="(value) => objectStore.setFilter('publication', 'publicationType', value)" />
					</div>
					<div class="searchSideBar-content-item-filters-item">
						<h4>Catalogi</h4>
						<NcSelect
							:value="objectStore.getFilter('publication', 'catalog')"
							:options="objectStore.getCollection('catalog').results.map((catalog) => ({ label: catalog.name, value: catalog.id }))"
							label="Selecteer een catalogi"
							@update:value="(value) => objectStore.setFilter('publication', 'catalog', value)" />
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { NcTextField, NcSelect } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'

export default {
	name: 'SearchSideBar',
	components: {
		NcTextField,
		NcSelect,
		Magnify,
	},
}
</script>

<style scoped>
.searchSideBar {
	padding: 1rem;
}

.searchSideBar-header {
	margin-bottom: 1rem;
}

.searchSideBar-content-item {
	margin-bottom: 1rem;
}

.searchSideBar-content-item-filters-item {
	margin-bottom: 1rem;
}

h2 {
	margin: 0;
}

h3 {
	margin: 0 0 0.5rem 0;
}

h4 {
	margin: 0 0 0.5rem 0;
}
</style>
