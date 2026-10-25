@extends('layouts.front')

@section('title', 'Zajęciownia.pl - System Obsługi Zajęć Pozalekcyjnych i Rozliczeń Online')
@section('meta_description', 'Kompleksowa platforma do zarządzania zajęciami pozalekcyjnymi, organizacjami i placówkami edukacyjnymi. Zapisy online, listy obecności, obłożenie grup i rozliczenia online dla rodziców.')

@section('content')

<!-- JSON-LD Structured Data (Schema.org) for SEO & AI Search -->
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "@graph": [
    {
      "@type": "SoftwareApplication",
      "name": "Zajeciownia.pl",
      "operatingSystem": "Web, iOS, Android",
      "applicationCategory": "EducationalApplication",
      "offers": {
        "@type": "AggregateOffer",
        "priceCurrency": "PLN",
        "lowPrice": "49.00",
        "highPrice": "399.00",
        "offerCount": "3"
      },
      "description": "System do obsługi szkół, kółek zainteresowań i zajęć pozalekcyjnych z modułem rozliczeń i płatności online."
    },
    {
      "@type": "Organization",
      "name": "Zajeciownia.pl",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/logo.png') }}"
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Jak działa automatyczne naliczanie opłat za zajęcia pozalekcyjne?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "System automatycznie przelicza kwoty w zależności od typu rozliczenia kursu (ryczałt miesięczny, opłata za zrealizowaną lekcję lub zajęcia jednorazowe) oraz frekwencji uczestnika."
          }
        },
        {
          "@type": "Question",
          "name": "Czy rodzice mogą płacić online za poszczególne zajęcia lub całe zaległości?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, rodzic ma możliwość opłacenia całej kwoty zaległości jednym kliknięciem lub wybrania pojedynczej pozycji rozliczeniowej za konkretny kurs."
          }
        },
        {
          "@type": "Question",
          "name": "W jaki sposób system uwzględnia nieobecności usprawiedliwione dzieci?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "W kursach indywidualnych oraz rozliczanych za lekcję nieobecność usprawiedliwiona jest automatycznie odliczana od miesięcznego rachunku rodzica."
          }
        },
        {
          "@type": "Question",
          "name": "Czy nauczyciele mogą sprawdzać obecność na telefonie lub tablecie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, moduł nauczyciela jest w pełni responsywny i umożliwia szybkie prowadzenie lekcji oraz odznaczanie frekwencji dzieci bezpośrednio na urządzeniu mobilnym."
          }
        }
      ]
    }
  ]
}
</script>

<!-- Sekcja HERO -->
<section class="hero-wrapper">
  <div class="container-xl">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="hero-badge">
          <i class="ti ti-sparkles text-primary"></i> System Wpłat Online & Rozliczeń 2.0
        </span>
        <h1 class="hero-title mb-3">
          Kompleksowa Platforma do Zarządzania <span class="text-gradient">Zajęciami Pozalekcyjnymi</span>
        </h1>
        <p class="fs-2 text-secondary mb-4 leading-relaxed">
          Jedno narzędzie dla <strong>Domów Kultury, Klubów Sportowych, Korepetytorów, Instruktorów i Rodziców</strong>. Automatyczne rozliczenia od momentu zapisu, listy obecności oraz płatności online.
        </p>

        <div class="d-flex flex-column flex-sm-row gap-3 mb-4">
          <a href="{{ route('register') }}" class="btn btn-primary btn-lg fw-bold shadow-sm py-3 px-4">
            <i class="ti ti-rocket me-2 fs-2"></i> Wypróbuj za darmo (14 dni)
          </a>
          <a href="#funkcje" class="btn btn-outline-secondary btn-lg fw-semibold py-3 px-4">
            <i class="ti ti-circle-play me-2 fs-2"></i> Zobacz funkcje
          </a>
        </div>

        <div class="d-flex align-items-center gap-4 text-secondary pt-2">
          <div class="d-flex align-items-center gap-1">
            <i class="ti ti-check text-success fs-2"></i>
            <span class="small fw-semibold">Bez karty kredytowej</span>
          </div>
          <div class="d-flex align-items-center gap-1">
            <i class="ti ti-check text-success fs-2"></i>
            <span class="small fw-semibold">Konfiguracja w 5 minut</span>
          </div>
          <div class="d-flex align-items-center gap-1">
            <i class="ti ti-check text-success fs-2"></i>
            <span class="small fw-semibold">Zgodne z RODO</span>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-image-box">
          <img src="{{ asset('images/hero-mockup.jpg') }}" alt="Zajęciownia" class="w-100 h-auto d-block">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Pasek Statystyk i Efektywności -->
