/* eslint-disable no-console */
// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import pinia from '../pinia.js'
import { useCatalogiStore } from './modules/catalogi.ts'
import { useConfigurationStore } from './modules/configuration.ts'
import { useDirectoryStore } from './modules/directory.ts'
import { useMetadataStore } from './modules/metadata.ts'
import { useNavigationStore } from './modules/navigation.js'
import { useOrganisationStore } from './modules/organisation.js'
import { usePublicationStore } from './modules/publication.js'
import { useSearchStore } from './modules/search.js'
import { useThemeStore } from './modules/theme.js'

const navigationStore = useNavigationStore(pinia)
const searchStore = useSearchStore(pinia)
const catalogiStore = useCatalogiStore(pinia)
const directoryStore = useDirectoryStore(pinia)
const metadataStore = useMetadataStore(pinia)
const publicationStore = usePublicationStore(pinia)
const organisationStore = useOrganisationStore(pinia)
const themeStore = useThemeStore(pinia)
const configurationStore = useConfigurationStore(pinia)

export {
	// generic
	navigationStore,
	searchStore,
	// feature-specific
	catalogiStore,
	directoryStore,
	metadataStore,
	publicationStore,
	organisationStore,
	themeStore,
	configurationStore,
}
