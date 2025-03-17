<img alt="DiversWorld" src="./docs/Diversworld-Wort-Logo.png" width="250"/>

# Wird nicht weiter verfolgt. EEin eigenes Modul (contao-diveclub-buhndle) für Tauchclubs wird bearbeitet 
# Welcome to Dive Course Manager Bundle (DiCoMa)
### Management of equipment and diving courses for diving clubs and/or diving schools
#### Release 1 contains the management of diving cylinders.
The first release includes the management of diving cylinders and the organization of regular TÜV inspections.

#### TÜV inspections
Recording of TÜV dates in the events.
- Enter the prices for the TÜV inspection in the events in a MultiColumnArray.
  - An entry must be made here for each bottle size if the prices for different bottles are different.
  - Prices for items that must always be paid are marked as default and do not require a cylinder size.
  - A TÜV date can be selected in the DCA of the scuba tanks. The date of the last inspection is then set to the date of the selected TÜV date and the date of the next inspection is calculated automatically.
  - By clicking on the Create invoice button, the total price for the inspection is then calculated and attached to the cylinder as an invoice.
- The assets are currently only managed in the backend. An overview of the invoices per member is in progress and will be published with release 1.1.

### Release planning
- Release 2 will then include the administration of diving courses.
- In Release 3, the management of equipment with a rental process is planned.


# Welcome to Dive Course Manager Bundle (DiCoMa)
### Verwaltung von Equipment und Tauchkursen für Tauchvereine und oder Tauchschulen
#### Release 1 enthält die Verwaltung von Tauchflaschen.
Im ersten Release gibt es eine verwaltung der Tauchflaschen und die organisation der regelmäßigen TÜV-Prüfungen.

#### TÜV Prüfungen
- Erfassen von TÜV Terminen in den Events.
- Erfassen der preise für die TÜV-Prüfung in den Events in einem MultiColumnArray.
  - Hier muss für jede Flaschengröße ein Eintrag vorgenommen werden, sofern die Preise für unterschiedliche Flaschen unterschiedlich sind.
  - Preise für Artikel, die immer bezahlt werden müssen, werden als default markiert und benötigen keine Flaschengröße.
  - Im DCA der Tauchflaschen, kann ein TÜV-Termin ausgewählt werden. Das Datum der letzten Prüfung wird dann auf den Termin des gewählten TÜV-Termins gesetzt und das datum der nächsten Prüfung wird automatisch berechnet.
  - Mit Klick auf den Button Rechnung erstellen, wird dann der Gesamtpreis für die Prüfung berechnet und als Rechnung an die Flasche angehängt.
- Aktuell werden die Assets nur im Backend verwaltet. Eine Übersicht über die Rechnungen pro Mitglied ist in Arbeit und wird mit Release 1.1 veröffentlicht.

### Releaseplanung
- Release 2 wird dann die Verwaltung von Tauchkursen enthalten.
- Im Release 3 ist dann die Verwaltung der Ausrüstung mit einem Leihprozess geplant.