<section class="py-5 bg-white border-bottom border-top">
  <div class="container-xl">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <div class="p-3">
          <div class="h1 fw-extrabold text-primary mb-1">100%</div>
          <div class="text-secondary fw-semibold">Automatyzacja rozliczeń</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3">
          <div class="h1 fw-extrabold text-success mb-1">15h+</div>
          <div class="text-secondary fw-semibold">Oszczędności czasu / tydzień</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3">
          <div class="h1 fw-extrabold text-warning mb-1">5 / 15</div>
          <div class="text-secondary fw-semibold">Podgląd obłożenia grup</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="p-3">
          <div class="h1 fw-extrabold text-info mb-1">1-Click</div>
          <div class="text-secondary fw-semibold">Szybkie płatności BLIK i online</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sekcja: Dla Kogo i Funkcjonalności -->
<section id="dla-kogo" class="py-6 bg-light">
  <div class="container-xl">
    <div class="text-center max-w-2xl mx-auto mb-5">
      <span class="badge bg-blue-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">DEDYKOWANE PANELE</span>
      <h2 class="display-6 fw-extrabold text-dark">Dostosowane do Potrzeb Każdego Użytkownika</h2>
      <p class="text-secondary fs-3">Poznaj dedykowane funkcjonalności stworzone specjalnie dla organizatorów zajęć, instruktorów oraz rodziców.</p>
    </div>

    <div class="row g-4">
      <!-- Karta: Administratorzy -->
      <div class="col-lg-4">
        <div class="feature-card shadow-sm">
          <div class="feature-icon-wrapper bg-primary-subtle text-primary">
            <i class="ti ti-building-community"></i>
          </div>
          <h3 class="fw-bold text-dark fs-2 mb-2">Dla Administratorów</h3>
          <p class="text-secondary mb-4">Pełna kontrola nad placówką, harmonogramem zajęć, salami oraz budżetem placówki.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Obłożenie grup (np. 5/15):</strong> Alerty o limitach miejsc.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Różne typy rozliczeń:</strong> Ryczałt, za lekcję, indywidualne, grupowe.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Księgowanie wpłat:</strong> Zestawienie wpłat online i gotówki.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Zarządzanie kadrą:</strong> Przypisywanie lekcji i sal.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Powiadomienia:</strong> Wygodny system powiadomień.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Wszystko pod kontrolą:</strong> Wygodny system raportowania.</span></li>
          </ul>
          <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-bold mt-auto">Zarejestruj placówkę &rarr;</a>
        </div>
      </div>

      <!-- Karta: Nauczyciele -->
      <div class="col-lg-4">
        <div class="feature-card shadow-sm">
          <div class="feature-icon-wrapper bg-success-subtle text-success">
            <i class="ti ti-user-check"></i>
          </div>
          <h3 class="fw-bold text-dark fs-2 mb-2">Dla Prowadzących zajęcia</h3>
          <p class="text-secondary mb-4">Wygodny mobilny dziennik lekcyjny dostępny na dowolnym telefonie i tablecie.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Realizacja lekcji:</strong> Wybór daty i tematu zajęć.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Sprawdzanie obecności:</strong> Obecny, Nieobecny, Usprawiedliwiony.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Lista uczestników:</strong> Szybki wgląd do bazy grupy.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Plan zajęć:</strong> Porządek tygodniowych zajęć.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Powiadomienia:</strong> Wygodny system powiadomień.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Wszytko w jednym miejscu:</strong> Jedno konto - wiele szkół i zajęć.</span></li>
          </ul>
          <a href="{{ route('login') }}" class="btn btn-outline-success w-100 fw-bold mt-auto">Przejdź do dziennika &rarr;</a>
        </div>
      </div>

      <!-- Karta: Rodzice -->
      <div class="col-lg-4">
        <div class="feature-card shadow-sm">
          <div class="feature-icon-wrapper bg-warning-subtle text-warning">
            <i class="ti ti-wallet"></i>
          </div>
          <h3 class="fw-bold text-dark fs-2 mb-2">Dla Rodziców</h3>
          <p class="text-secondary mb-4">Przejrzysty portal do zapisów na zajęcia oraz rozliczeń opłat za wszystkich uczestników zajęć.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Pełna kontrola:</strong> Podsumowanie kosztów dla każdego dodanego uczestnika.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Płatności Online:</strong> Wszystkie zaległości lub poszczególne zajęcia.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Wszytko w jednym miejscu:</strong> Jedno konto - wiele szkół i zajęć.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Zwolnienia z opłat:</strong> Odliczanie usprawiedliwionych.</span></li>
          </ul>
          <a href="{{ route('login') }}" class="btn btn-outline-warning w-100 fw-bold mt-auto">Panel Rodzica &rarr;</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sekcja Prezentacji Wizualnej -->
