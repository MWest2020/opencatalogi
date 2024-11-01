<script setup>
import { searchStore, publicationTypeStore } from '../../store/store.js'
</script>

<template>
	<ul>
		<NcListItem
			v-for="(result, i) in searchStore.searchResults.results"
			:key="`${result}${i}`"
			:name="result.title || 'Geen titel'"
			:subname="result.summary || 'Geen samenvatting'"
			:details="getPublicationTypeTitle(result.publicationType) || 'Geen publicatietype'"
			:bold="false"
			:force-display-actions="true"
			:counter-number="result.attachment_count || 0">
			<template #icon>
				<ListBoxOutline :size="44" />
			</template>
			<template #actions>
				<NcActionButton v-if="result.portal" @click="openLink(result.portal, '_blank')">
					<template #icon>
						<OpenInNew :size="20" />
					</template>
					Open portal page
				</NcActionButton>
				<NcActionButton @click="publicationStore.setPublicationItem(result); navigationStore.setSelected('publication')">
					<template #icon>
						<OpenInApp :size="20" />
					</template>
					Bekijken
				</NcActionButton>
			</template>
		</NcListItem>
	</ul>
</template>
<script>
import { NcListItem, NcActionButton } from '@nextcloud/vue'

// Icons
import ListBoxOutline from 'vue-material-design-icons/ListBoxOutline.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import OpenInNew from 'vue-material-design-icons/OpenInNew.vue'

export default {
	name: 'SearchList',
	components: {
		NcListItem,
		NcActionButton,
		// Icons
		ListBoxOutline,
		OpenInNew,
	},
	mounted() {
		publicationTypeStore.refreshPublicationTypeList()
	},
	methods: {
		openLink(link, type = '') {
			window.open(link, type)
		},

		getPublicationTypeTitle(source) {
			if (!publicationTypeStore.publicationTypeList) return
			const publicationTypeObject = publicationTypeStore.publicationTypeList.find((publicationType) => publicationType.source ? publicationType.source === source : publicationType.id === source)

			return publicationTypeObject?.title
		},

	},
}
</script>
