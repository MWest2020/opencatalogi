# Publiccode

OpenCatalogi ondersteunt het gebruik van de Publiccode-standaard. Dit betekent dat we ervan uitgaan dat alle publicaties in een publieke repository staan en zijn voorzien van een publiccode.yaml bestand. Dit bestand bevat de metadata en informatie van je publicatie, zoals naam, beschrijving en type. Het helpt het platform om jouw component te indexeren en gemakkelijk te vinden voor andere gebruikers.

> [!Note]
> OpenCatalogi scant GitHub elke nacht, als je een component sneller wilt aanmelden of bijwerken, kan dat door naar [opencatalogi.nl](https://opencatalogi.nl/documentation/usage/) te gaan en hier het formulier in te vullen om een repository toe te voegen. Deze trigger wordt ook afgevuurd door de workflow.

> [!Warning]
> Let op, OpenCatalogi bevraagt bij het inlezen van een repository de GitHub Search API, deze geeft alleen goede resultaten terug wanneer de repository is geïndexeerd. Dit kan worden geforceerd door een zoekopdracht uit te voeren op de GitHub website. Gebruik hiervoor de GitHub zoekbalk met bijvoorbeeld `repo:{{Uw Organisatie/Gebruiker}}/{{Uw repository}} publiccode` en wacht tot deze opdracht resultaten geeft. Dit kan zo'n 10 minuten duren en daarbij moet de pagina enige keren ververst worden.

## Maken met workflow

Vanuit het OpenCatalogi-project is een GitHub-workflow beschikbaar die een publiccode.yaml bestand aanmaakt, bijwerkt en het federatieve netwerk op de hoogte stelt van eventuele wijzigingen in de beschrijving van uw organisatie.

U kunt deze workflow op de volgende manier gebruiken:

> 1. Maak binnen de repository van uw component een directory aan met de naam `.github` (als u deze nog niet heeft).
> 2. Maak binnen deze directory een map `workflows` aan, die zelf binnen een `.github` map hoort te zitten. Plaats daarin [deze workflow.yaml](https://github.com/marketplace/actions/create-or-update-publiccode-yaml#usage).
> 3. Commit en push het workflow-bestand naar jouw repository.

## Handmatig Maken

U kunt er ook voor kiezen om handmatig een publicorganisation.yaml-bestand in uw repository op te nemen. Houdt er in dat geval rekening mee dat het tot 24 uur kan duren voordat wijzigingen in het federatieve netwerk zichtbaar worden.

> 1. Maak een `publiccode.yaml` bestand in de root van jouw repository met een teksteditor of een geïntegreerde ontwikkelomgeving (IDE).
> 2. Voeg de vereiste metadata toe aan het `publiccode.yaml` bestand. Een voorbeeld van een basisstructuur tref je hieronder.
> 3. Voeg eventuele aanvullende metadata toe die relevant kan zijn voor jouw component, zoals documentatie, afhankelijkheden, contactinformatie of onderhoudsinformatie.
> 4. Commit en push het `publiccode.yaml` bestand naar jouw repository. Houd er rekening mee dat het de eerste keer tot 24 uur kan duren voordat OpenCatalogi je component indexeert.

## Voorbeeld

```yaml
publiccodeYmlVersion: "0.2"
# Pas dit voorbeeld aan op basis van de specificaties van jouw component. Een volledige beschrijving van de publiccode standaard vind je op [yml.publiccode.tools](https://yml.publiccode.tools/schema.core.html#top-level-keys-and-sections) 
name: Medusa
url: "https://example.com/italia/medusa.git"
softwareVersion: "dev"    # Optional
releaseDate: "2017-04-15"
platforms:
  - web

categories:
  - financial-reporting

developmentStatus: development

softwareType: "standalone/desktop"

description:
  en:
    localisedName: medusa   # Optional
    shortDescription: >
          A short description of the software.
          
    longDescription: >
          Very long description of this software, also split
          on multiple rows. You should note what the software
          is and why one should need it. We can potentially
          have many pages of text here.

    features:
       - Just one feature

legal:
  license: AGPL-3.0-or-later

maintenance:
  type: "community"

  contacts:
    - name: Francesco Rossi

localisation:
  localisationReady: true
  availableLanguages:
    - en
# De Nederlandse uitbreiding op de Common Ground standaard
nl:
  countryExtensionVersion: "1.0"
  commonground:
  - layerType: "interface"
  - installationType: "helm"
  - intendedOrganisations: "https://github.com/Rotterdam"
  gemma:
    bedrijfsfuncties:
      - "sadsad"
      - "sadsad"
    bedrijfsservices:
      - "sadsad"
      - "sadsad"
    applicatiefunctie: "referentie component"
```

## Zijn er mininmum eisen aan een publiccode?

Nee, de publiccode.yaml mag zelfs leeg zijn. Puur het plaatsen daarvan in een open toegankenlijke repository spreekt de intentie uit om een opensource oplossing aan te bieden en is voldoende om te worden mee genomen in de indexatie. In het geval bepaalde gegevens missen worden deze aangevuld vanuit de repository (naam, beschrijving, organisatie, url, licentie).

## Welke velden kan ik verwachten in een publiccode?

In een publiccode.yaml bestand zijn er verschillende properties die gedefinieerd kunnen worden om verschillende aspecten van de software of het project te beschrijven. Deze properties variëren van het geven van basisinformatie zoals de naam van de software, tot meer specifieke informatie zoals de gebruikte licentie of de ontwikkelstatus van de software. De volgende tabel geeft een overzicht van de mogelijke properties, of ze verplicht zijn of niet, wat het verwachte type input is en een korte beschrijving van elk.

Hier is een voorbeeld van hoe de tabel eruit kan zien, gebaseerd op de standaard die wordt beschreven op [yml.publiccode.tools](https://yml.publiccode.tools) en uitgewerkt onder [top level formats](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#top-level-keys-and-sections) op docs.italia.it.:

| Property             | Verplicht | Verwachte Input | Default                                                            | Enum                                                                                                                                           | Voorbeeld                                 | Beschrijving                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
|----------------------|-----------|-----------------|--------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| publiccodeYmlVersion | Nee       | String (SEMVER)  | 0.2                                                                | Nee                                                                                                                                            | 0.2                                       | This key specifies the version to which the current publiccode.yml adheres to, for forward compatibility.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| name                 | Nee       | String          | De naam ven de repository waarin de public code is gevonden        | Nee                                                                                                                                            | Medusa                                    | This key contains the name of the software. It contains the (short) public name of the product, which can be localised in the specific localisation section. It should be the name most people usually refer to the software. In case the software has both an internal "code" name and a commercial name, use the commercial name.                                                                                                                                                                                                                                                                                                |
| applicationSuite     | Nee       | String          | n.v.t                                                              | Nee                                                                                                                                            | MegaProductivitySuite                     | This key contains the name of the "suite" to which the software belongs.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| url                  | Nee       | String (URL)     | De url van de repository waarin de public code is gevonden         | Nee                                                                                                                                            | https://example.com/italia/medusa.git     | A unique identifier for this software. This string must be a URL to the source code repository (git, svn, ...) in which the software is published. If the repository is available under multiple protocols, prefer HTTP/HTTPS URLs which don't require user authentication. Forks created for the purpose of contributing upstream should not modify this file; this helps software parsing publiccode.yml to immediately skip technical forks. On the contrary, a complete fork that is meant to be maintained separately from the original software should modify this line, to give themselves the status of a different project. |
| landingURL           | Nee       | String (URL)     | De url onder repository settings (indien opgegeven)                | Nee                                                                                                                                            | https://example.com/italia/medusa         | If the url parameter does not serve a human readable or browsable page, but only serves source code to a source control client, with this key you have an option to specify a landing page. This page, ideally, is where your users will land when they will click a button labeled something like "Go to the application source code". In case the product provides an automated graphical installer, this URL can point to a page which contains a reference to the source code but also offers the download of such an installer.                                                                                               |
| isBasedOn            | Nee       | String (URL)     | N.v.t.                                                             | Nee                                                                                                                                            | https://example.com/italia/medusa.gi      | In case this software is a variant or a fork of another software, which might or might not contain a publiccode.yml file, this key will contain the url of the original project(s).The existence of this key identifies the fork as a software variant, descending from the specified repositories..                                                                                                                                                                                                                                                                                                                               |
| softwareVersion      | Nee       | String (SEMVER)  | N.v.t.                                                             | Nee                                                                                                                                            | 1.0                                       | This key contains the latest stable version number of the software. The version number is a string that is not meant to be interpreted and parsed but just displayed; parsers should not assume semantic versioning or any other specific version format.The key can be omitted if the software is currently in initial development and has never been released yet.                                                                                                                                                                                                                                                               |
| releaseDate          | Nee       | String (DATE)    | De creatie datum van de repository (indien opgegeven)              | Nee                                                                                                                                            | 2023-01-01                                | This key contains the date at which the latest version was released. This date is mandatory if the software has been released at least once and thus the version number is present.                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| createdDate          | Nee       | String (DATE)    | De creatie datum van de repository (indien opgegeven)              | Nee                                                                                                                                            | 2023-01-01                                | This key contains the date at which the latest version was released. This date is mandatory if the software has been released at least once and thus the version number is present.                                                                                                                                                                                                                                                                                                                                                                                                                                                |
| logo                 | Nee       | String          | De afbeedling van de repository (indien opgegeven)                 | Nee                                                                                                                                            | img/logo.svg                              | This key contains the path to the logo of the software. Logos should be in vector format; raster formats are only allowed as a fallback. In this case, they should be transparent PNGs, minimum 1000px of width. The key value can be the relative path to the file starting from the root of the repository, or it can be an absolute URL pointing to the logo in raw version. In both cases, the file must reside inside the same repository where the publiccode.yml file is stored.                                                                                                                                            |
| monochromeLogo       | Nee       | String          | N.v.t.                                                             | Nee                                                                                                                                            | img/logo-mono.svg                         | A monochromatic (black) logo. The logo should be in vector format; raster formats are only allowed as a fallback. In this case, they should be transparent PNGs, minimum 1000px of width. The key value can be the relative path to the file starting from the root of the repository, or it can be an absolute URL pointing to the logo in raw version. In both cases, the file must reside inside the same repository where the publiccode.yml file is stored.                                                                                                                                                                   |
| platforms            | Nee       | Lijst           | N.v.t.                                                             | web, windows, mac, linux, ios, android, haven,kubernetes,azure,aws                                                                             | 0.2                                       | This key specifies which platform the software runs on. It is meant to describe the platforms that users will use to access and operate the software, rather than the platform the software itself runs on.Use the predefined values if possible. If the software runs on a platform for which a predefined value is not available, a different value can be used.                                                                                                                                                                                                                                                                 |
| categories           | Nee       | Lijst           | N.v.t.                                                             | Any of [the catagories list](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/categories-list.html#categories-list). | 0.2                                       | A list of words that can be used to describe the software and can help building catalogs of open software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| usedBy               | Nee       | Lijst           | N.v.t.                                                             | Nee | n.v.t | Websites of github organisatie pagina's van organisaties die dit component gebruiken                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| supportedBy          | Nee       | Lijst           | N.v.t.                                                             | Nee | n.v.t | Websites of github organisatie pagina's van organisaties die services en diensten afgeven op dit component                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         |
| roadmap              | Nee       | String (URL)     | N.v.t.                                                             | Nee                                                                                                                                            | https://example.com/italia/medusa/roadmap | A link to a public roadmap of the software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| developmentStatus    | Nee       | String          | N.v.t.                                                             | 'concept', 'development', 'beta', 'stable', 'obsolete'                                                                                                                                             | stable                                     | De huidige ontwikkelstatus van de software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| softwareType         | Nee       | String          | N.v.t.                                                             | 'standalone/mobile', 'standalone/iot', 'standalone/desktop', 'standalone/web', 'standalone/backend', 'standalone/other', 'addon', 'library', 'configurationFiles'                                                                                                                                             | 0.2                                       | Het type software (e.g., standalone, library, etc.).                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| intendedaudience     | Nee       | Object          | N.v.t.                                                             | Nee                                                                                                                                            | n.v.t.                                     | Bevat de licentie onder welke de software is vrijgegeven.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| description          | Nee       | Object          | De beschrijving van de repository waarind e publiccode is gevonden | Nee                                                                                                                                            | 0.2                                       | Bevat gelokaliseerde namen en beschrijvingen van de software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      |
| legal                | Nee       | Object          | De licentie van de repository (indien opgegeven)                   | Nee                                                                                                                                            | n.v.t.                                     | Bevat de doelgroepen voor de applicatie.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| maintenance          | Nee       | Object          | N.v.t.                                                             | Nee                                                                                                                                            | n.v.t.                                     | Bevat onderhoudsinformatie voor de software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| localisation         | Nee       | Object          | N.v.t.                                                             | Nee                                                                                                                                            | n.v.t.                                     | Bevat informatie over de beschikbare talen van de software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| dependsOn            | Nee       | Object          | N.v.t.                                                             | Nee                                                                                                                                            | n.v.t.                                     | Bevat de afhankenlijkheden (componenten) van de applicatie.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| nl                   | Nee       | object          | N.v.t.                                                             | Nee                                                                                                                                            | n.v.t.                                    | A link to a public roadmap of the software.                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        |
| inputTypes           | Nee       | array (String)   | N.v.t.                                                             | as per RFC 6838
| outputTypes          | Nee       | array (String)   | N.v.t.                                                             | as per RFC 6838
| hidden               | Nee       | Object          | N.v.t.                                                             | Nee | n.v.t. |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| downloads            | Nee       | Object          | N.v.t.                                                             | Nee | n.v.t. |                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    |
| upl                     | Nee       | array (String)   | N.v.t.   | N.v.t.  | One or more from the [UPL list](https://standaarden.overheid.nl/upl), defines products provided by this sotware
| bedrijfsfuncties | Nee       | Array (STRING)   | n.v.t   | n.v.t                                  | Een of meerdere [bedrijfsfuncties](https://www.gemmaonline.nl/index.php/GEMMA_Bedrijfsfuncties)
| bedrijfsservices                 | Nee       | Array (STRING)            | n.v.t.  | n.v.t                                  | Een of meerdere \[bedrijfsservices]

Dat laat dus een aantal mogelijke subobjecten over:

### description

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-description).

### intended audience

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-intendedaudience).

### description

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-description).

### legal

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-legal).

### maintenance

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-maintenance).

### localisation

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-localisation).