<section id="funkcje" class="py-6 bg-white border-top border-bottom">
  <div class="container-xl">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <img src="{{ asset('images/features-overview.jpg') }}" alt="Moduły Zajęciownia.pl" class="img-fluid rounded-4 shadow-lg border">
      </div>
      <div class="col-lg-6">
        <span class="badge bg-green-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">INNOWACYJNA ARCHITEKTURA</span>
        <h2 class="display-6 fw-extrabold text-dark mb-3">Jedna Platforma, Zero Papierowej Dokumentacji</h2>
        <p class="fs-3 text-secondary mb-4">
          Zbudowaliśmy system w taki sposób, aby wyeliminować pomyłki w rozliczeniach i oszczędzić Twój czas. Pozycje billingowe są przeliczane dynamicznie z uwzględnieniem daty zapisu dziecka oraz frekwencji na lekcjach.
        </p>

        <div class="d-flex flex-column gap-3">
          <div class="d-flex gap-3">
            <div class="avatar bg-blue-subtle text-primary rounded-3 flex-shrink-0 fs-2">
              <i class="ti ti-calculator"></i>
            </div>
            <div>
              <h4 class="fw-bold text-dark mb-1">Precyzyjny Algorytm Billingowy</h4>
              <p class="text-secondary mb-0">System automatycznie ignoruje nieobecności usprawiedliwione w kursach indywidualnych i tworzy rzetelne podsumowania finansowe.</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="avatar bg-green-subtle text-success rounded-3 flex-shrink-0 fs-2">
              <i class="ti ti-credit-card"></i>
            </div>
            <div>
              <h4 class="fw-bold text-dark mb-1">Płatności Zbiorcze i Jednostkowe</h4>
              <p class="text-secondary mb-0">Rodzice mogą szybko opłacić całą zaległość przyciskiem „Zapłać online” lub uregulować wybrane pojedyncze zajęcia.</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="avatar bg-purple-subtle text-purple rounded-3 flex-shrink-0 fs-2">
              <i class="ti ti-device-mobile"></i>
            </div>
            <div>
              <h4 class="fw-bold text-dark mb-1">Projektowany Mobile-First</h4>
              <p class="text-secondary mb-0">Interfejs został zoptymalizowany pod kątem smartfonów, dzięki czemu kadra i rodzice mają wygodny dostęp w drodze.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sekcja Katalogu Szkół -->
<section class="py-6 bg-light">
  <div class="container-xl">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <span class="badge bg-blue-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">DOŁĄCZ DO NAS</span>
        <h2 class="display-6 fw-extrabold text-dark mb-0">Wybrane Placówki i Szkoły w Systemie</h2>
      </div>
      <a href="#" class="btn btn-primary fw-bold">
        <i class="ti ti-building-store me-1"></i> Zobacz pełny katalog szkół &rarr;
      </a>
    </div>

    <div class="row row-cards">
      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <span class="avatar avatar-md bg-blue-subtle text-primary rounded-circle mb-3 fs-2"><i class="ti ti-school"></i></span>
            <h3 class="fw-bold text-dark mb-1">MDK Centrum Kultury</h3>
            <p class="text-secondary small mb-3">Szkoła artystyczna, muzyczna oraz kółka zainteresowań dla dzieci i młodzieży.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge bg-green-lt"><i class="ti ti-check me-1"></i> Rekrutacja otwarta</span>
              <a href="#" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <span class="avatar avatar-md bg-purple-subtle text-purple rounded-circle mb-3 fs-2"><i class="ti ti-language"></i></span>
            <h3 class="fw-bold text-dark mb-1">Szkoła Językowa LinguaPlus</h3>
            <p class="text-secondary small mb-3">Zajęcia z języka angielskiego, hiszpańskiego oraz niemieckiego w małych grupach.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge bg-green-lt"><i class="ti ti-check me-1"></i> Płatności online</span>
              <a href="#" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body">
            <span class="avatar avatar-md bg-warning-subtle text-warning rounded-circle mb-3 fs-2"><i class="ti ti-run"></i></span>
            <h3 class="fw-bold text-dark mb-1">Akademia Sportu i Tańca</h3>
            <p class="text-secondary small mb-3">Sekcje gimnastyczne, treningi piłki nożnej oraz taniec nowoczesny dla dzieci.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge bg-green-lt"><i class="ti ti-check me-1"></i> Wolne miejsca</span>
              <a href="#" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sekcja Cennika -->
