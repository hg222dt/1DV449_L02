#Rapport Labb 2

####URL till körbar version: http://www.bigmachine.se/1DV449_L02/index.php

##Del 1 - Säkerhetsproblem

###Autentiseringen ur funktion 
Ett ganska flagrant hål i säkerheten var den autentisering som inte riktigt existerade i den koden vi fick ta del av, dvs man kunde logga in med vilket lösenord som helst, bara man visste ett användarnamn. 
I scenariot av en riktig tjänst, skulle man i förlängningen skulle detta kunna bli en kapning av den riktiga användarens identitet, då förövaren skulle kunna kommunicera med användarens vänner, i vännernas tro om att denna illasinnade individ faktiskt är användaren.
Det ska dock tilläggas att denna tjänst endast är utformad med att man i varje meddelande själv måste identifiera sig, vilket skulle göra att vem som hels tom har tillgång till chattrummet skulle kunna utge sig för att vara någon annan. Men ponera att det är ett stängt chatt-rum som ingen annan ska få se, så är dock detta en allvarlig brist.
Lösningen var att returnera ett  “false” istället för en text-sträng när servern förstår att användarnamnet eventuellt inte hittats, samt returnera “false” när det inmatade lösenordet inte matchat lösenordet på databasen.

###Lösenord sparade i klartext
I databasen låg lösenorden sparade i klartext, dvs helt synliga för de som har tillgång till databasen. Hur de sparats till databasen framgår inte av koden, men det är tydligt att något måste göras åt de lösenord som befinner sig i databasen, samt även fixa hur scriptet sedan vid inloggning verifierar lösenordet.
Säkerhetshålet kan utnyttjas genom att en illasinnad individ får tillgång till databasen, och med egna ögon kan se alla lösenord och dess användarnamn. 
Detta medför ju dels det uppenbara, att hackern har tillgång till konton på siten. Men det kan då medför att alla dessa lösenord kan spridas med den digitala vinden på nätet, vilket kan vara extra arligt om även användarnamnen kan kopplas till enskilda individer som som via en e-post-adress mm. En risk finns även att dessa användare använder samma lösenord till sin e-post, vilken vem som helst då skulle kunna komma åt. 
Åtgärden har varit att använda sig av ett bibliotek som hashar lösenord åt oss. Just detta biblioteket ersätter ganska bra den hashnings-funkation som finns i PHP 5.5 och över (Använder mig själv av version 5.4). Genom att ersätta de lösenord som finns på databasen (med dess hashade motparter) så kan vi nu vid varje inloggning använda oss av det lösenord som användaren matat in, och låta vårt passwords-library hasha det åt oss, samt sen jämföra med det lösenord som finns på servern. Om dessa två matchar loggas användaren in.

###Indata till databasen parametriseras inte
När databasen ska ta emot ett meddelande i form av en SQL-sats, finns det ingen mekanism som kollar av så att det som skickats upp faktiskt inte är lömsk kod, som exempelvis skulle kunna operera i databasen. Man har riskerar att råka ut för så kallade SQL-injections. 
En illasinnad individ skulle genom detta säkerhetshål kunna skicka data som databasen tror är SQL-kod. I värsta fall (Och som säkerhetshålet här var utformat) skulle helt plötsligt denna illasinnade individ kunna ge sig själv befogenheter att exempelvis skicka ett SQL-statement i form av en “DROP TABLE”-sats, vilket direkt skulle radera alla meddelanden på databasen. 
Genom att parametrisera den indata som ska skickas till databasen, kan vi råda bot påd etta. Lösningn i detta fall blev prepare-funktionen som finns att tillgå i PDO, som gör att SQL-satserna automatiskt kontrolleras att de inte innehåller någon lömsk kod.

