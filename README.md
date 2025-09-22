# Gumi Webáruház Próbafeladat

## 1. Projekt áttekintés

Ez egy egyszerű webalkalmazás, ahol a felhasználók gumikat böngészhetnek.  
A backend Symfony + Doctrine ORM alapú, a frontend Vue.js 3 + Tailwind CSS.  
A termékek CSV-ből importálhatók.

---

## 2. Technológiák

### Backend
- Symfony 6 + PHP 8.2
- Doctrine ORM (MySQL vagy SQLite)
- REST API végpontok a frontend számára
- CSV import a termékekhez (`app:import-products`)

### Frontend
- Vue.js 3 + Vite
- Tailwind CSS 4
- Fetch alapú REST kommunikáció
- SPA jellegű működés

---

## 3. Adatbázis modell

### Termék (`Product`)
| Mező | Típus | Leírás |
|------|------|--------|
| id | integer | Automatikus azonosító |
| externalId | string | Külső azonosító |
| name | string | Termék neve |
| price | float | Bruttó ár |
| netPrice | float | Nettó ár |
| image | string | Kép URL |
| description | text | Rövid leírás |
| category | Category | Kapcsolódó kategória |

### Kategória (`Category`)
| Mező | Típus | Leírás |
|------|------|--------|
| id | integer | Automatikus azonosító |
| name | string | Kategória neve |
| slug | string | URL-barát név |

---

## 4. REST API végpontok

### Termékek listája
GET /api/products
Visszaadja az összes terméket JSON formátumban.

### Termék részlete
GET /api/product/{id}
Visszaadja a kiválasztott termék részleteit JSON-ban.

---

## 5. CSV import
A `app:import-products` parancs segítségével lehet a termékeket CSV-ből importálni.

---

## 6. Frontend
Vue.js komponensek a termékek listázásához és részletezéséhez
Tailwind CSS a reszponzív megjelenéshez
REST API hívások Fetch segítségével
SPA viselkedés

---

## 7. Fejlesztői útmutató

### Backend indítása:
cd backend
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console app:import-products products.csv
symfony serve

### Frontend indítása:
cd frontend
npm install
npm run dev

---

## 8. Tesztelés
API végpontok ellenőrzése:
http://localhost:8000/api/products