<section id="cennik" class="py-6 bg-white border-top border-bottom">
  <div class="container-xl">
    <div class="text-center max-w-2xl mx-auto mb-4">
      <span class="badge bg-green-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">PRZEJRZYSTY CENNIK</span>
      <h2 class="display-6 fw-extrabold text-dark">Wybierz Plan Dopasowany do Twojej Placówki</h2>
      <p class="text-secondary fs-3 mb-3">Elastyczne pakiety bez ukrytych opłat. Przetestuj dowolny pakiet za darmo przez 14 dni.</p>
      
      <!-- Przełącznik rozliczenia (Zachęta do umowy rocznej) -->
      <div class="d-inline-flex align-items-center bg-light p-1 rounded-pill border shadow-sm">
        <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold btn-primary" id="btn-billing-annual" onclick="switchBilling('annual')">
          Rozliczenie roczne <span class="badge bg-success text-white ms-1">2 mies. gratis (-20%)</span>
        </button>
        <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-secondary" id="btn-billing-monthly" onclick="switchBilling('monthly')">
          Rozliczenie miesięczne
        </button>
      </div>
    </div>

    <!-- Pasek gwarancji 14-dniowego testu -->
    <div class="alert alert-success bg-green-subtle border-green-subtle text-green-900 rounded-3 text-center mb-5 py-2 px-3 d-flex align-items-center justify-content-center gap-2">
      <i class="ti ti-circle-check-filled fs-2 text-success"></i>
      <span><strong>14 dni bezpłatnego testu</strong> bez podawania karty płatniczej. Pełny dostęp do modułów i natychmiastowe uruchomienie.</span>
    </div>

    <div class="row g-4 align-items-stretch">
      <!-- 1. Plan SOLO -->
      <div class="col-12 col-md-6 col-xl-3">
        <div class="pricing-card shadow-sm h-100 d-flex flex-column border">
          <div class="mb-3">
            <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1 rounded">🍮 SOLO</span>
            <h3 class="fw-bold text-dark fs-2 mt-2 mb-1">Solo</h3>
            <p class="text-secondary small mb-0">Dla korepetytorów i działalności 1-osobowej</p>
          </div>

          <div class="my-3 py-2 border-top border-bottom">
            <div class="pricing-price" data-annual-price="49" data-monthly-price="69">
              <span class="display-6 fw-extrabold text-dark price-val">49</span>
              <span class="fs-4 fw-bold text-dark">zł</span>
              <span class="text-secondary small">netto / mc</span>
            </div>
            <div class="text-muted small price-gross">(60,27 zł brutto)</div>
            <div class="text-success small fw-medium mt-1 billing-note">w umowie rocznej</div>
          </div>

          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary flex-grow-1">
            <li class="d-flex align-items-start">
              <i class="ti ti-users text-primary me-2 mt-1"></i>
              <span><strong>Do 30</strong> aktywnych zapisów <span class="text-muted small">(~20–30 uczestników)</span></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-user-check text-primary me-2 mt-1"></i>
              <span><strong>1 instruktor</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dziennik elektroniczny & zapisy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Płatności online lub gotówką</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dokumentacja RODO & Regulaminy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Powiadomienia E-mail & In-App</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-chart-bar text-muted me-2 mt-1"></i>
              <span>Raporty podstawowe</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-help text-muted me-2 mt-1"></i>
              <span>Wdrożenie: E-mail</span>
            </li>
          </ul>

          <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-bold py-2 mt-auto">
            Testuj 14 dni za darmo
          </a>
        </div>
      </div>

      <!-- 2. Plan START -->
      <div class="col-12 col-md-6 col-xl-3">
        <div class="pricing-card shadow-sm h-100 d-flex flex-column border">
          <div class="mb-3">
            <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1 rounded">🥇 START</span>
            <h3 class="fw-bold text-dark fs-2 mt-2 mb-1">Start</h3>
            <p class="text-secondary small mb-0">Mały GOK, kluby i sekcje</p>
          </div>

          <div class="my-3 py-2 border-top border-bottom">
            <div class="pricing-price" data-annual-price="149" data-monthly-price="189">
              <span class="display-6 fw-extrabold text-dark price-val">149</span>
              <span class="fs-4 fw-bold text-dark">zł</span>
              <span class="text-secondary small">netto / mc</span>
            </div>
            <div class="text-muted small price-gross">(183,27 zł brutto)</div>
            <div class="text-success small fw-medium mt-1 billing-note">w umowie rocznej</div>
          </div>

          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary flex-grow-1">
            <li class="d-flex align-items-start">
              <i class="ti ti-users text-primary me-2 mt-1"></i>
              <span><strong>Do 150</strong> aktywnych zapisów <span class="text-muted small">(~100-150 uczestników)</span></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-users-plus text-success me-2 mt-1"></i>
              <span><strong>Instruktorzy: Bez limitu</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dziennik elektroniczny & zapisy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Rozliczenia Tpay / Płatności z dołu</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dokumentacja RODO & Regulaminy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Powiadomienia E-mail & In-App</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-chart-bar text-primary me-2 mt-1"></i>
              <span>Raporty i zestawienia finansowe</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-mail text-primary me-2 mt-1"></i>
              <span>Wsparcie e-mail</span>
            </li>
          </ul>

          <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-bold py-2 mt-auto">
            Testuj 14 dni za darmo
          </a>
        </div>
      </div>

      <!-- 3. Plan STANDARD (Rekomendowany - Pozycjonowany) -->
      <div class="col-12 col-md-6 col-xl-3">
        <div class="pricing-card featured shadow-lg h-100 d-flex flex-column border-primary position-relative" style="background: linear-gradient(180deg, #f8faff 0%, #ffffff 100%); border-width: 2px;">
          <span class="pricing-badge bg-primary text-white fw-bold shadow-sm">
            <i class="ti ti-star-filled me-1 text-warning"></i> REKOMENDOWANY
          </span>

          <div class="mb-3">
            <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1 rounded">🥈 STANDARD</span>
            <h3 class="fw-bold text-primary fs-2 mt-2 mb-1">Standard</h3>
            <p class="text-secondary small mb-0">Średni MDK / OOK, placówki kultury</p>
          </div>

          <div class="my-3 py-2 border-top border-bottom border-primary-subtle bg-white rounded-2 px-2">
            <div class="pricing-price" data-annual-price="299" data-monthly-price="359">
              <span class="display-6 fw-extrabold text-primary price-val">299</span>
              <span class="fs-4 fw-bold text-primary">zł</span>
              <span class="text-secondary small">netto / mc</span>
            </div>
            <div class="text-muted small price-gross">(367,77 zł brutto)</div>
            <div class="text-success small fw-medium mt-1 billing-note">w umowie rocznej</div>
          </div>

          <div class="badge bg-blue-lt text-start text-wrap py-2 px-2 mb-3 small border border-blue-subtle">
            <i class="ti ti-sparkles text-primary me-1"></i> <strong>Najlepszy stosunek wartości:</strong> w cenie bezpłatne wdrożenie online i wsparcie startowe!
          </div>

          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary flex-grow-1">
            <li class="d-flex align-items-start">
              <i class="ti ti-users text-primary me-2 mt-1"></i>
              <span><strong>Do 350</strong> aktywnych zapisów <span class="text-muted small">(~200–350 uczestników)</span></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-users-plus text-success me-2 mt-1"></i>
              <span><strong>Instruktorzy: Bez limitu</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dziennik elektroniczny & zapisy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Rozliczenia Tpay / Płatności z dołu</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dokumentacja RODO & Regulaminy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Powiadomienia E-mail & In-App</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Wyróżnienie placówki w katalogu Zajęciowni</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-chart-arrows text-primary me-2 mt-1"></i>
              <span><strong>Raporty zaawansowane</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-device-laptop text-primary me-2 mt-1"></i>
              <span><strong>Dedykowane wdrożenie online</strong></span>
            </li>
          </ul>

          <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-100 fw-bold py-2 mt-auto shadow">
            Rozpocznij 14 dni testu
          </a>
        </div>
      </div>

      <!-- 4. Plan PRO / MAXI -->
      <div class="col-12 col-md-6 col-xl-3">
        <div class="pricing-card shadow-sm h-100 d-flex flex-column border">
          <div class="mb-3">
            <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1 rounded">🥉 PRO / MAXI</span>
            <h3 class="fw-bold text-dark fs-2 mt-2 mb-1">Pro / Maxi</h3>
            <p class="text-secondary small mb-0">Duży CKiS / MCK, wielosekcyjne ośrodki</p>
          </div>

          <div class="my-3 py-2 border-top border-bottom">
            <div class="pricing-price" data-annual-price="599" data-monthly-price="719">
              <span class="display-6 fw-extrabold text-dark price-val">599</span>
              <span class="fs-4 fw-bold text-dark">zł</span>
              <span class="text-secondary small">netto / mc</span>
            </div>
            <div class="text-muted small price-gross">(719,17 zł brutto)</div>
            <div class="text-success small fw-medium mt-1 billing-note">w umowie rocznej</div>
          </div>

          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary flex-grow-1">
            <li class="d-flex align-items-start">
              <i class="ti ti-users text-primary me-2 mt-1"></i>
              <span><strong>Do 800</strong> aktywnych zapisów <span class="text-muted small">(~500–800 dzieci)</span></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-users-plus text-success me-2 mt-1"></i>
              <span><strong>Instruktorzy: Bez limitu</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dziennik elektroniczny & zapisy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Rozliczenia Tpay / Płatności z dołu</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Dokumentacja RODO & Regulaminy</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Powiadomienia E-mail & In-App</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-check text-success me-2 mt-1"></i>
              <span>Wyróżnienie placówki na stronie głównej Zajęciowni</span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-chart-dots text-primary me-2 mt-1"></i>
              <span><strong>Raporty dedykowane</strong></span>
            </li>
            <li class="d-flex align-items-start">
              <i class="ti ti-headset text-primary me-2 mt-1"></i>
              <span><strong>Dedykowany opiekun</strong></span>
            </li>
          </ul>

          <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-bold py-2 mt-auto">
            Testuj 14 dni za darmo
          </a>
        </div>
      </div>
    </div>

    <!-- Tabela szczegółowego porównania modułów (wg specyfikacji) -->
    <div class="mt-5 pt-3">
      <div class="text-center mb-3">
        <button class="btn btn-outline-secondary btn-sm rounded-pill px-4" type="button" data-bs-toggle="collapse" data-bs-target="#pricingTableCollapse" aria-expanded="false" aria-controls="pricingTableCollapse">
          <i class="ti ti-table me-1"></i> Zobacz szczegółowe porównanie parametrów (tabela)
        </button>
      </div>

      <div class="collapse" id="pricingTableCollapse">
        <div class="table-responsive bg-white rounded-3 border shadow-sm p-3">
          <table class="table table-hover align-middle mb-0 text-center">
            <thead class="table-light">
              <tr>
                <th class="text-start" style="width: 28%;">Cecha / Moduł</th>
                <th style="width: 18%;">🍮 SOLO</th>
                <th style="width: 18%;">🥇 START</th>
                <th style="width: 18%;" class="bg-primary-subtle text-primary fw-bold">🥈 STANDARD (Rekomendowany)</th>
                <th style="width: 18%;">🥉 PRO / MAXI</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-start fw-semibold text-secondary">Główny odbiorca</td>
                <td>Korepetytor / 1 osoba</td>
                <td>Mały GOK</td>
                <td class="bg-primary-subtle fw-semibold">Średni Dom Kultury, Szkoła Językowa, Klub Sportowy</td>
                <td>Duże Centrum Kultury / Szkoła</td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Limit Aktywnych Zapisów</td>
                <td><strong>do 30</strong></td>
                <td><strong>do 150</strong></td>
                <td class="bg-primary-subtle fw-bold text-primary">do 350</td>
                <td><strong>do 800</strong></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Szacowana liczba dzieci</td>
                <td>(~20–30)</td>
                <td>(~100-150)</td>
                <td class="bg-primary-subtle fw-semibold">(~250–350)</td>
                <td>(~600–800)</td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Liczba Instruktorów</td>
                <td>1</td>
                <td><span class="badge bg-green-lt">Bez limitu</span></td>
                <td class="bg-primary-subtle"><span class="badge bg-primary">Bez limitu</span></td>
                <td><span class="badge bg-green-lt">Bez limitu</span></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Cena miesięczna (umowa roczna)</td>
                <td><strong>49 zł</strong> netto<br><span class="text-muted small">(60,27 zł brutto)</span></td>
                <td><strong>149 zł</strong> netto<br><span class="text-muted small">(183,27 zł brutto)</span></td>
                <td class="bg-primary-subtle fw-bold text-primary">299 zł netto<br><span class="text-muted small">(367,77 zł brutto)</span></td>
                <td><strong>599 zł</strong> netto<br><span class="text-muted small">(736,77 zł brutto)</span></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Cena przy rozliczeniu miesięcznym</td>
                <td>69 zł netto / mc</td>
                <td>189 zł netto / mc</td>
                <td class="bg-primary-subtle fw-semibold">359 zł netto / mc</td>
                <td>719 zł netto / mc</td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Dziennik elektroniczny & Zapisy</td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td class="bg-primary-subtle"><i class="ti ti-check text-primary fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Rozliczenia Tpay / Płatności z dołu</td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td class="bg-primary-subtle"><i class="ti ti-check text-primary fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Dokumentacja RODO & Regulaminy</td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td class="bg-primary-subtle"><i class="ti ti-check text-primary fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Powiadomienia E-mail & In-App</td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
                <td class="bg-primary-subtle"><i class="ti ti-check text-primary fs-2"></i></td>
                <td><i class="ti ti-check text-success fs-2"></i></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Raporty i zestawienia finansowe</td>
                <td>Podstawowe</td>
                <td>Tak</td>
                <td class="bg-primary-subtle fw-bold text-primary">Zaawansowane</td>
                <td><strong>Dedykowane</strong></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Pomoc przy wdrożeniu i migracji</td>
                <td>Samoobsługa</td>
                <td>Pomoc e-mail</td>
                <td class="bg-primary-subtle fw-bold text-primary">Dedykowane wdrożenie online</td>
                <td><strong>Dedykowany opiekun</strong></td>
              </tr>
              <tr>
                <td class="text-start fw-semibold text-secondary">Promocja placówki w Zajęciowni</td>
                <td>Zwykły wpis</td>
                <td>Zwykły wpis</td>
                <td class="bg-primary-subtle fw-bold text-primary">Wyróżnienie w katalogu</td>
                <td><strong>Wyróżnienie na stronie głównej + katalogu</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Skrypt obsługi przełącznika rozliczenia cennika -->
