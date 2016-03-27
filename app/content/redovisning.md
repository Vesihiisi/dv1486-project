Redovisning
====================================

Kmom 4
--------------------------------



    Vad tycker du om formulärhantering som visas i kursmomentet?
    Vad tycker du om databashanteringen som visas, föredrar du kanske traditionell SQL?
    Gjorde du några vägval, eller extra saker, när du utvecklade basklassen för modeller?
    Beskriv vilka vägval du gjorde och hur du valde att implementera kommentarer i databasen.



Kmom03
--------------------------------

Jag har ingen tidigare erfarenhet av CSS-ramverk förutom att jag i mindre utsträckning använt Bootstrap. De kan visst vara nyttiga och underlätta arbetet med komplexa projekt, särskilt om det är fler personer involverade. Man slipper skriva en del kod på egen hand och man vet att de element som finns med i ramverket kommer att ha ett enhetligt utseende. Särskilt användbara är ramverk när det krävs matematiska beräkningar, och det är i kombinationen med preprocessorer som LESS som de visar sin styrka och flexibilitet. Jag har upplevt det som att Bootstrap är bra när man snabbt behöver sätta ihop något som fungerar responsivt och ser ut och beter sig på ett förutsägbart vis, men ju mer anpassningar man vill göra desto fler begränsningar stöter man på.

LESS och andra CSS-preprocessorer har jag hört talas om, däremot inte använt. Efter att ha testat LESS kan jag se att det fyller en viktig lucka i CSS-utvecklingen och automatiserar en del saker. Det tar nog lite tid att lära sig utnyttja dess möjligheter till fullo, men bara det att kunna använda variabler besparar mycket tid. Det underlättar mycket att man får blanda LESS-kod och vanlig CSS-kod i samma fil.

Lessphp är nog fortfarande ganska "magiskt" i och med att det bara fungerar och smidigt serverar en färdig CSS-fil. Det enda jag hade problem med var att få det att fungera på skolservern då jag först missade att sätta rätt rättigheter på den relevanta katalogen. Katalogen där lessphp skall spara css-filen måste ju vara skrivbar, vilket ledde till en del felsökning.

Semantic.gs skulle jag vilja lära sig mer om. Till viss del liknar det Bootstrap, där man också arbetar med kolumner, men vad jag vill se är Semantic mycket mer flexibelt, på gott och ont. Att arbeta med semantiska begrepp är helt i linje med den riktning HTML5 går mot.

Normalize är ett typiskt verktyg som är bra att ha i bakgrunden. Det löser nog en del problem som vi inte är medvetna om i och med att det är omöjligt att testa i alla möjliga webbläsare. Det var intressant att läsa igenom dess källkod, som är välkommenterad, för att lära sig lite om vilka problem man kan stöta på (mest i Internet Explorer).

Font Awesome gick bra att installera och jag fick stor behållning av dess dokumentation med alla exemplen. Jag har tidigare stött på antligen det eller någon liknande uppsättning ikoner som finns inbyggd i Bootstrap, så det gick snabbt att implementera. Nuförtiden använder många webbplatser och applikationer liknande ikoner, så det är klart att vi har behov av dem. Man måste bara akta sig för överdriven användning som inte heller är bra.

Jag gjorde inga större utsvängningar i min stil utan följde guiden. Stilen innehåller samma antal regioner som i guiden och är responsiv med tre brytpunkter definierade i responsive.less. Något som jag gjorde annorlunda mot exemplet i guiden var att göra den responsiva delen mobile-first, dvs. att göra stilen för små skärmar default och använda "min-width" i mina media queries för att anpassa den till större skärmar. Delen med horisontellt och vertikallt rutnät är nog svårast att resonera kring eftersom det är svårt att se huruvida de olika elementen är anpassade till rutnätet eller inte. Detta gäller särskilt det horisontella, dvs. det vågrätta rutnätet, då det inte finns med i bakgrundsbilden vi fått. Jag kan tänka mig att det är bra att ha en linjal till hands när man arbetar med rutnät, även om det inte är en perfekt lösning då en bildskärms yta inte är helt platt.




Kmom02
-------------------------------------
I allmänhet gick även detta kursmoment bra att utföra, även om det var betydligt mer komplicerat än det första. Här gällde det att lägga tillräckligt med tid på att försöka sätta sig in i ramverkets uppbyggnad och hur de relevanta delarna hängde ihop. Vi fick även se hur ramverket hanterar sessions- och POST-data, vilket är relevant i flera olika sammanhang. Hela upplevelsen var väldigt lärorik.

