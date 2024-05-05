<img alt="DiversWorld" src="./docs/Diversworld-Wort-Logo.png" width="250"/>

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


