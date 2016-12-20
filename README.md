# Boodschappen

Boodschappen prijsvergelijker. Het backend is geschreven met Laravel 5.2 en PostgreSQL.

## Installatie

Bijna hetzelfde als de standaard Laravel installatiestappen. Let op het volgende:

De database structuur bevat dingen (eigen types, functies en triggers) die niet netjes in Laravel's migratie systeem passen. Gebruik daarom het bestand `database/structure.sql` om een verse database aan te maken.

## Crawling

Databronnen van diverse supermarkten voldoen in dit systeem allemaal aan de `ProductDataSource` interface. Daardoor is het mogelijk om gaandeweg nieuwe bronnen toe te voegen, die allemaal genormaliseerde data teruggeven.

Tot nu toe wordt er *on demand* gecrawled, in Laravel queue workers. Automatisch ingeroosterde crawls komen binnenkort!

## Datamodel

De prijsvergelijker heeft drie elementaire datatypen:

1. Categorie
2. Product
3. Prijs

**Categorie** is een hiërarchisch model waarin subcategorieën mogelijk zijn. Dit wordt gebruikt om vergelijkbare producten te zoeken. Als je bijvoorbeeld naar 'melk' zoekt, kunnen we de producten ophalen die in de categorie 'melk' en alle subcategorieën daarvan zitten. De gebruiker kan de zoekopdracht dan nog verfijnen naar bijv. 'halfvolle melk'.

Een **product** is een uniek identificeerbaar product, van een bepaald merk met een bepaald gewicht of volume. Producten worden uniek geïdentificeerd aan een SKU (stock-keeping unit) en een barcode wanneer deze aanwezig is.
Een **product** heeft altijd één **categorie** waar het aan toebehoort.

Een **prijs** is een prijs van een bepaald **product** op een gegeven moment in tijd. Door deze in een aparte tabel op te slaan is het mogelijk om historische prijzen vast te leggen.

## Licentie

MIT