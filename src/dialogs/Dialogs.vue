<template>
	<!-- Placeholder div -->
	<div>
		<DeleteCatalogDialog />
		<DeleteAttachmentDialog />
		<PublishAttachmentDialog />
		<DepublishAttachmentDialog />
		<CopyAttachmentDialog />
		<DeleteListingDialog />
		<DeletePublicationTypeDialog />
		<CopyPublicationTypeDialog />
		<DeletePublicationTypePropertiesDialog />
		<CopyPublicationTypePropertiesDialog />
		<DownloadPublicationDialog />
		<DeletePublicationDialog />
		<DeletePublicationDataDialog />
		<CopyPublicationDialog />
		<PublishPublicationDialog />
		<DepublishPublicationDialog />
		<DeleteCatalogDialog />
		<DeleteCatalogiPublicationType />
		<ArchivePublicationDialog />
		<ViewLogDialog />
		<DeleteOrganisationDialog />
		<CopyOrganisationDialog />
		<DeleteThemeDialog />
		<CopyThemeDialog />
	</div>
</template>

<script>
import CopyAttachmentDialog from './attachment/CopyAttachmentDialog.vue'
import DeleteAttachmentDialog from './attachment/DeleteAttachmentDialog.vue'
import DepublishAttachmentDialog from './attachment/DepublishAttachmentDialog.vue'
import PublishAttachmentDialog from './attachment/PublishAttachmentDialog.vue'
import DeleteCatalogDialog from './catalog/DeleteCatalogDialog.vue'
import DeleteCatalogiPublicationType from './catalogiPublicationType/DeleteCatalogiPublicationType.vue'
import DeleteListingDialog from './listing/DeleteListingDialog.vue'
import ViewLogDialog from './logs/ViewLogDialog.vue'
import CopyPublicationTypeDialog from './publicationType/CopyPublicationTypeDialog.vue'
import DeletePublicationTypeDialog from './publicationType/DeletePublicationTypeDialog.vue'
import CopyPublicationTypePropertiesDialog from './publicationTypeProperties/CopyPublicationTypePropertiesDialog.vue'
import DeletePublicationTypePropertiesDialog from './publicationTypeProperties/DeletePublicationTypePropertiesDialog.vue'
import CopyOrganisationDialog from './organisation/CopyOrganisationDialog.vue'
import DeleteOrganisationDialog from './organisation/DeleteOrganisationDialog.vue'
import ArchivePublicationDialog from './publication/ArchivePublicationDialog.vue'
import CopyPublicationDialog from './publication/CopyPublicationDialog.vue'
import DeletePublicationDialog from './publication/DeletePublicationDialog.vue'
import DepublishPublicationDialog from './publication/DepublishPublicationDialog.vue'
import PublishPublicationDialog from './publication/PublishPublicationDialog.vue'
import DeletePublicationDataDialog from './publicationData/DeletePublicationDataDialog.vue'
import CopyThemeDialog from './theme/CopyThemeDialog.vue'
import DeleteThemeDialog from './theme/DeleteThemeDialog.vue'
import DownloadPublicationDialog from './publication/DownloadPublicationDialog.vue'

export default {
	name: 'Dialogs',
	components: {
		DeleteCatalogDialog,
		DeleteCatalogiPublicationType,
		DeleteAttachmentDialog,
		PublishAttachmentDialog,
		DepublishAttachmentDialog,
		CopyAttachmentDialog,
		DeleteListingDialog,
		DeletePublicationTypeDialog,
		CopyPublicationTypeDialog,
		DeletePublicationTypePropertiesDialog,
		CopyPublicationTypePropertiesDialog,
		DeletePublicationDialog,
		CopyPublicationDialog,
		DeletePublicationDataDialog,
		PublishPublicationDialog,
		DepublishPublicationDialog,
		ArchivePublicationDialog,
		ViewLogDialog,
		DeleteOrganisationDialog,
		CopyOrganisationDialog,
		DeleteThemeDialog,
		CopyThemeDialog,
		DownloadPublicationDialog,
	},
}
</script>
