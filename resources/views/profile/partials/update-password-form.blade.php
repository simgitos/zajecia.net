<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label class="form-label fw-bold" for="update_password_current_password">Aktualne hasło</label>   
            <input type="password" id="update_password_current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" value="{{ old('current_password', $user->current_password) }}" required autocomplete="current-password">  
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="form-label fw-bold" for="update_password_password">Nowe hasło</label>
            <input type="password" id="update_password_password" name="password" class="form-control @error('password') is-invalid @enderror" value="{{ old('password', $user->password) }}" required autocomplete="new-password">  
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="form-label fw-bold" for="update_password_password_confirmation">Potwierdź nowe hasło</label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" value="{{ old('password_confirmation', $user->password_confirmation) }}" required autocomplete="new-password">  
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="btn btn-primary">Zapisz zmiany</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