### dependsOn

Conform specs [description](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/schema.core.html#section-dependson).

### nl

Een (concept) Nederlandse uitbreiding op de publiccode standaard in lijn met de [mogelijkheid tot regionale uitbreidingen](https://docs.italia.it/italia/developers-italia/publiccodeyml-en/en/master/country.html#italy).

| Property                | Verplicht | Verwachte Input | Default  | Enum | Beschrijving                                                                                                                                                                                                                                                                                                                                                                |
|-------------------------|-----------|-----------------|----------|------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| countryExtensionVersion | Ja        | String (SEMVER)  | N.v.t.   | N.v.t.  | This key specifies the version to which the current extension schema adheres to, for forward compatibility.Please note how the value of this key is independent from the top-level publiccodeYmlVersion one (see The Standard (core)). In such a way, the extensions schema versioning is independent both from the core version of the schema and from every other Country. |
| commonground            | Nee       | String          | N.v.t.   |N.v.t.| An object describing the commonground attributes of this software, look bellow for the object definitions.                                                                                                                                                                                                                                                                  |
| gemma                   | Nee       | String (URL)     | N.v.t.   | N.v.t.  | An object describing the GEMMA attributes of this software, look bellow for the object definitions.                                                                                                                                                                                                                                                                                                                  |
| upl                     | Nee       | array (String)   | N.v.t.   | N.v.t.  | One or more from the [UPL list](https://standaarden.overheid.nl/upl), defines products provided by this sotware                                                                                                                                                                                                                                                             |                                                                                                                                                                                                                                                             |                                                                                                                                                                                                                                                                                                                                                    |

Dit leidt tot de volgende subobjecten:

#### Commonground

| Property             | Verplicht | Verwachte Input | Default | Enum                                           | Beschrijving                                                                                                                                                                                                                                                                                                                                                                 |
|----------------------|-----------|----------------|---------|------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| intendedOrganisations | Nee       | Array          | n.v.t   | n.v.t.                                         | This key specifies the version to which the current extension schema adheres to, for forward compatibility.Please note how the value of this key is independent from the top-level publiccodeYmlVersion one (see The Standard (core)). In such a way, the extensions schema versioning is independent both from the core version of the schema and from every other Country. |
| installationType                 | Nee       | String         | n.v.t.  | self, helm, provision                          | Defines how the software should be installed                                                                                                                                                                                                                                                                                                                                 |
| layerType                  | Nee       | String     | n.v.t   | interface, integration, data, service, process | An extension to public code based on the componentencatalogus. Refers to the layer on wich the component oprates.                                                                                                                                                                                                                                                            |

#### Gemma

| Property             | Verplicht | Verwachte Input | Default | Enum                                   | Beschrijving                                                                                                                                   |
|----------------------|-----------|-----------------|---------|----------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| bedrijfsfuncties | Nee       | Array (STRING)   | n.v.t   | n.v.t                                  | Een of meerdere [bedrijfsfuncties](https://www.gemmaonline.nl/index.php/GEMMA_Bedrijfsfuncties)                                                |
| bedrijfsservices                 | Nee       | Array (STRING)            | n.v.t.  | n.v.t                                  | Een of meerdere \[bedrijfsservices]                                                                                                             |
| applicatiefunctie                  | Nee       | String          | n.v.t   | n.v.t                                  | Een van [de mogenlijke applicatie functies](https://www.gemmaonline.nl/index.php/GEMMAkennismodel/1.0/id-35825388-05d9-45aa-98f4-86dfb82337f5) |
| model                  | Nee       | String          | n.v.t   | semantic, conceptual,logical, physical | Het soort model (mag alleen worden gebruikt als het type schema is).                                                                           |

In theorie zijn er meer mogelijke Nederlandse utibreidingen te bedenken, maar voor fase 1 hebben we ons bewust tot het bovenstaande beperkt.

## Zijn er uitbreidingen op en afwijkingen van de publiccode standaard?

We hebben op verschillende plekken afgeweken en uitgebreid op de publiccode standaard, met namen omdat deze te beperkend bleek. We hebben er overal voor gekozen om aan te vullen of eisen te verlagen. Dat betekent dat een (volgens de standaard) geldige publiccode.yaml ook voor OC werkt maar dat je aanvullende informatie zou kunnen opnemen.

Bij het veld softwareType ondersteunen we extra mogelijkheden

| Software Type         | Beschrijving                                                                                       |
|-----------------------|---------------------------------------------------------------------------------------------------|
| standalone/mobile     | The software is a standalone, self-contained. The software is a native mobile app.                |
| standalone/iot        | The software is suitable for an IoT context.                                                      |
| standalone/desktop    | The software is typically installed and run in a a desktop operating system environment.          |
| standalone/web        | The software represents a web application usable by means of a browser.                           |
| standalone/backend    | The software is a backend application.                                                            |
| standalone/other      | The software has a different nature from the ones listed above.                                   |
| softwareAddon         | The software is an addon, such as a plugin or a theme, for a more complex software.               |
| library               | The software contains a library or an SDK to make it easier to third party developers.            |
| configurationFiles    | The software does not contain executable script but a set of configuration files.                 |
| api                   | The repository/folder doesn't contain software but an OAS api description.                        |
| schema                | The repository/folder doesn't contain software but a schema.json object description.              |
| data                  | The repository/folder doesn't contain software but a public data file (e.g. csv, xml etc).        |
| process               | The repository/folder doesn't contain software but an executable process (e.g. bpmn2, camunda).   |
| model                 | The repository/folder doesn't contain software but a model (e.g. uml).                            |

Bij het veld platforms ondersteunen we extra opties "haven", "kubernetes", "azure", "aws"

Daarnaast zijn in de normale versie van de standaard de velden "publiccodeYmlVersion", "name", "url" verplicht en kent Public Code vanuit de standaard geen default values (die wij onttrekken aan de repository)

Bij logo laten we naast een relatief pad ook een absolute URL naar het logo toe.

## Monorepo

Het kan voorkomen dat uw organisatie code en documenten niet over meerdere repositories verdeeld maar alles opslaat in één repository, een zogenoemde [monorepo](https://en.wikipedia.org/wiki/Monorepo). In dat geval is het mogenlijk om meerdere Open Catalogi publicaties vanuit dezelfde repository te publiceren. Voor het het publiceren van een tweede publicatie kunt u simpelweg een tweede publiccode.yaml in de repository toevoegen (let er hierbij wel op dat er geen twee publiccode bestanden in één folder kunnen staan).

Let er wel op de alle verijkings functies op repositorie niveau gaan, met andere woorden als bijvoorbeeld de beschrijving in de publiccode zal die worden overgenomen vanuti de repository. Het zelfde geld ook voor de punten beoordeling van de publicatie.

## Parsing en verschil tussen publiccode.yaml en Open Catalogi datamodel

Zo als je welicht opvalt wijkt de publicode.yaml af van de Open catalogi api en data model, dat komt omdat voor een aantal properties binnen Open Catalogi in het datamodel objecten worden gebruikt. Bij het inlezen van de publiccode worden de daar aangetroffen waarden omgezet naar objecten en indien nodig verijkt. Dit betreft

| Property             |
|-----------------------|
| applicationSuite |
| url |
| usedBy |
| supportedBy |
| rating |
| downloads |