<script>
function switchBilling(type) {
  const btnAnnual = document.getElementById('btn-billing-annual');
  const btnMonthly = document.getElementById('btn-billing-monthly');
  const priceElements = document.querySelectorAll('.pricing-price');
  const grossPrices = {
    annual: ['(60,27 zł brutto)', '(183,27 zł brutto)', '(429,27 zł brutto)', '(736,77 zł brutto)'],
    monthly: ['(84,87 zł brutto)', '(232,47 zł brutto)', '(527,67 zł brutto)', '(921,27 zł brutto)']
  };

  if (type === 'annual') {
    btnAnnual.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold btn-primary';
    btnMonthly.className = 'btn btn-sm rounded-pill px-3 py-2 fw-semibold text-secondary';
    
    priceElements.forEach((el, index) => {
      const val = el.getAttribute('data-annual-price');
      el.querySelector('.price-val').textContent = val;
      const card = el.closest('.pricing-card');
      card.querySelector('.price-gross').textContent = grossPrices.annual[index];
      card.querySelector('.billing-note').textContent = 'w umowie rocznej';
      card.querySelector('.billing-note').className = 'text-success small fw-medium mt-1 billing-note';
    });
  } else {
    btnMonthly.className = 'btn btn-sm rounded-pill px-3 py-2 fw-bold btn-primary';
    btnAnnual.className = 'btn btn-sm rounded-pill px-3 py-2 fw-semibold text-secondary';
    
    priceElements.forEach((el, index) => {
      const val = el.getAttribute('data-monthly-price');
      el.querySelector('.price-val').textContent = val;
      const card = el.closest('.pricing-card');
      card.querySelector('.price-gross').textContent = grossPrices.monthly[index];
      card.querySelector('.billing-note').textContent = 'rozliczenie miesięczne (bez zobowiązań)';
      card.querySelector('.billing-note').className = 'text-muted small fw-medium mt-1 billing-note';
    });
  }
}
</script>

