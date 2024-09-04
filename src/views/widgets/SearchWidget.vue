<script setup>
import { searchStore } from '../../store/store.js'
</script>

<template>
	<div>
		<div class="searchContainer">
			<NcTextField class="searchField"
				:value.sync="searchStore.search"
				label="Zoeken" />
			<NcButton type="primary" @click="search">
				Zoeken
			</NcButton>
		</div>
		<NcNoteCard v-if="!searchStore.searchResults?.results?.length > 0 || !searchStore.searchResults" type="info">
			<p>Er zijn op dit moment geen publicaties die aan uw zoekopdracht voldoen</p>
		</NcNoteCard>
		<NcLoadingIcon v-if="!searchStore.searchResults"
			:size="64"
			class="loadingIcon"
			appearance="dark"
			name="Publicaties aan het laden" />
		<SearchList v-if="searchStore.searchResults?.results?.length > 0" />
	</div>
</template>

<script>
// Components
import { NcTextField, NcButton, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import SearchList from '../search/SearchList.vue'

export default {
	name: 'SearchWidget',

	components: {

	},

	data() {
		return {
			loading: false,
		}
	},

	mounted() {
		searchStore.getSearchResults()
	},

	methods: {
		onShow() {
			window.open('/apps/opencatalogi/catalogi', '_self')
		},
		search() {
			searchStore.getSearchResults()
		},
	},

}
</script>
<style>
.searchContainer{
    display: flex;
    gap: 10px;
}
</style>
