Receiver
========

tx\_mkmailer\_receiver\_BaseTemplate
------------------------------------

Das ist ein Basis Receiver, welcher die E-Mail-Nachricht in ein Template einfügt. Statische Angaben wie Header, Footer, Signatur, ... sind somit möglich.

Kindklassen sollten zusätzlich folgende Methoden bereitstellen:

-   protected function getConfId()

    Liefert die Confid. Diese wird an addAdditionalData übergeben und liefert die Daten für das Template..
-   protected function addAdditionalData(&\$mailText, &\$mailHtml, &\$mailSubject, \$formatter, \$confId, \$idx)

    Hier hat der Receiver die Möglichkeit eigene Änderungen am Template zu machen (Marker eretzen).
-   public function getSingleAddress

Beispiel TypoScript Konfiguration für das Frontend (z.B. auf der Seite mit dem Plugin für den Mailversand):

~~~~ {.sourceCode .ts}
lib.mkmailer.basetemplate {
   wrapTemplate = 1
   textTemplate = EXT:mkmailer/templates/mailwraptext.html
   textSubpart = ###CONTENTTEXT###
   htmlTemplate = EXT:mkmailer/templates/mailwraphtml.html
   htmlSubpart = ###CONTENTHTML###

   ### dem receiver stehen momentan folgende felder zur verfügung:
   ### address und addressid
   ### weiter sind hier dc marker (siehe rn_base) möglich.
   receivertext {
   }
   receiverhtml {
   }
}
~~~~
