# MarketFlow - Platforma E-commerce B2B/B2C

MarketFlow to nowoczesna i lekka platforma e-commerce napędzana przez PHP 8, zaprojektowana do obsługi zarówno klientów detalicznych (B2C), jak i biznesowych (B2B). System oferuje zaawansowane zarządzanie cennikami, stanami magazynowymi oraz przejrzyste panele dla klienta i administratora.

## 🚀 Kluczowe Funkcje

### Dla Klientów
*   **Hybrydowy model sprzedaży:** Dynamiczne przełączanie między cenami brutto (B2C) a cenami hurtowymi netto (B2B) po weryfikacji firmy.
*   **Panel Klienta:** Zarządzanie historią zamówień, podgląd faktur oraz profilu firmowego.
*   **Wyszukiwarka i Kategorie:** Intuicyjny katalog produktów z podziałem na typy (Elektronika, Żywność, Narzędzia itp.).
*   **Responsywny Design:** Pełna obsługa urządzeń mobilnych dzięki dedykowanemu paskowi nawigacji.

### Dla Administratorów
*   **Dashboard sprzedażowy:** Wizualizacja przychodów w czasie rzeczywistym przy użyciu Chart.js.
*   **Zarządzanie Magazynem:** System powiadomień o niskim stanie magazynowym.
*   **Zarządzanie zamówieniami:** Pełna kontrola nad statusem zamówienia i danymi do wysyłki.
*   **Audyt i Logi:** Rejestracja kluczowych akcji w systemie dla zwiększenia bezpieczeństwa.

## 🛠 Stos Technologiczny

*   **Backend:** PHP 8.x (Typowanie ścisłe, wzorce Repository i Service)
*   **Baza Danych:** MySQL / MariaDB (Interfejs PDO)
*   **Frontend:** Vanilla JS, CSS3 (BEM, Custom Properties, CSS Grid/Flexbox)
*   **Ikony:** Google Material Symbols
*   **Wykresy:** Chart.js

## 📂 Struktura Projektu

```text
├── admin/               # Panel administratora
├── config/              # Konfiguracja bazy danych
├── includes/            # Rdzeń systemu
│   ├── repositories/    # Warstwa dostępu do danych (PDO)
│   ├── services/        # Logika biznesowa (Koszyk, Zamówienia)
│   ├── layout/          # Wspólne komponenty UI
│   └── bootstrap.php    # Inicjalizacja aplikacji
├── panel/               # Panel klienta B2B
├── public/              # Katalog publiczny (Assets, Storefront)
│   └── assets/css/      # Główne style aplikacji
└── .env                 # Konfiguracja środowiskowa
```

## 🔧 Instalacja

1.  Sklonuj repozytorium na swój serwer lokalny (np. XAMPP, Laragon, Apache).
2.  Skonfiguruj plik `.env` w katalogu głównym projektu:
    ```env
    DB_HOST=localhost
    DB_NAME=marketflow
    DB_USER=root
    DB_PASS=
    APP_URL=http://localhost/marketflow
    VAT_RATE=0.23
    ```
3.  System posiada wbudowany mechanizm migracji i seedowania danych. Przy pierwszym uruchomieniu `bootstrap.php` automatycznie utworzy niezbędne tabele i przykładowe dane w bazie danych.
4.  Upewnij się, że moduł `mod_rewrite` w Apache jest włączony.

## 🔐 Bezpieczeństwo

*   **XSS Protection:** Konsekwentne użycie funkcji `e()` (htmlspecialchars) przy renderowaniu danych.
*   **CSRF Protection:** Formularze zabezpieczone tokenami weryfikowanymi po stronie serwera.
*   **SQL Injection:** Wykorzystanie Prepared Statements (PDO) w całej warstwie repozytoriów.
*   **Auth:** Bezpieczne hashowanie haseł (`password_hash`) oraz kontrola dostępu oparta na rolach (`requireRole`).

## 📈 Przykładowe dane logowania

*   **Administrator:** `admin@marketflow.pl` / `admin123`
*   **Klient testowy:** `example@example.com` / `client123`

## 📄 Licencja

Projekt stworzony na potrzeby własne. Wszystkie prawa zastrzeżone.

---
© 2024 MarketFlow. Built with passion for B2B efficiency.
```
