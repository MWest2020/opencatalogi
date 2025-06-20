_Dit is een transcript en dient als input voor het verslag van de kwartaalbijeenkomst van de OpenCatalogi community van 23-01-2025_

**Locatie**: ICTU
**Aanwezigen**: Jaap van Vliet, Steven Gort, Martin de Bijl, Ronald Kok, Maurice te Wispelaere, Gordon Thomsett, RCortenberghe, Jaap Huib  
**Afwezigen**: @@

**Opening**: 

Jaap: Wat wil je ophalen in deze sessie?

- Jaap: Use-cases om te demonstreren op BZK - ICTU - Innovatiedag 13 februari
- Ronald K (RK): Doorontwikkeling community
- Steven: Snappen hoe je (OpenCatalogi) OC aan de praat krijgt
- Ronald C (RC): Hoe organiseren we dat hulpvragen in de community kunnen landen
- Jaap Huib (JH): Update waar we nu staan met OC
- Maurice: Wat ondersteunt deze tool en zijn we er al? Wat zijn de ambities. High-value.
- Gordon: Waar past deze tooling in ons metadata landschap en wat hebben we eraan.

RC: ontstaan OpenCatalogi vanuit Rotterdam: Hebben wij al een applicatie voor deze functionaliteit? Probleem: diverse bronnen (CMDB. software catalogi intern/extern, in productie hebben). We gaan voor 5 laags model CG. Te veel werk om antwoord te geven. Ik wil op 1 plek een vraag stellen en dan antwoord krijgen uit die bronnen (Trivago like..). Trivago voor allerlei soorten objecten die vallen in het 5 laags model. Dit bereiken door standaarden toe te passen naar object types. 

Maurice: is het een framework en zoek het zelf uit en zaken die missen toevoegen. 
RC: Ooit begonnen met publiccodes, later dataset/dcat, in gesprek met Avola (Decision Modelling). Ja, het is een framework en welke partijen willen aansluiten. En ja, je zal koppelingen moeten realiseren die naar een standaard werken.

RC: Nu bezig op wat er al is. Welke datasets, formulieren, procesbeschrijvingen. Doel is hergebruik stimuleren voor kostenbesparing. Verder trekken, initiatieven aangeven om nieuwe ontwikkelingen toe te voegen. En trekkers per iniitiatief en daarmee kan je in contact treden. Ga je cofinanciering bijv. doen. Wat je niet wilt is dat ieder voor zich een eigen initiatief start.

Maurice: Geeft inzicht in alle plekken waar initiatieven plaatsvinden. 

