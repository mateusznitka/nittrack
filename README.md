# Nittrack
Projekt zrealizowany w ramach pracy inżynierskiej "Projekt Systemu Monitoringu Pozycji Obiektów Mobilnych" na kierunku Mechatronika na Wojskowej Akademii Technicznej.

## Zakres
1. Opracowanie wymagań technicznych
2. Opracowanie koncepcji budowy
3. Wykonanie modelu fizycznego systemu
4. Opracowanie dokumentacji

## Koncepcja systemu
Na system składa się: 
* jednostka lokalizacyjna,
* serwer,
* użytkownik.
Jednostka lokalizacyjna składa się z mikrokontrolera oraz modułów:
* modułu GPS,
* modułu GSM.

Jednostka wpięta jest w instalację elektryczną pojazdu przy pomocy przetwornicy impulsowej zmieniającej napięcie akumulatora na niższe.
Jeśli zapłon pojazdu jest włączony, jednostka wysyła na serwer pozycje w interwałach około 20 sekund. Wyłączenie zapłonu powoduje przejście w tryb zmniejszonego poboru energii. Pozycje nie są wtedy wysyłane. Powrót do stanu monitorowania odbywa się po włączeniu zapłonu lub po wysłaniu SMSa "ON". Możliwe jest również chwilowe wybudzenie jednostki w celu jednokrotnego wysłania pozycji (SMS o treści "PULL").
Serwer przetwarza i przechowuje dane oraz pozwala na wyświetlanie aplikacji użytkownika w przeglądarce.

## Jednostka lokalizacyjna
Do działania jednostki konieczna jest ingerencja w instalację elektryczną pojazdu. Jednostka zostaje podłączona do zasilania z akumulatora, konieczne jest również podłączenie do mikrokontrolera tzw. „zapłonu”. Włączenie zapłonu  jest  jednoznaczne z wysłaniem  sygnału  sterującego na wejście cyfrowe mikrokontrolera.  Aby  jednak  możliwa  była  poprawna  praca  urządzenia,  zarówno  napięcie zasilania, jak i sygnał zapłonu musi być zmniejszony do wartości nominalnych mikrokontrolera oraz  modułu  GSM.  Moduł  GSM,  w  przeciwieństwie  do  modułu  lokalizacyjnego,  nie  jest zasilany  bezpośrednio  z  mikrokontrolera,  ponieważ  jego  maksymalny  pobór  prądu  może chwilowo sięgać nawet 2 A, co mogłoby spowodować uszkodzenie mikrokontrolera, w którym maksymalny prąd wyprowadzeń wynosi 20 mA. W związku z tym, do zasilania tych elementów wykorzystany został układ obniżający napięcie – uniwersalna przetwornica impulsowa LM-2956 z  regulowanym  napięciem  wyjściowym – w przypadku omawianego  układu  napięcie wyjściowe zostało ustalone na około 4,1 V, a więc mieści się w dopuszczalnych zakresach obydwu urządzeń. Dodatkowo przed modułem GSM został połączony równolegle kondensator 220 µF, którego zadaniem jest filtracja zasilania.

![schemat ogólny](https://raw.githubusercontent.com/mateusznitka/nittrack/master/docs/img/schema_general.jpg)

Wejścia cyfrowe w płytce Teensy LC pracują w  logice  3,3  V,  aby  więc  możliwe  było  skorzystanie  z  napięcia  na  „zapłonie”  jako  stanu wysokiego na wejściu cyfrowym, zastosowany został układ z transoptorem, dzięki któremu układ elektroniczny mikrokontrolera jest odseparowany od instalacji elektrycznej samochodu, a  mimo  to  pozwala  na  sterowanie  wyprowadzeniem  cyfrowym.  Układ  ten  składa się z transoptora PC817, z którym po stronie fotoemitera szeregowo połączony jest rezystor 2,2 kΩ  oraz  dioda  prostownicza  1N4148,  która  służy  jako  zabezpieczenie  przed  spaleniem diody transoptora w przypadku ojawienia się odwrotnej polaryzacji. 
Kolektor fototranzystora 36 połączony  jest  z  wyprowadzeniem  3,3  V  z  mikrokontrolera,  a  emiter z  wyprowadzeniem cyfrowym nr 15 oraz przez rezystor 10 kΩ z masą mikrokontrolera. Kiedy więc użytkownik uruchomi  zapłon  pojazdu,  transoptor  pozwoli  na  przepływ  prądu  obwodu  wyjściowego, ustalając tym samym stan wysoki na pinie nr 15, który obsługuje przerwanie zmieniające tryb pracy urządzenia. 

![schemat połączeń](https://raw.githubusercontent.com/mateusznitka/nittrack/master/docs/img/schema_git.png)

Moduł GPS zasilany jest z wyprowadzenia 3,3 V mikrokontrolera poprzez układ  z  tranzystorem.  Zadaniem  tranzystora  jest  uruchamianie  bądź  wyłączanie  modułu w zależności od stanu pinu cyfrowego nr 16, który poprzez rezystor 6,3 kΩ połączony jest z bazą  tranzystora.  Kolektor połączony jest z masą modułu  GPS, a emiter z masą mikrokontrolera.  Umożliwia  to  sterowanie  zasilaniem  modułu  z  poziomu programu – stan wysoki na pinie 16 powoduje przepływ prądu w obwodzie zasilając tym samym moduł GPS. 
 
Komunikacja  pomiędzy  modułami  GSM  i  lokalizacyjnym  a  mikrokontrolerem  odbywa się za  pomocą  interfejsu  szeregowego. Mikrokontroler Teensy LC posiada 3 niezależne porty szeregowe i każdy z nich ma przypisane konkretne numery pinów na płytce. 
Moduł GPS podłączony jest do portu Serial 1, czyli pinów o numerach 0 i 1, natomiast moduł GSM do portu Serial 2, czyli pinów 9 i 10.

W celu wykonania jednostki została zaprojektowana płytka PCB realizująca powyższe schematy, do której zostały dolutowane wszystkie wymagane elementy:

![PCB](https://raw.githubusercontent.com/mateusznitka/nittrack/master/docs/img/pcb.png)

Płytka została zaprojektowana przy pomocy programu EasyEDA i wykonana w firmie JLCPCB.

## Aplikacja użytkownika
Aplikacja użytkownika została opracowana przy pomocy czystego HTML+CSS, JavaScripta(jQuery, Leaflet), oraz PHP. Baza danych: MariaDB.

Od strony użytkownika aplikacja składa się z następujących podstron:
* Strona logowania - login.php
* Strona główna - index.php
* Raporty - reports.php
* Urządzenie - device.php
* Informacje o systemie - info.php
* Kontakt - contact.php
* FAQ - help.php
Dodatkowo aplikacja zawiera szereg skryptów backendowych:
* connect.php - połączenie z bazą danych
* convert.php, convert2.php - konwertowanie danych na GeoJSON
* dodaj.php - zapisywanie pozycji z jednostki lokalizacyjnej
* get_login.php - obsługa logowania
* get_report.php - obsługa raportów
* logout.php - wylogowywanie
* submit.php - obsługa wyświetlania pozycji na mapie

## Uwagi końcowe
Projekt używany jest w celach prywatnych.