Jag tror jag har förstått vilka som är dispatcher, kontroller respektive modeller. En sak som var bra i sammanhanget var den tydliga uppdelningen av kontrollerkod i CommentController och CommentsInSession, vilket gjorde det lättare att förstå flödet i programmet. När allt kom omkring så var det inte så komplicerat att bygga på de befintliga funktionerna då grundstrukturen fanns redan där.

Något som jag undrade över var hur mycket php-kod man fick ha i vyerna, dvs. i tpl-filerna. Just nu är det en blandning av php och html-kod som kanske inte är så lättläst. Samtidigt så är all den kod relevant för hur informationen formateras och presenteras.

Jag skapade två separata kommentarsflöden, ett på indexsidan och ett på en separat sida tillgänglig från navigeringen. Det löstes genom att skicka en extra parameter, pagekey, till kontrollern. Jag lade till stöd för gravatar och markdown samt en enkel validering av formuläret på klientsidan. Det blir säkert intressant att se om samma grundläggande struktur kan användas till en riktig kommentarsfunktion som sparar allt i en databas.

Det gick bra att installera och använda Composer och Packagist verkar innehålla flera intressanta paket som kan underlätta arbete med mer komplexa projekt. Till exempel google/apiclient för att kunna arbeta med Googles API:er. Jag hittade även flera paket för inputvalidering som kan vara användbara om man skriver en riktig forum- eller kommentarsfunktion. Det är vädligt smidigt att ha ett verktyg som håller koll på alla de externa bibliotek man använder.
 
Kmom01
------------------------------------
### Vilken utvecklingsmiljö använder du?

Jag använder operativsystemet Ubuntu 14.10, redigerar i Sublime Text och testar i Firefox. Som server har jag installerat XAMPP.


### Är du bekant med ramverk sedan tidigare?

Nej, jag besitter inga kunskaper om ramverk sedan tidigare. Jag är medveten om att de finns och att det är viktigt att lära sig om dem eftersom de  används väldigt ofta. Kurslitteraturen ger en bra översiktlig bild över hur ramverk kan fungera och vilka fördelar de erbjuder. Att läsa om olika problem och frågeställningar gynnar vår lärprocess och jag hoppas att den här kursen är mer litteraturtung än de tidigare kurserna.

### Är du sedan tidigare bekant med de lite mer avancerade begrepp som introduceras?

Den här frågan är egentligen omöjlig att besvara eftersom bedömningen av vilka begrepp som kan klassificeras som "lite mer avancerade" är oklar och individuell. Jag tolkar det som att det handlar om de nyckelbegrepp som behandlas i kursmomentets litteratur men som inte förekom i kursen DV1485. Till dessa räknas, bland annat: MVC, ramverk, dependency injection, routes. Dessa är jag inte bekant med.

### Din uppfattning om Anax, och speciellt Anax-MVC?

Det finns en del strukturella likheter mellan Anax-MVC och den mall som vi 
utvecklade i kursen DV1485. Till exempel begreppet sidkontroller, teman som används för att rendera sidor och en gemensam konfigurationsfil. Vad det aktuella ramverket gör är att vidare separera form från innehåll, vilket kan ses som en positiv utveckling eftersom det gör det möjligt att bygga mer komplexa och omfångsrika applikationer på ett modulärt sätt. Genom att arbeta med en liten beståndsdel i taget minskar vi risken för fel och har mer kontroll över detaljerna.

En annan fördel med ramverket är att det har en tydlig struktur, vilket gör det lätt att organisera större mängder material. Att skriva innehållet i markdown är vidare behjälpligt för att undvika fel och främja konsekvens i uppbyggandet av webbplatsen.

### Allmänt om kursmomentet

Det gick bra att komma igång med kursmomentet på grund av de detaljerade instruktionerna. Även kurslitteraturen var intressant och gav oss en bra uppfattning av vad kursen kommer att handla om. Trots att man inte förstod allt så fick man ändå en inblick i hur ramverkets många beståndsdelar kan hänga ihop. Den största skillnaden gentemot kursen DV1485 är att vi numera bara har en sidkontroller som hanterar alla routes. Att ha dem samlade på ett och samma ställe är en intressant utveckling som gör det lättare att överblicka hela webbplatsstrukturen.

Något som jag tyckte var ganska krångligt var delen med "snygga länkar", och det därför att instruktionstexten inte förklarar vad de är för något. 
Efter att ha sökt i forumet kunde jag lägga till rätt sökväg i .htaccess-filen men jag är fortfarande osäker på om allt fungerar som det ska och huruvida mina länkar är "snygga" eller inte. För att slippa hålla reda på olika filer gjorde jag så att sidkontrollern kontrollerar vilken server webbplatsen ligger på och bara aktiverar funktionen "snygga länkar" på skolservern.