###Kod kunde skickas in i chatten
Vid skickande av meddelande till chatten, kunde man inte bara skicka med text, utan även kod. Problemet detta är att den inmatade kod som skickas till chatten, faktiskt kommer att bete sig som om det vore kod, när den senare lägger sig som ett meddelande i chatten. Dvs om en illasinnad individ matar in en a-tag, kommer denna att bete sig som ett html-element och se ut som en helt vanlig länk i chatten när den är iväg skickad. Problemet med detta är att koden kan vara lömsk och detta kan utnyttjas av en illasinnad individ, att exempelvis dirigera en användare som klicka på denna länk, till sin egen server för att sno åt sig användaren session-id, på chatt-siten. En illasinnad individ skulle därmed kunna stjäla sessionen från en annan användare, i en såkallad XSS-attack. 
Lösningen blev att validera indatat till servern så att alla element med hak-parenteser tas bort vid sådan inmatning. Detta gör att all eventuell kod skrivs ut i klartext i det meddelande som chatten sedan renderar.

###CSRF-attacker kan utföras
När meddelande sänds till servern finns det ingenting i postningen som identiferar att det faktiskt är användaren som skickat detta från meddelandet i från den öppna fliken med formuläret. Detta kan utnyttjas genom att en illasinnad individ vars site användaren är inne på i en annan webbläsar-flik, kan försöka göra anrop mot servern mezzy-labbage-servern som användaren är kopplad till från sin andra flik. Andra sidor som är öppna i en webbläsare kan göra sådana anrop, och i värsta fall kunna operera i andras konton på sidor som de egentligen inte ska ha tillgång till alls. En illasinnad individ skulle kunna utnyttja detta genom att göra anrop mot ett offers bank som denne har öppen i en annan flik, och därmed åsamka stor skada, som även kan vara svår att motbevisa i rättsliga processer.
Jag har motverkat detta genom att vid varje anrop identifiera att anropet faktiskt har gjorts från den korrekta fliken, genom att formuläret skickar med ett token som sedan sessions-start genererats och sparats i sessionen, samt skickats ut till formuläret. Vid en postning av formuläret jämförs sedan sessionens token med detta medskickade token, och om dessa båda stämmer överens med varandra så vet servern att anropet gjorts från rätt flik i webbläsaren.

###Session hijacking kan utföras
En användares session skulle kunna stjälas genom att en illasinnad individ på något vis stjäler användarens sessions-id och använder det i sin egen webbläsare. Detta kan sedan utnyttjas för att mycket enkelt logga in på användarens konto, vilket kan få konsekvenser av identitesstölder och i förlängningen bedrägerier mm.
Hur detta nu förhindras är genom att servern vid inloggning från inloggningsformuläret, alltid sparar klientens HTTP-USER-AGENT. När en klient sedan vill logga in med hjälp av en tidigare satt cookie, så jämförs den HTTP-USER-AGENT som är relaterad till denna cookie. Om denna tidigare sparade user agent stämmer överens med användarens verkliga klient, så kan användaren loggas in via cookie. Om inte, förhindras inloggning via cookie.

###Utloggning loggar inte ut användaren
När användaren försöker logga ut rensas inte den data som finns i sessionen. Det betyder att användarens cookis och all information fortfarande finns kvar i sessionen, trots att användaren loggats ut. Detta innebär att man trots en explicit utloggning kommer att loggas in på kontot igen, om man anropar sidans URL. Detta kan utnyttjas av förövare på exempeliv publika datorer som där användare tror sig loggat ut, men sedan kommer loggas in igen av en annan användare som använder samma dator efteråt. Detta kan leda till kapning av dessa personers identiteter.
Lösningen blev att rensa all cookie-information samt att sen sätta användaren till utloggad i en sessions-variabel. Detta gör att servern vet om att denna användaren inte är inloggad, och kommer inte loggas in via cookie.


##Del 2 - Optimering
Jag har utgått från mess.php när jag gjort optimeringar och mätningar. Detta då detta är den sida som kräver mest resureser och mest relevant ut andvändarperspektiv.
Innan jag började göra optimeringar planerade jag bland annat att en optimering skulle vara att lägga scripten på rätt plats i HTML-dokumentet, då detta är ett sätt att mer optimalt ladda en sida, då HTML-elementer för användaren kan synas innan själva scripten laddas.

