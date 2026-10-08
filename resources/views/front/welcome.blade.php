@extends('layouts.front')

@section('title', 'EduZajęcia.net - System Obsługi Zajęć Pozalekcyjnych, Szkół i Rozliczeń Online')
@section('meta_description', 'Kompleksowa platforma SaaS do zarządzania zajęciami pozalekcyjnymi, szkołami językowymi i MDK. Zapisy online, listy obecności, obłożenie grup (5/15) i rozliczenia dla rodziców.')

@section('content')

<!-- JSON-LD Structured Data (Schema.org) for SEO & AI Search -->
<script type="application/ld+json">
{
  "{{ '@context' }}": "https://schema.org",
  "@graph": [
    {
      "@type": "SoftwareApplication",
      "name": "EduZajęcia.net",
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
      "name": "EduZajęcia.net",
      "url": "{{ url('/') }}",
      "logo": "{{ asset('images/logo.jpg') }}",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+48 22 123 45 67",
        "contactType": "customer service"
      }
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
          Jedno narzędzie dla <strong>Dyrektorów Szkół, Nauczycieli i Rodziców</strong>. Automatyczne rozliczenia od momentu zapisu, listy obecności oraz płatności online 1-Click.
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
          <img src="{{ asset('images/hero-mockup.jpg') }}" alt="Mokup aplikacji EduZajęcia.net" class="w-100 h-auto d-block">
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
          <div class="text-secondary fw-semibold">Szybkie płatności BLIK</div>
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
      <p class="text-secondary fs-3">Poznaj dedykowane funkcjonalności stworzone specjalnie dla administratorów szkół, kadry nauczycielskiej oraz rodziców.</p>
    </div>

    <div class="row g-4">
      <!-- Karta: Administratorzy -->
      <div class="col-lg-4">
        <div class="feature-card shadow-sm">
          <div class="feature-icon-wrapper bg-primary-subtle text-primary">
            <i class="ti ti-building-community"></i>
          </div>
          <h3 class="fw-bold text-dark fs-2 mb-2">Dla Administratorów</h3>
          <p class="text-secondary mb-4">Pełna kontrola nad placówką, harmonogramem zajęć, salami oraz budżetem szkoły.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Obłożenie grup (np. 5/15):</strong> Alerty o limitach miejsc.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>3 typy rozliczeń:</strong> Ryczałt, za lekcję, indywidualne.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Księgowanie wpłat:</strong> Zestawienie wpłat online i gotówki.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-primary me-2 fs-3"></i> <span><strong>Zarządzanie kadrą:</strong> Przypisywanie lekcji i sal.</span></li>
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
          <h3 class="fw-bold text-dark fs-2 mb-2">Dla Nauczycieli</h3>
          <p class="text-secondary mb-4">Wygodny mobilny dziennik lekcyjny dostępny na dowolnym telefonie i tablecie.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Realizacja lekcji:</strong> Wybór daty i tematu zajęć.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Sprawdzanie obecności:</strong> Obecny, Nieobecny, Usprawiedliwiony.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Lista dzieci:</strong> Szybki wgląd do bazy grupy.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-success me-2 fs-3"></i> <span><strong>Plan lekcji:</strong> Porządek tygodniowych zajęć.</span></li>
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
          <p class="text-secondary mb-4">Przejrzysty portal do zapisów na zajęcia oraz rozliczeń opłat za wszystkie dzieci.</p>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4">
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Accordion dzieci:</strong> Podsumowanie kosztów na dziecko.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Płatności Online:</strong> 1-Click za całość lub 1 pozycję.</span></li>
            <li class="d-flex align-items-center"><i class="ti ti-circle-check-filled text-warning me-2 fs-3"></i> <span><strong>Rozliczenie od zapisu:</strong> Brak starych opłat.</span></li>
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
        <img src="{{ asset('images/features-overview.jpg') }}" alt="Moduły EduZajęcia.net" class="img-fluid rounded-4 shadow-lg border">
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
      <a href="{{ route('schools.index') }}" class="btn btn-primary fw-bold">
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
              <a href="{{ route('schools.index') }}" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
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
              <a href="{{ route('schools.index') }}" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
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
              <a href="{{ route('schools.index') }}" class="btn btn-sm btn-outline-primary">Zobacz kursy</a>
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
    <div class="text-center max-w-2xl mx-auto mb-5">
      <span class="badge bg-green-subtle text-success fw-bold px-3 py-2 rounded-pill mb-2">PRZERZYSTY CENNIK</span>
      <h2 class="display-6 fw-extrabold text-dark">Proste Plany Bez Ukrytych Kosztów</h2>
      <p class="text-secondary fs-3">Wybierz plan dopasowany do wielkości Twojej szkoły lub placówki edukacyjnej.</p>
    </div>

    <div class="row g-4 align-items-stretch">
      <!-- Plan Start -->
      <div class="col-lg-4">
        <div class="pricing-card shadow-sm">
          <h3 class="fw-bold text-dark fs-2 mb-1">Plan Start</h3>
          <p class="text-secondary small mb-4">Dla małych kółek zainteresowań i samodzielnych instruktorów.</p>
          <div class="mb-4">
            <span class="display-5 fw-extrabold text-dark">49 zł</span>
            <span class="text-secondary">/ miesiąc</span>
          </div>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary">
            <li><i class="ti ti-check text-success me-2"></i> Do 50 uczniów</li>
            <li><i class="ti ti-check text-success me-2"></i> Zarządzanie kursami i salami</li>
            <li><i class="ti ti-check text-success me-2"></i> Dziennik obecności na telefonie</li>
            <li><i class="ti ti-check text-success me-2"></i> Podstawowe rozliczenia opłat</li>
          </ul>
          <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-bold mt-auto py-2">Wybierz Plan Start</a>
        </div>
      </div>

      <!-- Plan Pro (Rekomendowany) -->
      <div class="col-lg-4">
        <div class="pricing-card featured shadow">
          <span class="pricing-badge">Najchętniej wybierany</span>
          <h3 class="fw-bold text-primary fs-2 mb-1">Plan Pro</h3>
          <p class="text-secondary small mb-4">Dla szkół językowych, muzycznych, sportowych i MDK-ów.</p>
          <div class="mb-4">
            <span class="display-5 fw-extrabold text-primary">149 zł</span>
            <span class="text-secondary">/ miesiąc</span>
          </div>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary">
            <li><i class="ti ti-check text-success me-2"></i> <strong>Nielimitowana liczba dzieci</strong></li>
            <li><i class="ti ti-check text-success me-2"></i> Płatności online BLIK i kartą dla rodziców</li>
            <li><i class="ti ti-check text-success me-2"></i> Wskaźnik obłożenia grup (np. 5/15)</li>
            <li><i class="ti ti-check text-success me-2"></i> Portal Rodzica z widokiem Accordion</li>
            <li><i class="ti ti-check text-success me-2"></i> Automatyczne rozliczenia od zapisu</li>
          </ul>
          <a href="{{ route('register') }}" class="btn btn-primary w-100 fw-bold mt-auto py-2 shadow-sm">Rozpocznij 14 dni za darmo</a>
        </div>
      </div>

      <!-- Plan Enterprise -->
      <div class="col-lg-4">
        <div class="pricing-card shadow-sm">
          <h3 class="fw-bold text-dark fs-2 mb-1">Plan Enterprise</h3>
          <p class="text-secondary small mb-4">Dla sieci szkół, dużych placówek oraz jednostek samorządowych.</p>
          <div class="mb-4">
            <span class="display-5 fw-extrabold text-dark">399 zł</span>
            <span class="text-secondary">/ miesiąc</span>
          </div>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-4 fs-4 text-secondary">
            <li><i class="ti ti-check text-success me-2"></i> Obsługa wielu placówek i szkół</li>
            <li><i class="ti ti-check text-success me-2"></i> Dedykowany opiekun konta</li>
            <li><i class="ti ti-check text-success me-2"></i> Dedykowane integracje płatności</li>
            <li><i class="ti ti-check text-success me-2"></i> Dedykowany serwer i SLA 99.9%</li>
          </ul>
          <a href="{{ route('register') }}" class="btn btn-outline-dark w-100 fw-bold mt-auto py-2">Skontaktuj się z nami</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Sekcja FAQ -->
<section id="faq" class="py-6 bg-light front-faq">
  <div class="container-xl max-w-4xl">
    <div class="text-center mb-5">
      <span class="badge bg-blue-subtle text-primary fw-bold px-3 py-2 rounded-pill mb-2">ODPOWIEDZI NA PYTANIA</span>
      <h2 class="display-6 fw-extrabold text-dark">Najczęściej Zadawane Pytania (FAQ)</h2>
      <p class="text-secondary fs-3">Wszystko, co musisz wiedzieć o wdrożeniu i codziennym korzystaniu z EduZajęcia.net.</p>
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
            System EduZajęcia.net wspiera 3 elastyczne typy billingowe: stały ryczałt miesięczny (<code>monthly_flat</code>), stawkę za zrealizowaną lekcję (<code>per_lesson_monthly</code>) oraz opłaty jednorazowe. Ponadto rozliczenie dziecka jest naliczane precyzyjnie od momentu jego dołączenia do grupy, bez wstecznych zaległości.
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
            Tak! Rodzic w swoim panelu widzi przejrzystą listę nieopłaconych zajęć z rozbiciem na poszczególne dzieci (accordion). Może użyć przycisku głównego <strong>„Zapłać online”</strong>, aby uregulować całą zaległość naraz, lub kliknąć mały przycisk <strong>„Zapłać”</strong> przy konkretnym kursie.
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
      <h2 class="display-5 fw-extrabold mb-3">Gotowy na Automatyzację Twojej Szkoły?</h2>
      <p class="fs-2 text-blue-100 max-w-2xl mx-auto mb-4">
        Dołącz do nowoczesnych placówek edukacyjnych. Zarejestruj się w 2 minuty i przetestuj pełne możliwości platformy przez 14 dni bez zobowiązań.
      </p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('register') }}" class="btn btn-success btn-lg fw-bold px-5 py-3 shadow">
          <i class="ti ti-user-plus me-2 fs-2"></i> Załóż konto placówki
        </a>
        <a href="{{ route('schools.index') }}" class="btn btn-outline-light btn-lg fw-semibold px-4 py-3">
          <i class="ti ti-building me-2 fs-2"></i> Przeglądaj katalog szkół
        </a>
      </div>
    </div>
  </div>
</section>

@endsection