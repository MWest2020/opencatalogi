/**
 * PageIndex.vue
 * Component for displaying the page index
 * @category Components
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
	<div class="page-index">
		<NcEmptyContent v-if="!hasPages" :title="t('opencatalogi', 'Geen pagina\'s gevonden')">
			<template #icon>
				<FolderIcon />
			</template>
		</NcEmptyContent>
		<NcLoadingIcon v-else-if="loading" :size="20" />
		<div v-else class="page-index__content">
			<NcTextField class="page-index__search"
				:value="objectStore.getSearchTerm('page')"
				label="Zoeken"
				trailing-button-icon="close"
				:show-trailing-button="objectStore.getSearchTerm('page') !== ''"
				@update:value="(value) => objectStore.setSearchTerm('page', value)"
				@trailing-button-click="objectStore.clearSearch('page')">
				<template #icon>
					<MagnifyIcon />
				</template>
			</NcTextField>
			<div class="page-index__actions">
				<NcActionButton @click="objectStore.fetchCollection('page')">
					<template #icon>
						<RefreshIcon />
					</template>
					{{ t('opencatalogi', 'Vernieuwen') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('page')">
					<template #icon>
						<PlusIcon />
					</template>
					{{ t('opencatalogi', 'Nieuwe pagina') }}
				</NcActionButton>
			</div>
			<PageList />
		</div>
	</div>
</template>

<script>
// Components
import { NcEmptyContent, NcLoadingIcon, NcTextField, NcActionButton } from '@nextcloud/vue'
import PageList from './PageList.vue'

// Icons
import FolderIcon from 'vue-material-design-icons/Folder.vue'
import MagnifyIcon from 'vue-material-design-icons/Magnify.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'PageIndex',
	components: {
		NcEmptyContent,
		NcLoadingIcon,
		NcTextField,
		NcActionButton,
		PageList,
		FolderIcon,
		MagnifyIcon,
		RefreshIcon,
		PlusIcon,
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		/**
		 * Check if there are any pages
		 * @return {boolean}
		 */
		hasPages() {
			return objectStore.getCollection('page').results.length === 0
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		/**
		 * Fetch the page data
		 * @return {Promise<void>}
		 */
		async fetchData() {
			this.loading = true
			try {
				await objectStore.fetchCollection('page')
			} finally {
				this.loading = false
			}
		},
	},
}
</script>

<style scoped>
.page-index {
	padding: 20px;
}

.page-index__content {
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.page-index__search {
	max-width: 300px;
}

.page-index__actions {
	display: flex;
	gap: 10px;
}
</style>