Steven: meer content en dan een generiek interface om te vinden. Soort van google / web over de objecten in onderstaand plaatje.
![image](https://github.com/user-attachments/assets/40a8e903-3c6a-4f5e-8743-74fe3966de1a)

RC: Zoeken op WOZ en je krijgt direct alle relevante onderdelen. 

Maurice: dienstfiets intikken -> fietsvergoeding, manier om aan te vragen. Soort van Rio (intranet Rotterdam). Dus slim zoeken. 

RC: Ja, slim zoeken. Zijn we er al, nee nog niet. Het zou mooi zijn als we een routine krijgen om telkens metadata toe te voegen zodat de gebruiker makkelijk kan vinden.

Maurice: er zijn meer collega's die deze functionaliteit nog niet kennen. Wat kan ik er mee? 

Jaap: 'OC is geen duizend dingen doekje'. Aanpak is te werken langs use-cases zodat we ontdekken dat je OC er ook voor in kan zetten. Zo ook aangesloten op common ground voor keurmerk op toepassingen. Ook daarvoor toegepast.
En zo voor Rotterdam: datasets, Dimpact: inrichting formulieren, 

Maurice: hoe vinden de mensen de weg naar deze oplossing. 

Jaap: Er moet iets te vinden zijn en zichtbaarheid binnen de organisatie. 

Maurice: inderdaad, bijvoorbeeld alle indicatoren. Framework zonder vulling levert geen uitnutting. 
We zoeken een manier om bronbestanden te ontsluiten via een connector. Zelf iets maken, of wellicht binnen OC op te lossen. 

Steven: Regels combineren met formulieren - in POC al aangetoond. Kan buiten OC. Ontstaat organisch. Kan helaas niet de groeistap maken. 

Jaap: Een schaap over de dam.... 

Maurice: Je zal een gebruikersgroep moeten organiseren binnen je eigen organisatie. 

Jaap: Samenvattend: als een usecase is gestart dient deze in gebruik genomen te worden en "gevuld". Ook een benoemde eigenaar / Ambassadeur. En dan bepalen hoe verder opgeschaald kan worden binnen en/of buiten de organisatie. Om de olievlek te vergroten.

RC: Gebruikersgroep binnen eigen organisatie en ook met andere organisaties. 

Maurice: Moet er wel iets te zien zijn; volume hebben. 

Jaap: Antwoord op ambities zijn gegeven. Graag use-cases nu induiken. 
Jaap: CG: Winnie trok de keurmerk toepassing. Is stil gevallen. Valt en staat met eigenaarschap. Wat meevalt is dat VNG voor OC gekozen heeft. Dat helpt al met opschaling. 
RK: High-value datasets - live: wat in Rotterdam kan. op z'n minst G4. Rotterdam zoekt de opschaling intern door OC in werkproces te plaatsen en te borgen. Daarnaast doorontwikkeling in samenspraak met G5 met accent op het metamodel voor informatie als zijnde het fundament.
Jaap: formulieren - inzichtelijk krijgen hoe zo'n formulier in elkaar zit. Open Formulieren is een ontwikkeling die content levert, te vinden 
Martin: service blueprint - customer journey's - visueel. 79 blueprints. Worden veel gemaakt in omnichannel, zou je ook erbij willen hebben.
Steven: regels - bijv. normenbrief, iedere gemeente maakt daar een normenkaart van. Wil een linked data machine readable - vindbaar via OC. Aanbieden aan Wego4It. 

Jaap: volgende punt: 

Roadmap: RC: use-cases als projectjes. En programma structuur er overheen. En groep die architectuur bewaakt en keuzes daarin maakt. je krijgt ook PO's met eigen budgetjes. Hoe ga je dit ook als hoofd PO oppakken omdat je geen grip hebt op die projecten en financiering. Je wilt 1 code-base en **BESLUIT**vorming structuur. 
Hiervoor is een basisvoorstel uitgewerkt. Voor VNG moest dat ook. 

Ruben: Na koffie hierin duiken. 

Ruben (14:55): Projectmatig werken en zorgen dat dingen bij elkaar komen. Idee is om fork te starten, is zelfstandig ding. 

Steven: Een branche heeft een zekere procesbewaking. Met approve op code change. 

Ruben: branche dwing je meer grip op doorontwikkeling. 
Steven: code-base is single point of truth. Er is maar 1 landingsplek. Steven wil de beheerfunctie wel opnemen. 4-ogen principe. 
Ruben: voordeel aan 1 repository is dat alle issues op 1 plek zetten. 

**BESLUIT**: B-20250123-1 branches met aanbod van Steven voor beheersrol op zich te nemen. Pull-request voor branche merges. managen door Steven. Release keuze door productowner (bijvoorbeeld per kwartaal). Voldoen we aan semantic versioning. 

Ruben: Dit betekent dat we op Github projecten kunnen aanmaken en issues kunnen sharen. En we kunnen het volgen over het geheel. En daarmee zichtbare roadmap. En daarmee kan je in community overleg over features hebben ondersteund door een kanban. 

![image](https://github.com/user-attachments/assets/3d509625-d83c-49e1-9d2e-8ead138b0b6f)

Martin: Blijft dat werken met Main (oude naamgeving: master/slave). 

Steven: je zal ook iets van een acceptatie omgeving moeten hebben. Je zal ook iets met issues over en weer moeten doen. 

**BESLUIT**: B-20250123-2: https://github.com/orgs/OpenCatalogi wordt de basis voor ons allen. Daar projecten onder hangen. En daar je issues in noteren en die relateren aan main issues. 

Jaap: Wil afspraak over structuur, ook voor Q2; dan loopt innovatiesubsidie af. VNG deelname aan community. Kijken naar productownerschap voor na Q2. Op korte termijn de **BESLUIT**en doorvoeren. 

Ruben: zorg er dan ook voor dat de use-cases als projecten erin staan. 

Jaap: Wanneer in uitvoering. Wanneer klaar? 

- [ ]  Actie A-20250123-1: Ruben: voor 3 februari moeten de use-cases als projecten erop staan.
- [ ]  Actie A-20250123-2: Steven: user-stories -> epics
- [ ]  Actie A-20250123-3: RK: zelfde voor datasets
- [ ]  Actie A-20250123-4: RC: werkafspraken
- [ ]  Actie A-20250123-5: All: red je het niet - update mailtje. 

Jaap: Agenda community - volgende fase. 

Ruben: VNG aansluiting, gaat over vervangingsvraagstuk software catalogus. IBDS vragen, waterschapshuis. 16 juni is oplevering. Die andere partijen draaien eigen koers. Na zomervakantie weer samenvoegen, ook nadenken over PO's, financiering en partijen. 

**BESLUIT**: Kopteam; RC:, Ruben, Steven en Jaap als schrijver van agenda voorstel - community . 

Jaap: Tussen 3 - 13 feb bij elkaar. Topics: standaard agenda, wie is eigenaar. hoe vaak bij elkaar, marketing & communicatie. Voor de zomer 3 keer bij elkaar met eigenaar van use-cases. Mooie oefenperiode om daarna beter beslagen ten ijs met VNG in gesprek te gaan. 

Ruben: Idee voor werkgroepje voor installeren OC. Daaruit aanvullen documentatie. 
Steven: klein clubje, nu is code-base nog niet stabiel genoeg voor documenteren. Kennisclubje vormen en als taskforce in te zetten om anderen op weg te helpen. Wego4it doet dat ook als service. 

**BESLUIT**: Vrijdagochtend clubje uitbouwen naar werkgroep.
- [ ] Actie A-20250123-6: RK + RC - afstemmen of Edwin van Eersel wil aansluiten.

RC: Ook iets regelen voor hulpvragen. In kopteam richting aangeven voor hoe organiseren we met elkaar de hulpvragen.
RC: Bijvoorbeeld over connectoren.

Maurice: Veel collega's laten koppelen aan leveranciers over. Is er ook een mechanisme waarover je contact kan opnemen over bijvoorbeeld die koppelvlakken? Sluit aan op eerdere opmerking.  

Steven: 3 doelgroepen: nerds - vinden hun eigen weg. bestuurlijk - goed uitgelegd krijgen. doelgroep die er tussen zit die verantwoording in portfolio / inkoop. Die laatste doelgroep moet je frequenter engagen.  Pleio is daarin niet voldoende. 

Jaap: op 13 februari willen we goede demo voor BZK. Daar nog mee aan de slag. 
Ruben: wat hebben we klaarstaan aan demo's? En OpenCatalogi.nl wordt omgezet naar nieuwe release. 

- [ ] Actie A-20250123-7: RC en Ruben passen dit aan. Componenten moeten nog ingeladen doen o.a.
- [ ] Actie A-20250123-8: Jaap: 6 februari check voor opzet van de demo. 14:16 uur ingeplanned. Locatie nader te bepalen. 