<!-- Sekcja FAQ -->
<section id="faq" class="py-6 bg-light front-faq">
  <div class="container-xl max-w-4xl">
    <div class="text-center mb-5">
      <span class="badge bg-blue-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">ODPOWIEDZI NA PYTANIA</span>
      <h2 class="display-6 fw-extrabold text-dark">Najczęściej Zadawane Pytania (FAQ)</h2>
      <p class="text-secondary fs-3">Wszystko, co musisz wiedzieć o wdrożeniu i codziennym korzystaniu z Zajęciownia.pl.</p>
    </div>

    <div class="accordion shadow-sm" id="accordion-faq">
      <!-- FAQ 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-faq-1">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1" aria-expanded="true" aria-controls="faq-1">
            <i class="ti ti-help-circle text-primary me-2 fs-2"></i> Jak działa automatyczne naliczanie opłat za zajęcia pozalekcyjne?
          </button>
        </h2>
        <div id="faq-1" class="accordion-collapse collapse show" aria-labelledby="heading-faq-1" data-bs-parent="#accordion-faq">
          <div class="accordion-body text-secondary fs-4 leading-relaxed">
            System Zajęciownia.pl wspiera 3 elastyczne typy billingowe: stały ryczałt miesięczny, stawkę za zrealizowaną lekcję oraz opłaty jednorazowe. Ponadto rozliczenie dziecka jest naliczane precyzyjnie od momentu jego dołączenia do grupy, bez wstecznych zaległości.
          </div>
        </div>
      </div>

      <!-- FAQ 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-faq-2">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2" aria-expanded="false" aria-controls="faq-2">
            <i class="ti ti-help-circle text-primary me-2 fs-2"></i> Czy rodzice mogą płacić online za pojedyncze zajęcia lub zaległości?
          </button>
        </h2>
        <div id="faq-2" class="accordion-collapse collapse" aria-labelledby="heading-faq-2" data-bs-parent="#accordion-faq">
          <div class="accordion-body text-secondary fs-4 leading-relaxed">
            Tak! Użytkownik w swoim panelu widzi przejrzystą listę nieopłaconych zajęć z rozbiciem na dodanych uczestników. Może użyć przycisku głównego <strong>„Zapłać online”</strong>, aby uregulować całą zaległość naraz, lub kliknąć mały przycisk <strong>„Zapłać”</strong> przy konkretnych zajęciach.
          </div>
        </div>
      </div>

      <!-- FAQ 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-faq-3">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3" aria-expanded="false" aria-controls="faq-3">
            <i class="ti ti-help-circle text-primary me-2 fs-2"></i> W jaki sposób system uwzględnia nieobecności usprawiedliwione?
          </button>
        </h2>
        <div id="faq-3" class="accordion-collapse collapse" aria-labelledby="heading-faq-3" data-bs-parent="#accordion-faq">
          <div class="accordion-body text-secondary fs-4 leading-relaxed">
            Gdy nauczyciel odznacza obecność dziecka jako <em>usprawiedliwioną</em>, moduł rozliczeniowy automatycznie odejmuje to zajęcie z rachunku miesięcznego w kursach indywidualnych oraz kursach rozliczanych za lekcję.
          </div>
        </div>
      </div>

      <!-- FAQ 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-faq-4">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4" aria-expanded="false" aria-controls="faq-4">
            <i class="ti ti-help-circle text-primary me-2 fs-2"></i> Czy nauczyciele mogą sprawdzać obecność na telefonie lub tablecie?
          </button>
        </h2>
        <div id="faq-4" class="accordion-collapse collapse" aria-labelledby="heading-faq-4" data-bs-parent="#accordion-faq">
          <div class="accordion-body text-secondary fs-4 leading-relaxed">
            Tak, panel nauczyciela został zaprojektowany w standardzie Mobile-First. Nauczyciel wchodząc na zajęcia wybiera kurs, tworzy lekcję z datą i jednym kliknięciem odznacza obecności uczniów.
          </div>
        </div>
      </div>

      <!-- FAQ 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading-faq-5">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-5" aria-expanded="false" aria-controls="faq-5">
            <i class="ti ti-help-circle text-primary me-2 fs-2"></i> Co oznacza wskaźnik obłożenia grupy (np. 5/15 miejsc)?
          </button>
        </h2>
        <div id="faq-5" class="accordion-collapse collapse" aria-labelledby="heading-faq-5" data-bs-parent="#accordion-faq">
          <div class="accordion-body text-secondary fs-4 leading-relaxed">
            Wskaźnik obłożenia informuje administratora oraz nauczyciela o liczbie aktualnie zapisanych dzieci w stosunku do maksymalnego limitu sali/kursu. Zapobiega to przepełnieniu grup i ułatwia planowanie nowych sekcji.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Baner CTA -->
<section class="py-6 bg-white">
  <div class="container-xl">
    <div class="cta-banner text-center">
      <h2 class="display-5 fw-extrabold mb-3 test-white">Gotowy na Automatyzację Twojej Pracy?</h2>
      <p class="fs-2 text-blue-100 max-w-2xl mx-auto mb-4">
        Dołącz do nowoczesnych placówek edukacyjnych. Zarejestruj się w 2 minuty i przetestuj pełne możliwości platformy przez 14 dni bez żadnych zobowiązań.
      </p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('register') }}" class="btn btn-success btn-lg fw-bold px-5 py-3 shadow">
          <i class="ti ti-user-plus me-2 fs-2"></i> Załóż konto placówki
        </a>
        <a href="#" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3">
          <i class="ti ti-building me-2 fs-2"></i> Umów się na prezentację
        </a>
      </div>
    </div>
  </div>
</section>

@endsection