###Gör färre HTTP-requests 
Genom att göra färre http-requests minskar man laddningstiden. I detta fall innebär det att vi 

1. Bakar samman två stycken javascript-filer, 

2. Att vi inte anropar resurser som inte används och är onödiga att anropa (exempelvis bakgrundsbilden som laddas men inte syns) samt script som laddas dubbelt.

3. Lägg därtill även CSS-sprites som bilderna ska göras om till, vilket även detta minska anropen och laddningstiden.

Referens: High Performance Web Sites. Kapitel 3

Observation före åtgärd
Antal requests: 12
Total storlek resurser: 720 kb
Laddningstider:
1.21 - 1.14 - 1.08 - 1,46 - 1,18
Medel : 1.214 s

Observation efter åtgärd
Antal requests: 8
Total storlek resurser: 519 kb
Laddningstider: 
1,02 - 1,06 - 1,03 - 1,12 - 0,78
Medel: 1,002 s

————

###CDN

Genom att hämta vissa resurser genom olika content delivery networks, så kan jag bör laddningstiden gå snabbare. I detta fall kan vi hämta vår bootstrap jquery och css från ett separat CDN, samt vårt generiska jQuery-bibliotek från Googles CDN.

Referens: High Performance Web Sites. Kapitel 4

Observation före åtgärd
Antal requests: 8
Total storlek resurser: 519 kb
Laddningstider: 
1,02 - 1,06 - 1,03 - 1,12 - 0,78
Medel: 1,002 s

Observation efter åtgärd
Antal requests: 8
Total storlek resurser: 127 kb
Laddningstider: 
0,79 - 0,84 - 0,55 - 0,66 - 0,53
Medel: 0,674 s

————

###Minifiering av javascript

Genom att använda sig av mjukvara som kan minifiera javascript-filer så kan man minska utrymmet som dessa filer tar, och därmed även laddningstiden. 

Referens: High Performance Web Sites. Kaptiel 12

Observation före åtgärd
Antal requests: 8
Total storlek resurser: 127 kb
Laddningstider: 
0,79 - 0,84 - 0,55 - 0,66 - 0,53
Medel: 0,674 s

Observation efter åtgärd
Antal requests: 8
Total storlek resurser: 125 kb
Laddningstider: 
0,63 - 0,77 - 0,56 - 0,52 - 0,56
Medel: 0,608 s


##Del 3 - Long-polling

I grunden går detta ut på att låta servern, under en viss tidsrymd, göra anrop mot databasen, för att returnera eventuella nyinkomna meddelanden. Efter loopen på servern, görs en ny förfrågan mot servern för att sefortästta kolla efter meddelanden.

I min get.php loopar jag min databasförfrågan för att få reda på om
nya meddelanden har inkommit till databasen. Loopen körs i 20 sekunder
under förutsättning att inget nytt meddelande returneras (med en implementerad sleep på 3 sekunder). Om inget nytt
meddelande returneras på 20 sekunder,returnerar php-scriptet “false”
till klienten, som i sin ajax-metod för hämtning av meddelanden,
anropar sig själv rekursivt, och prosessen körs igen. Om ett nytt
meddelande inkommer till databasen under 20-sekunders-loopen, så
returneras meddelandet till klienten, och ajax-metoden gör ännu ett
rekursivt anrop. osv.

Fördelen med detta är att det var en relativt enkel implementation att göra, och resultatet gör att man får en utåt sett smidig app, som nästan direkt uppdateras på andra klienter, istället för att användaren själv ska behöva uppdatera sin klient för att se om nya meddelanden inkommit.

Nackdelen är delvis att det är resurskrävande för server att hela tiden göra detta arbete, då en connection uppehålls varje gång ett sådant anrop görs, vilket kräver en del av kapaciteten från servern. Detta resursbehov multipliceras med antalet användaren, vilket gör den ganska krävande vid större användarbaser.



