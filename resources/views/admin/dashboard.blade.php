<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
    <div class="card my-6 col-md-6">
        <div class="card-header">
            <h2>Dane kontaktowe</h2>
        </div>
        <div class="card-body">
            <x-input name="fieldset-name" label="Full name" required />
            <x-input name="company" label="Company" required />
            <x-input name="fieldset-email" label="Email" type="email" required />
            <x-input name="fieldset-phone" label="Phone number" type="tel" />

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                <label class="form-check-label required" for="terms">I agree to the Terms &amp; Conditions</label>
                @error('terms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3"><label class="form-label required" for="fieldset-name">Full name</label><input type="text"
                    id="fieldset-name" class="form-control" autocomplete="name" required /></div>
            <div class="mb-3"><label class="form-label required" for="company">Company</label><input type="text"
                    id="company" class="form-control" autocomplete="organization" required /></div>
            <div class="mb-3"><label class="form-label required" for="fieldset-email">Email</label><input type="email"
                    id="fieldset-email" class="form-control" autocomplete="email" required /></div>
            <div class="mb-3"><label class="form-label" for="fieldset-phone">Phone number</label><input type="tel"
                    id="fieldset-phone" class="form-control" autocomplete="tel" /></div><label class="form-check"><input
                    type="checkbox" class="form-check-input" required /><span class="form-check-label required">I agree
                    to
                    the Terms &amp; Conditions</span></label>

        </div>
    </div>
    <div class="card my-6 col-md-6">
        <div class="card-header">
            <h2>Dane kontaktowe</h2>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>John</td>
                        <td>Doe</td>
                        <td>@social</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="accordion" id="accordion-overview">
        <div class="accordion-item bg-surface">
            <h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse"
                    data-bs-target="#overview-item-1" aria-expanded="true" aria-controls="overview-item-1"> Account
                    details <span
                        class="accordion-button-toggle"><!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true" focusable="false" class="icon">
                            <path d="M6 9l6 6l6 -6" />
                        </svg></span> </button></h3>
            <div id="overview-item-1" class="accordion-collapse collapse show" data-bs-parent="#accordion-overview">
                <div class="accordion-body">Update profile fields, contact details, and team role settings in this
                    section.</div>
            </div>
        </div>
        <div class="accordion-item bg-surface">
            <h3 class="accordion-header"><button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#overview-item-2" aria-expanded="false"
                    aria-controls="overview-item-2"> Security settings <span
                        class="accordion-button-toggle"><!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true" focusable="false" class="icon">
                            <path d="M6 9l6 6l6 -6" />
                        </svg></span> </button></h3>
            <div id="overview-item-2" class="accordion-collapse collapse" data-bs-parent="#accordion-overview">
                <div class="accordion-body">Configure password policy, two-factor requirements, and active device
                    sessions.</div>
            </div>
        </div>
        <div class="accordion-item bg-surface">
            <h3 class="accordion-header"><button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#overview-item-3" aria-expanded="false"
                    aria-controls="overview-item-3"> Notification preferences <span
                        class="accordion-button-toggle"><!-- Download SVG icon from http://tabler.io/icons/icon/chevron-down -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            aria-hidden="true" focusable="false" class="icon">
                            <path d="M6 9l6 6l6 -6" />
                        </svg></span> </button></h3>
            <div id="overview-item-3" class="accordion-collapse collapse" data-bs-parent="#accordion-overview">
                <div class="accordion-body"> Choose which updates to receive by email, browser push, and weekly
                    digest.
                    You can set separate rules for account alerts and product announcements. </div>
            </div>
        </div>
    </div>
</x-app-layout>